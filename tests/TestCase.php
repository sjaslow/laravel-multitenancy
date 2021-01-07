<?php
/**
 * Multitenancy Unit Tests
 *
 * @package     laravel-multitenancy
 * @link        https://github.com/sjaslow/laravel-multitenancy
 * @author      Seth Jaslow <sjaslow@ngdcorp.com>
 * @version     v0.1.0
 */

namespace Ngdcorp\Multitenancy\Tests;

use AustinHeap\Database\Encryption\EncryptionFacade;
use AustinHeap\Database\Encryption\EncryptionServiceProvider;
use DB, RuntimeException, ReflectionClass;
use Ngdcorp\Multitenancy\MultitenancyServiceProvicer;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * TestCase
 */
class TestCase extends BaseTestCase
{
    protected $last_random;


    protected function getEnvironmentSetUp($app)
    {
    }

    public static function callProtectedMethod($object, $method, array $args = [])
    {
        $class  = new ReflectionClass(get_class($object));
        $method = $class->getMethod($method);

        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }

    public function testTestCase()
    {
        $this->assertEquals('placeholder to silence phpunit warnings', 'placeholder to silence phpunit warnings');
    }

    protected function newRandomFromArray(array $array)
    {
        return $this->newRandom($array);
    }

    protected function newRandom($value)
    {
        if (is_array($value)) {
            $value = array_random($value);
        }

        $this->last_random = is_string($value) ? sprintf($value, (string)rand(1111, 9999)) : $value;

        return $this->last_random;
    }

    protected function currentRandom()
    {
        return $this->last_random;
    }

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [MultitenancyServiceProvicer::class]);
    }

}
