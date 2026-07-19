<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect user to Google's OAuth page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google after authentication.
     */
    public function callback()
    {
        try {
            $caPath = storage_path('cacert.pem');
            $client = new \GuzzleHttp\Client([
                'verify' => file_exists($caPath) ? $caPath : true,
            ]);

            $googleUser = Socialite::driver('google')
                ->setHttpClient($client)
                ->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['google' => 'Login dengan Google gagal: ' . $e->getMessage()]);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'              => $googleUser->getName(),
                'google_id'         => $googleUser->getId(),
                'email_verified_at' => now(),
                'password'          => null,
                'role'              => 'user',
            ]
        );

        if (! $user->google_id) {
            $user->update([
                'google_id'         => $googleUser->getId(),
                'name'              => $googleUser->getName(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

        Auth::login($user, remember: true);

        $route = $user->role === 'admin'
            ? route('admin.dashboard')
            : route('user.dashboard');

        return redirect()->intended($route);
    }
}
