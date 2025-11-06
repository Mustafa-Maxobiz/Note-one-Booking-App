<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Booking Declined</title>
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
            color: #FFFFFF !important;
        }
        .btn-success {
            background-color: #28a745;
            color: #FFFFFF !important;
        }
        .btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
        .highlight {
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .highlight strong {
            color: #721c24;
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
        <h1>❌ Session Booking Declined</h1>
        <p>Your booking request has been declined by the teacher</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $student_name }}</strong>,</p>

        <p>We regret to inform you that <strong>{{ $teacher_name }}</strong> has declined your session booking request for the following session:</p>

        <div class="booking-details">
            <h3>📅 Session Details</h3>
            <p><strong>Date:</strong> {{ $booking_date }}</p>
            <p><strong>Time:</strong> {{ $booking_time }}</p>
            <p><strong>Duration:</strong> {{ $booking_duration }}</p>
        </div>

        <div class="highlight">
            <p><strong>💡 What you can do:</strong></p>
            <ul>
                <li>Try booking with a different teacher</li>
                <li>Choose a different time slot</li>
                <li>Contact the teacher directly to discuss availability</li>
                <li>Check other available teachers in your subject area</li>
            </ul>
        </div>

        <div class="info-box">
            <p><strong>🔍 Suggestions:</strong></p>
            <ul>
                <li>Browse our teacher directory to find other qualified teachers</li>
                <li>Check teacher availability for different dates and times</li>
                <li>Consider booking a shorter session if available</li>
                <li>Contact our support team if you need assistance</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="{{ route('student.booking.calendar') }}" class="btn btn-success">📅 Book via Calendar</a>
            <a href="{{ $booking_url }}" class="btn btn-primary">🔍 Browse Teachers</a>
        </div>

        <p><strong>Need help?</strong></p>
        <p>If you have any questions or need assistance finding another teacher, please don't hesitate to contact our support team. We're here to help you find the perfect learning experience!</p>

        <p>Thank you for using our Online Lesson Booking System.</p>
    </div>

    <div class="footer">
        <p>This email was sent from the Online Lesson Booking System</p>
        <p>If you have any questions, please contact our support team</p>
    </div>
</body>
</html>
