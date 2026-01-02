<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey();

        $recoveryCodes = collect(range(1, 8))->map(fn() => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10)))->toArray();

        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@rbpr.com',
            'is_admin' => true,
            'password' => Hash::make('admin'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }
}
