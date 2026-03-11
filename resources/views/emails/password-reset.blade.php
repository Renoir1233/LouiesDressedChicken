@component('mail::message')
# Password Reset Request

Hello {{ $user->name }},

You have requested to reset your password. Click the button below to reset your password:

@component('mail::button', ['url' => $resetLink, 'color' => 'primary'])
Reset Password
@endcomponent

This password reset link will expire in 60 minutes.

If you did not request a password reset, no further action is required.

Thanks,  
{{ config('app.name') }}
@endcomponent
