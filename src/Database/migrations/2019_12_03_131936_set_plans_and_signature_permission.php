<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetPlansAndSignaturePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::updateOrCreate(['name' => 'Signature Plan'], array('id' => 3003, 'name' => 'Signature Plan', 'parent_id' => 2316, 'order' => 1200, 'is_menu' => 1, 'url' => '', 'icon' => 'fa fa-font'));
        Permission::updateOrCreate(['name' => 'Signature Plan Add'], array('name' => 'Signature Plan Add', 'parent_id' => 3003, 'order' => 1201, 'is_menu' => 1, 'url' => '/admin/plan/add', 'icon' => 'fa fa-pencil'));
        Permission::updateOrCreate(['name' => 'Signature Plan List'], array('name' => 'Signature Plan List', 'parent_id' => 3003, 'order' => 1202, 'is_menu' => 1, 'url' => '/admin/plan/list', 'icon' => 'fa fa-bar-chart'));
        
        Permission::updateOrCreate(['name' => 'Signature List'], array('name' => 'Signature List', 'parent_id' => 2316, 'order' => 1200, 'is_menu' => 1, 'url' => '/admin/signature', 'icon' => 'fa fa-bar-chart'));



        $admins = \Admin::select('id','profile_id')->get();
        $permissions = \Permission::select('id')
                    ->where('name', 'Signature Plan')
                    ->orWhere('name', 'Signature Plan Add')
                    ->orWhere('name', 'Signature Plan List')
                    ->orWhere('name', 'Signature List')
                    ->get();

        foreach ( $permissions as $permission ) {
            if($admins){
                $findProfiles = array();
                foreach($admins as $admin){
                    \AdminPermission::updateOrCreate(
                        ['admin_id' => $admin->id, 'permission_id' => $permission->id],
                        ['admin_id' => $admin->id, 'permission_id' => $permission->id]
                    );
                    
                    if (!in_array($admin->profile_id, $findProfiles)) {
                        $findProfiles = array_merge($findProfiles, array($admin->profile_id));
                        \ProfilePermission::updateOrCreate(
                            ['profile_id' => $admin->profile_id, 'permission_id' => $permission->id],
                            ['profile_id' => $admin->profile_id, 'permission_id' => $permission->id]
                        );
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissions = \Permission::select('id')
                    ->where('name', 'Signature Plan')
                    ->orWhere('name', 'Signature Plan Add')
                    ->orWhere('name', 'Signature Plan List')
                    ->orWhere('name', 'Signature List')
                    ->get();

        foreach ( $permissions as $permission ) {
            \AdminPermission::where('permission_id', $permission->id)->delete();
            \ProfilePermission::where('permission_id', $permission->id)->delete();
        }
    }
}