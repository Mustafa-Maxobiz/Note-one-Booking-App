<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Session Booking Request</title>
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
        .btn-accept {
            background-color: #28a745;
            color: #FFFFFF !important;
        }
        .btn-decline {
            background-color: #dc3545;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 New Session Booking Request</h1>
        <p>You have received a new booking request from a student</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $teacher_name }}</strong>,</p>

        <p>A student has requested to book a session with you. Please review the details below and take action.</p>

        <div class="booking-details">
            <h3>📅 Session Details</h3>
            <p><strong>Date:</strong> {{ $booking_date }}</p>
            <p><strong>Time:</strong> {{ $booking_time }}</p>
            <p><strong>Duration:</strong> {{ $booking_duration }}</p>
            @if($booking_notes && $booking_notes !== 'No additional notes')
                <p><strong>Student Notes:</strong> {{ $booking_notes }}</p>
            @endif
        </div>

        <div class="student-info">
            <h3>👤 Student Information</h3>
            <p><strong>Name:</strong> {{ $student_name }}</p>
            <p><strong>Email:</strong> {{ $student_email }}</p>
            @if($student_phone && $student_phone !== 'Not provided')
                <p><strong>Phone:</strong> {{ $student_phone }}</p>
            @endif
        </div>

        <div class="highlight">
            <p><strong>⚠️ Important:</strong> Please respond to this booking request within 24 hours. If you don't respond, the booking will remain pending.</p>
        </div>

        <div class="action-buttons">
            <a href="{{ $accept_url }}" class="btn btn-primary">📋 View Booking Details</a>
        </div>

        <p><strong>What happens next?</strong></p>
        <ul>
            <li>Click the button above to view the full booking details</li>
            <li>On the booking details page, you can <strong>Accept</strong> or <strong>Decline</strong> the booking</li>
            <li>If you <strong>accept</strong>: The student will be notified and a Zoom meeting will be created automatically</li>
            <li>If you <strong>decline</strong>: The student will be notified and the booking will be cancelled</li>
            <li>You can also manage all your bookings from your teacher dashboard</li>
        </ul>

        <p>Thank you for using our Online Lesson Booking System!</p>
    </div>

    <div class="footer">
        <p>This email was sent from the Online Lesson Booking System</p>
        <p>If you have any questions, please contact our support team</p>
    </div>
</body>
</html>
