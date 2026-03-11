@component('mail::message')
# Two-Factor Authentication Code

Hello {{ $user->name }},

You have just logged in from a new or unrecognized device. To complete the authentication process, please use the following verification code:

@component('mail::panel')
# {{ $code }}
@endcomponent

This code will expire in 15 minutes.

If you did not attempt to log in, please ignore this email and change your password immediately.

@component('mail::button', ['url' => config('app.url') . '/auth/verify-2fa', 'color' => 'primary'])
Enter Verification Code
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
