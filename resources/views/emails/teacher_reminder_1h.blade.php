<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teaching Reminder - 1 Hour</title>
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
        .zoom-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
            text-align: center;
        }
        .checklist {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .urgent {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
            color: #721c24;
            font-weight: bold;
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
            <h1>🚨 URGENT: Your Lesson Starts in 1 Hour!</h1>
            <p>Time to prepare for your teaching session!</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $teacher->user->name }}!</h2>
            
            <p class="urgent">Your lesson with <strong>{{ $student->user->name }}</strong> starts in just 1 hour!</p>
            
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
            
            <div class="zoom-section">
                <h3>🔗 Start Your Zoom Lesson Now!</h3>
                @if($booking->zoom_meeting_id)
                    <p><strong>Meeting ID:</strong> {{ $booking->zoom_meeting_id }}</p>
                    @if($booking->zoom_password)
                        <p><strong>Password:</strong> {{ $booking->zoom_password }}</p>
                    @endif
                    <a href="{{ $booking->zoom_start_url }}" class="btn btn-success" target="_blank">🎥 Start Zoom Meeting</a>
                    <p><small>Or share this link with your student: <a href="{{ $booking->zoom_join_url }}">{{ $booking->zoom_join_url }}</a></small></p>
                @else
                    <p><strong>⚠️ Zoom meeting details not yet available. Please create a meeting and share the details with your student.</strong></p>
                @endif
            </div>
            
            <div class="checklist">
                <h3>⚡ Final Preparation Checklist:</h3>
                <ul>
                    <li>✅ Zoom app is installed and updated</li>
                    <li>✅ Camera and microphone are working</li>
                    <li>✅ You're in a quiet, well-lit teaching space</li>
                    <li>✅ Lesson materials are ready and organized</li>
                    <li>✅ Internet connection is stable</li>
                    <li>✅ Screen sharing is set up (if needed)</li>
                    <li>✅ Recording is enabled (if required)</li>
                    <li>✅ Student contact information is handy</li>
                </ul>
            </div>
            
            <h3>👤 Student Information:</h3>
            <p><strong>Name:</strong> {{ $student->user->name }}</p>
            <p><strong>Email:</strong> {{ $student->user->email }}</p>
            @if($student->user->phone)
                <p><strong>Phone:</strong> {{ $student->user->phone }}</p>
            @endif
            <p><strong>Learning Level:</strong> {{ $student->level ?? 'Not specified' }}</p>
            @if($student->learning_goals)
                <p><strong>Learning Goals:</strong> {{ $student->learning_goals }}</p>
            @endif
            
            <h3>📱 Alternative Meeting Setup:</h3>
            <p>If Zoom isn't working, you can:</p>
            <ol>
                <li>Use Google Meet, Microsoft Teams, or Skype as backup</li>
                <li>Call the student directly if you have their phone number</li>
                <li>Reschedule if technical issues persist</li>
            </ol>
            
            <h3>💡 Last-Minute Teaching Tips:</h3>
            <ul>
                <li>Start the meeting 5 minutes early to test everything</li>
                <li>Greet the student warmly and check their audio/video</li>
                <li>Have a backup plan ready if technology fails</li>
                <li>Keep the lesson engaging and interactive</li>
                <li>Take notes for follow-up and progress tracking</li>
            </ul>
            
            <h3>❓ Need Help?</h3>
            <p>If you're having technical issues, contact support immediately or call the student directly.</p>
            
            <a href="{{ url('/teacher/dashboard') }}" class="button">Go to Teacher Dashboard</a>
        </div>
        
        <div class="footer">
            <p>This is an automated reminder from your Online Lesson Booking System.</p>
            <p>Your lesson starts in 1 hour - make sure you're ready!</p>
        </div>
    </div>
</body>
</html>
