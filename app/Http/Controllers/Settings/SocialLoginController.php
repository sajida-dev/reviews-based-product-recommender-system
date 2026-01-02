<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    // ---------- GOOGLE LOGIN ----------
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            Log::info('Google Login Request');
            /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
            $provider = Socialite::driver('google');
            $googleUser = $provider->stateless()->user();

            Log::info('Google Login Success: ' . $googleUser->getEmail());

            return $this->loginOrCreateAccount($googleUser, 'google');
        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['msg' => 'Google login failed.']);
        }
    }

    // ---------- FACEBOOK LOGIN ----------
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            Log::info('Facebook Login Request');
            /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
            $provider = Socialite::driver('facebook');
            $fbUser = $provider->stateless()->user();

            Log::info('Facebook Login Success: ' . $fbUser->getEmail());

            return $this->loginOrCreateAccount($fbUser, 'facebook');
        } catch (\Exception $e) {
            Log::error('Facebook Login Error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['msg' => 'Facebook login failed.']);
        }
    }

    // ---------- SHARED LOGIN / CREATE LOGIC ----------
    private function loginOrCreateAccount($providerUser, $provider)
    {
        $user = User::firstOrCreate(
            ['email' => $providerUser->getEmail()],
            [
                'name' => $providerUser->getName(),
                'provider' => $provider,
                'provider_id' => $providerUser->getId(),
                'password' => bcrypt(Str::random(16)),
            ]
        );

        Auth::login($user);
        session()->regenerate();

        return redirect('/dashboard');
    }
}
