
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaValidation implements ValidationRule
{
    protected float $threshold;

    /**
     * Create a new rule instance.
     *
     * @param float $threshold Minimum score required (0.0 to 1.0). Default 0.5
     */
    public function __construct(float $threshold = 0.5)
    {
        $this->threshold = $threshold;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.recaptcha.secret_key');

        if (empty($secretKey)) {
            Log::warning('reCAPTCHA secret key not configured');
            // In development, allow pass if not configured
            if (app()->environment('local')) {
                return;
            }
            $fail('reCAPTCHA configuration error');
            return;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            if (!$result['success']) {
                Log::info('reCAPTCHA validation failed', [
                    'error-codes' => $result['error-codes'] ?? [],
                ]);
                $fail('Please complete the reCAPTCHA verification');
                return;
            }

            // Check score for v3
            if (isset($result['score']) && $result['score'] < $this->threshold) {
                Log::info('reCAPTCHA score too low', [
                    'score' => $result['score'],
                    'threshold' => $this->threshold,
                ]);
                $fail('Suspicious activity detected. Please try again later');
                return;
            }

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            // In production, fail closed
            if (!app()->environment('local')) {
                $fail('Verification error. Please try again');
            }
        }
    }
}
