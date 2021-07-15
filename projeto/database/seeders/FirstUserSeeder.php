<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name' => 'mauricio',
            'email' => 'bruno@mail.com',
            'password' => bcrypt('123')
        ]);
    }
}
