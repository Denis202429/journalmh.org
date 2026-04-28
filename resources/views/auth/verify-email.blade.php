<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }} - {{ __('Verify Email') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verify-container {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
        }
        .verify-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #6c63ff;
            border-color: #6c63ff;
        }
        .btn-primary:hover {
            background-color: #5a52d5;
            border-color: #5a52d5;
        }
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .info-text {
            color: #6c757d;
            font-size: 0.9rem;
            text-align: center;
        }
        .alert-success {
            border-color: #d1e7dd;
        }
        .logout-btn {
            color: #6c63ff;
            background: none;
            border: none;
            padding: 0;
            font: inherit;
            cursor: pointer;
            text-decoration: underline;
        }
        .logout-btn:hover {
            color: #5a52d5;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="card verify-card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">{{ __('Verify Email') }}</h2>
                </div>

                <p class="info-text mb-4">
                    {{ trans('auth.Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ trans('auth.A new verification link has been sent to the email address you provided during registration.') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex flex-column gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-envelope-check me-2"></i>{{ trans('auth.Resend Verification Email') }}
                            </button>
                        </div>
                    </form>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house-door me-2"></i>{{ __('Вернуться на сайт') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-btn">
                                <i class="bi bi-box-arrow-right me-1"></i>{{ trans('auth.Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>