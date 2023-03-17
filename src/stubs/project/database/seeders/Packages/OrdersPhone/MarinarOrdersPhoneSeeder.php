<?php
namespace Database\Seeders\Packages\OrdersPhone;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MarinarOrdersPhoneSeeder extends Seeder {

    public function run() {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::upsert([
            ['guard_name' => 'admin', 'name' => 'orders.order_phone_view'],
            ['guard_name' => 'admin', 'name' => 'orders.order_phone_create'],
        ], ['guard_name','name']);
    }
}
