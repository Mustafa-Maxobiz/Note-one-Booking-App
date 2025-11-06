<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lesson Reminder - 24 Hours</title>
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
            <h1>📚 Lesson Reminder</h1>
            <p>Your lesson starts in 24 hours!</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $student->user->name }}!</h2>
            
            <p>This is a friendly reminder that your lesson with <strong>{{ $teacher->user->name }}</strong> is scheduled to start in 24 hours.</p>
            
            <div class="session-details">
                <h3>📅 Session Details:</h3>
                <p><strong>Date:</strong> {{ $booking->start_time->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</p>
                <p><strong>Subject:</strong> {{ $booking->subject }}</p>
                <p><strong>Teacher:</strong> {{ $teacher->user->name }}</p>
                <p><strong>Duration:</strong> {{ $booking->duration }} minutes</p>
            </div>
            
            <h3>🔗 Join Your Lesson:</h3>
            <p>You'll receive the Zoom meeting link 1 hour before your lesson starts. Make sure you have Zoom installed and ready.</p>
            
            <h3>📝 Preparation Tips:</h3>
            <ul>
                <li>Test your microphone and camera</li>
                <li>Find a quiet, well-lit space</li>
                <li>Have your materials ready</li>
                <li>Join the meeting 5 minutes early</li>
            </ul>
            
            <h3>❓ Need to Reschedule?</h3>
            <p>If you need to reschedule or cancel your lesson, please do so at least 24 hours in advance through your student dashboard.</p>
            
            <div class="action-buttons">
                <a href="{{ url('/student/dashboard') }}" class="btn btn-primary">📊 Go to Dashboard</a>
            </div>
        </div>
        
        <div class="footer">
            <p>This email was sent from the Online Lesson Booking System</p>
            <p>If you have any questions, please contact our support team</p>
        </div>
</body>
</html>
