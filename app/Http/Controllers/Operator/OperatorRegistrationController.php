<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\BusOperator;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class OperatorRegistrationController extends Controller
{
    /**
     * Display the operator registration form.
     */
    public function create(): View
    {
        return view('operator.register');
    }

    /**
     * Handle an incoming operator registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // User fields
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            
            // Bus Operator fields
            'operator_name' => ['required', 'string', 'max:255'],
            'operator_code' => ['required', 'string', 'max:20', 'unique:bus_operators,code'],
            'operator_description' => ['nullable', 'string', 'max:1000'],
            'operator_email' => ['required', 'email', 'max:255'],
            'operator_phone' => ['required', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($request) {
            // Create the bus operator first (pending approval)
            $busOperator = BusOperator::create([
                'name' => $request->operator_name,
                'code' => strtoupper($request->operator_code),
                'description' => $request->operator_description,
                'contact_email' => $request->operator_email,
                'contact_phone' => $request->operator_phone,
                'is_active' => false,
                'approval_status' => 'pending',
            ]);

            // Create the user as company admin with pending status
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'company_admin',
                'user_status' => 'pending',
                'bus_operator_id' => $busOperator->id,
            ]);

            // Link the submitter
            $busOperator->update(['submitted_by' => $user->id]);

            event(new Registered($user));

            Auth::login($user);
        });

        return redirect()->route('operator.pending');
    }

    /**
     * Show the pending approval page for operators.
     */
    public function pending(): View
    {
        return view('operator.pending');
    }
}
