@extends('layouts.base')

@section('page.title', 'Управление пользователями')

@section('content')
<div class="container mt-4">
    <h2>Управление пользователями</h2>
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Организация</th>
                    <th>Статус</th>
                    <th>Администратор</th>
                    <th>Суперадминистратор</th>
                    <th>Дата регистрации</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->organization ?? '-' }}</td>
                    <td>
                        @if($user->active)
                            <span class="badge bg-success">Активен</span>
                        @else
                            <span class="badge bg-danger">Неактивен</span>
                        @endif
                    </td>
                    <td>
                        @if($user->admin)
                            <span class="badge bg-primary">Да</span>
                        @else
                            <span class="badge bg-secondary">Нет</span>
                        @endif
                    </td>
                    <td>
                        @if($user->super_admin)
                            <span class="badge bg-warning">Да</span>
                        @else
                            <span class="badge bg-secondary">Нет</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                    <td style="min-width: 200px;">
                        @if(auth()->check() && auth()->user()->isSuperAdmin())
                            @if($user->isSuperAdmin())
                                <span class="badge bg-warning">Суперадмин</span>
                            @elseif($user->id === auth()->id())
                                <span class="badge bg-info">Вы</span>
                            @else
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="admin" 
                                                       value="1"
                                                       id="admin_{{ $user->id }}"
                                                       {{ $user->admin ? 'checked' : '' }} 
                                                       onchange="this.form.submit()">
                                                <label class="form-check-label" for="admin_{{ $user->id }}">Админ</label>
                                            </div>
                                        </form>
                                    </div>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                          style="display: inline-block;"
                                          onsubmit="return confirm('Вы уверены, что хотите удалить пользователя {{ $user->name }}? Это действие нельзя отменить!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Удалить пользователя">
                                            <i class="bi bi-trash-fill"></i> Удалить
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
