<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Email</title>
</head>
<body>
    <h1>Test Email</h1>
    <p>This is a test email from the Online Lesson Booking System.</p>
    <p>Message: {{ $message ?? 'No message provided' }}</p>
    <p>Time: {{ now() }}</p>
</body>
</html>
