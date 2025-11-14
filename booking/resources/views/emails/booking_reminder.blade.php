<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson Reminder</title>
    <style>
        body {
            font-family: 'Sora', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
            color: white;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #fdb838;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #070c39;
            margin-bottom: 10px;
        }
        .title {
            color: #ef473e;
            font-size: 28px;
            margin: 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .booking-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #fdb838;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .detail-label {
            font-weight: bold;
            color: #070c39;
        }
        .detail-value {
            color: #333;
        }
        .zoom-link {
            background: linear-gradient(135deg, #fdb838, #ef473e);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .zoom-link:hover {
            background: linear-gradient(135deg, #ef473e, #fdb838);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            background: linear-gradient(135deg, #fdb838, #ef473e);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 20px;
            display: inline-block;
            margin: 10px 5px;
            font-weight: bold;
        }
        .button:hover {
            background: linear-gradient(135deg, #ef473e, #fdb838);
        }
        .reminder-icon {
            color: #ffc107;
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }
        .time-highlight {
            background: linear-gradient(135deg, #fdb838, #ef473e);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Online Lesson Booking System</div>
            <h1 class="title">Lesson Reminder</h1>
        </div>

        <div class="reminder-icon">⏰</div>

        <div class="content">
            <p>Dear <strong>{{ $data['student_name'] }}</strong>,</p>
            
            <p>This is a friendly reminder that you have a lesson coming up!</p>

            <div class="time-highlight">
                {{ $data['reminder_time'] }} until your lesson starts
            </div>

            <div class="booking-details">
                <h3 style="color: #070c39; margin-top: 0;">📅 Lesson Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Teacher:</span>
                    <span class="detail-value">{{ $data['teacher_name'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time:</span>
                    <span class="detail-value">{{ $data['lesson_date'] }} at {{ $data['lesson_time'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">{{ $data['duration'] }} minutes</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Subject:</span>
                    <span class="detail-value">{{ $data['subject'] ?? 'General Lesson' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Booking ID:</span>
                    <span class="detail-value">#{{ $data['booking_id'] }}</span>
                </div>
            </div>

            @if(isset($data['zoom_link']) && $data['zoom_link'])
            <div style="text-align: center; margin: 30px 0;">
                <h3 style="color: #070c39;">🎥 Join Your Lesson</h3>
                <p>Click the button below to join your Zoom lesson:</p>
                <a href="{{ $data['zoom_link'] }}" class="zoom-link">
                    🚀 Join Zoom Lesson
                </a>
                <p style="font-size: 12px; color: #6c757d; margin-top: 10px;">
                    Meeting ID: {{ $data['zoom_meeting_id'] ?? 'N/A' }}<br>
                    Password: {{ $data['zoom_password'] ?? 'No password required' }}
                </p>
            </div>
            @endif

            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
                <h4 style="color: #856404; margin-top: 0;">📝 Pre-Lesson Checklist:</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>✅ Test your internet connection</li>
                    <li>✅ Check your camera and microphone</li>
                    <li>✅ Have your learning materials ready</li>
                    <li>✅ Find a quiet, well-lit space</li>
                    <li>✅ Join 5 minutes before the lesson starts</li>
                </ul>
            </div>

            <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #0c5460; margin-top: 0;">💡 Pro Tips:</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Close unnecessary applications to improve performance</li>
                    <li>Use headphones for better audio quality</li>
                    <li>Have a backup device ready in case of technical issues</li>
                    <li>Keep your phone nearby for emergency contact</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $data['dashboard_url'] }}" class="button">View Dashboard</a>
                <a href="{{ $data['reschedule_url'] ?? '#' }}" class="button">Reschedule</a>
            </div>
        </div>

        <div class="footer">
            <p>We're looking forward to seeing you in class!</p>
            <p><strong>Online Lesson Booking System</strong><br>
            Email: {{ $data['contact_email'] ?? 'support@example.com' }}<br>
            Phone: {{ $data['contact_phone'] ?? '+1 (555) 123-4567' }}</p>
        </div>
    </div>
</body>
</html>
