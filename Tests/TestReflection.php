<?php

namespace Svd\CoreBundle\Tests;

use ReflectionException;
use ReflectionObject;

/**
 * Tests
 */
class TestReflection extends ReflectionObject
{
    /** @var object */
    protected $object;

    /**
     * Constructor
     *
     * @param object $object object to reflect
     */
    public function __construct($object)
    {
        $this->object = $object;
        parent::__construct($object);
    }

    /**
     * Invoke method passing arguments one after another
     *
     * @param string $name method name
     *
     * @return mixed
     */
    public function invokeMethod($name)
    {
        $arguments = func_get_args();
        array_shift($arguments);

        return $this->invokeMethodArgs($name, $arguments);
    }

    /**
     * Invoke method passing arguments as an array
     *
     * @param string $name      method name
     * @param array  $arguments arguments
     *
     * @return mixed
     */
    public function invokeMethodArgs($name, array $arguments = array())
    {
        $method = $this->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->object, $arguments);
    }

    /**
     * Get property value
     *
     * @param string $name property name
     *
     * @return mixed
     */
    public function getPropertyValue($name)
    {
        try {
            $property = $this->getProperty($name);
            $property->setAccessible(true);
            $value = $property->getValue($this->object);
        } catch (ReflectionException $e) {
            // attempt to use non-existent property
            $value = null;
        }

        return $value;
    }

    /**
     * Set property value
     *
     * @param string $name  property name
     * @param mixed  $value value
     *
     * @return self
     */
    public function setPropertyValue($name, $value)
    {
        try {
            $property = $this->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($this->object, $value);
        } catch (ReflectionException $e) {
            // attempt to use non-existent property
        }

        return $this;
    }
}
