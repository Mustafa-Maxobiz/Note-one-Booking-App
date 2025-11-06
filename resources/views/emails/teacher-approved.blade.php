<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teacher Account Approved</title>
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
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .success-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
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
        <h1>🎉 Congratulations! Your Account is Approved</h1>
        <p>You're now a verified teacher on our platform</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $teacher_name }}</strong>,</p>

        <p>Great news! Your teacher account has been approved and verified by our team.</p>

        <div class="success-box">
            <h3>✅ Account Status: VERIFIED</h3>
            <p><strong>Approved on:</strong> {{ $approved_at }}</p>
            <p><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">Active & Ready to Teach</span></p>
        </div>

        <p><strong>What this means for you:</strong></p>
        <ul>
            <li>✅ Your profile is now visible to students</li>
            <li>✅ Students can book sessions with you</li>
            <li>✅ You can accept booking requests</li>
            <li>✅ You'll receive notifications for new bookings</li>
        </ul>

        <div class="action-buttons">
            <a href="{{ $login_url }}" class="btn btn-primary">🔑 Login Now</a>
            <a href="{{ $dashboard_url }}" class="btn btn-success">📊 Go to Dashboard</a>
        </div>

        <div class="info-box">
            <p><strong>🚀 Next Steps:</strong></p>
            <ul>
                <li>Set up your teaching availability schedule</li>
                <li>Complete your profile with qualifications and teaching style</li>
                <li>Add your bio to help students know you better</li>
                <li>Start accepting booking requests from students</li>
            </ul>
        </div>

        <p>We're excited to have you as part of our teaching community!</p>

        <p>If you have any questions, feel free to reach out to our support team.</p>

        <p>Happy teaching!</p>
    </div>

    <div class="footer">
        <p>This email was sent from the Online Lesson Booking System</p>
        <p>If you have any questions, please contact our support team</p>
    </div>
</body>
</html>

