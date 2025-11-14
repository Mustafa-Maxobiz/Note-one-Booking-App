<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teaching Reminder - 24 Hours</title>
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
        .session-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .prep-checklist {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
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
    <div class="container">
        <div class="header">
            <h1>👨‍🏫 Teaching Reminder</h1>
            <p>Your lesson starts in 24 hours!</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $teacher->user->name }}!</h2>
            
            <p>This is a friendly reminder that you have a lesson with <strong>{{ $student->user->name }}</strong> scheduled to start in 24 hours.</p>
            
            <div class="session-details">
                <h3>📅 Session Details:</h3>
                <p><strong>Date:</strong> {{ $booking->start_time->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</p>
                <p><strong>Student:</strong> {{ $student->user->name }}</p>
                <p><strong>Student Level:</strong> {{ $student->level ?? 'Not specified' }}</p>
                <p><strong>Duration:</strong> {{ $booking->duration_minutes }} minutes</p>
                @if($booking->notes)
                    <p><strong>Notes:</strong> {{ $booking->notes }}</p>
                @endif
            </div>
            
            <div class="prep-checklist">
                <h3>📝 Teaching Preparation Checklist:</h3>
                <ul>
                    <li>✅ Review student's learning goals and progress</li>
                    <li>✅ Prepare lesson materials and resources</li>
                    <li>✅ Test your Zoom setup (camera, microphone, screen sharing)</li>
                    <li>✅ Ensure stable internet connection</li>
                    <li>✅ Set up your teaching space (good lighting, quiet environment)</li>
                    <li>✅ Have backup lesson plans ready</li>
                    <li>✅ Check Zoom meeting settings and recording options</li>
                </ul>
            </div>
            
            <h3>🔗 Zoom Meeting Setup:</h3>
            @if($booking->zoom_meeting_id)
                <p><strong>Meeting ID:</strong> {{ $booking->zoom_meeting_id }}</p>
                <p><strong>Start URL:</strong> <a href="{{ $booking->zoom_start_url }}" target="_blank">Start Meeting</a></p>
                <p><strong>Join URL:</strong> <a href="{{ $booking->zoom_join_url }}" target="_blank">Join Meeting</a></p>
            @else
                <p><strong>Zoom meeting details will be created automatically before the session.</strong></p>
            @endif
            
            <h3>📚 Student Information:</h3>
            <p><strong>Name:</strong> {{ $student->user->name }}</p>
            <p><strong>Email:</strong> {{ $student->user->email }}</p>
            @if($student->user->phone)
                <p><strong>Phone:</strong> {{ $student->user->phone }}</p>
            @endif
            <p><strong>Learning Level:</strong> {{ $student->level ?? 'Not specified' }}</p>
            @if($student->learning_goals)
                <p><strong>Learning Goals:</strong> {{ $student->learning_goals }}</p>
            @endif
            
            <h3>💡 Teaching Tips:</h3>
            <ul>
                <li>Start the meeting 5 minutes early to test everything</li>
                <li>Have a backup plan if technology fails</li>
                <li>Keep the student engaged with interactive activities</li>
                <li>Take notes during the session for follow-up</li>
                <li>End with clear next steps and homework</li>
            </ul>
            
            <h3>❓ Need to Reschedule?</h3>
            <p>If you need to reschedule or cancel this lesson, please do so at least 24 hours in advance and notify the student.</p>
            
            <div class="action-buttons">
                <a href="{{ url('/teacher/dashboard') }}" class="btn btn-primary">📊 Go to Teacher Dashboard</a>
            </div>
        </div>
        
        <div class="footer">
            <p>This email was sent from the Online Lesson Booking System</p>
            <p>If you have any questions, please contact our support team</p>
        </div>
</body>
</html>
