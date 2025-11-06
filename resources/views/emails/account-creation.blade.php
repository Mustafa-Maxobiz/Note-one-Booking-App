<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Online Lesson Booking System</title>
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
            background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .credentials {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .credentials h3 {
            color: #495057;
            margin-top: 0;
            font-size: 18px;
        }
        .credential-item {
            margin: 15px 0;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .credential-label {
            font-weight: bold;
            color: #495057;
            display: block;
            margin-bottom: 5px;
        }
        .credential-value {
            color: #667eea;
            font-family: monospace;
            font-size: 16px;
            font-weight: bold;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        .highlight strong {
            color: #856404;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #667eea;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
        .info-box {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        ul {
            padding-left: 20px;
        }
        ul li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 Welcome to Online Lesson Booking System</h1>
        <p>Your {{ $role }} account has been created successfully!</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $name }}</strong>,</p>

        <p>Welcome to the Online Lesson Booking System! Your account has been created and you're all set to get started.</p>

        <div class="credentials">
            <h3>🔐 Your Login Credentials</h3>
            <div class="credential-item">
                <span class="credential-label">Email Address:</span>
                <div class="credential-value">{{ $email }}</div>
            </div>
            <div class="credential-item">
                <span class="credential-label">Temporary Password:</span>
                <div class="credential-value">{{ $password }}</div>
            </div>
        </div>

        <div class="highlight">
            <p><strong>⚠️ Important Security Notice:</strong> Please change your password immediately after your first login for security purposes.</p>
        </div>

        <div class="action-buttons">
            <a href="{{ $login_url }}" class="btn btn-primary">🔑 Login Now</a>
            <a href="{{ $dashboard_url }}" class="btn btn-success">📊 Go to Dashboard</a>
        </div>

        <div class="info-box">
            <p><strong>🚀 Getting Started:</strong></p>
            <ul>
                <li>Log in to your account using the credentials above</li>
                <li>Change your password for security</li>
                <li>Complete your profile information</li>
                @if($role === 'Teacher')
                    <li>Set up your teaching availability schedule</li>
                    <li>Add your qualifications and teaching style</li>
                    <li>Start accepting student bookings</li>
                @elseif($role === 'Admin')
                    <li>Configure system settings</li>
                    <li>Manage users and bookings</li>
                    <li>Monitor system performance</li>
                @else
                    <li>Browse available teachers</li>
                    <li>Book your first session</li>
                    <li>Access learning materials</li>
                @endif
            </ul>
        </div>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Thank you for joining our platform!</p>
    </div>

    <div class="footer">
        <p>This email was sent from the Online Lesson Booking System</p>
        <p>If you have any questions, please contact our support team</p>
    </div>
</body>
</html>
