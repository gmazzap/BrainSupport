<?php namespace Brain\Tests;

class ContextStub {

    use \Brain\Contextable;

    public $context;

    function __construct() {
        $this->context = new \ArrayObject;
    }

}