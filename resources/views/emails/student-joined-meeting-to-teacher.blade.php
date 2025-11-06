<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Joined Your Meeting</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .meeting-info {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .join-button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .join-button:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .meeting-details {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .meeting-details h3 {
            color: #28a745;
            margin-top: 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .success-notice {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #155724;
        }
        .student-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Student Joined!</h1>
            <p>Your student has entered the meeting</p>
        </div>
        
        <div class="content">
            <div class="success-notice">
                <strong>✅ Great News:</strong> Your student {{ $student->name }} has joined the meeting and is ready to start the session!
            </div>

            <div class="meeting-info">
                <h2>📅 Session Details</h2>
                <div class="meeting-details">
                    <div class="detail-row">
                        <span class="detail-label">Student:</span>
                        <span class="detail-value">{{ $student->name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date & Time:</span>
                        <span class="detail-value">{{ $booking->start_time->format('l, F j, Y \a\t g:i A') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Duration:</span>
                        <span class="detail-value">{{ $booking->duration_minutes }} minutes</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Meeting ID:</span>
                        <span class="detail-value">{{ $booking->zoom_meeting_id }}</span>
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $start_url }}" class="join-button">
                    🎯 Start Teaching Now
                </a>
            </div>

            <div class="student-info">
                <h3>👨‍🎓 Student Information</h3>
                <p><strong>Name:</strong> {{ $student->name }}</p>
                <p><strong>Email:</strong> {{ $student->email }}</p>
                @if($student->phone)
                <p><strong>Phone:</strong> {{ $student->phone }}</p>
                @endif
                @if($student->student && $student->student->level)
                <p><strong>Learning Level:</strong> {{ $student->student->level }}</p>
                @endif
            </div>

            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>💡 Teaching Reminders:</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Welcome your student and check their audio/video</li>
                    <li>Review the session objectives together</li>
                    <li>Engage actively and maintain eye contact</li>
                    <li>Take notes during the session for follow-up</li>
                    <li>End with clear next steps and homework</li>
                </ul>
            </div>

            <div style="background-color: #e8f5e8; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0;"><strong>🎉 Session Status:</strong> Your student is now in the meeting and ready to begin. Make sure to provide an excellent learning experience!</p>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from your teaching platform.</p>
            <p>If you're having trouble with the link above, copy and paste this URL into your browser:</p>
            <p style="word-break: break-all; color: #28a745;">{{ $start_url }}</p>
        </div>
    </div>
</body>
</html>
