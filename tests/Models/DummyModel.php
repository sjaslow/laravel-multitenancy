<?php
namespace Ngdcorp\Multitenancy\Tests\Models;


use App\Models\AppModel;
use Ngdcorp\Multitenancy\Traits\MultiTenantTrait;

class DummyModel extends AppModel
{
    use MultiTenantTrait;

}
