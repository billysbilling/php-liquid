<?php

/**
 * The filter bank is where all registered filters are stored, and where filter invocation is handled
 * it supports a variety of different filter types; objects, class, and simple methods
 */
class LiquidFilterbank
{
    /**
     * The registerd filter objects
     *
     * @var array
     */
    public $filters;

    /**
     * A map of all filters and the class that contain them (in the case of methods)
     *
     */
    public $methodMap;

    /**
     * Reference to the current context object
     *
     * @var LiquidContext
     */
    public $context;


    /**
     * Constructor
     *
     * @return LiquidFilterbank
     */
    public function __construct(&$context)
    {
        $this->context = $context;

        $this->addFilter('LiquidStandardFilters');
    }


    /**
     * Adds a filter to the bank
     * filters will be called statically) or the name of a function.
     * @return bool
     */
    function addFilter(string $filter): bool
    {
        // if the filter is a class, register all its methods
        if (class_exists($filter)) {
            $methods = array_flip(get_class_methods($filter));
            foreach ($methods as $method => $null) {
                $this->methodMap[$method] = $filter;
            }

            return true;
        }

        // if it's a function register it simply
        if (function_exists($filter)) {
            $this->methodMap[$filter] = false;
            return true;
        }

        throw new LiquidException("Parameter passed to addFilter must a class or a function");
    }


    /**
     * Invokes the filter with the given name
     *
     * @param string $name The name of the filter
     * @param string $value The value to filter
     * @param array $args The additional arguments for the filter
     * @return string
     */
    function invoke(string $name, string $value, $args): string
    {
        if (!is_array($args)) {
            $args = [];
        }

        array_unshift($args, $value);

        // consult the mapping
        if (isset($this->methodMap[$name])) {
            $class = $this->methodMap[$name];

            // if we have a registered object for the class, use that instead
            if (isset($this->filters[$class])) {
                $class = &$this->filters[$class];

            }

            // if we're calling a function
            if ($class === false) {
                return call_user_func_array($name, $args);

            } else {
                return call_user_func_array(array(
                    &$class,
                    $name
                ), $args);

            }

        }

        return $value;
    }
}
