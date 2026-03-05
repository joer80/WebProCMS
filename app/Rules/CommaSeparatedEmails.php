<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CommaSeparatedEmails implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach (array_map('trim', explode(',', (string) $value)) as $email) {
            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fail("The :attribute contains an invalid email address: {$email}.");
            }
        }
    }
}
