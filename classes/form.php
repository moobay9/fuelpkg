<?php
/**
 * フォームプロパティ設定関連クラス
 *
 *   \Input::method() !== 'POST' and \Response::redirect('mypage/profile/edit');
 *
 *   // フォームプロパティのロード
 *   \Config::load('app_form.yml', 'form', false, true);
 *   $form_properties = \Config::get('form.mypage.profile.edit');
 *
 *   $user_id = $this->current_user['id'];
 *
 *   $user_model = \Model_User::find($user_id);
 *
 *   // フォームプロパティにオブジェクトデータを詰める（valueの差し替え）
 *   $formdata = \Form::placeholder_object($user_model, $form_properties,  *\Input::post());
 *
 *   $val = \Formvalidation\profile::change($formdata);
 *
 *   // バリデーション
 *   if ($val->run())
 *   {
 *       $validdata = $val->validated();
 *
 *       $this->template->set_global('formdata', $formdata);
 *       $this->template->title   = 'マイページ | プロフィール確認';
 *       $this->template->content = \View::forge('mypage/profile/confirm');
 *   }
 *   else
 *   {
 *       // エラーメッセージをフォームプロパティに格納する
 *       $formdata = \Form::setup_errormessage($formdata, $val);
 *       $this->template->title = 'マイページ | プロフィール編集';
 *       $this->template->set_global('formdata', $formdata);
 *       $this->template->content = \View::forge('mypage/profile/edit');
 *   }
 *
 * @package Form
 * @author  Isamu Watanabe
 * @version 1.0.0
 */
class Form extends \Fuel\Core\Form
{
    /**
     * デフォルトのフォーム用のデータ
     */
    protected static $_default_form_data = [
        'label'           => null,
        'label2'          => null,
        'type'            => 'text',
        'value'           => null,
        'alt'             => null,
        'options'         => [],
        'required'        => false,
        'minlength'       => null,
        'maxlength'       => null,
        'min'             => null,
        'max'             => null,
        'placeholder'     => null,
        'class'           => null,
        'class2'          => null,
        'attributes'      => [],
        'prefix'          => null,
        'suffix'          => null,
        'key'             => null,
        'description'     => null,
        'help'            => null,
        'error'           => null,
        'file'            => null,
        'file_dir_key'    => null,
        'format'          => false,
        'replacement'     => false,
        'formatted'       => false,
    ];

    // 言語ファイルやコンフィグファイルの値と置き換える対象
    protected static $_replace_form_data = [
        'label'           => null,
        'label2'          => null,
        'alt'             => null,
        'options'         => [],
        'placeholder'     => null,
        'class'           => null,
        'class2'          => null,
        'description'     => null,
        'help'            => null,

    ];

    /**
     * フォームのプロパティ設定を元にフォームプロパティを構築
     *
     * @param  object $model       Model::forge() or Model::find(id)
     * @param  array  $form_config config/app_form.yml
     * @param  array  $post        Input::post()
     * @return array  $properties  フォームプロパティ
     */
    public static function placeholder_object($model, $form_config, $post = [])
    {
        $properties = static::load_property($form_config);

        if (empty($model)) throw new \Exception('not found model.');

        foreach (array_keys($model::properties()) as $prop)
        {
            if (! in_array($prop, array_keys($properties))) continue;

            $value = $model->get($prop);

            if ($value !== false) $properties[$prop]['value'] = $value;
        }

        if ( ! empty($post))
        {
            $properties = static::place_array_walk($post, $properties);
        }

        return $properties;
    }

    /**
     * データベースに保存するためのデータを構築
     *
     * @param  array  $formdata  フォームプロパティ
     * @param  array  $validdata $val->validated()
     * @param  object $model     Model::forge() or Model::find(id)
     * @return array  $data      保存用データ
     */
    public static function setup_savedata($formdata, $validdata, $model)
    {
        // 入力値 > DBデータ > システムデフォルトで保存データをセットアップ
        $formdata = static::place_array_walk($validdata, $formdata);

        $data = [];
        foreach (array_keys($model::properties()) as $prop)
        {
            $dat = false;
            if (isset($formdata[$prop])) $dat = $formdata[$prop]['value'];
            if ($dat !== false)
            {
                // 値が異なる場合のみそのフィールドを更新対象とする
                if ($dat !== $model->{$prop} and $dat !== '')
                {
                    $data[$prop]  = $dat;
                }
            }
        }

        return $data;
    }

    /**
     * バリデーション後のエラーメッセージをフォームプロパティにセット
     *
     * @param  array  $formdata フォームプロパティ
     * @param  object $val      バリデーションオブジェクト
     * @return array  $formdata フォームプロパティ
     */
    public static function setup_errormessage($formdata, $val)
    {
        // エラーを配列に格納する
        foreach ($val->error() as $key => $error)
        {
            $formdata[$key]['value'] = $error->value;
            $formdata[$key]['error'] = ''.$error;
        }

        foreach ($val->validated() as $key => $value)
        {
            $formdata[$key]['value'] = $value;
        }

        return $formdata;
    }

    /**
     * フォームプロパティのデフォルトを構築
     *
     * @param  array $form_config config/app_form.yml
     * @return array $form_config 構築されたデフォルトフォームプロパティ
     */
    protected static function load_property($form_config)
    {
        $replace = function(&$item, $key, $default_form_data) {
            $value = '';
            if (in_array($key, $default_form_data))
            {
                $_lang   = \Lang::get($item);
                $_config = \Config::get($item);
                $value   = $_lang;

                ! $_lang and $value = $_config;
                $value and $item    = $value;
            }
        };

        array_walk_recursive($form_config, $replace, array_keys(static::$_replace_form_data));

        foreach (array_keys($form_config) as $key)
        {
            if ( ! isset($form_config[$key]['key']))
            {
                $form_config[$key]['key'] = $key;
            }

            // キーを設定する
            if (! is_string($form_config[$key]['key']) && ! is_numeric($form_config[$key]['key']))
            {
                $form_config[$key]['key'] = $key;
            }
        }

        return $form_config;
    }

    /**
     * フォームプロパティに値をセット
     * key - value の value を $record['value'] にセット
     *
     * @param  array $source Input::post は $val->validated()など
     * @param  array $target フォームプロパティ
     * @return array $target 値セットされたフォームプロパティ
     */
    protected static function place_array_walk($source, $target)
    {
        // 入力値 > DBデータ > システムデフォルトで保存データをセットアップ
        array_walk(
            $target,
            function (&$record, $key, $params) {
                if (isset($params[$key])) $record['value'] = $params[$key];
            },
            $source
        );

        return $target;
    }
}
