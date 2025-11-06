@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        📹 My Session Recordings
                        <span class="badge bg-primary">{{ $recordings->total() }}</span>
                    </h4>
                </div>
                
                <div class="card-body">
                    @if($recordings->count() > 0)
                        <div class="row">
                            @foreach($recordings as $recording)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                📚 {{ $recording->booking->subject }}
                                            </h6>
                                            
                                            <p class="card-text">
                                                <strong>Teacher:</strong> {{ $recording->booking->teacher->user->name }}<br>
                                                <strong>Date:</strong> {{ $recording->booking->start_time->format('M j, Y') }}<br>
                                                <strong>Time:</strong> {{ $recording->booking->start_time->format('g:i A') }}<br>
                                                <strong>Duration:</strong> {{ $recording->booking->duration }} minutes
                                            </p>
                                            
                                            <div class="d-flex justify-content-between">
                                                @if($recording->play_url)
                                                    <a href="{{ route('student.recordings.play', $recording->id) }}" 
                                                       class="btn btn-primary btn-sm" target="_blank">
                                                        ▶️ Play
                                                    </a>
                                                @endif
                                                
                                                @if($recording->download_url)
                                                    <a href="{{ route('student.recordings.download', $recording->id) }}" 
                                                       class="btn btn-success btn-sm">
                                                        💾 Download
                                                    </a>
                                                @endif
                                                
                                                <a href="{{ route('student.recordings.show', $recording->id) }}" 
                                                   class="btn btn-info btn-sm">
                                                    📋 Details
                                                </a>
                                            </div>
                                            
                                            @if($recording->file_size > 0)
                                                <small class="text-muted">
                                                    File size: {{ number_format($recording->file_size / 1024 / 1024, 2) }} MB
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $recordings->links() }}
                        </div>
                        
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-video fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No recordings available yet</h5>
                            <p class="text-muted">
                                Your session recordings will appear here after your lessons are completed.
                            </p>
                            <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                                Go to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
