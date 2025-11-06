<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class EmailSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'mail_mailer' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
            'enable_booking_emails' => Cache::get('enable_booking_emails', true),
            'enable_reminder_emails' => Cache::get('enable_reminder_emails', true),
            'enable_notification_emails' => Cache::get('enable_notification_emails', true),
            'reminder_hours_before' => Cache::get('reminder_hours_before', 24),
            'email_template_header' => Cache::get('email_template_header', 'Online Lesson Booking System'),
            'email_template_footer' => Cache::get('email_template_footer', 'Thank you for using our service.'),
        ];

        return view('admin.email-settings.index', compact('settings'));
    }

    public function templates()
    {
        // In a real application, you would fetch from database
        // For now, we'll create sample template objects
        $emailTemplates = collect([
            (object) [
                'id' => 1,
                'name' => 'Booking Confirmation',
                'description' => 'Email sent when a booking is confirmed',
                'subject' => 'Session Booking Confirmed',
                'type' => 'booking_confirmation',
                'is_active' => true,
                'updated_at' => now(),
            ],
            (object) [
                'id' => 2,
                'name' => 'Booking Reminder',
                'description' => 'Email sent as a reminder before the session',
                'subject' => 'Reminder: Your Session Tomorrow',
                'type' => 'booking_reminder',
                'is_active' => true,
                'updated_at' => now()->subDays(1),
            ],
            (object) [
                'id' => 3,
                'name' => 'Booking Cancelled',
                'description' => 'Email sent when a booking is cancelled',
                'subject' => 'Session Booking Cancelled',
                'type' => 'booking_cancelled',
                'is_active' => true,
                'updated_at' => now()->subDays(2),
            ],
            (object) [
                'id' => 4,
                'name' => 'Welcome Email',
                'description' => 'Email sent to new users',
                'subject' => 'Welcome to Online Lesson Booking System',
                'type' => 'welcome_email',
                'is_active' => true,
                'updated_at' => now(),
            ],
        ]);

        return view('admin.email-settings.templates', compact('emailTemplates'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|in:smtp,mailgun,ses,postmark,log,array',
            'mail_host' => 'required_if:mail_mailer,smtp|nullable|string',
            'mail_port' => 'required_if:mail_mailer,smtp|nullable|integer',
            'mail_username' => 'required_if:mail_mailer,smtp|nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required_if:mail_mailer,smtp|nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
            'enable_booking_emails' => 'boolean',
            'enable_reminder_emails' => 'boolean',
            'enable_notification_emails' => 'boolean',
            'reminder_hours_before' => 'integer|min:1|max:168',
            'email_template_header' => 'nullable|string|max:500',
            'email_template_footer' => 'nullable|string|max:500',
        ]);

        try {
            // Update mail configuration
            $this->updateMailConfig($validated);

            // Update email settings
            Cache::put('enable_booking_emails', $validated['enable_booking_emails'] ?? false, 86400);
            Cache::put('enable_reminder_emails', $validated['enable_reminder_emails'] ?? false, 86400);
            Cache::put('enable_notification_emails', $validated['enable_notification_emails'] ?? false, 86400);
            Cache::put('reminder_hours_before', $validated['reminder_hours_before'] ?? 24, 86400);
            Cache::put('email_template_header', $validated['email_template_header'] ?? '', 86400);
            Cache::put('email_template_footer', $validated['email_template_footer'] ?? '', 86400);

            return redirect()->route('admin.email-settings.index')
                ->with('success', 'Email settings updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    private function updateMailConfig($settings)
    {
        $configPath = config_path('mail.php');
        
        if (!File::exists($configPath)) {
            throw new \Exception('Mail configuration file not found.');
        }

        $config = require $configPath;

        // Update mail configuration
        $config['default'] = $settings['mail_mailer'];
        $config['from']['address'] = $settings['mail_from_address'];
        $config['from']['name'] = $settings['mail_from_name'];

        if ($settings['mail_mailer'] === 'smtp') {
            $config['mailers']['smtp']['host'] = $settings['mail_host'];
            $config['mailers']['smtp']['port'] = $settings['mail_port'];
            $config['mailers']['smtp']['username'] = $settings['mail_username'];
            $config['mailers']['smtp']['encryption'] = $settings['mail_encryption'];

            // Only update password if provided
            if (!empty($settings['mail_password'])) {
                $config['mailers']['smtp']['password'] = $settings['mail_password'];
            }
        }

        // Write configuration back to file
        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        File::put($configPath, $configContent);

        // Clear config cache
        Cache::forget('config.mail');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $testEmail = $request->get('test_email');
            
            // Send test email
            \Mail::raw('This is a test email from Online Lesson Booking System. If you receive this, your email configuration is working correctly.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email - Online Lesson Booking System');
            });

            return back()->with('success', 'Test email sent successfully to ' . $testEmail);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }



    public function updateEmailTemplate(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $templateName = $request->get('template_name');
        $subject = $request->get('subject');
        $body = $request->get('body');

        // Store template in cache (in production, you might want to store in database)
        Cache::put("email_template_{$templateName}_subject", $subject, 86400);
        Cache::put("email_template_{$templateName}_body", $body, 86400);

        return back()->with('success', 'Email template updated successfully!');
    }

    public function getTemplateData(Request $request)
    {
        $templateId = $request->get('template_id');
        
        // In a real application, you would fetch from database
        // For now, return sample data based on template ID
        $templates = [
            1 => [
                'name' => 'Booking Confirmation',
                'description' => 'Email sent when a booking is confirmed',
                'subject' => 'Session Booking Confirmed',
                'type' => 'booking_confirmation',
                'body' => "Dear {student_name},\n\nThank you for booking a session with {teacher_name}.\n\nSession Details:\n- Date: {session_date}\n- Time: {session_time}\n- Duration: {session_duration}\n\nZoom Meeting Link: {zoom_join_url}\n\nIf you have any questions, please contact us.\n\nBest regards,\nOnline Lesson Booking System Team"
            ],
            2 => [
                'name' => 'Booking Reminder',
                'description' => 'Email sent as a reminder before the session',
                'subject' => 'Reminder: Your Session Tomorrow',
                'type' => 'booking_reminder',
                'body' => "Dear {student_name},\n\nThis is a reminder that you have a session with {teacher_name} tomorrow.\n\nSession Details:\n- Date: {session_date}\n- Time: {session_time}\n- Zoom URL: {zoom_join_url}\n\nPlease ensure you're ready for your session.\n\nBest regards,\nOnline Lesson Booking System Team"
            ],
            3 => [
                'name' => 'Booking Cancelled',
                'description' => 'Email sent when a booking is cancelled',
                'subject' => 'Session Booking Cancelled',
                'type' => 'booking_cancelled',
                'body' => "Dear {student_name},\n\nUnfortunately, your session booking with {teacher_name} has been cancelled.\n\nSession Details:\n- Date: {session_date}\n- Time: {session_time}\n\nPlease feel free to book another session with a different teacher.\n\nBest regards,\nOnline Lesson Booking System Team"
            ],
            4 => [
                'name' => 'Welcome Email',
                'description' => 'Email sent to new users',
                'subject' => 'Welcome to Online Lesson Booking System',
                'type' => 'welcome_email',
                'body' => "Dear {student_name},\n\nWelcome to Online Lesson Booking System!\n\nWe're excited to have you join our platform. You can now book sessions with our qualified teachers.\n\nTo get started, visit: {booking_url}\n\nIf you have any questions, please contact us.\n\nBest regards,\nOnline Lesson Booking System Team"
            ]
        ];

        return response()->json($templates[$templateId] ?? null);
    }

    public function saveTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // In a real application, you would save to database
        // For now, we'll just return success
        return response()->json([
            'success' => true,
            'message' => 'Template saved successfully!'
        ]);
    }

    public function toggleTemplateStatus(Request $request)
    {
        $request->validate([
            'template_id' => 'required|integer',
            'is_active' => 'required|boolean'
        ]);

        // In a real application, you would update database
        // For now, we'll just return success
        $status = $request->get('is_active') ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Template {$status} successfully!"
        ]);
    }

    private function getEmailTemplate($templateName)
    {
        $defaultTemplates = [
            'booking_request' => "Dear {teacher_name},\n\nYou have received a new session booking request from {student_name}.\n\nSession Details:\n- Date: {session_date}\n- Time: {session_time}\n- Duration: {session_duration}\n\nPlease review and respond to this request.\n\nBest regards,\nOnline Lesson Booking System",
            
            'booking_accepted' => "Dear {student_name},\n\nGreat news! Your session booking with {teacher_name} has been accepted.\n\nSession Details:\n- Date: {session_date}\n- Time: {session_time}\n- Duration: {session_duration}\n- Zoom URL: {zoom_join_url}\n\nWe look forward to your session!\n\nBest regards,\nOnline Lesson Booking System",
            
            'booking_declined' => "Dear {student_name},\n\nUnfortunately, your session booking with {teacher_name} has been declined.\n\nSession Details:\n- Date: {session_date}\n- Time: {session_time}\n\nPlease feel free to book another session with a different teacher.\n\nBest regards,\nOnline Lesson Booking System",
            
            'session_reminder' => "Dear {student_name},\n\nThis is a reminder that you have a session with {teacher_name} tomorrow.\n\nSession Details:\n- Date: {session_date}\n- Time: {session_time}\n- Zoom URL: {zoom_join_url}\n\nPlease ensure you're ready for your session.\n\nBest regards,\nOnline Lesson Booking System",
            
            'zoom_details' => "Dear {student_name},\n\nHere are the Zoom meeting details for your upcoming session with {teacher_name}.\n\nSession Details:\n- Date: {session_date}\n- Time: {session_time}\n- Zoom Meeting ID: {zoom_meeting_id}\n- Zoom Join URL: {zoom_join_url}\n\nPlease join the meeting 5 minutes before the scheduled time.\n\nBest regards,\nOnline Lesson Booking System",
        ];

        return Cache::get("email_template_{$templateName}_body", $defaultTemplates[$templateName] ?? '');
    }

    public function emailLogs()
    {
        // In a real application, you would fetch email logs from the database
        // For now, we'll create sample log objects
        $emailLogs = collect([
            (object) [
                'id' => 1,
                'recipient_email' => 'student@example.com',
                'recipient_name' => 'John Doe',
                'subject' => 'Session Booking Confirmed',
                'body' => 'Your session with Jane Smith has been confirmed...',
                'email_type' => 'booking_confirmation',
                'status' => 'sent',
                'created_at' => now(),
            ],
            (object) [
                'id' => 2,
                'recipient_email' => 'teacher@example.com',
                'recipient_name' => 'Jane Smith',
                'subject' => 'New Booking Request',
                'body' => 'You have received a new booking request...',
                'email_type' => 'booking_request',
                'status' => 'sent',
                'created_at' => now()->subHours(2),
            ],
            (object) [
                'id' => 3,
                'recipient_email' => 'student2@example.com',
                'recipient_name' => 'Alice Johnson',
                'subject' => 'Session Reminder',
                'body' => 'Reminder: Your session is tomorrow...',
                'email_type' => 'booking_reminder',
                'status' => 'failed',
                'created_at' => now()->subHours(4),
            ],
            (object) [
                'id' => 4,
                'recipient_email' => 'student3@example.com',
                'recipient_name' => 'Bob Wilson',
                'subject' => 'Welcome to Our Platform',
                'body' => 'Welcome to Online Lesson Booking System...',
                'email_type' => 'welcome_email',
                'status' => 'pending',
                'created_at' => now()->subHours(6),
            ],
        ]);

        return view('admin.email-settings.logs', compact('emailLogs'));
    }

    public function clearEmailCache()
    {
        Cache::forget('enable_booking_emails');
        Cache::forget('enable_reminder_emails');
        Cache::forget('enable_notification_emails');
        Cache::forget('reminder_hours_before');
        Cache::forget('email_template_header');
        Cache::forget('email_template_footer');

        return back()->with('success', 'Email cache cleared successfully!');
    }
}
