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
 * Observer class to format the properties of the model after load.
 */
class Observer_Format extends \Orm\Observer
{
    /**
     * @var string default prefix
     */
    public static $prefix = 'formatted_';

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
     * @param Model The model object to format
     */
    public function after_save(\Orm\Model $obj)
    {
        $this->format($obj);
    }

    /**
     * Execute after loading the Model.
     *
     * @param Model The model object to format
     */
    public function after_load(\Orm\Model $obj)
    {
        $this->format($obj);
    }

    /**
     * Format the model.
     *
     * @param Model The model object to format
     */
    public function format(\Orm\Model $obj)
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

            if ( ! empty($settings['format']))
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
                        foreach ((array)$settings['format'] as $func => $args)
                        {
                            if (is_bool($value) or $value === null or $value === '' or $value === array())
                            {
                                break;
                            }

                            if (is_int($func) and is_string($args))
                            {
                                $value = call_user_func($args, $value);
                            }
                            else
                            {
                                if (isset($args['args']))
                                {
                                    $line     = isset($args['input_args_line']) && $args['input_args_line'] > 0 ? $args['input_args_line'] - 1 : 0;
                                    $new_args = (array)$args['args'];
                                    \Arr::insert($new_args, $value, $line);
                                    $value = call_user_func_array($func, $new_args);
                                }
                                else
                                {
                                    $new_args = (array)$args;
                                    if (preg_match('/format_date/', $func))
                                    {
                                        $func = [
                                            \Date::forge($value),
                                            'format'
                                        ];
                                    }
                                    else
                                    {
                                        array_unshift($new_args, $value);
                                    }
                                    $value = call_user_func_array($func, $new_args);
                                }
                            }
                        }
                    }

                    return $value;
                };
                $obj->{$this->_prefix.$p} = $callback($obj->{$p});
            }
        }
    }
}
