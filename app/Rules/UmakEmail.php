<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UmakEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * Only @umak.edu.ph email addresses are accepted.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!str_ends_with(strtolower((string) $value), '@umak.edu.ph')) {
            $fail('Only @umak.edu.ph email addresses are allowed.');
        }
    }
}
