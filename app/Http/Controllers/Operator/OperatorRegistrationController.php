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
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+?62|0)[0-9]{9,12}$/'],
            
            // Bus Operator fields
            'operator_name' => ['required', 'string', 'max:255'],
            'operator_code' => ['required', 'string', 'max:20', 'unique:bus_operators,code', 'alpha_num'],
            'operator_description' => ['required', 'string', 'min:100', 'max:1000'],
            'operator_email' => ['required', 'email', 'max:255', 'unique:bus_operators,contact_email'],
            'operator_phone' => ['required', 'string', 'max:20', 'regex:/^(\+?62|0)[0-9]{9,12}$/', 'unique:bus_operators,contact_phone'],
            
            // Document uploads
            'business_license' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'business_permit' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'tax_id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            
            // reCAPTCHA
            'recaptcha_token' => ['required', new \App\Rules\RecaptchaValidation(0.5)],
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

            // Handle document uploads
            $this->storeDocument($request, 'business_license', $busOperator->id, 'business_license');
            if ($request->hasFile('business_permit')) {
                $this->storeDocument($request, 'business_permit', $busOperator->id, 'business_permit');
            }
            if ($request->hasFile('tax_id_document')) {
                $this->storeDocument($request, 'tax_id_document', $busOperator->id, 'tax_id');
            }

            // Log the registration attempt
            \App\Models\ActivityLog::log(
                action: 'created',
                subjectType: 'BusOperator',
                subjectId: $busOperator->id,
                description: "New operator registration submitted: {$busOperator->name} ({$busOperator->code}) by {$user->email}"
            );

            event(new Registered($user));

            Auth::login($user);
        });

        return redirect()->route('operator.pending');
    }

    /**
     * Store uploaded document
     */
    protected function storeDocument(Request $request, string $fieldName, int $busOperatorId, string $documentType): void
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $path = $file->store('operator-documents', 'private');

            \App\Models\OperatorDocument::create([
                'bus_operator_id' => $busOperatorId,
                'document_type' => $documentType,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }

    /**
     * Show the pending approval page for operators.
     */
    public function pending(): View
    {
        return view('operator.pending');
    }
}
