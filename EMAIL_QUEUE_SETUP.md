# Email Queue Setup - Fast Login

## Problem Fixed
The email notification was blocking the login response, making it slow. Now emails are sent asynchronously using Laravel queues.

## How It Works Now

1. **Login happens immediately** - Response is returned right away
2. **Email is queued** - Sent in background without blocking
3. **User can proceed** - No waiting for email to send

## Setup Instructions

### 1. Make sure queue is configured

Check `backend/.env`:
```env
QUEUE_CONNECTION=database
```

### 2. Run queue worker (in a separate terminal)

```bash
cd backend
php artisan queue:work
```

This will process queued emails in the background.

### 3. For Development (Optional - Auto Process)

If you want emails to process automatically without running queue worker separately, you can use:

```bash
php artisan queue:listen
```

Or for testing, you can process jobs synchronously by changing `.env`:
```env
QUEUE_CONNECTION=sync
```

But this will make emails block again. **Recommended: Use `database` queue with `queue:work`**

## Running Queue Worker

### Option 1: Manual (Recommended for Development)
```bash
cd backend
php artisan queue:work
```

Keep this terminal open - it will process emails as they come in.

### Option 2: Background Process (Production)
```bash
php artisan queue:work --daemon
```

### Option 3: Supervisor (Production - Recommended)
Set up Supervisor to automatically restart queue workers.

## Testing

1. Start Laravel server: `php artisan serve`
2. Start queue worker: `php artisan queue:work` (in another terminal)
3. Login from React Native app
4. Login should be instant - no waiting!
5. Email will be sent in background

## Monitoring

Check queue status:
```bash
php artisan queue:monitor
```

View failed jobs:
```bash
php artisan queue:failed
```

Retry failed jobs:
```bash
php artisan queue:retry all
```

## Notes

- **Login is now instant** - No waiting for email
- **Emails are queued** - Processed by queue worker
- **If queue worker is not running** - Emails will be queued but not sent until worker starts
- **For development** - You can run queue worker in same terminal or separate terminal

## Quick Start

```bash
# Terminal 1: Laravel Server
cd backend
php artisan serve

# Terminal 2: Queue Worker
cd backend
php artisan queue:work
```

Now login will be fast! 🚀

