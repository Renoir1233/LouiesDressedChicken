# IT14-PROG Security Updates - Session 2

## Updates Completed ✅

### 1. **Super Admin Email Changed**
- Old Email: `admin@louieschicken.com`
- New Email: `guadalquivercarlpatrick@gmail.com`
- Password: `Admin123!`
- Status: ✅ Database updated and seeder modified

### 2. **Forgot Password Feature Added**
- Added "Forgot Password?" link to login page
- Link routes to: `/auth/forgot-password`
- Features:
  - Users enter email to receive password reset link
  - Reset link valid for 60 minutes
  - Email sent with reset instructions
  - Secure token-based password reset
- Status: ✅ Integrated and visible on login page

### 3. **Progressive Login Lockout System** 🔒
Implements escalating account lockouts after consecutive failed password attempts:

#### Lockout Progression:
| Attempts | Status | Action |
|----------|--------|--------|
| 1-2 | No action | Normal failed login |
| 3 | Warning | ⚠️ User warned: "Next failed attempt will lock account for 2 minutes" |
| 4 | 2-Min Lockout | 🔒 Account locked for 2 minutes |
| 5 | 10-Min Lockout | 🔒 Account locked for 10 minutes |
| 6 | 1-Hour Lockout | 🔒 Account locked for 1 hour |
| 7+ | 24-Hour Lockout | 🔒 Account locked for 24 hours |

#### Features:
- Database-backed tracking (persistent across server restarts)
- Displays warning messages before lockouts
- Shows remaining lockout time to user
- Resets attempt counter if no attempts for 1 hour
- Clears attempts on successful login

---

## Files Created

### Models
- ✅ `app/Models/LoginAttempt.php` - Tracks login attempts and lockouts

### Services  
- ✅ `app/Services/ProgressiveLoginLockoutService.php` - Manages lockout logic and rules

### Migrations
- ✅ `database/migrations/2026_03_09_000004_create_login_attempts_table.php` - Stores attempt data

### Database Schema (login_attempts table)
```sql
- id (primary key)
- email (indexed)
- ip_address (indexed)
- failed_attempts (counter)
- first_attempt_at (timestamp)
- last_attempt_at (timestamp)
- locked_until (timestamp for lockout duration)
- created_at & updated_at
```

---

## Files Modified

### 1. Database Seeders
- **`database/seeders/AdminUserSeeder.php`**
  - Changed super admin email to `guadalquivercarlpatrick@gmail.com`
  - Added cleanup to remove old admin email
  - Updated display credentials in console output

### 2. Views
- **`resources/views/auth/login.blade.php`**
  - Added lockout warning display section
  - Added "Forgot Password?" link below login button
  - Styled warning messages with yellow background and warning icon

### 3. Authentication
- **`app/Http/Requests/Auth/LoginRequest.php`**
  - Integrated progressive lockout checking
  - Records failed attempts with progressive lockouts
  - Clears attempts on successful login
  - Shows session warnings to user
  - Enhanced error messages with lockout information

---

## How the Progressive Lockout Works

### Example Scenario:

**User attempts login with wrong password:**

1st attempt ❌
```
Error: "The provided credentials do not match our records."
No warning, can retry immediately
```

2nd attempt ❌
```
Error: "The provided credentials do not match our records."
No warning, can retry immediately
```

3rd attempt ❌
```
⚠️ WARNING SHOWN: "Your next failed attempt will lock your account for 2 minutes."
Error: "The provided credentials do not match our records."
```

4th attempt ❌
```
🔒 Account Locked!
Error: "Account locked. Please try again in 2 minute(s)."
Cannot login for 2 minutes
```

After 2 minutes, user can try again:

5th attempt ❌
```
🔒 Account Locked for 10 minutes!
Error: "Account locked. Please try again in 10 minute(s)."
Cannot login for 10 minutes
```

And so on... escalating to 1 hour, then 24 hours.

**Successful Login ✅**
```
- Attempts counter reset to 0
- Account fully unlocked
- User can retry if needed next time
```

---

## Testing the Progressive Lockout

### Manual Testing:
1. Open login page
2. Enter your email address
3. Enter wrong password 3 times
4. Observe warning message
5. Enter wrong password 4th time
6. Observe 2-minute lockout message
7. Wait 2 minutes and try again
8. Observe 10-minute lockout

### Test Credentials:
```
Super Admin: guadalquivercarlpatrick@gmail.com / Admin123!
Admin: admin2@louieschicken.com / Admin123!
Manager: manager@louieschicken.com / Manager123!
Cashier: cashier@louieschicken.com / Cashier123!
Staff: staff@louieschicken.com / Staff123!
```

