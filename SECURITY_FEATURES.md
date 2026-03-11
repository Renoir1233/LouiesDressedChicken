# Security Features Documentation

This document outlines the enhanced security features implemented in the IT14-PROG system.

## Features Implemented

### 1. **Work Email Domain Restriction**

Only employees with specific company email domains can create accounts and log in. This ensures that only authorized personnel with validated work email addresses can access the system.

#### Configuration:

**Add a work email domain for a role:**
```bash
php artisan work-email:add super-admin company.com --description="Super Admin Domain"
php artisan work-email:add admin company.com --description="Admin Domain"
php artisan work-email:add manager company.com --description="Manager Domain"
php artisan work-email:add cashier company.com --description="Cashier Domain"
php artisan work-email:add staff company.com --description="Staff Domain"
```

**View all configured domains:**
```bash
php artisan work-email:list
```

**Remove a domain:**
```bash
php artisan work-email:remove super-admin company.com
```

#### Validation:
- When a user tries to create an account or login, the system validates that their email address belongs to an approved domain for their role.
- Only emails ending with approved domains will be allowed.
- You can configure different domains for different roles.

---

### 2. **Two-Factor Authentication (2FA)**

Two-factor authentication adds an extra layer of security by requiring users to verify their identity using a code sent to their email address when logging in from an unfamiliar device.

#### How It Works:

1. **Device Detection**: The system tracks devices based on a fingerprint composed of:
   - IP Address
   - User Agent
   - Browser Type
   - Operating System

2. **First Login/Unfamiliar Device**: When a user logs in from a new device:
   - A 6-digit verification code is generated
   - The code is sent to the user's registered email address
   - The code expires in 15 minutes
   - User must enter the code on the "Two-Factor Authentication" screen

3. **Device Trusting**: Users can opt to "trust" a device:
   - Trusted devices are remembered
   - Future logins from trusted devices won't require 2FA
   - Users can manually untrust devices

#### Managing 2FA:

**Enable 2FA for a user:**
```bash
php artisan user:enable-2fa admin@company.com
```

**Disable 2FA for a user:**
```bash
php artisan user:disable-2fa admin@company.com
```

#### Database Fields:

User table additions:
- `two_factor_enabled`: Boolean flag indicating if 2FA is active
- `two_factor_code`: Current 6-digit verification code
- `two_factor_code_expires_at`: Timestamp when code expires
- `trusted_devices`: JSON array of trusted device fingerprints

---

### 3. **Device Login Tracking**

The system maintains a complete record of all devices that have logged into an account.

#### Device Information Tracked:
- Device fingerprint (unique identifier)
- Device name (browser + OS)
- Browser type (Chrome, Firefox, Safari, etc.)
- Operating system (Windows, macOS, iOS, Android, etc.)
- IP Address
- Last login time
- Trust status

#### Database Table: `device_logins`

Fields:
- `id`: Primary key
- `user_id`: Associated user
- `device_fingerprint`: Unique device identifier
- `device_name`: Human-readable device name
- `browser`: Browser type
- `os`: Operating system
- `ip_address`: Login IP address
- `is_trusted`: Whether device is trusted
- `last_login_at`: Last successful login timestamp

---

### 4. **Forgot Password Feature**

Users can reset forgotten passwords by providing their email address. A password reset link is sent to their email.

#### How It Works:

1. User clicks "Forgot Password" on login page
2. User enters their email address
3. System sends password reset link to email (valid for 60 minutes)
4. User clicks link in email
5. User enters new password
6. Password is updated

#### Routes:
- GET `/auth/forgot-password` - Show forgot password form
- POST `/auth/forgot-password` - Send reset link
- GET `/auth/reset-password/{token}` - Show reset form
- POST `/auth/reset-password` - Update password

---

## Database Migrations

Three new migrations have been created:

### 1. `2026_03_09_000001_add_two_factor_auth_to_users_table.php`
Adds columns to the `users` table:
- `two_factor_enabled` (boolean)
- `two_factor_code` (string, nullable)
- `two_factor_code_expires_at` (timestamp, nullable)
- `trusted_devices` (text, nullable)

