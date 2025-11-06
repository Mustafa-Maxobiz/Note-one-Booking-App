<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Rescheduled - New Time Confirmed</title>
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
        .details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
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
        .muted {
            color: #6c757d;
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
        <h1>📆 Session Rescheduled</h1>
        <p>Your booking with {{ $teacher_name ?? 'your teacher' }} has a new time</p>
    </div>
    <div class="content">
        <h2>Hello {{ $student_name ?? 'Student' }},</h2>
        <p>Your session has been rescheduled. Please review the updated details below.</p>

        <div class="details">
            <h3>🕒 Time Change</h3>
            @if(!empty($old_date) && !empty($old_time_range))
                <p class="muted"><strong>Previous:</strong> {{ $old_date }} • {{ $old_time_range }}</p>
            @endif
            <p><strong>New:</strong> {{ $new_date ?? '' }} • {{ $new_time_range ?? '' }} ({{ $duration ?? '' }})</p>
        </div>

        @if(!empty($zoom_join_url))
            <div class="details" style="border-left-color:#28a745;">
                <h3>🔗 Zoom Meeting</h3>
                <p><a href="{{ $zoom_join_url }}" class="btn btn-success">🎥 Join Meeting</a></p>
            </div>
        @endif

        <div class="action-buttons" style="text-align: center; margin: 30px 0;">
            @if(!empty($booking_details_url))
                <a href="{{ $booking_details_url }}" class="btn btn-primary">📋 View Updated Details</a>
            @endif
        </div>

        <p>If the new time doesn’t work for you, reply to this email to coordinate a better time.</p>

        <p>Best regards,<br>The Online Booking System Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Online Lesson Booking System. All rights reserved.</p>
    </div>
</body>
</html>


