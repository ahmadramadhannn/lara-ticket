<?php

namespace App\Http\Middleware;

use App\Models\Terminal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTerminalAccess
{
    /**
     * Handle an incoming request.
     * Verifies that the user has permission to access/manage a specific terminal.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  The permission to check: 'manage', 'verify', 'confirm_arrivals'
     */
    public function handle(Request $request, Closure $next, string $permission = 'manage'): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Get terminal ID from route or request
        $terminalId = $request->route('terminal') 
            ?? $request->route('terminal_id')
            ?? $request->input('terminal_id');

        if (!$terminalId) {
            abort(400, 'Terminal ID is required.');
        }

        $terminal = Terminal::find($terminalId);

        if (!$terminal) {
            abort(404, 'Terminal not found.');
        }

        // Super admins have full access
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Company admins can access all terminals for routes their company operates
        if ($user->isCompanyAdmin() && $user->hasApprovedOperator()) {
            // TODO: Could add more granular check for company's active routes
            return $next($request);
        }

        // Terminal admins need explicit assignment
        if ($user->isTerminalAdmin()) {
            $hasAccess = match ($permission) {
                'manage' => $user->canManageTerminal($terminal),
                'verify' => $user->canVerifyAtTerminal($terminal),
                'confirm_arrivals' => $user->canConfirmArrivalsAt($terminal),
                default => false,
            };

            if (!$hasAccess) {
                abort(403, 'You do not have permission for this terminal.');
            }

            return $next($request);
        }

        abort(403, 'Unauthorized access to terminal.');
    }
}
