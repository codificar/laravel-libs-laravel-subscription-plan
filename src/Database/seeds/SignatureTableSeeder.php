<?php

use Illuminate\Database\Seeder;

class SignatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        subscriptionPlan::create([
            'id'                =>      1,
            'provider_id'       =>      '24',
            'plan_id'           =>      '1',
            'finance_id'        =>      '900',
            'next_expiration'   =>      '2018-12-10 18:07:01',
            'activity'          =>      1
        ]);

        subscriptionPlan::create([
            'id'                =>      2,
            'provider_id'       =>      '25',
            'plan_id'           =>      '2',
            'finance_id'        =>      '902',
            'next_expiration'   =>      '2019-04-13 18:07:01',
            'activity'          =>      1
        ]);

        subscriptionPlan::create([
            'id'                =>      3,
            'provider_id'       =>      '26',
            'plan_id'           =>      '3',
            'finance_id'        =>      '905',
            'next_expiration'   =>      '2019-03-10 18:07:01',
            'activity'          =>      1
        ]);

        subscriptionPlan::create([
            'id'                =>      4,
            'provider_id'       =>      '24',
            'plan_id'           =>      '4',
            'finance_id'        =>      '900',
            'next_expiration'   =>      '2019-03-11 18:07:01',
            'activity'          =>      1
        ]);

    }
}