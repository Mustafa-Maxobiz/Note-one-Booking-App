@extends('layouts.app')

@section('title', 'Email Settings')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-envelope me-3"></i>Email Settings
            </h1>
            <p class="page-subtitle">Configure email server settings and templates</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <button type="submit" form="emailSettingsForm" class="btn btn-light">
                    <i class="fas fa-save me-2"></i>Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="fas fa-cog me-2"></i>Email Configuration
                    </h5>
                    <p class="modern-card-subtitle">Manage your email server settings and preferences</p>
                </div>
                <div class="modern-card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.email-settings.update') }}" method="POST" id="emailSettingsForm">
                        @csrf
                        
                        <!-- SMTP Configuration -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="modern-card">
                                    <div class="modern-card-header">
                                        <h5 class="modern-card-title">
                                            <i class="fas fa-server me-2"></i>SMTP Configuration
                                        </h5>
                                        <p class="modern-card-subtitle">Configure your email server settings</p>
                                    </div>
                                    <div class="modern-card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mail_mailer" class="form-label">Mail Driver</label>
                                                    <select class="form-select" id="mail_mailer" name="mail_mailer" required>
                                                        <option value="smtp" {{ $settings['mail_mailer'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                        <option value="mailgun" {{ $settings['mail_mailer'] == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                                        <option value="ses" {{ $settings['mail_mailer'] == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                                        <option value="postmark" {{ $settings['mail_mailer'] == 'postmark' ? 'selected' : '' }}>Postmark</option>
                                                        <option value="log" {{ $settings['mail_mailer'] == 'log' ? 'selected' : '' }}>Log (Development)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mail_host" class="form-label">SMTP Host</label>
                                                    <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                                           value="{{ $settings['mail_host'] }}" placeholder="smtp.gmail.com">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mail_port" class="form-label">SMTP Port</label>
                                                    <input type="number" class="form-control" id="mail_port" name="mail_port" 
                                                           value="{{ $settings['mail_port'] }}" placeholder="587">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mail_username" class="form-label">SMTP Username</label>
                                                    <input type="text" class="form-control" id="mail_username" name="mail_username" 
                                                           value="{{ $settings['mail_username'] }}" placeholder="your-email@gmail.com">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mail_password" class="form-label">SMTP Password</label>
                                                    <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                                           placeholder="Enter password to update">
                                                    <small class="form-text text-muted">Leave blank to keep current password</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mail_encryption" class="form-label">Encryption</label>
                                                    <select class="form-select" id="mail_encryption" name="mail_encryption">
                                                        <option value="tls" {{ $settings['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                                                        <option value="ssl" {{ $settings['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- From Address Configuration -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="modern-card">
                                    <div class="modern-card-header">
                                        <h5 class="modern-card-title">
                                            <i class="fas fa-at me-2"></i>From Address Configuration
                                        </h5>
                                        <p class="modern-card-subtitle">Configure sender information for outgoing emails</p>
                                    </div>
                                    <div class="modern-card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mail_from_address" class="form-label">From Email Address</label>
                                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                                           value="{{ $settings['mail_from_address'] }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mail_from_name" class="form-label">From Name</label>
                                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                                           value="{{ $settings['mail_from_name'] }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Preferences -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="modern-card">
                                    <div class="modern-card-header">
                                        <h5 class="modern-card-title">
                                            <i class="fas fa-cog me-2"></i>Email Preferences
                                        </h5>
                                        <p class="modern-card-subtitle">Configure email notification preferences</p>
                                    </div>
                                    <div class="modern-card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="enable_booking_emails" 
                                                           name="enable_booking_emails" value="1" 
                                                           {{ $settings['enable_booking_emails'] ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="enable_booking_emails">
                                                        Enable Booking Emails
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="enable_reminder_emails" 
                                                           name="enable_reminder_emails" value="1" 
                                                           {{ $settings['enable_reminder_emails'] ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="enable_reminder_emails">
                                                        Enable Reminder Emails
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="enable_notification_emails" 
                                                           name="enable_notification_emails" value="1" 
                                                           {{ $settings['enable_notification_emails'] ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="enable_notification_emails">
                                                        Enable Notification Emails
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="reminder_hours_before" class="form-label">Reminder Hours Before Session</label>
                                                    <input type="number" class="form-control" id="reminder_hours_before" 
                                                           name="reminder_hours_before" value="{{ $settings['reminder_hours_before'] }}" 
                                                           min="1" max="168">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Template Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="modern-card">
                                    <div class="modern-card-header">
                                        <h5 class="modern-card-title">
                                            <i class="fas fa-file-alt me-2"></i>Email Template Settings
                                        </h5>
                                        <p class="modern-card-subtitle">Customize email templates and branding</p>
                                    </div>
                                    <div class="modern-card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email_template_header" class="form-label">Email Header</label>
                                                    <textarea class="form-control" id="email_template_header" name="email_template_header" 
                                                              rows="3" placeholder="Enter email header text">{{ $settings['email_template_header'] }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email_template_footer" class="form-label">Email Footer</label>
                                                    <textarea class="form-control" id="email_template_footer" name="email_template_footer" 
                                                              rows="3" placeholder="Enter email footer text">{{ $settings['email_template_footer'] }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('admin.email-settings.templates') }}" class="btn btn-info">
                                            <i class="fas fa-edit me-2"></i>
                                            Manage Email Templates
                                        </a>
                                        <a href="{{ route('admin.email-settings.logs') }}" class="btn btn-secondary">
                                            <i class="fas fa-list me-2"></i>
                                            View Email Logs
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Save Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Test Email Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="modern-card">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-paper-plane me-2"></i>Test Email Configuration
                                    </h5>
                                    <p class="modern-card-subtitle">Test your email configuration to ensure it's working properly</p>
                                </div>
                                <div class="modern-card-body">
                                    <form action="{{ route('admin.email-settings.test') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="test_email" class="form-label">Test Email Address</label>
                                                    <input type="email" class="form-control" id="test_email" name="test_email" 
                                                           placeholder="Enter email address to send test email" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="submit" class="btn btn-dark w-100">
                                                        <i class="fas fa-paper-plane me-2"></i>
                                                        Send Test Email
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="modern-card">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-tools me-2"></i>Quick Actions
                                    </h5>
                                    <p class="modern-card-subtitle">Quick access to email management tools</p>
                                </div>
                                <div class="modern-card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <form action="{{ route('admin.email-settings.clear-cache') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-secondary" 
                                                        onclick="return confirm('Are you sure you want to clear email cache?')">
                                                    <i class="fas fa-broom me-2"></i>
                                                    Clear Email Cache
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="{{ route('admin.email-settings.templates') }}" class="btn btn-outline-info">
                                                <i class="fas fa-edit me-2"></i>
                                                Edit Email Templates
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="{{ route('admin.email-settings.logs') }}" class="btn btn-outline-dark">
                                                <i class="fas fa-history me-2"></i>
                                                View Email History
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0.5rem 0 0 0;
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    /* Modern Card */
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .modern-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .modern-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .modern-card-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0.25rem 0 0 0;
    }
    
    .modern-card-body {
        padding: 1.5rem;
    }
    
    /* Form Styling */
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        display: block;
        font-size: 0.95rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
        outline: none;
    }
    
    .btn {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-light {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .btn-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);
        border: none;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(239, 71, 62, 0.3);
        color: white;
    }
    
    /* Alert Styling */
    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .header-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
    
    @media (max-width: 576px) {
        .page-header {
            padding: 1.5rem;
        }
        
        .modern-card-header,
        .modern-card-body {
            padding: 1rem;
        }
    }
</style>
@endsection

@push('scripts')
<script>
    // Show/hide SMTP fields based on mailer selection
    document.getElementById('mail_mailer').addEventListener('change', function() {
        const smtpFields = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption'];
        const isSmtp = this.value === 'smtp';
        
        smtpFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const parent = field.closest('.col-md-6');
            if (parent) {
                parent.style.display = isSmtp ? 'block' : 'none';
            }
        });
    });

    // Trigger change event on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('mail_mailer').dispatchEvent(new Event('change'));
    });
</script>
@endpush
