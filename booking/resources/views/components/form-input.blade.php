@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'help' => '',
    'error' => '',
    'icon' => '',
    'options' => [],
    'multiple' => false,
    'rows' => 3
])

@php
    $inputId = $name ?: 'input_' . uniqid();
    $hasError = $errors->has($name) || $error;
    $errorMessage = $error ?: $errors->first($name);
@endphp

<div class="form-group mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label {{ $required ? 'required' : '' }}">
            @if($icon)
                <i class="{{ $icon }} me-2"></i>
            @endif
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($type === 'select')
        <select 
            name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
            id="{{ $inputId }}" 
            class="form-select modern-select {{ $hasError ? 'is-invalid' : '' }}"
            {{ $required ? 'required' : '' }}
            {{ $multiple ? 'multiple' : '' }}
            {{ $attributes->merge(['aria-describedby' => $help ? $inputId . '_help' : null]) }}
        >
            @if(!$required)
                <option value="">{{ $placeholder ?: 'Select an option' }}</option>
            @endif
            @foreach($options as $value => $text)
                <option value="{{ $value }}" {{ (is_array(old($name)) ? in_array($value, old($name)) : old($name) == $value) ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea 
            name="{{ $name }}" 
            id="{{ $inputId }}" 
            class="form-control modern-input {{ $hasError ? 'is-invalid' : '' }}"
            {{ $required ? 'required' : '' }}
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['aria-describedby' => $help ? $inputId . '_help' : null]) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $inputId }}" 
            class="form-control modern-input {{ $hasError ? 'is-invalid' : '' }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['aria-describedby' => $help ? $inputId . '_help' : null]) }}
        />
    @endif

    @if($help)
        <div id="{{ $inputId }}_help" class="form-text">{{ $help }}</div>
    @endif

    @if($hasError)
        <div class="invalid-feedback">
            {{ $errorMessage }}
        </div>
    @endif
</div>

<style>
    .form-label.required::after {
        content: " *";
        color: #dc3545;
    }
    
    .modern-input, .modern-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }
    
    .modern-input:focus, .modern-select:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.15);
        background: white;
        outline: none;
    }
    
    .modern-input.is-invalid, .modern-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .modern-input.is-valid, .modern-select.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
</style>
