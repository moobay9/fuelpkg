# fuelpkg
Funaffect FuelPHP original pkg

## Observer_Format

利用方法

Modelの _properties を以下要領で修正「+ は追記箇所」
```
<?php

class Model_User extends \Orm\Model
{
    protected static $_properties = [
        'id',
        ・・・
        ・・・省略
        ・・・
        'deleted_at' => [
+            'format'    => ['format_date' => '%Y/%m/%d %H:%M:%S'],
        ],
        'created_at' => [
+            'format'    => ['format_date' => '%Y/%m/%d %H:%M:%S'],
        ],
        'updated_at' => [
+            'format'    => ['format_date' => '%Y/%m/%d %H:%M:%S'],
        ],
    ];

    protected static $_observers = [
        'Orm\Observer_CreatedAt' => [
            'events' => ['before_insert'],
            'mysql_timestamp' => false,
        ],
        'Orm\Observer_UpdatedAt' => [
            'events' => ['before_update'],
            'mysql_timestamp' => false,
        ],
        +'Funaffect\Observer_Format' => [
        +    'events' => ['after_save', 'after_load'],
        +],
    ];
}

```

View側にて呼び出し
```
<? foreach ($users as $item): ?>
  <?= $item->formatted_created_at; ?>
<? endforeach; ?>
```

## Observer_Replacement

Modelの _properties を以下要領で修正「+ は追記箇所」
```
<?php

class Model_User extends \Orm\Model
{
    protected static $_properties = [
        'id',
        ・・・
        ・・・省略
        ・・・
        'group' => [
            // lang/[言語]/selector.php 
            // 'group' => [1 => '一般', '10' => '管理者', '50' => 'システム', '100' => 'システム管理者']
            // selectorではなく、common参照の場合は、selectorの箇所を修正。
            'replacement'  => 'selector.group', 
        ],
    ];

    protected static $_observers = [
        'Orm\Observer_CreatedAt' => [
            'events' => ['before_insert'],
            'mysql_timestamp' => false,
        ],
        'Orm\Observer_UpdatedAt' => [
            'events' => ['before_update'],
            'mysql_timestamp' => false,
        ],
        +'Funaffect\Observer_Replacement' => [
        +    'events' => ['after_save', 'after_load'],
        +],
    ];
}

```

langファイルを用意

View側にて呼び出し
```
<? foreach ($users as $item): ?>
  <?= $item->replaced_group; ?>
<? endforeach; ?>
```
