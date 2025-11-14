@extends('layouts.app')

@section('title', 'Email Logs')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-envelope me-3"></i>Email Logs
            </h1>
            <p class="page-subtitle">View and manage email sending history and logs</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <button class="btn btn-outline-light me-2" onclick="clearLogs()">
                    <i class="fas fa-trash me-2"></i>Clear Logs
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
                    <i class="fas fa-history me-2"></i>Email Sending History
                </h5>
                <p class="modern-card-subtitle">Track all email sending activities and status</p>
                <div class="d-flex gap-2">
                    <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="sent">Sent</option>
                        <option value="failed">Failed</option>
                        <option value="pending">Pending</option>
                    </select>
                    <input type="date" id="dateFilter" class="form-control form-control-sm" style="width: auto;">
                </div>
            </div>
            <div class="card-body">
                @if(isset($emailLogs) && count($emailLogs) > 0)
                    <div class="modern-table-container">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Recipient</th>
                                    <th>Subject</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emailLogs as $log)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $log->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $log->created_at->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <span class="text-white fw-bold">{{ substr($log->recipient_email, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $log->recipient_name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $log->recipient_email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $log->subject }}</div>
                                            <small class="text-muted">{{ Str::limit($log->body, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $log->email_type ?? 'General' }}</span>
                                        </td>
                                        <td>
                                            @if($log->status === 'sent')
                                                <span class="badge bg-success">Sent</span>
                                            @elseif($log->status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @elseif($log->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $log->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewEmailDetails({{ $log->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($log->status === 'failed')
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            onclick="retryEmail({{ $log->id }})">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if(isset($emailLogs) && method_exists($emailLogs, 'links'))
                        <div class="pagination-container">
                            <div class="pagination-info">
                                Showing <strong>{{ $emailLogs->firstItem() }}</strong> to <strong>{{ $emailLogs->lastItem() }}</strong> of <strong>{{ $emailLogs->total() }}</strong> logs
                            </div>
                            {{ $emailLogs->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No email logs found</h5>
                        <p class="text-muted">Email sending history will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Email Details Modal -->
<div class="modal fade" id="emailDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="emailDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function viewEmailDetails(logId) {
    // This would typically make an AJAX call to get email details
    // For now, we'll show a placeholder
    document.getElementById('emailDetailsContent').innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
            <p>Loading email details...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('emailDetailsModal'));
    modal.show();
    
    // Simulate loading email details
    setTimeout(() => {
        document.getElementById('emailDetailsContent').innerHTML = `
            <div class="mb-3">
                <h6>Email Information</h6>
                <p><strong>Recipient:</strong> <span id="recipientEmail"></span></p>
                <p><strong>Subject:</strong> <span id="emailSubject"></span></p>
                <p><strong>Sent:</strong> <span id="emailDate"></span></p>
                <p><strong>Status:</strong> <span id="emailStatus"></span></p>
            </div>
            <div class="mb-3">
                <h6>Email Content</h6>
                <div class="border p-3 bg-light" id="emailBody">
                    <!-- Email body content -->
                </div>
            </div>
        `;
    }, 1000);
}

function retryEmail(logId) {
    if (confirm('Are you sure you want to retry sending this email?')) {
        // This would typically make an AJAX call to retry the email
        alert('Email retry functionality would be implemented here.');
    }
}

function clearLogs() {
    if (confirm('Are you sure you want to clear all email logs? This action cannot be undone.')) {
        // This would typically make an AJAX call to clear logs
        alert('Clear logs functionality would be implemented here.');
    }
}

// Filter functionality
document.getElementById('statusFilter').addEventListener('change', function() {
    // Implement status filtering
    console.log('Filter by status:', this.value);
});

document.getElementById('dateFilter').addEventListener('change', function() {
    // Implement date filtering
    console.log('Filter by date:', this.value);
});
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
    
    /* Table Styling */
    .table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .table thead th {
        border: none;
        font-weight: 600;
        color: #2c3e50;
        padding: 1rem;
    }
    
    .table tbody td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
    }
    
    .table tbody tr {
        border-bottom: 1px solid #f8f9fa;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    /* Status Badges */
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .badge-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .badge-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
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
    
    /* Form Styling */
    .form-select, .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
        outline: none;
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
