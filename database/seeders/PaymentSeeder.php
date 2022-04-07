<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_methods = [
            ['name' => 'Crypto'],
            ['name' => 'Amazone'],
            ['name' => 'Paypal']
        ];

        DB::table('payments')->insert($payment_methods);
    }
}
