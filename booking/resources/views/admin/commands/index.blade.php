@extends('layouts.app')

@section('title', 'System Commands & Maintenance')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-terminal me-3"></i>System Commands & Maintenance
            </h1>
            <p class="page-subtitle">Execute system commands and perform maintenance tasks</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('admin.settings.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Settings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- System Commands & Maintenance -->
    <div class="modern-card">
        <div class="modern-card-header">
            <h5 class="modern-card-title">
                <i class="fas fa-terminal me-2"></i>System Commands & Maintenance
            </h5>
            <p class="modern-card-subtitle">Execute system commands and perform maintenance tasks</p>
        </div>
        <div class="modern-card-body">
            <!-- Database Operations -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-database me-2"></i>Database Operations
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('migrate')">
                            <i class="fas fa-sync me-2"></i>Run Migrations
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('seed')">
                            <i class="fas fa-seedling me-2"></i>Run Seeders
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('fresh')">
                            <i class="fas fa-trash me-2"></i>Fresh Migration
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('backup')">
                            <i class="fas fa-download me-2"></i>Backup Database
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-broom me-2"></i>System Cleanup
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('clear-cache')">
                            <i class="fas fa-trash me-2"></i>Clear Cache
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('clear-config')">
                            <i class="fas fa-cog me-2"></i>Clear Config
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('clear-views')">
                            <i class="fas fa-eye me-2"></i>Clear Views
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('clear-routes')">
                            <i class="fas fa-route me-2"></i>Clear Routes
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Maintenance -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-tools me-2"></i>System Maintenance
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('optimize')">
                            <i class="fas fa-tachometer-alt me-2"></i>Optimize Application
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('config-cache')">
                            <i class="fas fa-cog me-2"></i>Cache Config
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('route-cache')">
                            <i class="fas fa-route me-2"></i>Cache Routes
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('view-cache')">
                            <i class="fas fa-eye me-2"></i>Cache Views
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-shield-alt me-2"></i>Security & Permissions
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('key-generate')">
                            <i class="fas fa-key me-2"></i>Generate App Key
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('storage-link')">
                            <i class="fas fa-link me-2"></i>Create Storage Link
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('permissions')">
                            <i class="fas fa-lock me-2"></i>Fix Permissions
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('composer-install')">
                            <i class="fas fa-download me-2"></i>Composer Install
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notifications & Real-time -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-bell me-2"></i>Notifications & Real-time
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('notification-test')">
                            <i class="fas fa-bell me-2"></i>Test Notifications
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('pusher-test')">
                            <i class="fas fa-broadcast-tower me-2"></i>Test Pusher
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('websocket-test')">
                            <i class="fas fa-wifi me-2"></i>Test WebSocket
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('notification-clear')">
                            <i class="fas fa-trash me-2"></i>Clear Notifications
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-clock me-2"></i>Cron Jobs & Scheduling
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('cron-list')">
                            <i class="fas fa-list me-2"></i>List Cron Jobs
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('cron-test')">
                            <i class="fas fa-play me-2"></i>Test Cron Jobs
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('schedule-list')">
                            <i class="fas fa-calendar me-2"></i>List Schedules
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('cron-install')">
                            <i class="fas fa-download me-2"></i>Install Cron
                        </button>
                    </div>
                </div>
            </div>

            <!-- Email & Communication -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-envelope me-2"></i>Email & Communication
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('mail-test')">
                            <i class="fas fa-envelope me-2"></i>Test Email
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('mail-queue')">
                            <i class="fas fa-inbox me-2"></i>Process Mail Queue
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('mail-failed')">
                            <i class="fas fa-exclamation-triangle me-2"></i>Retry Failed Mails
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('mail-clear')">
                            <i class="fas fa-trash me-2"></i>Clear Mail Queue
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-database me-2"></i>Database & Storage
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('db-status')">
                            <i class="fas fa-info-circle me-2"></i>Database Status
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('db-optimize')">
                            <i class="fas fa-tachometer-alt me-2"></i>Optimize Database
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('storage-info')">
                            <i class="fas fa-hdd me-2"></i>Storage Info
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('storage-cleanup')">
                            <i class="fas fa-broom me-2"></i>Clean Storage
                        </button>
                    </div>
                </div>
            </div>

            <!-- Security & Logs -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-shield-alt me-2"></i>Security & Logs
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('log-clear')">
                            <i class="fas fa-file-alt me-2"></i>Clear Logs
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('log-rotate')">
                            <i class="fas fa-sync me-2"></i>Rotate Logs
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('security-scan')">
                            <i class="fas fa-shield-alt me-2"></i>Security Scan
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('audit-log')">
                            <i class="fas fa-clipboard-list me-2"></i>View Audit Log
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-chart-line me-2"></i>Performance & Monitoring
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="runCommand('queue-work')">
                            <i class="fas fa-tasks me-2"></i>Start Queue Worker
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('schedule-run')">
                            <i class="fas fa-clock me-2"></i>Run Scheduler
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('horizon')">
                            <i class="fas fa-chart-bar me-2"></i>Start Horizon
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('telescope')">
                            <i class="fas fa-search me-2"></i>Start Telescope
                        </button>
                    </div>
                </div>
            </div>

            <!-- Emergency Actions -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>Emergency Actions
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-danger" onclick="runCommand('down')">
                            <i class="fas fa-power-off me-2"></i>Put Site Down
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="runCommand('up')">
                            <i class="fas fa-play me-2"></i>Bring Site Up
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="runCommand('maintenance')">
                            <i class="fas fa-tools me-2"></i>Maintenance Mode
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="runCommand('health-check')">
                            <i class="fas fa-heartbeat me-2"></i>Health Check
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-terminal me-2"></i>Custom Commands
                    </h6>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="customCommand" placeholder="Enter custom artisan command (e.g., make:controller UserController)">
                        <button class="btn btn-outline-primary" type="button" onclick="runCustomCommand()">
                            <i class="fas fa-play me-2"></i>Execute
                        </button>
                    </div>
                    <small class="form-text text-muted">Enter any artisan command to execute it directly</small>
                </div>
            </div>

            <!-- Command Output Area -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-terminal me-2"></i>Command Output
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="commandOutput" class="bg-dark text-light p-3 rounded" style="min-height: 200px; font-family: monospace; font-size: 12px;">
                                <div class="text-muted">Command output will appear here...</div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearOutput()">
                                    <i class="fas fa-trash me-1"></i>Clear Output
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="downloadOutput()">
                                    <i class="fas fa-download me-1"></i>Download Log
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function runCommand(command) {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Running...';
    button.disabled = true;
    
    fetch('{{ route("admin.settings.execute-command") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ command: command })
    })
    .then(response => response.json())
    .then(data => {
        // Reset button
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Add to output
        addToOutput(`\n> ${command}\n${data.output}\n`);
        
        if (data.success) {
            addToOutput(`✓ Command completed successfully\n`);
        } else {
            addToOutput(`✗ Command failed: ${data.message}\n`);
        }
    })
    .catch(error => {
        // Reset button
        button.innerHTML = originalText;
        button.disabled = false;
        
        addToOutput(`\n> ${command}\n✗ Error: ${error.message}\n`);
    });
}

function runCustomCommand() {
    const command = document.getElementById('customCommand').value;
    if (!command) {
        alert('Please enter a command');
        return;
    }
    
    runCommand(command);
    document.getElementById('customCommand').value = '';
}

function addToOutput(text) {
    const output = document.getElementById('commandOutput');
    const timestamp = new Date().toLocaleTimeString();
    output.innerHTML += `<span class="text-muted">[${timestamp}]</span> ${text}`;
    output.scrollTop = output.scrollHeight;
}

function clearOutput() {
    document.getElementById('commandOutput').innerHTML = '<div class="text-muted">Command output will appear here...</div>';
}

function downloadOutput() {
    const output = document.getElementById('commandOutput').innerText;
    const blob = new Blob([output], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'command-output-' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') + '.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
