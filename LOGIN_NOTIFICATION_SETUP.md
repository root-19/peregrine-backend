# Login Notification Setup

## Features Implemented

### 1. Email Notification on Login
- When a user successfully logs in, an email notification is automatically sent
- Email includes:
  - Account name
  - Email address
  - Account type (HR, Manager/COO, or User)
  - Login time
  - IP address
  - Security warning if login was unauthorized

### 2. Terminal/Console Logging

#### Backend (Laravel)
When a login occurs, the backend terminal will display:
```
========================================
🔐 LOGIN SUCCESSFUL - Backend API
========================================
👤 Account Name: [Name]
📧 Email: [email]
🔑 Account Type: [HR/MANAGER_COO/USER]
⏰ Login Time: [timestamp]
🌐 IP Address: [IP]
🆔 User ID: [ID]
========================================
```

#### Frontend (React Native)
When a login occurs, the React Native terminal will display:
```
========================================
🔐 LOGIN SUCCESSFUL
========================================
👤 Account Name: [Name]
📧 Email: [email]
🔑 Account Type: [HR/MANAGER_COO/USER]
⏰ Login Time: [timestamp]
🆔 User ID: [ID]
========================================
```

## Email Configuration

### Current Setup
The email is configured to use the `log` driver by default (for development). This means emails will be logged to `storage/logs/laravel.log` instead of being sent.

### To Enable Real Email Sending

1. Update `.env` file in `backend/`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Peregrine System"
```

2. For Gmail:
   - Enable 2-factor authentication
   - Generate an App Password
   - Use the App Password in `MAIL_PASSWORD`

3. For other email providers, update the SMTP settings accordingly.

### Testing Email in Development

To test emails without sending real emails, you can:
1. Keep `MAIL_MAILER=log` in `.env`
2. Check `storage/logs/laravel.log` for email content
3. Or use Mailtrap for testing: https://mailtrap.io/

## Files Created/Modified

### Created:
- `backend/app/Mail/LoginNotification.php` - Mailable class
- `backend/resources/views/emails/login-notification.blade.php` - Email template

### Modified:
- `backend/app/Http/Controllers/Api/AuthController.php` - Added email sending and logging
- `peregrine/app/login.tsx` - Added terminal logging

## How It Works

1. User logs in through React Native app
2. Frontend sends login request to Laravel API
3. Backend validates credentials
4. On successful login:
   - Backend displays login info in terminal
   - Backend sends email notification (async, non-blocking)
   - Backend logs to Laravel log file
   - Frontend displays login info in terminal
   - Frontend proceeds with OTP flow

## Email Template

The email template is located at:
`backend/resources/views/emails/login-notification.blade.php`

You can customize the email design by editing this file.

## Logging

All login events are logged to:
- Terminal/Console (immediate visibility)
- `storage/logs/laravel.log` (persistent log)

To view logs:
```bash
tail -f storage/logs/laravel.log
```

## Security Notes

- Email sending is non-blocking (won't delay login)
- If email fails, login still succeeds
- IP address is captured for security monitoring
- Security warning included in email for unauthorized access awareness

