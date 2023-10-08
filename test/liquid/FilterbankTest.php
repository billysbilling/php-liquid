<?php

function test_function_filter($value)
{
	return "worked";
}

class TestClassFilter
{

	var $variable = 'not set';


	function static_test()
	{
		return "worked";

	}

	function instance_test_one()
	{
		$this->variable = 'set';
		return 'set';
	}

	function instance_test_two()
	{
		return $this->variable;

	}
}


class FilterbankTest extends UnitTestCase
{

	/**
	 * @var LiquidFilterBank
	 */
	var $context;

	function setup()
	{
		$this->context = new LiquidContext();
	}

}
