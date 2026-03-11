<?php

namespace App\Rules;

use App\Models\WorkEmailDomain;
use Illuminate\Contracts\Validation\Rule;

class ValidWorkEmail implements Rule
{
    protected $role;
    protected $errorMessage = '';

    /**
     * Create a new rule instance.
     */
    public function __construct($role)
    {
        $this->role = $role;
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        // Extract domain from email
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = 'The ' . $attribute . ' must be a valid email address.';
            return false;
        }

        // Check if email is allowed for this role
        if (!WorkEmailDomain::isEmailAllowedForRole($value, $this->role)) {
            $this->errorMessage = 'The ' . $attribute . ' domain is not allowed for the ' . ucfirst(str_replace('-', ' ', $this->role)) . ' role.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return $this->errorMessage ?: 'The validation of work email failed.';
    }
}
