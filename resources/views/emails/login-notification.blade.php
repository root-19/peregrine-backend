<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #228B22;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #228B22;
            border-radius: 4px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .warning {
            background-color: #fff3cd;
            border-left-color: #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Login Notification</h1>
    </div>
    
    <div class="content">
        <p>Hello <strong>{{ $accountName }}</strong>,</p>
        
        <p>This is to notify you that a login was detected on your account.</p>
        
        <div class="info-box">
            <div class="info-label">Account Email:</div>
            <div class="info-value">{{ $accountEmail }}</div>
        </div>
        
        <div class="info-box">
            <div class="info-label">Account Type:</div>
            <div class="info-value">{{ $accountType }}</div>
        </div>
        
        <div class="info-box">
            <div class="info-label">Login Time:</div>
            <div class="info-value">{{ $loginTime }}</div>
        </div>
        
        <div class="info-box">
            <div class="info-label">IP Address:</div>
            <div class="info-value">{{ $ipAddress }}</div>
        </div>
        
        <div class="warning">
            <strong>⚠️ Security Notice:</strong><br>
            If you did not perform this login, please contact the system administrator immediately and change your password.
        </div>
        
        <p>Thank you for using Peregrine System.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from Peregrine System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>

