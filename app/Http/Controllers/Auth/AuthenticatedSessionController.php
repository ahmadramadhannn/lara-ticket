<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Role-based redirect
        $user = Auth::user();

        // Super Admin goes to Filament admin panel
        if ($user->hasRole('super_admin')) {
            return redirect()->intended('/super-admin');
        }

        // Operator goes to Filament operator panel (or pending page if not approved)
        if ($user->hasRole('operator')) {
            if ($user->busOperator && $user->busOperator->approval_status === 'approved') {
                return redirect()->intended('/operator');
            }
            return redirect()->route('operator.pending');
        }

        // Buyers/Passengers go to home page
        return redirect()->intended(route('home'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
