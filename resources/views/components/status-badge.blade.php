@props([
    'status' => '',
    'type' => 'default',
    'size' => 'md',
    'icon' => true,
    'text' => null
])

@php
    $statusConfig = [
        'active' => ['class' => 'status-active', 'icon' => 'fa-check-circle', 'color' => 'success'],
        'inactive' => ['class' => 'status-inactive', 'icon' => 'fa-times-circle', 'color' => 'secondary'],
        'pending' => ['class' => 'status-pending', 'icon' => 'fa-clock', 'color' => 'warning'],
        'confirmed' => ['class' => 'status-confirmed', 'icon' => 'fa-check-circle', 'color' => 'success'],
        'completed' => ['class' => 'status-completed', 'icon' => 'fa-check-double', 'color' => 'info'],
        'cancelled' => ['class' => 'status-cancelled', 'icon' => 'fa-times-circle', 'color' => 'danger'],
        'declined' => ['class' => 'status-declined', 'icon' => 'fa-ban', 'color' => 'secondary'],
        'no_show' => ['class' => 'status-no-show', 'icon' => 'fa-user-times', 'color' => 'warning'],
        'verified' => ['class' => 'status-verified', 'icon' => 'fa-certificate', 'color' => 'info'],
        'unverified' => ['class' => 'status-unverified', 'icon' => 'fa-exclamation-triangle', 'color' => 'warning'],
        'available' => ['class' => 'status-available', 'icon' => 'fa-clock', 'color' => 'success'],
        'unavailable' => ['class' => 'status-unavailable', 'icon' => 'fa-pause', 'color' => 'danger'],
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['inactive'];
    $displayText = $text ?: ucfirst(str_replace('_', ' ', $status));
    $sizeClass = $size === 'sm' ? 'badge-sm' : ($size === 'lg' ? 'badge-lg' : '');
@endphp

<span class="status-badge {{ $config['class'] }} {{ $sizeClass }}" 
      title="{{ $displayText }}"
      aria-label="Status: {{ $displayText }}">
    @if($icon && $config['icon'])
        <i class="fas {{ $config['icon'] }} me-1"></i>
    @endif
    {{ $displayText }}
</span>

<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        transition: all 0.3s ease;
    }
    
    .badge-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.65rem;
    }
    
    .badge-lg {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    /* Status Colors - Consistent across all pages */
    .status-active, .status-confirmed, .status-available {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-inactive, .status-declined {
        background: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-completed {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
    
    .status-cancelled, .status-unavailable {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .status-no-show {
        background: #ffeaa7;
        color: #856404;
        border: 1px solid #fdcb6e;
    }
    
    .status-verified {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
    
    .status-unverified {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>
