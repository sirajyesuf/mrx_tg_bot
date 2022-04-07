<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $interests = [
            ['name' => 'Prime'],
            ['name' => 'Nutrition'],
            ['name' => 'General'],
            ['name' => 'Technical Stuff'],
            ['name' => 'Pet/Vet']


        ];
        DB::table('interests')->insert($interests);
    }
}
