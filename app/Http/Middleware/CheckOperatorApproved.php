<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOperatorApproved
{
    /**
     * Handle an incoming request.
     * Check if the operator's registration has been approved.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Only check for operators
        if ($user->role !== 'operator') {
            return $next($request);
        }

        // Check if user is pending
        if ($user->isPending()) {
            return redirect()->route('operator.pending');
        }

        // Check if user is suspended
        if ($user->isSuspended()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah ditangguhkan.']);
        }

        // Check if operator has approved PO
        if (!$user->hasApprovedOperator()) {
            return redirect()->route('operator.pending');
        }

        return $next($request);
    }
}
