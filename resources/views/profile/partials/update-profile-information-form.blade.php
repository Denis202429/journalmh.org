<form method="post" action="{{ route('profile.update') }}" class="needs-validation" novalidate>
    @csrf
    @method('patch')

    <!-- Имя -->
    <div class="mb-3">
        <label for="name" class="form-label">Имя <span class="text-danger">*</span></label>
        <input type="text" 
               id="name" 
               name="name" 
               class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name', $user->name) }}" 
               required 
               autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" 
               id="email" 
               name="email" 
               class="form-control @error('email') is-invalid @enderror" 
               value="{{ old('email', $user->email) }}" 
               required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <!-- Верификация email -->
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning mt-2">
                <p class="mb-1">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Ваш email не подтвержден
                </p>
                <form method="post" action="{{ route('verification.send') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm p-0">
                        Отправить письмо для подтверждения
                    </button>
                </form>
                
                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2 mb-0">
                        <i class="bi bi-check-circle me-1"></i>
                        Ссылка для подтверждения отправлена на ваш email
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Организация (если есть в модели) -->
    @if(isset($user->organization))
    <div class="mb-3">
        <label for="organization" class="form-label">Организация</label>
        <input type="text" 
               id="organization" 
               name="organization" 
               class="form-control @error('organization') is-invalid @enderror" 
               value="{{ old('organization', $user->organization) }}">
        @error('organization')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @endif

    <!-- Кнопка сохранения -->
    <div class="d-flex justify-content-between align-items-center">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Сохранить изменения
        </button>
    </div>
</form>