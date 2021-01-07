<?php
namespace Ngdcorp\Multitenancy\Traits;

use Ngdcorp\Multitenancy\Scopes\MultiTenantScope;

/**
 * Trait MultiTenantTrait
 * Adds Multitenancy support to a model. Allows developers to use regular Eloquent syntax without needing to explictly perform tenancy checks.
 * This is accomplished by registering a global scope to limit queries by a specific column "tenant_id".
 * TODO: If ever needed, "tenant_id" could be changed to a configurable column on the model. For now, tenant_id works well.
 * @package Ngdcorp\Multitenancy\Traits
 */

trait MultiTenantTrait {

    /**
     * Boot the multitenant trait for a model.
     *
     * @return void
     */
    public static function bootMultiTenantTrait()
    {
        static::addGlobalScope(new MultiTenantScope);
    }

	public function inTenantScope() {
		if (MultiTenantScope::$tenantId != null && MultiTenantScope::$tenantId == "all") return true;	// 'all' = no tenant check.

		$tenant = MultiTenantScope::$tenantId;
		// Null tenant (default) means use the current user.
		if ($tenant == null) {
			$currentUser = Auth::user();
			if ($currentUser == null) throw new Exception("Unable to obtain valid tenant, user is not logged in.");
			$tenant = $currentUser->tenant_id;
		}
		// Non-null tenant means use the tenant id set
		else {
			$tenant = intval(MultiTenantScope::$tenantId);
		}
		return ($tenant == $this->tenant_id);
	}

    public function getTenantId() {
        return $this->tenant_id;
    }

    public function tenant() {
        return $this->belongsTo('App\Models\Tenant');
    }


}
