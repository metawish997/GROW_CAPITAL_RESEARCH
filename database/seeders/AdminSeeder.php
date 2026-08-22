<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'Super Admin',
                'email'             => 'admin@example.com',
                'password'          => Hash::make('11111111'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        \App\Models\AppSetting::updateOrCreate(
            ['group' => 'kyc', 'key' => 'declaration'],
            ['value' => 'I hereby authorize Grow Capital Research to retrieve my profile and verify my identity details via Digio secure KYC gateway. I confirm that the Aadhaar and PAN details provided belong to me and are correct.']
        );

        $this->command->info('✅ Admin user seeded: admin@example.com / 11111111');
    }
}
