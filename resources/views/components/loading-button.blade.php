@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'loading' => false,
    'disabled' => false,
    'icon' => '',
    'text' => 'Button',
    'loadingText' => 'Loading...',
    'onclick' => ''
])

@php
    $sizeClass = $size === 'sm' ? 'btn-sm' : ($size === 'lg' ? 'btn-lg' : '');
    $variantClass = 'btn-' . $variant;
    $loadingClass = $loading ? 'btn-loading' : '';
    $disabledAttr = $disabled || $loading ? 'disabled' : '';
@endphp

<button 
    type="{{ $type }}" 
    class="btn {{ $variantClass }} {{ $sizeClass }} {{ $loadingClass }}"
    {{ $disabledAttr }}
    @if($onclick) onclick="{{ $onclick }}" @endif
    {{ $attributes->merge(['aria-label' => $text]) }}
>
    @if($icon && !$loading)
        <i class="{{ $icon }} me-2"></i>
    @endif
    <span class="btn-text">
        {{ $loading ? $loadingText : $text }}
    </span>
    @if($loading)
        <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
    @endif
</button>

<style>
    .btn-loading {
        position: relative;
        pointer-events: none;
    }
    
    .btn-loading .btn-text {
        opacity: 0.7;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Enhanced button states */
    .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn:active:not(:disabled) {
        transform: translateY(0);
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
