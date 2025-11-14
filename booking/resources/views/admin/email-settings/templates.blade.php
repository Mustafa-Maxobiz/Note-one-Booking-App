@extends('layouts.app')

@section('title', 'Email Templates')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-file-alt me-3"></i>Email Templates
            </h1>
            <p class="page-subtitle">Manage email templates for automated communications</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <button class="btn btn-outline-light me-2" onclick="createNewTemplate()">
                    <i class="fas fa-plus me-2"></i>New Template
                </button>
                <a href="{{ route('admin.email-settings.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Settings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-file-alt me-2"></i>Email Templates Management
                </h5>
                <p class="modern-card-subtitle">Create and manage email templates for different scenarios</p>
            </div>
            <div class="modern-card-body">
                @php
            // Sample data for template placeholders
            $sampleData = [
                'student_name' => 'John Doe',
                'teacher_name' => 'Jane Smith',
                'booking_date' => 'December 15, 2024',
                'booking_time' => '2:00 PM',
                'booking_date' => 'December 15, 2024',
                'booking_time' => '2:00 PM',
                'booking_duration' => '60 minutes',
                'zoom_join_url' => 'https://zoom.us/j/123456789',
                'zoom_meeting_id' => '123456789',
                'booking_details_url' => 'http://127.0.0.1:8000/student/bookings/1',
                'booking_url' => 'http://127.0.0.1:8000/student/book-new',
            ];
        @endphp
        
        @if(isset($emailTemplates) && count($emailTemplates) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Template Name</th>
                                    <th>Subject</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emailTemplates as $template)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $template->name }}</div>
                                            <small class="text-muted">{{ $template->description ?? 'No description' }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $template->subject }}</div>
                                            <small class="text-muted">{{ Str::limit($template->subject, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $template->type ?? 'General' }}</span>
                                        </td>
                                        <td>
                                            @if($template->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $template->updated_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $template->updated_at->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="editTemplate({{ $template->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        onclick="previewTemplate({{ $template->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="testTemplate({{ $template->id }})">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                                @if($template->is_active)
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            onclick="toggleTemplateStatus({{ $template->id }}, false)">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            onclick="toggleTemplateStatus({{ $template->id }}, true)">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No email templates found</h5>
                        <p class="text-muted">Create your first email template to get started.</p>
                        <button class="btn btn-primary" onclick="createNewTemplate()">
                            <i class="fas fa-plus me-2"></i>Create First Template
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Template Editor Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="templateModalTitle">Edit Email Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="templateForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="templateName" class="form-label">Template Name</label>
                                <input type="text" class="form-control" id="templateName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="templateType" class="form-label">Template Type</label>
                                <select class="form-select" id="templateType" name="type">
                                    <option value="booking_confirmation">Booking Confirmation</option>
                                    <option value="booking_reminder">Booking Reminder</option>
                                    <option value="booking_cancelled">Booking Cancelled</option>
                                    <option value="welcome_email">Welcome Email</option>
                                    <option value="password_reset">Password Reset</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="templateSubject" class="form-label">Email Subject</label>
                        <input type="text" class="form-control" id="templateSubject" name="subject" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="templateDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="templateDescription" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="templateBody" class="form-label">Email Body</label>
                        <textarea class="form-control" id="templateBody" name="body" rows="15" required></textarea>
                        <div class="form-text">
                            <strong>Available Variables:</strong><br>
                            {student_name}, {teacher_name}, {booking_date}, {booking_time}, 
                            {booking_date}, {booking_time}, {booking_duration},
                            {zoom_join_url}, {zoom_meeting_id}, {booking_details_url}, {booking_url}
                        </div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="templateActive" name="is_active" checked>
                        <label class="form-check-label" for="templateActive">
                            Active Template
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTemplate()">Save Template</button>
            </div>
        </div>
    </div>
</div>

<!-- Template Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Template Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Email Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testEmailForm">
                    <div class="mb-3">
                        <label for="testEmail" class="form-label">Test Email Address</label>
                        <input type="email" class="form-control" id="testEmail" name="test_email" required>
                        <div class="form-text">Enter an email address to send a test email.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendTestEmail()">Send Test Email</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentTemplateId = null;

function createNewTemplate() {
    currentTemplateId = null;
    document.getElementById('templateModalTitle').textContent = 'Create New Email Template';
    document.getElementById('templateForm').reset();
    
    // Set default values
    document.getElementById('templateBody').value = `Dear {student_name},

Thank you for booking a session with {teacher_name}.

Session Details:
- Date: {booking_date}
- Time: {booking_time}
- Duration: {booking_duration}

Zoom Meeting Link: {zoom_join_url}

If you have any questions, please contact us.

Best regards,
Online Lesson Booking System Team`;
    
    const modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();
}

function editTemplate(templateId) {
    currentTemplateId = templateId;
    document.getElementById('templateModalTitle').textContent = 'Edit Email Template';
    
    // Load template data via AJAX
    fetch(`{{ route('admin.email-settings.templates.data') }}?template_id=${templateId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data) {
            document.getElementById('templateName').value = data.name;
            document.getElementById('templateType').value = data.type;
            document.getElementById('templateSubject').value = data.subject;
            document.getElementById('templateDescription').value = data.description || '';
            document.getElementById('templateBody').value = data.body;
            document.getElementById('templateActive').checked = true;
        }
    })
    .catch(error => {
        console.error('Error loading template:', error);
        alert('Error loading template data');
    });
    
    const modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();
}

function previewTemplate(templateId) {
    document.getElementById('previewContent').innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
            <p>Loading template preview...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
    
    // Simulate loading preview
    setTimeout(() => {
        document.getElementById('previewContent').innerHTML = `
            <div class="border p-3">
                <h6>Subject: Booking Confirmation</h6>
                <hr>
                <div class="email-preview">
                    <p>Dear John Doe,</p>
                    <p>Thank you for booking a session with Jane Smith.</p>
                    <p><strong>Session Details:</strong></p>
                    <ul>
                        <li>Date: December 15, 2024</li>
                        <li>Time: 2:00 PM - 3:00 PM</li>
                        <li>Duration: 60 minutes</li>
                    </ul>
                    <p><strong>Zoom Meeting Link:</strong> https://zoom.us/j/123456789</p>
                    <p>If you have any questions, please contact us at admin@example.com.</p>
                    <p>Best regards,<br>Online Lesson Booking System Team</p>
                </div>
            </div>
        `;
    }, 1000);
}

function testTemplate(templateId) {
    currentTemplateId = templateId;
    document.getElementById('testEmailForm').reset();
    
    const modal = new bootstrap.Modal(document.getElementById('testEmailModal'));
    modal.show();
}

function saveTemplate() {
    const formData = new FormData(document.getElementById('templateForm'));
    const data = {
        name: formData.get('name'),
        type: formData.get('type'),
        subject: formData.get('subject'),
        description: formData.get('description'),
        body: formData.get('body'),
        is_active: formData.get('is_active') === 'on'
    };
    
    fetch('{{ route("admin.email-settings.templates.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            location.reload(); // Reload page to show updated data
        } else {
            alert('Error saving template');
        }
    })
    .catch(error => {
        console.error('Error saving template:', error);
        alert('Error saving template');
    });
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('templateModal'));
    modal.hide();
}

function sendTestEmail() {
    const testEmail = document.getElementById('testEmail').value;
    
    if (!testEmail) {
        alert('Please enter a test email address.');
        return;
    }
    
    // This would typically make an AJAX call to send test email
    alert(`Test email would be sent to ${testEmail}`);
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('testEmailModal'));
    modal.hide();
}

function toggleTemplateStatus(templateId, isActive) {
    const action = isActive ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${action} this template?`)) {
        fetch('{{ route("admin.email-settings.templates.toggle-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                template_id: templateId,
                is_active: isActive
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                location.reload(); // Reload page to show updated status
            } else {
                alert('Error updating template status');
            }
        })
        .catch(error => {
            console.error('Error updating template status:', error);
            alert('Error updating template status');
        });
    }
}
</script>
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
    
    /* Template Cards */
    .template-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .template-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }
    
    .template-card-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        padding: 1.5rem;
        color: white;
    }
    
    .template-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .template-card-body {
        padding: 1.5rem;
    }
    
    /* Button Styling */
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
    
    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        background: transparent;
    }
    
    .btn-outline-light:hover {
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
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        border: none;
        color: white;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(40, 167, 69, 0.3);
        color: white;
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
        color: #212529;
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(255, 193, 7, 0.3);
        color: #212529;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(220, 53, 69, 0.3);
        color: white;
    }
    
    /* Status Badge */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
    }
    
    .status-active {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .status-inactive {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
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
        .modern-card-body,
        .template-card-header,
        .template-card-body {
            padding: 1rem;
        }
    }
</style>
@endsection
