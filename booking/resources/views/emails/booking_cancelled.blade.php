<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancelled</title>
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
            border-left: 4px solid #ef473e;
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
        .cancelled-icon {
            color: #dc3545;
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }
        .reason-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .refund-info {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Online Lesson Booking System</div>
            <h1 class="title">Booking Cancelled</h1>
        </div>

        <div class="cancelled-icon">❌</div>

        <div class="content">
            <p>Dear <strong>{{ $data['student_name'] }}</strong>,</p>
            
            <p>We're writing to inform you that your lesson booking has been cancelled.</p>

            <div class="booking-details">
                <h3 style="color: #070c39; margin-top: 0;">📅 Cancelled Lesson Details</h3>
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
                <div class="detail-row">
                    <span class="detail-label">Cancelled On:</span>
                    <span class="detail-value">{{ $data['cancelled_date'] ?? now()->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>

            @if(isset($data['cancellation_reason']) && $data['cancellation_reason'])
            <div class="reason-box">
                <h4 style="color: #721c24; margin-top: 0;">📝 Cancellation Reason:</h4>
                <p style="margin: 0;">{{ $data['cancellation_reason'] }}</p>
            </div>
            @endif

            @if(isset($data['refund_amount']) && $data['refund_amount'] > 0)
            <div class="refund-info">
                <h4 style="color: #155724; margin-top: 0;">💰 Refund Information:</h4>
                <p style="margin: 0;">
                    <strong>Refund Amount:</strong> ${{ number_format($data['refund_amount'], 2) }}<br>
                    <strong>Refund Method:</strong> {{ $data['refund_method'] ?? 'Original payment method' }}<br>
                    <strong>Processing Time:</strong> {{ $data['refund_timeframe'] ?? '3-5 business days' }}
                </p>
            </div>
            @endif

            <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #1976d2; margin-top: 0;">🔄 What's Next?</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>You can book a new lesson with any available teacher</li>
                    <li>Check our available time slots for rescheduling</li>
                    <li>Contact us if you have any questions or concerns</li>
                    <li>We're here to help you continue your learning journey</li>
                </ul>
            </div>

            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #856404; margin-top: 0;">💡 Alternative Options:</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Browse other available teachers for the same subject</li>
                    <li>Consider different time slots that work better for you</li>
                    <li>Try our group lesson options if available</li>
                    <li>Explore our self-paced learning materials</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $data['dashboard_url'] }}" class="button">View Dashboard</a>
                <a href="{{ $data['book_new_url'] ?? '#' }}" class="button">Book New Lesson</a>
            </div>

            <div style="text-align: center; margin: 20px 0;">
                <p style="color: #6c757d; font-style: italic;">
                    We apologize for any inconvenience this may cause. 
                    We're committed to providing you with the best learning experience.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>If you have any questions about this cancellation, please don't hesitate to contact us.</p>
            <p><strong>Online Lesson Booking System</strong><br>
            Email: {{ $data['contact_email'] ?? 'support@example.com' }}<br>
            Phone: {{ $data['contact_phone'] ?? '+1 (555) 123-4567' }}</p>
        </div>
    </div>
</body>
</html>
