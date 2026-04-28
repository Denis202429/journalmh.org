<div class="delete-account-section">
    <div class="alert alert-danger">
        <h5 class="alert-heading">
            <i class="bi bi-exclamation-triangle me-2"></i>Удаление аккаунта
        </h5>
        <p class="mb-2">
            После удаления вашего аккаунта все ваши данные будут безвозвратно удалены. 
            Перед удалением аккаунта, пожалуйста, сохраните всю важную информацию.
        </p>
    </div>

    <!-- Кнопка для открытия модального окна -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        <i class="bi bi-trash me-1"></i> Удалить аккаунт
    </button>

    <!-- Модальное окно подтверждения -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>Подтверждение удаления
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body">
                        <p class="text-danger fw-bold">Внимание! Это действие нельзя отменить!</p>
                        <p>Вы уверены, что хотите удалить свой аккаунт? Все ваши данные будут удалены без возможности восстановления.</p>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Введите ваш пароль для подтверждения:</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                   placeholder="Ваш пароль"
                                   required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Отмена
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Да, удалить аккаунт
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>