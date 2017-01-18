<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */
namespace Funaffect;

/**
 * Observer class to replace the properties of the model after load.
 */
class Observer_Replacement extends \Orm\Observer
{
    /**
     * @var string default prefix
     */
    public static $prefix = 'replaced_';

    /**
     * @var string prefix for
     */
    protected $_prefix;

    /**
     * Set the properties for this observer instance, based on the parent model's
     * configuration or the defined defaults.
     *
     * @param  string  Model class this observer is called on
     */
    public function __construct($class)
    {
        $props = $class::observers(get_class($this));
        $this->_prefix = isset($props['prefix']) ? $props['prefix'] : static::$prefix;
    }

    /**
     * Execute after saving the Model.
     *
     * @param Model The model object to replace
     */
    public function after_save(\Orm\Model $obj)
    {
        $this->replace($obj);
    }

    /**
     * Execute after loading the Model.
     *
     * @param Model The model object to replace
     */
    public function after_load(\Orm\Model $obj)
    {
        $this->replace($obj);
    }

    /**
     * Replace the model.
     *
     * @param Model The model object to replace
     */
    public function replace(\Orm\Model $obj)
    {
        $class        = is_object($obj) ? get_class($obj) : $obj;
        $primary_keys = is_object($obj) ? $obj->primary_key() : $class::primary_key();
        $properties   = is_object($obj) ? $obj->properties() : $class::properties();
        foreach ($properties as $p => $settings)
        {
            if (\Arr::get($settings, 'skip', in_array($p, $primary_keys)))
            {
                continue;
            }

            if ( ! empty($settings['replacement']))
            {
                $callback = function ($value) use ($settings, &$callback)
                {
                    if (is_array($value))
                    {
                        foreach ($value as $key => $val)
                        {
                            $value[$key] = $callback($val);
                        }
                    }
                    else
                    {
                        $data = $settings['replacement'];
                        if (is_string($data))
                        {
                            $value = \Lang::get($data.'.'.$value, array(), \Config::get($data.'.'.$value, $data));
                        }
                        else if (is_array($data))
                        {
                            if (isset($data[$value]))
                            {
                                $value = $data[$value];
                            }
                            else
                            {
                                reset($data);
                                switch (key($data))
                                {
                                    case 'lang':
                                        $value = \Lang::get(reset($data).'.'.$value);
                                        break;

                                    case 'config':
                                        $value = \Config::get(reset($data).'.'.$value);
                                        break;

                                    default:
                                        $value = null;
                                        break;
                                }
                            }
                        }
                        else
                        {
                            $value = $data;
                        }
                    }

                    return $value;
                };
                $obj->{$this->_prefix.$p} = $callback($obj->{$p});
            }
        }
    }
}
