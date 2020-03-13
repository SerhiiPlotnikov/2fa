<?php

use Illuminate\Database\Seeder;
use  Illuminate\Support\Facades\DB;

class DiallingCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dialling_codes')->insert(
            [
                ['name' => 'Ukraine', 'dialling_code' => 380],
                ['name' => 'UK', 'dialling_code' => 44]
            ]
        );
    }
}
