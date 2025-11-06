@extends('layouts.app')

@section('title', 'Edit Lesson Note')

@section('content')
<!-- Welcome Header -->
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title">
                <i class="fas fa-edit me-3"></i>Edit Lesson Note
            </h1>
            <p class="dashboard-subtitle">Update the lesson details and content.</p>
        </div>
        <div class="col-md-4 text-end">
                <div class="action-button">
                    <a href="{{ route('lesson-notes.show', $lessonNote) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i>Back to Note
                    </a>
                    <a href="{{ route('lesson-notes.index', ['student_id' => $lessonNote->student_id]) }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-list"></i>Lesson Log
                    </a>
                </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('lesson-notes.update', $lessonNote) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <!-- Current Students Display -->
                                            <div class="current-students-display bg-light p-3 rounded mb-3 d-none">
                                                <h6 class="mb-3">
                                                    <i class="fas fa-users me-2"></i>Current Students
                                                </h6>
                                                
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-user me-2 text-primary"></i>
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $lessonNote->student->user->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $lessonNote->student->user->email }}</small>
                                                    </div>
                                                    <span class="badge bg-primary">Primary</span>
                                                </div>
                                                
                                                @if($lessonNote->additional_students && count($lessonNote->additional_students) > 0)
                                                    @foreach($lessonNote->additional_students as $additionalStudentId)
                                                        @php
                                                            $additionalStudent = \App\Models\Student::with('user')->find($additionalStudentId);
                                                        @endphp
                                                        @if($additionalStudent)
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-user me-2 text-secondary"></i>
                                                            <div class="flex-grow-1">
                                                                <strong>{{ $additionalStudent->user->name }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $additionalStudent->user->email }}</small>
                                                            </div>
                                                            <span class="badge bg-secondary">Additional</span>
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            
                                            @php
                                                // Get all students for the teacher or admin
                                                if (Auth::user()->isTeacher() && Auth::user()->teacher) {
                                                    $teacherId = Auth::user()->teacher->id;
                                                    $availableStudents = \App\Models\Student::whereHas('bookings', function($query) use ($teacherId) {
                                                        $query->where('teacher_id', $teacherId)
                                                                ->where('status', 'confirmed');
                                                    })->with('user')->get();
                                                } else {
                                                    // For admins, show all students
                                                    $availableStudents = \App\Models\Student::with('user')->get();
                                                }
                                                
                                                // Get current student IDs
                                                $currentStudentIds = $lessonNote->getAllStudents()->toArray();
                                            @endphp
                                            
                                            
                                            <label class="form-label">Students <span class="text-danger">*</span></label>
                                            @if($availableStudents->count() > 1)
                                            <div class="mb-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-students">
                                                    <i class="fas fa-check-square me-1"></i>Select All
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-students">
                                                    <i class="fas fa-square me-1"></i>Deselect All
                                                </button>
                                            </div>
                                            @endif
                                            <!-- Student Selection for Editing -->
                                            <div class="student-selection">
                                                <div class="student-checkboxes border rounded p-3 @error('student_ids') is-invalid @enderror" style="max-height: 200px; overflow-y: auto;">
                                                    
                                                    @foreach($availableStudents as $studentOption)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" 
                                                                name="student_ids[]" 
                                                                value="{{ $studentOption->id }}" 
                                                                id="student_{{ $studentOption->id }}"
                                                                {{ in_array($studentOption->id, $currentStudentIds) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="student_{{ $studentOption->id }}">
                                                            <strong>{{ $studentOption->user->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $studentOption->user->email }}</small>
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Select one or more students for this lesson note
                                                </div>
                                            </div>
                                            
                                            @error('student_ids')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Session</label>
                                            <div class="form-control-plaintext bg-light p-3 rounded">
                                                @if($lessonNote->booking)
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-video me-2 text-success"></i>
                                                        <div>
                                                            <a href="{{ route('bookings.show', $lessonNote->booking) }}" class="text-decoration-none fw-bold">
                                                                Session #{{ $lessonNote->booking->id }}
                                                            </a>
                                                            <br>
                                                            <small class="text-muted">{{ $lessonNote->booking->start_time->format('M d, Y g:i A') }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-info-circle me-2 text-muted"></i>
                                                        <div>
                                                            <span class="text-muted">No specific session</span>
                                                            <br>
                                                            <small class="text-muted">General lesson note</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="lesson_date" class="form-label">Lesson Date <span class="text-danger">*</span></label>
                                            <input type="datetime-local" 
                                                   name="lesson_date" 
                                                   id="lesson_date" 
                                                   class="form-control @error('lesson_date') is-invalid @enderror" 
                                                   value="{{ old('lesson_date', $lessonNote->lesson_date->format('Y-m-d\TH:i')) }}" 
                                                   required>
                                            @error('lesson_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="visibility" class="form-label">Visibility <span class="text-danger">*</span></label>
                                            <select name="visibility" id="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
                                                <option value="student_and_teacher" {{ old('visibility', $lessonNote->visibility) == 'student_and_teacher' ? 'selected' : '' }}>
                                                    Student & Teacher
                                                </option>
                                                <option value="teacher_only" {{ old('visibility', $lessonNote->visibility) == 'teacher_only' ? 'selected' : '' }}>
                                                    Teacher Only
                                                </option>
                                            </select>
                                            @error('visibility')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Choose who can see this lesson note.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Lesson Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $lessonNote->title) }}" 
                                           placeholder="e.g., G Major Scale, 'Levitating' by Dua Lipa, Minor Pentatonic Pattern 1" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Lesson Content</label>
                                    <textarea name="content" 
                                              id="content" 
                                              class="form-control @error('content') is-invalid @enderror" 
                                              rows="6" 
                                              placeholder="Describe what was covered in this lesson, key concepts, techniques practiced, homework assigned, etc.">{{ old('content', $lessonNote->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($lessonNote->attachments && is_array($lessonNote->attachments) && count($lessonNote->attachments) > 0)
                                <div class="mb-3">
                                    <label class="form-label">Current Attachments</label>
                                    <div class="current-attachments">
                                        @foreach($lessonNote->attachments as $attachment)
                                        @if(is_array($attachment) && isset($attachment['url']))
                                        <div class="attachment-item d-flex align-items-center justify-content-between" data-filename="{{ $attachment['name'] ?? 'Attachment' }}">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-paperclip me-2"></i>
                                                <span>{{ $attachment['name'] ?? 'Attachment' }}</span>
                                            </div>
                                            <div class="attachment-actions">
                                                <a href="{{ $attachment['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAttachment('{{ $attachment['name'] ?? 'Attachment' }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @elseif(is_string($attachment))
                                        <div class="attachment-item d-flex align-items-center justify-content-between" data-filename="{{ $attachment }}">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i>
                                                <span>{{ $attachment }}</span>
                                            </div>
                                            <div class="attachment-actions">
                                                <a href="{{ asset('storage/app/public/attachments/' . $attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAttachment('{{ $attachment }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- AJAX attachment removal - no hidden input needed -->

                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Add New Attachments (Optional)</label>
                                    
                                    <!-- Drag and drop area for new files -->
                                    <div id="new-drop-zone" class="drop-zone border border-2 border-dashed rounded p-4 text-center mb-3" 
                                         style="border-color: #dee2e6; transition: all 0.3s ease;">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="mb-2">Drag and drop new files here or click to browse</p>
                                        <small class="text-muted">Supports: PDF, DOC, DOCX, JPG, PNG, GIF, MP3, MP4</small>
                                    </div>
                                    
                                    <input type="file" 
                                           name="attachments[]" 
                                           id="attachments" 
                                           class="form-control" 
                                           multiple 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp3,.mp4"
                                           style="display: none;">
                                    
                                    <!-- New file preview area -->
                                    <div id="new-file-preview" class="mt-3" style="display: none;">
                                        <h6>New Files to Upload:</h6>
                                        <div id="new-file-list" class="list-group"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('lesson-notes.show', $lessonNote) }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Lesson Note
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-info-circle me-2"></i>Lesson Note Info
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <label class="info-label">Created:</label>
                                <div class="info-value">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ $lessonNote->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <label class="info-label">Last Updated:</label>
                                <div class="info-value">
                                    <i class="fas fa-edit me-2"></i>
                                    {{ $lessonNote->updated_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <label class="info-label">Teacher:</label>
                                <div class="info-value">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>
                                    {{ $lessonNote->teacher->user->name }}
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <label class="info-label">Students:</label>
                                <div class="info-value">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-user me-2 text-primary"></i>
                                        <span class="fw-bold">{{ $lessonNote->student->user->name }}</span>
                                        <span class="badge bg-primary ms-2">Primary</span>
                                    </div>
                                    
                                    @if($lessonNote->additional_students && count($lessonNote->additional_students) > 0)
                                        @foreach($lessonNote->additional_students as $additionalStudentId)
                                            @php
                                                $additionalStudent = \App\Models\Student::with('user')->find($additionalStudentId);
                                            @endphp
                                            @if($additionalStudent)
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-user me-2 text-secondary"></i>
                                                <span>{{ $additionalStudent->user->name }}</span>
                                                <span class="badge bg-secondary ms-2">Additional</span>
                                            </div>
                                            @endif
                                        @endforeach
                                    @endif
                                    
                                    @if($lessonNote->additional_students && count($lessonNote->additional_students) > 0)
                                    <small class="text-muted">
                                        Total: {{ count($lessonNote->additional_students) + 1 }} students
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-lightbulb me-2"></i>Editing Tips
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Update content:</strong> Add more details or correct information.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Change visibility:</strong> Make notes private or visible to students.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Add attachments:</strong> Upload additional materials.
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Preserve history:</strong> Changes are tracked with timestamps.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Dashboard Styles */
.dashboard-header {
    background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.dashboard-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.dashboard-date {
    background: rgba(255,255,255,0.2);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

.action-section {
    margin-bottom: 2rem;
}

.action-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    border-radius: 20px;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.action-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.action-icon {
    font-size: 3rem;
    opacity: 0.8;
    animation: pulse 2s infinite;
}

.action-text {
    flex: 1;
}

.action-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.action-description {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.action-button {
    flex-shrink: 0;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.current-attachments {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.attachment-item {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 0.9rem;
}

.info-item {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    display: block;
}

.info-value {
    color: #333;
    font-size: 0.95rem;
}

.students-display {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.students-display .d-flex {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.students-display .d-flex:last-child {
    border-bottom: none;
}

.students-display .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.student-checkboxes {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6 !important;
}

.student-checkboxes .form-check {
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: background-color 0.15s ease-in-out;
}

.student-checkboxes .form-check:hover {
    background-color: #e9ecef;
}

.student-checkboxes .form-check-input:checked + .form-check-label {
    color: #0d6efd;
}

.student-checkboxes .form-check-label {
    cursor: pointer;
    margin-bottom: 0;
}

.student-checkboxes .form-check-label strong {
    font-weight: 600;
}

.student-checkboxes .form-check-label small {
    font-size: 0.875rem;
}

.student-checkboxes .border-top {
    border-color: #dee2e6 !important;
}

        .student-checkboxes .btn {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        /* Attachment removal styles */
        .attachment-item {
            transition: all 0.3s ease;
        }

        .attachment-item.removing {
            opacity: 0.5;
            text-decoration: line-through;
            background-color: #f8f9fa;
        }

        .attachment-actions .btn {
            margin-left: 0.25rem;
        }

.current-students-display {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.current-students-display .d-flex {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.current-students-display .d-flex:last-child {
    border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Select All / Deselect All buttons
    const selectAllBtn = document.getElementById('select-all-students');
    const deselectAllBtn = document.getElementById('deselect-all-students');
    const studentCheckboxes = document.querySelectorAll('input[name="student_ids[]"]');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
    }
    
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    }
    
    // Handle form submission validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('input[name="student_ids[]"]:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one student.');
                return false;
            }
            
            // Form validation complete
        });
    }
    
    // Handle new file selection preview
    const newFileInput = document.getElementById('attachments');
    const newFilePreview = document.getElementById('new-file-preview');
    const newFileList = document.getElementById('new-file-list');
    
    if (newFileInput) {
        newFileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            if (files.length > 0) {
                newFilePreview.style.display = 'block';
                newFileList.innerHTML = '';
                
                files.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    fileItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file me-2"></i>
                            <div>
                                <strong>${file.name}</strong>
                                <br>
                                <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeNewFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    newFileList.appendChild(fileItem);
                });
            } else {
                newFilePreview.style.display = 'none';
            }
        });
    }
    
    // Function to remove new file from preview
    window.removeNewFile = function(index) {
        const dt = new DataTransfer();
        
        Array.from(newFileInput.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        
        newFileInput.files = dt.files;
        newFileInput.dispatchEvent(new Event('change'));
    };
    
    // Drag and drop functionality for new files
    const newDropZone = document.getElementById('new-drop-zone');
    
    if (newDropZone) {
        // Click to browse
        newDropZone.addEventListener('click', () => newFileInput.click());
        
        // Drag and drop events
        newDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            newDropZone.style.borderColor = '#007bff';
            newDropZone.style.backgroundColor = '#f8f9fa';
        });
        
        newDropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            newDropZone.style.borderColor = '#dee2e6';
            newDropZone.style.backgroundColor = 'transparent';
        });
        
        newDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            newDropZone.style.borderColor = '#dee2e6';
            newDropZone.style.backgroundColor = 'transparent';
            
            const files = Array.from(e.dataTransfer.files);
            const dt = new DataTransfer();
            
            // Add existing files
            Array.from(newFileInput.files).forEach(file => dt.items.add(file));
            
            // Add new files
            files.forEach(file => dt.items.add(file));
            
            newFileInput.files = dt.files;
            newFileInput.dispatchEvent(new Event('change'));
        });
    }
});

