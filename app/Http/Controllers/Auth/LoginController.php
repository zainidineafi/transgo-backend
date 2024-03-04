<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Mengakses pengguna yang telah berhasil login
            $user = Auth::user();

            // Menyimpan level pengguna ke dalam sesi
            $request->session()->put('user_level', $user->type);

            // Cetak level pengguna untuk memeriksanya
            $userLevel = $request->session()->get('user_level');
            dd($userLevel); // Mencetak nilai level pengguna

            // Redirect sesuai dengan level pengguna
            switch ($user->type) {
                case 0: // Superadmin
                    return redirect()->route('dashboard');
                    break;
                case 1: // Admin
                    return redirect()->route('admin.dashboard');
                    break;
                case 2: // Kondektur/Sopir
                    return redirect()->route('driver.dashboard');
                    break;
                case 3: // User
                    return redirect()->route('user.dashboard');
                    break;
            }
        }

        return redirect()->back()->withInput($request->only('email'))->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