---

## Database Changes Summary

### New Table: `login_attempts`
Stores failed login attempts with lockout tracking:

```php
Schema::create('login_attempts', function (Blueprint $table) {
    $table->id();
    $table->string('email');                           // User email
    $table->string('ip_address');                      // Client IP
    $table->integer('failed_attempts')->default(1);    // Count of failures
    $table->timestamp('first_attempt_at');             // When first attempt was
    $table->timestamp('last_attempt_at');              // When last attempt was
    $table->timestamp('locked_until')->nullable();     // When lockout expires
    $table->timestamps();                              // created_at, updated_at
    
    $table->index(['email', 'ip_address']);            // Quick lookup
    $table->index('locked_until');                     // Find expired locks
});
```

### LoginAttempt Model Methods:
- `getOrCreate()` - Get or create attempt record
- `recordFailedAttempt()` - Increment attempt counter
- `isLocked()` - Check if currently locked
- `getRemainingLockoutMinutes()` - Time until unlock
- `lockFor()` - Apply lockout duration
- `clearAttempts()` - Reset counter
- `resetIfExpired()` - Auto-reset if 1+ hour passed

---

## Security Benefits

1. **Brute Force Protection** 🛡️
   - Prevents continuous password guessing
   - Escalating delays make attacks increasingly difficult

2. **User Experience** 👤
   - Clear warnings before lockout happens
   - Shows remaining time
   - Resets automatically after 1 hour of inactivity

3. **Persistent Tracking** 💾
   - Database-backed (survives server restarts)
   - Tracks IP + Email combination
   - Can be audited for security analysis

4. **Flexible Configuration** ⚙️
   - Easily adjust lockout durations in `ProgressiveLoginLockoutService`
   - Add more rules or change thresholds
   - Separate admin command to manually clear attempts (future)

---

## Configuration & Customization

### To Adjust Lockout Durations:

Edit `app/Services/ProgressiveLoginLockoutService.php`:

```php
public static function getLockoutDuration($attemptCount): ?int
{
    return match($attemptCount) {
        4 => 2,      // Change 2 to desired minutes
        5 => 10,     // Change 10 to desired minutes
        6 => 60,     // Change 60 to desired minutes
        default => $attemptCount >= 7 ? 1440 : null, // Change 1440 to desired minutes
    };
}
```

### To Adjust Warning Threshold:

```php
public static function getWarningThreshold(): int
{
    return 3; // Change 3 to show warning after different attempt count
}
```

---

## Admin Commands (To Implement)

Commands that could be added for admin management:

```bash
# Clear failed attempts for a specific user-IP combination
php artisan login:clear-attempts user@email.com 192.168.1.1

# View all current lockouts
php artisan login:view-lockouts

# Unlock a specific user
php artisan login:unlock user@email.com
```

---

## Logs & Monitoring

Failed login attempts are tracked in the `login_attempts` table:

```sql
SELECT * FROM login_attempts 
WHERE locked_until > NOW() 
ORDER BY last_attempt_at DESC;
```

This shows:
- Currently locked accounts
- Which IPs are being targeted
- Historical attempt patterns

---

## Security Best Practices

1. **Monitor the login_attempts table** for suspicious activity
2. **Regular backups** to preserve security logs
3. **Review locked accounts** - may indicate attacks
4. **Update IP detection** if behind proxies
5. **Test periodically** to ensure system still works

---

## Session Summary

| Feature | Status | Notes |
|---------|--------|-------|
| Super Admin Email Change | ✅ Complete | Updated to guadalquivercarlpatrick@gmail.com |
| Forgot Password Link | ✅ Complete | Visible on login page |
| Progressive Lockout | ✅ Complete | 3→2m→10m→1h→24h escalation |
| Database Tracking | ✅ Complete | Persistent storage of attempts |
| Warning Messages | ✅ Complete | Displayed before lockouts |
| Email Validation | ✅ Ready | From Session 1 implementation |
| Two-Factor Auth | ✅ Ready | From Session 1 implementation |

---

## Next Steps (Optional)

1. Create admin commands to manage lockouts
2. Add lockout history/audit logging
3. Create admin dashboard to view failed attempts
4. Add email notifications for multiple lockouts
5. Implement captcha after N attempts
6. Add whitelist for admin IPs

---

**Last Updated**: March 9, 2026  
**Status**: All requested features implemented and tested ✅
