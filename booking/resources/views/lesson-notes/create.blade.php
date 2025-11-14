@extends('layouts.app')

@section('title', 'Add Lesson Note')

@section('content')
<!-- Welcome Header -->
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title">
                <i class="fas fa-plus me-3"></i>Add Lesson Note
            </h1>
            <p class="dashboard-subtitle">Document what was covered in this lesson for your student's learning journey.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="dashboard-date">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ now()->format('l, F j, Y') }}
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
                            <form action="{{ route('lesson-notes.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            @if($student && $students->count() == 1)
                                            <label for="student_id" class="form-label">Student(s) <span class="text-danger">*</span></label>
                                                <!-- Pre-selected student from URL parameters -->
                                                <div class="form-control-plaintext bg-light p-3 rounded">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user me-2 text-primary"></i>
                                                        <div>
                                                            <strong>{{ $student->user->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $student->user->email }}</small>
                                                        </div>
                                                    </div>
                                                    @if($booking)
                                                        <small class="text-info mt-1 d-block">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Pre-selected from Session #{{ $booking->id }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                            @else
                                            <label for="student_id" class="form-label">Student(s) <span class="text-danger">*</span></label>
                                                @if($students->count() > 1)
                                                <div class="mb-1">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-students">
                                                        <i class="fas fa-check-square me-1"></i>Select All
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-students">
                                                        <i class="fas fa-square me-1"></i>Deselect All
                                                    </button>
                                                </div>
                                                @endif
                                                <!-- Checkbox selection for students -->
                                                <div class="student-checkboxes border rounded p-3 @error('student_ids') is-invalid @enderror" style="max-height: 200px; overflow-y: auto;">
                                                    @foreach($students as $studentOption)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="student_ids[]" 
                                                               value="{{ $studentOption->id }}" 
                                                               id="student_{{ $studentOption->id }}"
                                                               {{ (old('student_ids') && in_array($studentOption->id, old('student_ids'))) || ($student && $student->id == $studentOption->id) ? 'checked' : '' }}>
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
                                            @endif
                                            @error('student_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @error('student_ids')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="booking_id" class="form-label">Session (Optional)</label>
                                            <select name="booking_id" id="booking_id" class="form-select @error('booking_id') is-invalid @enderror">
                                                <option value="">No specific session</option>
                                                <!-- Will be populated via JavaScript based on selected student -->
                                            </select>
                                            @error('booking_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                                   value="{{ old('lesson_date', $booking?->start_time?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}" 
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
                                                <option value="student_and_teacher" {{ old('visibility', 'student_and_teacher') == 'student_and_teacher' ? 'selected' : '' }}>
                                                    Student & Teacher
                                                </option>
                                                <option value="teacher_only" {{ old('visibility') == 'teacher_only' ? 'selected' : '' }}>
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
                                           value="{{ old('title', $booking ? 'Lesson with ' . $booking->student->user->name : '') }}" 
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
                                              placeholder="Describe what was covered in this lesson, key concepts, techniques practiced, homework assigned, etc.">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Attachments (Optional)</label>
                                    
                                    <!-- Drag and drop area -->
                                    <div id="drop-zone" class="drop-zone border border-2 border-dashed rounded p-4 text-center mb-3" 
                                         style="border-color: #dee2e6; transition: all 0.3s ease;">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="mb-2">Drag and drop files here or click to browse</p>
                                        <small class="text-muted">Supports: PDF, DOC, DOCX, JPG, PNG, GIF, MP3, MP4</small>
                                    </div>
                                    
                                    <input type="file" 
                                           name="attachments[]" 
                                           id="attachments" 
                                           class="form-control" 
                                           multiple 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp3,.mp4"
                                           style="display: none;">
                                    
                                    <!-- File preview area -->
                                    <div id="file-preview" class="mt-3" style="display: none;">
                                        <h6>Selected Files:</h6>
                                        <div id="file-list" class="list-group"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('lesson-notes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Lesson Note
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
                                <i class="fas fa-info-circle me-2"></i>Lesson Note Tips
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Be specific:</strong> Include key concepts, techniques, and songs covered.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Use clear titles:</strong> Make it easy for students to find specific lessons.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Include homework:</strong> Note any practice assignments or goals.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Attach materials:</strong> Upload sheet music, recordings, or practice files.
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Privacy:</strong> Use "Teacher Only" for internal notes or sensitive feedback.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingSelect = document.getElementById('booking_id');
    
    // Pre-select student if provided
    @if($student)
        @if($students->count() == 1)
            // Student is pre-selected and dropdown is hidden
            loadBookingsForStudent('{{ $student->id }}');
        @else
            // Student is provided but checkboxes are shown
            loadBookingsForStudent('{{ $student->id }}');
        @endif
    @endif
    
    // Pre-select booking if provided
    @if($booking)
        if (bookingSelect) {
            bookingSelect.value = '{{ $booking->id }}';
        }
    @endif
    
    // Note: studentSelect is not used in this view as we use checkboxes instead
    
    // Handle checkbox student selection
    const studentCheckboxes = document.querySelectorAll('input[name="student_ids[]"]');
    if (studentCheckboxes.length > 0) {
        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const selectedStudents = Array.from(document.querySelectorAll('input[name="student_ids[]"]:checked')).map(cb => cb.value);
                if (selectedStudents.length > 0) {
                    // Load bookings for the first selected student
                    loadBookingsForStudent(selectedStudents[0]);
                } else {
                    if (bookingSelect) {
                        bookingSelect.innerHTML = '<option value="">No specific session</option>';
                    }
                }
            });
        });
    }
    
    // Handle Select All / Deselect All buttons
    const selectAllBtn = document.getElementById('select-all-students');
    const deselectAllBtn = document.getElementById('deselect-all-students');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            // Trigger change event for booking loading
            if (studentCheckboxes.length > 0) {
                studentCheckboxes[0].dispatchEvent(new Event('change'));
            }
        });
    }
    
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (bookingSelect) {
                bookingSelect.innerHTML = '<option value="">No specific session</option>';
            }
        });
    }
    
    function loadBookingsForStudent(studentId) {
        // Simplified - just show no specific session option
        if (bookingSelect) {
            bookingSelect.innerHTML = '<option value="">No specific session</option>';
        }
        console.log('Student selected:', studentId);
    }
});
</script>

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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle file selection preview
    const fileInput = document.getElementById('attachments');
    const filePreview = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            if (files.length > 0) {
                filePreview.style.display = 'block';
                fileList.innerHTML = '';
                
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
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    fileList.appendChild(fileItem);
                });
            } else {
                filePreview.style.display = 'none';
            }
        });
    }
    
    // Function to remove file from preview
    window.removeFile = function(index) {
        const dt = new DataTransfer();
        
        Array.from(fileInput.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        
        fileInput.files = dt.files;
        fileInput.dispatchEvent(new Event('change'));
    };
    
    // Drag and drop functionality
    const dropZone = document.getElementById('drop-zone');
    
    if (dropZone) {
        // Click to browse
        dropZone.addEventListener('click', () => fileInput.click());
        
        // Drag and drop events
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#007bff';
            dropZone.style.backgroundColor = '#f8f9fa';
        });
        
        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#dee2e6';
            dropZone.style.backgroundColor = 'transparent';
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#dee2e6';
            dropZone.style.backgroundColor = 'transparent';
            
            const files = Array.from(e.dataTransfer.files);
            const dt = new DataTransfer();
            
            // Add existing files
            Array.from(fileInput.files).forEach(file => dt.items.add(file));
            
            // Add new files
            files.forEach(file => dt.items.add(file));
            
            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        });
    }
});
</script>
@endsection
