<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class chenAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('admins')->insert([
       ['adminId' => '1','name' => 'HTC','email'=>'HTC@gmail.com','password' => "kk1"],
       ['adminId' => '2','name' => 'Acer','email'=>'Acer@gmail.com','password' => "kk2"],
       ['adminId' => '3','name' => 'Lonovo','email'=>'Lonovo@gmail.com','password' => "k41"]
    ]);
    }
}
