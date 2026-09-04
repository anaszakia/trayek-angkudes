<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Reload user dengan relasi passkeys untuk memastikan data terbaru
            $user = Auth::user()->loadMissing(['roles', 'passkeys']);

            if (! $this->isStaffUser($user)) {
                Auth::logout();
                return back()->withErrors(['email' => 'Login hanya tersedia untuk pengemudi dan super admin.'])->withInput();
            }

            session([
                'user_id'        => Auth::id(),
                'user_name'      => $user->name,
                'user_role'      => $user->role?->slug,
                'last_activity'  => now()->timestamp,
            ]);

            // Selalu tunjukkan prompt passkey jika user belum punya passkey
            if ($user->passkeys()->doesntExist()) {
                return redirect()->route($this->isDriverUser($user) ? 'driver.dashboard' : 'dashboard')->with('show_passkey_prompt', true);
            }

            return redirect()->route($this->isDriverUser($user) ? 'driver.dashboard' : 'dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function redirectToGoogle(Request $request)
    {
        // Save intent so callback knows whether to auto-register or only login
        $intent = $request->query('intent', 'login');
        session(['oauth_intent' => $intent]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google login gagal: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google gagal, coba lagi.']);
        }

        // Retrieve intent (login or register) and then remove it from session
        $intent = session()->pull('oauth_intent', 'login');

        // Cari berdasarkan google_id dulu, fallback ke email (untuk user lama yang daftar manual)
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            return redirect()->route('login')->withErrors(['email' => 'Akun belum terdaftar. Silakan hubungi administrator.']);
        }

        $user->loadMissing('roles');
        if (! $this->isStaffUser($user)) {
            return redirect()->route('login')->withErrors(['email' => 'Login hanya tersedia untuk pengemudi dan super admin.']);
        }

        if (! $user->google_id) {
            $user->forceFill(['google_id' => $googleUser->getId()])->save();
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        // Reload user dengan relasi passkeys untuk memastikan data terbaru
        $user = Auth::user()->loadMissing(['roles', 'passkeys']);

        session([
            'user_id'        => Auth::id(),
            'user_name'      => $user->name,
            'user_role'      => $user->role?->slug,
            'last_activity'  => now()->timestamp,
        ]);

        // Selalu tunjukkan prompt passkey jika user belum punya passkey
        if ($user->passkeys()->doesntExist()) {
            return redirect()->route($this->isDriverUser($user) ? 'driver.dashboard' : 'dashboard')->with('show_passkey_prompt', true);
        }

        return redirect()->route($this->isDriverUser($user) ? 'driver.dashboard' : 'dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'Anda telah logout.');
    }

    private function isStaffUser(User $user): bool
    {
        return $user->roles->pluck('slug')->intersect(['super_admin', 'superadmin', 'driver'])->isNotEmpty()
            || in_array($user->role?->slug, ['super_admin', 'superadmin', 'driver'], true);
    }

    private function isDriverUser(User $user): bool
    {
        return $user->roles->contains('slug', 'driver') || $user->role?->slug === 'driver';
    }
}
