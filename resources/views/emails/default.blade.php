<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Lesson Booking System</title>
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
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Online Lesson Booking System</h1>
    </div>

    <div class="content">
        @if(isset($message))
            <p>{{ $message }}</p>
        @endif
        
        @if(isset($data) && is_array($data))
            @foreach($data as $key => $value)
                @if(is_string($value))
                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                @endif
            @endforeach
        @endif
    </div>

    <div class="footer">
        <p>This is an automated message from the Online Lesson Booking System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>
