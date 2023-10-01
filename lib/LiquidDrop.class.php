<?php
/**
 * A drop in liquid is a class which allows you to to export DOM like things to liquid
 * Methods of drops are callable.
 * The main use for liquid drops is the implement lazy loaded objects.
 * If you would like to make data available to the web designers which you don't want loaded unless needed then
 * a drop is a great way to do that
 *
 * Example:
 *
 * class ProductDrop extends LiquidDrop {
 *   function top_sales() {
 *      Products::find('all', array('order' => 'sales', 'limit' => 10 ));
 *   }
 * }
 *
 * tmpl = Liquid::Template.parse( ' {% for product in product.top_sales %} {{ product.name }} {%endfor%} '  )
 * tmpl.render('product' => ProductDrop.new ) * will invoke top_sales query.
 *
 * Your drop can either implement the methods sans any parameters or implement the _beforeMethod(name) method which is a
 * catch all
 *
 * @package Liquid
 * @copyright Copyright (c) 2011-2012 Harald Hanek,
 * fork of php-liquid (c) 2006 Mateo Murphy,
 * based on Liquid for Ruby (c) 2006 Tobias Luetke
 * @license http://harrydeluxe.mit-license.org
 */

abstract class LiquidDrop
{

    /**
     * @var LiquidContext
     */
    protected $_context;


    /**
     * Catch all method that is invoked before a specific method
     */
    protected function _beforeMethod(string $method): mixed
    {
        return null;
    }

    public function setContext(object $context): void
    {
        $this->_context = $context;
    }


    /**
     * Invoke a specific method
     *
     * @param string $method
     * @return mixed
     */
    public function invokeDrop(string $method): mixed
    {
        $result = $this->_beforeMethod($method);

        if (is_null($result) && is_callable(array(
                $this,
                $method
            ))) //if(is_null($result) && method_exists($this, $method))
        {
            $result = $this->$method();
        }

        return $result;
    }


    /**
     * Returns true if the drop supports the given method
     *
     * @param $name
     * @return bool
     */
    public function hasKey($name): bool
    {
        return true;
    }

    public function toLiquid(): static
    {
        return $this;
    }


    public function __toString(): string
    {
        return get_class($this);
    }
}
