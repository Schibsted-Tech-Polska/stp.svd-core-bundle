<?php

namespace Svd\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests
 */
abstract class BaseTest extends WebTestCase
{
    /** @var Client|null */
    private $client;

    /**
     * Get Symfony's client
     *
     * @return Client
     */
    protected function getClient()
    {
        if (!isset($this->client)) {
            static::$kernel = static::createKernel();
            static::$kernel->boot();
            $this->client = static::createClient(array(
                'environment' => 'test',
            ));
        }

        return $this->client;
    }

    /**
     * Get reflection of current object
     *
     * @param object $object object
     *
     * @return TestReflection
     */
    protected function getReflection($object)
    {
        $reflection = new TestReflection($object);

        return $reflection;
    }

    /**
     * Asserts that arrays match
     *
     * @param array  $expected expected array
     * @param array  $actual   actual array
     * @param string $message  message
     */
    protected function assertArraysMatch(array $expected, array $actual, $message = '')
    {
        $expected = $this->convertObjectsIntoStrings($expected);
        $actual = $this->convertObjectsIntoStrings($actual);

        $onlyInExpected = array_diff($expected, $actual);
        $onlyInActual = array_diff($actual, $expected);
        $differences = array_merge($onlyInExpected, $onlyInActual);
        $ifMatch = empty($differences);

        $this->assertTrue($ifMatch, $message);
    }

    /**
     * Get mock with constructor disabled
     *
     * @param string $originalClassName original class name
     * @param array  $methods           methods
     * @param array  $arguments         arguments
     * @param string $mockClassName     mock class name
     * @param bool   $callOriginalClone call original clone
     * @param bool   $callAutoload      call autoload
     * @param bool   $cloneArguments    clone arguments
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockWithConstructorDisabled($originalClassName, $methods = array(),
        array $arguments = array(), $mockClassName = '', $callOriginalClone = true, $callAutoload = true,
        $cloneArguments = false)
    {
        $mock = $this->getMock($originalClassName, $methods, $arguments, $mockClassName, false, $callOriginalClone,
            $callAutoload, $cloneArguments);

        return $mock;
    }

    /**
     * Convert objects into strings
     *
     * @param array $array array
     *
     * @return array
     */
    protected function convertObjectsIntoStrings(array $array)
    {
        foreach ($array as $key => $item) {
            $array[$key] = is_object($item) ? get_class($item) . '#' . spl_object_hash($item) : $item;
        }

        return $array;
    }
}