### 2. `2026_03_09_000002_create_device_logins_table.php`
Creates new `device_logins` table for tracking login devices.

### 3. `2026_03_09_000003_create_work_email_domains_table.php`
Creates new `work_email_domains` table for configuring role-specific email domain restrictions.

---

## Models & Classes

### Models
- **App\Models\User**: Enhanced with 2FA methods
- **App\Models\DeviceLogin**: Tracks login devices
- **App\Models\WorkEmailDomain**: Manages work email domain configurations

### Services
- **App\Services\DeviceFingerprintService**: Generates device fingerprints and extracts device information

### Rules
- **App\Rules\ValidWorkEmail**: Custom validation rule for work emails

### Mail/Notifications
- **App\Mail\TwoFactorCodeMail**: Email notification for 2FA codes
- **App\Mail\PasswordResetMail**: Email notification for password reset

### Controllers
- **App\Http\Controllers\Auth\TwoFactorAuthController**: Handles 2FA verification

### Middleware
- **App\Http\Middleware\TwoFactorAuthPending**: Ensures 2FA is completed before redirecting

---

## Configuration Steps

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Set Up Work Email Domains
Add your company's email domains:
```bash
php artisan work-email:add super-admin company.com
php artisan work-email:add admin company.com
php artisan work-email:add manager company.com
php artisan work-email:add cashier company.com
php artisan work-email:add staff company.com
```

### Step 3: Enable 2FA for Users
```bash
php artisan user:enable-2fa admin@company.com
php artisan user:enable-2fa manager@company.com
```

### Step 4: Configure Email Settings
Ensure your `.env` file has proper mail configuration:
```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@company.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## User Help Guide

### For End Users

#### First Time Login from New Device

1. Enter your email and password
2. Wait for verification code email
3. Enter the 6-digit code from the email
4. Optionally check "Trust this device" to avoid 2FA on future logins from this device
5. Click "Verify Code"

#### If Code Expires

- Click "Didn't receive a code? Resend"
- A new code will be sent to your email

#### Forgot Password

1. Click "Forgot Password" on login page
2. Enter your email address
3. Check your email for password reset link
4. Click the link and follow instructions to set new password

#### Remove Trusted Devices

Users can view and manage their trusted devices in their account settings (to be implemented in user dashboard).

---

## Security Best Practices

1. **Always Enable 2FA**: For administrators and super-admin accounts
2. **Regularly Review Trusted Devices**: Users should periodically review and revoke trusted devices
3. **Use Strong Passwords**: Combine with 2FA for maximum security
4. **Email Security**: Ensure company email is secure
5. **Logout Properly**: Always logout when finished, especially on shared devices

---

## Troubleshooting

### User not receiving 2FA code
- Check mail configuration in `.env`
- Check spam/junk folder
- Verify user email address is correct
- Check server logs: `storage/logs/`

### User locked out
Admin can reset by:
1. Disabling 2FA: `php artisan user:disable-2fa email@company.com`
2. Clearing trusted devices in database
3. Re-enabling 2FA if needed

### Reset migrations
If you need to reset everything:
```bash
php artisan migrate:reset
php artisan migrate
php artisan db:seed --class=WorkEmailDomainSeeder
```

---

## API Development Notes

For developers extending this system:

### Adding 2FA to an API

```php
// In API Controller
$user = Auth::user();
if ($user->two_factor_enabled && !$user->isTrustedDevice($deviceFingerprint)) {
    // Trigger 2FA flow
}
```

### Checking Work Email Validation

```php
use App\Rules\ValidWorkEmail;

$validated = $request->validate([
    'email' => ['required', 'email', new ValidWorkEmail($role)]
]);
```

### Building Device Fingerprint

```php
use App\Services\DeviceFingerprintService;

$fingerprint = DeviceFingerprintService::generate($request);
$info = DeviceFingerprintService::extractDeviceInfo($request);
```

---

## Support & Maintenance

For questions or issues:
1. Check logs: `storage/logs/laravel.log`
2. Review this documentation
3. Check console commands: `php artisan list work-email:` `php artisan list user:`

---

**Last Updated**: March 9, 2026
**System**: IT14-PROG Inventory Management System
