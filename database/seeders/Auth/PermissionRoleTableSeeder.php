<?php

namespace Database\Seeders\Auth;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $admin = Role::firstOrCreate(['name' => 'admin', 'title' => 'Admin', 'is_fixed' => true]);
        $demo_admin = Role::firstOrCreate(['name' => 'demo_admin', 'title' => 'Demo Admin', 'is_fixed' => true]);
        $user = Role::firstOrCreate(['name' => 'user', 'title' => 'user', 'is_fixed' => true]);
        $vendor = Role::firstOrCreate(['name' => 'vendor', 'title' => 'vendor', 'is_fixed' => true]);
        $collector = Role::firstOrCreate(['name' => 'collector', 'title' => 'collector', 'is_fixed' => true]);
    
        Permission::firstOrCreate(['name' => 'edit_settings', 'is_fixed' => true]);
        Permission::firstOrCreate(['name' => 'view_logs', 'is_fixed' => true]);

        $modules = config('constant.MODULES');

        foreach ($modules as $key => $module) {
            $permissions = ['view', 'add', 'edit', 'delete', 'restore' ,'force_delete'];
            $module_name = strtolower(str_replace(' ', '_', $module['module_name']));
            foreach ($permissions as $key => $value) {
                $permission_name = $value.'_'.$module_name;
                Permission::firstOrCreate(['name' => $permission_name, 'is_fixed' => true]);
            }
            if (isset($module['more_permission']) && is_array($module['more_permission'])) {
                foreach ($module['more_permission'] as $key => $value) {
                    $permission_name = $module_name.'_'.$value;
                    Permission::firstOrCreate(['name' => $permission_name, 'is_fixed' => true]);
                }
            }

            if ($module['module_name'] === 'Clinic Categories') {
                $permission_name = 'view_' . $module_name;
                Permission::firstOrCreate(['name' => $permission_name, 'is_fixed' => true]);
            }
        }

        // Assign Permissions to Roles
        $admin->givePermissionTo(Permission::get());

        $demo_admin->givePermissionTo(Permission::get());

        $vendor->givePermissionTo(['view_bookings','view_collector','view_collectordocuments','view_lab','view_catelog','view_packages','view_reviews','add_reviews','edit_reviews','delete_reviews','view_coupons',
                                                'add_catelog','edit_catelog','delete_catelog','restore_catelog','force_delete_catelog',
                                                'add_packages','edit_packages','delete_packages','restore_packages','force_delete_packages',
                                                'add_collector','edit_collector','delete_collector','restore_collector','force_delete_collector','add_vendordocuments','edit_vendordocuments','delete_vendordocuments','restore_vendordocuments','force_delete_vendordocuments',
                                                'add_collectordocuments','edit_collectordocuments','delete_collectordocuments','restore_collectordocuments','force_delete_collectordocuments',
                                                'add_bookings','edit_bookings','delete_bookings','restore_bookings','force_delete_bookings',
                                                'add_coupons','edit_coupons','delete_coupons','restore_coupons','force_delete_coupons',
                                                'add_lab','view_prescription','delete_prescription','edit_lab','delete_lab','restore_lab','force_delete_lab','view_payment_list','view_cash_payment_list','view_collector_payouts','edit_collector_payouts','delete_collector_payouts','view_vendor_payouts','view_collector_earnings','view_reports','view_helpdesks','add_helpdesks','edit_helpdesks','delete_helpdesks'
                                                ,'edit_payment_list','add_vendor_bank','edit_vendor_bank','delete_vendor_bank','view_collector_bank','add_collector_bank','edit_collector_bank','delete_collector_bank','view_notification','view_vendor_bank','view_vendordocuments']);
        Schema::enableForeignKeyConstraints();
    }
}
