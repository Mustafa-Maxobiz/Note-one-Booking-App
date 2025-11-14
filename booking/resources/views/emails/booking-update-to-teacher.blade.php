<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Schedule Updated</title>
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
        .change-highlight {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        .original-details {
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #dc3545;
        }
        .new-details {
            background: #d4edda;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        .student-info {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
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
            background-color: #007bff;
            color: #FFFFFF !important;
        }
        .btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
        .info-box {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
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
        <h1>📅 Session Schedule Updated</h1>
        <p>Important changes have been made to your teaching session</p>
    </div>

    <div class="content">
        <h2>Hello {{ $teacher_name }},</h2>
        
        <p>We're writing to inform you that your session with <strong>{{ $student_name }}</strong> has been updated by the administrator. Please review the changes below:</p>

        <div class="change-highlight">
            <h3>⚠️ Schedule Changes</h3>
            <p><strong>Your session details have been modified by the administrator.</strong></p>
        </div>

        <div class="original-details">
            <h3>📅 Original Session Details</h3>
            <p><strong>Date:</strong> {{ $original_date }}</p>
            <p><strong>Time:</strong> {{ $original_time }}</p>
            <p><strong>Duration:</strong> {{ $duration }}</p>
        </div>

        <div class="new-details">
            <h3>✅ Updated Session Details</h3>
            <p><strong>Date:</strong> {{ $new_date }}</p>
            <p><strong>Time:</strong> {{ $new_time }}</p>
            <p><strong>Duration:</strong> {{ $duration }}</p>
            @if($notes && $notes !== 'No additional notes')
                <p><strong>Notes:</strong> {{ $notes }}</p>
            @endif
        </div>

        <div class="student-info">
            <h3>👤 Student Information</h3>
            <p><strong>Name:</strong> {{ $student_name }}</p>
            <p><strong>Level:</strong> {{ $booking->student->level ?? 'Not specified' }}</p>
        </div>

        <div class="highlight">
            <p><strong>📝 Important Notes:</strong></p>
            <ul>
                <li>Please update your calendar with the new session time</li>
                <li>Make sure you're available for the new scheduled time</li>
                <li>Prepare your teaching materials for the session</li>
                <li>If the new time doesn't work for you, contact the administrator immediately</li>
            </ul>
        </div>

        <div class="info-box">
            <p><strong>💡 What to do next:</strong></p>
            <ul>
                <li>Mark the new date and time in your calendar</li>
                <li>Set a reminder for the session</li>
                <li>Prepare your lesson plan and materials</li>
                <li>Ensure your teaching environment is ready</li>
                <li>Contact the student if you need to discuss the session</li>
            </ul>
        </div>

        <p>We apologize for any inconvenience this change may cause. If you have any concerns about the new schedule, please don't hesitate to contact the administrator.</p>

        <p>Thank you for using our Online Lesson Booking System!</p>
    </div>

    <div class="footer">
        <p>This email was sent from the Online Lesson Booking System</p>
        <p>If you have any questions, please contact our support team</p>
    </div>
</body>
</html>
