<form method="post" action="{{ route('password.update') }}" class="needs-validation" novalidate>
    @csrf
    @method('put')

    <!-- Текущий пароль -->
    <div class="mb-3">
        <label for="update_password_current_password" class="form-label">Текущий пароль <span class="text-danger">*</span></label>
        <input type="password" 
               id="update_password_current_password" 
               name="current_password" 
               class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
               required 
               autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Новый пароль -->
    <div class="mb-3">
        <label for="update_password_password" class="form-label">Новый пароль <span class="text-danger">*</span></label>
        <input type="password" 
               id="update_password_password" 
               name="password" 
               class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
               required 
               autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Пароль должен содержать не менее 8 символов</div>
    </div>

    <!-- Подтверждение пароля -->
    <div class="mb-4">
        <label for="update_password_password_confirmation" class="form-label">Подтвердите новый пароль <span class="text-danger">*</span></label>
        <input type="password" 
               id="update_password_password_confirmation" 
               name="password_confirmation" 
               class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
               required 
               autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Кнопка сохранения -->
    <div class="d-flex justify-content-between align-items-center">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-key me-1"></i> Изменить пароль
        </button>
    </div>
</form>