// Handle attachment removal
function removeAttachment(filename) {
    console.log('removeAttachment called with:', filename);
    
    if (confirm('Are you sure you want to remove this attachment?')) {
        // Find the attachment item first
        let attachmentItem = document.querySelector(`[data-filename="${filename}"]`);
        
        if (!attachmentItem) {
            // Try to find by partial match for array attachments
            attachmentItem = document.querySelector(`[data-filename*="${filename}"]`);
        }
        
        if (!attachmentItem) {
            console.error('Attachment item not found for filename:', filename);
            return;
        }
        
        // Add visual feedback first
        attachmentItem.classList.add('removing');
        
        // Get lesson note ID from the page
        const lessonNoteId = {{ $lessonNote->id }};
        console.log('Lesson note ID:', lessonNoteId);
        
        // Send AJAX request to delete attachment
        fetch(`/booking/lesson-notes/${lessonNoteId}/remove-attachment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                filename: filename
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                // Hide the attachment item after successful deletion
                setTimeout(() => {
                    attachmentItem.style.display = 'none';
                }, 300);
                
                console.log('Attachment removed successfully:', filename);
                
                // Show success message
                showMessage('Attachment removed successfully!', 'success');
            } else {
                // Show error message
                showMessage('Failed to remove attachment: ' + (data.message || 'Unknown error'), 'error');
                
                // Remove visual feedback on error
                attachmentItem.classList.remove('removing');
            }
        })
        .catch(error => {
            console.error('Error removing attachment:', error);
            showMessage('Error removing attachment: ' + error.message, 'error');
            
            // Remove visual feedback on error
            attachmentItem.classList.remove('removing');
        });
    }
}

// Show message function
function showMessage(message, type = 'info') {
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
    messageDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the form
    const form = document.querySelector('form');
    form.insertBefore(messageDiv, form.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

// Debug function to check attachment items
function debugAttachments() {
    console.log('All attachment items:', document.querySelectorAll('.attachment-item'));
    document.querySelectorAll('.attachment-item').forEach((item, index) => {
        console.log(`Item ${index}:`, item.dataset.filename);
    });
}
</script>
@endsection
