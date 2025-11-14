<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lesson Reminder - 1 Hour</title>
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
        .urgent {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
            color: #721c24;
            font-weight: bold;
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
            <h1>🚨 URGENT: Lesson Starts in 1 Hour!</h1>
            <p>Time to join your Zoom lesson!</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $student->user->name }}!</h2>
            
            <p class="urgent">Your lesson with <strong>{{ $teacher->user->name }}</strong> starts in just 1 hour!</p>
            
            <div class="session-details">
                <h3>📅 Session Details:</h3>
                <p><strong>Date:</strong> {{ $booking->start_time->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</p>
                <p><strong>Subject:</strong> {{ $booking->subject }}</p>
                <p><strong>Teacher:</strong> {{ $teacher->user->name }}</p>
                <p><strong>Duration:</strong> {{ $booking->duration }} minutes</p>
            </div>
            
            <div class="zoom-section">
                <h3>🔗 Join Your Zoom Lesson Now!</h3>
                @if($booking->zoom_meeting_id)
                    <p><strong>Meeting ID:</strong> {{ $booking->zoom_meeting_id }}</p>
                    <p><strong>Password:</strong> Will be provided by your teacher</p>
                    <a href="https://zoom.us/j/{{ $booking->zoom_meeting_id }}" class="btn btn-success" target="_blank">🎥 Join Zoom Meeting</a>
                @else
                    <p><strong>Zoom meeting details will be provided by your teacher.</strong></p>
                @endif
            </div>
            
            <h3>⚡ Quick Checklist:</h3>
            <ul>
                <li>✅ Zoom app is installed and updated</li>
                <li>✅ Microphone and camera are working</li>
                <li>✅ You're in a quiet, well-lit space</li>
                <li>✅ Your materials are ready</li>
                <li>✅ Join 5 minutes early</li>
            </ul>
            
            <h3>📱 Alternative Join Methods:</h3>
            <p>If the button above doesn't work, you can:</p>
            <ol>
                <li>Open Zoom app and click "Join"</li>
                <li>Enter Meeting ID: <strong>{{ $booking->zoom_meeting_id ?? 'TBD' }}</strong></li>
                <li>Enter your name and join</li>
            </ol>
            
            <h3>❓ Need Help?</h3>
            <p>If you're having technical issues, contact support immediately or call your teacher.</p>
            
            <div class="action-buttons">
                <a href="{{ url('/student/dashboard') }}" class="btn btn-primary">📊 Go to Dashboard</a>
            </div>
        </div>
        
        <div class="footer">
            <p>This email was sent from the Online Lesson Booking System</p>
            <p>Your lesson starts in 1 hour - don't miss it!</p>
        </div>
</body>
</html>
