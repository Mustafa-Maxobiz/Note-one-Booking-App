<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Booking Accepted!</title>
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
        .booking-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .zoom-details {
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
        <h1>✅ Session Booking Accepted!</h1>
        <p>Great news! Your teacher has accepted your booking request</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $student_name }}</strong>,</p>

        <p>Excellent news! <strong>{{ $teacher_name }}</strong> has accepted your session booking request. Your session is now confirmed!</p>

        <div class="booking-details">
            <h3>📅 Session Details</h3>
            <p><strong>Date:</strong> {{ $booking_date }}</p>
            <p><strong>Time:</strong> {{ $booking_time }}</p>
            <p><strong>Duration:</strong> {{ $booking_duration }}</p>
        </div>

        <div class="zoom-details">
            <h3>🔗 Zoom Meeting Details</h3>
            @if($zoom_join_url && $zoom_join_url !== 'Will be provided soon')
                <p><strong>Join URL:</strong> <a href="{{ $zoom_join_url }}" style="color: #28a745;">{{ $zoom_join_url }}</a></p>
                <p><strong>Meeting ID:</strong> Will be provided in the Zoom link</p>
                <p><strong>Password:</strong> Will be provided in the Zoom link</p>
            @else
                <p><strong>Zoom meeting details will be provided soon by your teacher.</strong></p>
            @endif
        </div>

        <div class="highlight">
            <p><strong>📝 Important Reminders:</strong></p>
            <ul>
                <li>Please join the Zoom meeting 5 minutes before your scheduled time</li>
                <li>Make sure you have a stable internet connection</li>
                <li>Test your microphone and camera before the session</li>
                <li>Have any materials or questions ready</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="{{ $session_details_url }}" class="btn btn-primary">📋 View Session Details</a>
        </div>

        <p><strong>Need to make changes?</strong></p>
        <p>If you need to reschedule or cancel your session, please contact your teacher as soon as possible.</p>

        <p>We hope you have a great learning session!</p>
    </div>

    <div class="footer">
        <p>This email was sent from the Online Lesson Booking System</p>
        <p>If you have any questions, please contact our support team</p>
    </div>
</body>
</html>
