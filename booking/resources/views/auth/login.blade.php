<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Booking System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Sora Font -->
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand-black: #000000;
            --brand-red: #ef473e;
            --brand-orange: #fdb838;
            --brand-dark-blue: #070c39;
            --brand-gradient: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
            --brand-gradient-hover: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);
            --font-family: 'Sora', sans-serif;
            --font-weight-semibold: 600;
            --font-weight-bold: 700;
            --font-weight-medium: 500;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-family);
            background: #f8f9fa;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .login-container {
            display: flex;
            min-height: 100vh;
        }
        
        .login-form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: white;
            position: relative;
        }
        
        .login-form-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(239, 71, 62, 0.05));
        }
        
        .login-form-container {
            width: 100%;
            max-width: 400px;
            /* padding: 2rem 0; */
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .login-header h1 {
            font-size: 2.5rem;
            font-weight: var(--font-weight-bold);
            color: var(--brand-black);
            margin-bottom: 0.5rem;
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: var(--font-weight-medium);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: var(--font-weight-semibold);
            color: var(--brand-black);
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            display: block;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 0.5rem;
        }
        
        .input-group-text {
            background: var(--brand-gradient);
            border: 2px solid #e9ecef;
            border-right: none;
            color: white;
            padding: 0.875rem 1rem;
            border-radius: 12px 0 0 12px;
            transition: all 0.3s ease;
        }
        
        .password-toggle-btn {
            background: #f8f9fa !important;
            border: 2px solid #e9ecef !important;
            border-left: none !important;
            border-right: none !important;
            color: #6c757d !important;
            padding: 0.875rem 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 0 !important;
        }
        
        .password-toggle-btn:hover {
            background: #e9ecef !important;
            color: var(--brand-red) !important;
            transform: scale(1.05);
        }
        
        .password-toggle-btn:active {
            transform: scale(0.95);
        }
        
        .form-control {
            border-radius: 0;
            border: 2px solid #e9ecef;
            border-left: none;
            border-right: none;
            padding: 0.875rem 1rem;
            font-family: var(--font-family);
            transition: all 0.3s ease;
            background: #fafafa;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.15);
            background: white;
            outline: none;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--brand-red);
            transform: scale(1.02);
        }
        
        .btn-login {
            background: var(--brand-gradient);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: var(--font-weight-semibold);
            width: 100%;
            color: white;
            transition: all 0.3s ease;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
            margin-top: 1rem;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            background: var(--brand-gradient-hover);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(239, 71, 62, 0.4);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-weight: var(--font-weight-medium);
        }
        
        .demo-accounts {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid #e9ecef;
        }
        
        .demo-accounts h6 {
            color: var(--brand-black);
            font-weight: var(--font-weight-semibold);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .demo-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 6px;
            padding: 0.5rem;
        }
        
        .demo-account:hover {
            background: rgba(239, 71, 62, 0.1);
        }
        
        .demo-account:last-child {
            border-bottom: none;
        }
        
        .demo-account strong {
            color: var(--brand-red);
            font-weight: var(--font-weight-semibold);
        }
        
        .demo-account span {
            color: var(--brand-dark-blue);
            font-weight: var(--font-weight-medium);
        }
        
        .image-section {
            flex: 1;
            background: linear-gradient(135deg, #070c39 0%, #000000 50%, #ef473e 100%);
            position: relative;
            overflow: hidden;
        }
        
        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80') center/cover;
            opacity: 0.3;
            z-index: 1;
        }
        
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(7, 12, 57, 0.8) 0%, rgba(0, 0, 0, 0.6) 50%, rgba(239, 71, 62, 0.7) 100%);
            z-index: 2;
        }
        
        .image-content {
            position: relative;
            z-index: 3;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 3rem;
            color: white;
        }
        
        .image-content h2 {
            font-size: 3rem;
            font-weight: var(--font-weight-bold);
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .image-content p {
            font-size: 1.2rem;
            font-weight: var(--font-weight-medium);
            opacity: 0.9;
            max-width: 400px;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
        
        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-element:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-element:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .floating-element:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .image-section {
                min-height: 40vh;
            }
            
            .image-content h2 {
                font-size: 2rem;
            }
            
            .image-content p {
                font-size: 1rem;
            }
            
            .login-form-section {
                padding: 1rem;
            }
            
            .login-form-container {
                padding: 1rem 0;
            }
        }
        
        @media (max-width: 576px) {
            .login-header h1 {
                font-size: 2rem;
            }
            
            .image-content {
                padding: 2rem 1rem;
            }
            
            .image-content h2 {
                font-size: 1.5rem;
            }
        }
        
        /* Loading animation */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn-login.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* WordPress OAuth Styles */
        .divider {
            position: relative;
            text-align: center;
            margin: 2rem 0;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }
        
        .divider-text {
            background: white;
            padding: 0 1rem;
            color: #6c757d;
            font-weight: var(--font-weight-medium);
            position: relative;
            z-index: 1;
        }
        
        .btn-wordpress {
            background: #21759b;
            border: 2px solid #21759b;
            color: white;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: var(--font-weight-semibold);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            width: 100%;
        }
        
        .btn-wordpress:hover {
            background: #1e6a8c;
            border-color: #1e6a8c;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(33, 117, 155, 0.3);
        }
        
        .btn-wordpress:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Login Form Section (Left Side) -->
        <div class="login-form-section">
            <div class="login-form-container">
                    <div class="login-header">
                    <h1><i class="fas fa-graduation-cap me-2"></i>Online Booking System</h1>
                    <p>Welcome back! Please sign in to continue</p>
                    </div>
                    
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if(session('expired'))
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Session Expired:</strong> Your session has expired for security reasons. Please log in again to continue.
                            </div>
                        @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf
                            
                    <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="Enter your email address">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                    <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required
                                   placeholder="Enter your password">
                                    <button type="button" class="input-group-text password-toggle-btn" id="passwordToggle" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-login" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>

                        <!-- WordPress OAuth Login -->
                        @if(\App\Models\SystemSetting::getValue('wp_oauth_enabled') == '1')
                        <div class="text-center mt-1">
                            <div class="divider">
                                <span class="divider-text">OR</span>
                            </div>
                            <a href="{{ route('login.wordpress') }}" class="btn btn-outline-primary btn-wordpress mt-3">
                                <i class="fab fa-wordpress me-1"></i>Login with WordPress OAuth
                            </a>
                            
                            <!-- Alternative WordPress REST API Login -->
                            <div class="mt-3 d-none">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleWordPressForm()">
                                    <i class="fas fa-key me-2"></i>WordPress REST API Login
                                </button>
                            </div>
                            
                            <!-- WordPress REST API Form (Hidden by default) -->
                            <div id="wordpressForm" style="display: none;" class="mt-3">
                                <form method="POST" action="{{ route('login.wordpress.rest') }}" id="wordpressRestForm">
                                    @csrf
                                    <div class="form-group">
                                        <label for="wp_username" class="form-label">WordPress Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="wp_username" name="username" 
                                                   placeholder="Enter WordPress username" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="wp_password" class="form-label">WordPress Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="wp_password" name="password" 
                                                   placeholder="Enter WordPress password" required>
                                            <button type="button" class="input-group-text password-toggle-btn" id="wpPasswordToggle" onclick="toggleWpPassword()">
                                                <i class="fas fa-eye" id="wpPasswordToggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login with WordPress
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                        </div>
                    </div>

        <!-- Image Section (Right Side) -->
        <div class="image-section">
            <div class="floating-elements">
                <div class="floating-element"></div>
                <div class="floating-element"></div>
                <div class="floating-element"></div>
                </div>
            <div class="image-overlay"></div>
            <div class="image-content">
                <h2>Transform Your Learning Experience</h2>
                <p>Join thousands of students and teachers who are already using our platform to create meaningful educational connections and achieve their learning goals.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            
            // Add loading state on form submission
            loginForm.addEventListener('submit', function() {
                //loginBtn.classList.add('loading');
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
            });
            
            // Add focus effects to input groups
            const inputGroups = document.querySelectorAll('.input-group');
            inputGroups.forEach(group => {
                const input = group.querySelector('.form-control');
                const icon = group.querySelector('.input-group-text');
                
                input.addEventListener('focus', function() {
                    group.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!input.value) {
                        group.classList.remove('focused');
                    }
                });
            });
            
            // Add click-to-fill demo accounts
            const demoAccounts = document.querySelectorAll('.demo-account');
            demoAccounts.forEach(account => {
                account.style.cursor = 'pointer';
                account.addEventListener('click', function() {
                    const emailSpan = account.querySelector('span');
                    const emailText = emailSpan.textContent.split(' / ')[0];
                    const passwordText = emailSpan.textContent.split(' / ')[1];
                    
                    document.getElementById('email').value = emailText;
                    document.getElementById('password').value = passwordText;
                    
                    // Add visual feedback
                    account.style.background = 'rgba(239, 71, 62, 0.1)';
                    setTimeout(() => {
                        account.style.background = '';
                    }, 1000);
                });
            });
            
            // Add smooth animations
            const loginCard = document.querySelector('.login-card');
            if (loginCard) {
            loginCard.style.opacity = '0';
            loginCard.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                loginCard.style.transition = 'all 0.6s ease';
                loginCard.style.opacity = '1';
                loginCard.style.transform = 'translateY(0)';
            }, 100);
            }
        });
        
        // Toggle WordPress REST API form
        function toggleWordPressForm() {
            const form = document.getElementById('wordpressForm');
            const button = event.target;
            
            if (form.style.display === 'none') {
                form.style.display = 'block';
                button.innerHTML = '<i class="fas fa-times me-2"></i>Hide WordPress Form';
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-outline-danger');
            } else {
                form.style.display = 'none';
                button.innerHTML = '<i class="fas fa-key me-2"></i>WordPress REST API Login';
                button.classList.remove('btn-outline-danger');
                button.classList.add('btn-outline-secondary');
            }
        }
        
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            const toggleBtn = document.getElementById('passwordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                toggleBtn.setAttribute('title', 'Hide password');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleBtn.setAttribute('title', 'Show password');
            }
        }
        
        // Toggle WordPress password visibility
        function toggleWpPassword() {
            const passwordInput = document.getElementById('wp_password');
            const toggleIcon = document.getElementById('wpPasswordToggleIcon');
            const toggleBtn = document.getElementById('wpPasswordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                toggleBtn.setAttribute('title', 'Hide password');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleBtn.setAttribute('title', 'Show password');
            }
        }
    </script>
</body>
</html>
