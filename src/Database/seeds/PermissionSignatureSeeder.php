<?php

use Illuminate\Database\Seeder;

class PermissionSignatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::updateOrCreate(['name' => 'Signature Plan'], array('id' => 3003, 'name' => 'Signature Plan', 'parent_id' => 2316, 'order' => 1200, 'is_menu' => 1, 'url' => '', 'icon' => 'fa fa-font'));
        Permission::updateOrCreate(['name' => 'Signature Plan Add'], array('name' => 'Signature Plan Add', 'parent_id' => 3003, 'order' => 1201, 'is_menu' => 1, 'url' => '/admin/plan/add', 'icon' => 'fa fa-pencil'));
        Permission::updateOrCreate(['name' => 'Signature Plan List'], array('name' => 'Signature Plan List', 'parent_id' => 3003, 'order' => 1202, 'is_menu' => 1, 'url' => '/admin/plan/list', 'icon' => 'fa fa-bar-chart'));
        
        Permission::updateOrCreate(['name' => 'Signature List'], array('name' => 'Signature List', 'parent_id' => 2316, 'order' => 1200, 'is_menu' => 1, 'url' => '/admin/signature', 'icon' => 'fa fa-bar-chart'));


		$this->command->info('Permissions created!');
    }
}