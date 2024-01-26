<?php

namespace App\Helpers;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class App
{
    /**
     * Available permissions grouped into roles.
     *
     * @var array
     */
    public $permissions;
    
    public function __construct() {
        $this->permissions = config('user_permissions');
    }

    /**
     * Save and set permissions for each available roles.
     *
     * @return void
     */
    public function setPermissions()
    {
        //delete current available roles and permissions
        Role::query()->delete();
        Permission::query()->delete();
        
        // Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        //save new roles and permissions
        foreach($this->permissions as $roleName => $rolePermissions) {
            $savedRolePermissions = [];
            foreach($rolePermissions as $permissionName) {
                $permission = Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
                array_push($savedRolePermissions, $permission->id);
            }
		    $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            
            $role->syncPermissions($savedRolePermissions);
        }
        
        // Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
    
    /**
     * Save details of admin being created. i.e email and password.
     *
     * @return void
     */
    public function saveAdminDetails($detail, $value)
    {
        session([$detail => $value]);
    }
    
    /**
     * Confirm entered admin password if it match.
     *
     * @return boolean
     */
    public function confirmAdminPassword($password)
    {
        return (session('admin_password') === $password);
    }
    
    /**
     * Save admin details to the database.
     *
     * @param  none
     * @return boolean
     */
    public function createAdmin()
    {
		$email = session('admin_email');
        $password = session('admin_password');
        
        $admin = User::create([
					'email' => $email,
					'password' => Hash::make($password),
				]);
			
        $admin->assignRole('system admin');
        $admin->assignRole('user');
        
        // Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        return $admin->hasRole('system admin');
    }
    
    /**
     * Save default app settings to the database.
     *
     * @param  none
     * @return void
     */
    public function initializeSettings()
    {
        Setting::init();
    }
    
    /**
     * Update existing permissions for each available roles.
     *
     * @return void
     */
    public function updatePermissions()
    {
        // Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        //Go through permissions config file and save new roles and permissions
        foreach($this->permissions as $roleName => $rolePermissions) {
            $savedRolePermissions = [];
            foreach($rolePermissions as $permissionName) {
                $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
                array_push($savedRolePermissions, $permission->id);
            }
		    $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            
            $role->syncPermissions($savedRolePermissions);
        }
        
        // Reset cached roles and permissions
		app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
