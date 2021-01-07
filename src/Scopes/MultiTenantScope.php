<?php
namespace Ngdcorp\Multitenancy\Scopes;

class MultiTenantScope implements \Illuminate\Database\Eloquent\Scope {

	/** Default tenant id to null ("no such thing as a tenant") so the package can be added/used out of the box with no code changes. */
	static public $tenantId = null;

	// This one is used for temporary escalation of privileges. Stack seemed like overkill with no use cases, so a single value is used.
	static public $originalTenantId = null;

	// Add support for a "root" user, if we want one. Useful for SMB clients who tend to want a single "owner" account.
	static public $rootUser = null;

    /**
     * Set the current state to a "Root" user). Useful for automated root jobs, such as scheduled cross-tenant reports. If multiple root users exist,
     * the choice of user is nondeterministic.
     * @throws \Exception
     */
	public static function setRoot() {
	    MultiTenantScope::disable();
	    if (MultiTenantScope::$rootUser == null) {
            MultiTenantScope::$rootUser = \App\Models\User::whereHas('roles', function($q) {$q->whereName('Root');})->first();
        }
        if (MultiTenantScope::$rootUser == null) throw new \Exception("No root user configured.");
        \Auth::onceUsingId(MultiTenantScope::$rootUser->id);
    }

    /**
     * Turns multitenancy off. Use this to enable system-wide queries for things such as admin functions.
     */

	public static function disable() {
	    $tenantId = MultiTenantScope::getTenantId();
        if ($tenantId != null && $tenantId != "all") {
            MultiTenantScope::$originalTenantId = $tenantId;
        }
		MultiTenantScope::$tenantId = "all";
	}

    /**
     * Turns on multitenancy for the application, limiting Eloquent queries to a specific tenant.
     */
	public static function enable() {
        if (MultiTenantScope::$originalTenantId != null) {
            MultiTenantScope::$tenantId = MultiTenantScope::$originalTenantId;
        }
    }

    /**
     * Finds the currently configured tenant ID, or "all" for system-wide querying. This is disctinct from a null
     * return value, which is returned if the user is not logged in and therefore tenancy has no contextual meaning.
     * @return int|string|null
     */
	public static function getTenantId() {
		if (MultiTenantScope::$tenantId != null && MultiTenantScope::$tenantId == "all") return "all";
		// Null tenant (default) means use the current user.
		$currentUser = \Auth::user();
		$tenant = MultiTenantScope::$tenantId;
		if ($tenant == null) {
			if ($currentUser == null) return null;
			$tenant = $currentUser->tenant_id;
		}
		// $tenant is non-null (i.e. an existing, valid tenant the user can access) so we are good to use the configured $tenantId
        // This is just normal input validation / security check to prevent malicious users from data mining.
		else {
			$tenant = intval(MultiTenantScope::$tenantId);
		}
		return $tenant;
	}

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function apply(\Illuminate\Database\Eloquent\Builder $builder, \Illuminate\Database\Eloquent\Model $model)
	{

		if (MultiTenantScope::$tenantId != null && MultiTenantScope::$tenantId == "all") return;	// 'all' = no tenant check.

                // We check statement $model->attributesToArray()["tenant_id"] to check if we add new tenant
		if (isset($model->attributesToArray()["tenant_id"]))
			$tenant = $model->attributesToArray()["tenant_id"];
		else
			$tenant = MultiTenantScope::$tenantId;

		// Null tenant (default) means use the current user.
		$currentUser = \Auth::user();
		if ($tenant == null) {
			if ($currentUser == null) throw new \Exception("Unable to obtain valid tenant, user is not logged in.");
			$tenant = $currentUser->tenant_id;
		}
		// Non-null tenant means use the tenant id set
		else {
			$tenant = intval(MultiTenantScope::$tenantId);
		}


		// Now apply this tenant check to the query builder.
                // If user role is "ROOT" - checking disable
		if ($currentUser == null || !($currentUser->hasRole('Root'))) {
			$builder->where("tenant_id", '=', $tenant);
		}
	}

	// TODO: Consider converting the below to private / protected functions. Could cause obscure bugs if used by an unfamiliar programmer.

	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function remove(\Illuminate\Database\Eloquent\Builder $builder)
	{
		$column = "tenant_id";

		$query = $builder->getQuery();

		foreach ((array) $query->wheres as $key => $where)
		{
			if ($this->isTenantConstraint($where, $column))
			{
				$this->removeWhere($query, $key);
				$this->removeBinding($query, $key);
			}
		}
	}

	protected function removeWhere($query, $key)
	{
		unset($query->wheres[$key]);

		$query->wheres = array_values($query->wheres);
	}


	protected function removeBinding($query, $key)
	{
		$bindings = $query->getRawBindings()['where'];

		unset($bindings[$key]);

		$query->setBindings(array_values($bindings));
	}

	protected function isTenantConstraint(array $where, $column)
	{
		return $where['type'] == 'Basic' && $where['column'] == $column;
	}


}
