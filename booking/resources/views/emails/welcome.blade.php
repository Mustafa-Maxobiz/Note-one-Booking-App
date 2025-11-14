<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Online Lesson Booking System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .welcome-message {
            font-size: 18px;
            margin-bottom: 20px;
            color: #070c39;
        }
        .features {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .features h3 {
            color: #070c39;
            margin-top: 0;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            position: relative;
            padding-left: 25px;
        }
        .feature-list li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 10px 20px 0;
            font-weight: bold;
            text-align: center;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .button-secondary {
            background: #070c39;
        }
        .footer {
            background-color: #070c39;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer a {
            color: #fdb838;
            text-decoration: none;
        }
        .role-badge {
            display: inline-block;
            background-color: #fdb838;
            color: #070c39;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
        }
        .highlight {
            background-color: #fff3cd;
            border-left: 4px solid #fdb838;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Welcome to Online Lesson Booking System!</h1>
            <p>Your journey to better learning starts here</p>
        </div>

        <div class="content">
            <div class="welcome-message">
                Hello <strong>{{ $data['name'] }}</strong>,
            </div>

            <p>Welcome to the Online Lesson Booking System! We're thrilled to have you join our community of learners and educators.</p>

            <div class="role-badge">
                You're registered as a {{ $data['role'] }}
            </div>

            <div class="features">
                <h3>🚀 What you can do now:</h3>
                <ul class="feature-list">
                    @if($data['role'] === 'Teacher')
                        <li>Set your availability schedule</li>
                        <li>Accept or decline lesson requests</li>
                        <li>Conduct online sessions via Zoom</li>
                        <li>Track your earnings and performance</li>
                        <li>Receive feedback from students</li>
                    @else
                        <li>Browse available teachers</li>
                        <li>Book lessons at your convenience</li>
                        <li>Join online sessions via Zoom</li>
                        <li>Access session recordings</li>
                        <li>Track your learning progress</li>
                    @endif
                </ul>
            </div>

            <div class="highlight">
                <strong>💡 Pro Tip:</strong> Complete your profile to get the most out of the platform. Add your bio, qualifications, and preferences to help others find you easily.
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $data['dashboard_url'] }}" class="button">
                    🏠 Go to Dashboard
                </a>
                
                @if($data['role'] === 'Student')
                    <a href="{{ $data['booking_url'] }}" class="button button-secondary">
                        📅 Book Your First Lesson
                    </a>
                @endif
            </div>

            <p>If you have any questions or need assistance, don't hesitate to reach out to our support team. We're here to help you succeed!</p>

            <p>Best regards,<br>
            <strong>The Online Lesson Booking System Team</strong></p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Online Lesson Booking System. All rights reserved.</p>
            <p>This email was sent to {{ $data['email'] }}. If you didn't create an account, please ignore this email.</p>
        </div>
    </div>
</body>
</html>

