<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $seeds = $this->getSeeds();

        foreach($seeds as $seed) {
            $check = collect($seed)->only('email')->toArray();

            User::updateOrCreate($check, $seed);
        }
    }

    private function getSeeds(): array
    {
        return [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'super@jnw.io',
                'password' => 'password',
            ]
        ];
    }
}
