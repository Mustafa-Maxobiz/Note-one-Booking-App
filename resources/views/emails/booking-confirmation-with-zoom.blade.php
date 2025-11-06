<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Confirmed - Zoom Meeting Details</title>
    <style>
        body {
            font-family: 'Sora', Arial, sans-serif;
            line-height: 1.6;
            color: #000000;
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
        .session-details {
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
        <h1>🎉 Session Confirmed!</h1>
        <p>Your booking has been accepted by {{ $teacher_name }}</p>
    </div>

    <div class="content">
        <h2>Hello {{ $student_name }},</h2>
        
        <p>Great news! Your session with <strong>{{ $teacher_name }}</strong> has been confirmed. Here are all the details you need:</p>

        <div class="session-details">
            <h3>📅 Session Details</h3>
            <p><strong>Date:</strong> {{ $booking_date }}</p>
            <p><strong>Time:</strong> {{ $booking_time }}</p>
            <p><strong>Duration:</strong> {{ $booking_duration }}</p>
            @if($notes && $notes !== 'No additional notes')
                <p><strong>Notes:</strong> {{ $notes }}</p>
            @endif
        </div>

        <div class="zoom-details">
            <h3>🔗 Zoom Meeting Details</h3>
            @if($zoom_join_url && $zoom_join_url !== 'Will be provided by your teacher')
                <p><strong>Join URL:</strong> <a href="{{ $zoom_join_url }}" class="button">Join Meeting</a></p>
            @else
                <p><strong>Join URL:</strong> Will be provided by your teacher</p>
            @endif
            
            @if($zoom_meeting_id && $zoom_meeting_id !== 'Will be provided by your teacher')
                <p><strong>Meeting ID:</strong> {{ $zoom_meeting_id }}</p>
            @else
                <p><strong>Meeting ID:</strong> Will be provided by your teacher</p>
            @endif
            
            @if($zoom_password && $zoom_password !== 'Will be provided by your teacher')
                <p><strong>Password:</strong> {{ $zoom_password }}</p>
            @else
                <p><strong>Password:</strong> Will be provided by your teacher</p>
            @endif
        </div>

        <div class="highlight">
            <h4>💡 Important Reminders:</h4>
            <ul>
                <li>Please join the meeting 5 minutes before the scheduled time</li>
                <li>Make sure your microphone and camera are working</li>
                <li>Have a stable internet connection</li>
                <li>Find a quiet environment for the session</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="{{ $booking_details_url }}" class="btn btn-primary">📋 View Session Details</a>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4>📞 Contact Information</h4>
            <p><strong>Teacher:</strong> {{ $teacher_name }}</p>
            <p><strong>Email:</strong> {{ $teacher_email }}</p>
            @if($teacher_phone && $teacher_phone !== 'Not provided')
                <p><strong>Phone:</strong> {{ $teacher_phone }}</p>
            @endif
        </div>

        <p>If you have any questions or need to reschedule, please contact your teacher directly.</p>
        
        <p>We hope you have a great learning session!</p>
        
        <p>Best regards,<br>
        The Online Booking System Team</p>
    </div>

    <div class="footer">
        <p>This email was sent to {{ $student_email }}. If you have any questions, please contact support.</p>
        <p>&copy; {{ date('Y') }} Online Lesson Booking System. All rights reserved.</p>
    </div>
</body>
</html>
