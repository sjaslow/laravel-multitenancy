<?php
/**
 * Multitenancy Unit Tests
 *
 * @package     laravel-multitenancy
 * @link        https://github.com/sjaslow/laravel-multitenancy
 * @author      Seth Jaslow <sjaslow@ngdcorp.com>
 * @version     v0.1.0
 */

namespace Ngdcorp\Multitenancy\Tests\Traits;

use Ngdcorp\Multitenancy\Scopes\MultiTenantScope;
use Ngdcorp\Multitenancy\Tests\TestCase;
use Ngdcorp\Multitenancy\Tests\Models\DummyModel;

/**
 * Let's test it!
 */
class MultitenantTraitTest extends TestCase
{
    public function testTenancyDisableEnable()
    {
        MultitenantScope::enable();
        // No auth = no default tenant
        $this->assertNull(MultiTenantScope::$tenantId);

        MultitenantScope::disable();
        // Should now be non-null value of "all"
        $this->assertTrue(MultiTenantScope::$tenantId == "all");

        MultitenantScope::enable();
        // IMPORTANT SECURITY TEST: Must no longer be "all" - even if not previously logged in
        $this->assertFalse(MultiTenantScope::$tenantId == "all");
    }

    public function testTenancyScope() {
        // Scope should be enabled for multi-tenant models regardless of whether tenancy checks are enabled or not.
        $model = new DummyModel();
        $this->assertTrue($model->hasGlobalScope("Ngdcorp\Multitenancy\Scopes\MultiTenantScope"));
        MultitenantScope::disable();
        $this->assertTrue($model->hasGlobalScope("Ngdcorp\Multitenancy\Scopes\MultiTenantScope"));
    }

}
