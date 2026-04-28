<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return redirect('/')->with('error', 'Доступ запрещен');
        }
        
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }
    
    public function updateAdminStatus(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return redirect('/')->with('error', 'Доступ запрещен');
        }
        
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Вы не можете изменить свои собственные права');
        }
        
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Нельзя изменять права суперадминистратора');
        }
        
        $user->admin = $request->input('admin') == '1' || $request->has('admin') ? 1 : 0;
        $user->save();
        
        $message = $user->admin ? 'Права администратора назначены' : 'Права администратора сняты';
        return back()->with('success', $message);
    }
    
    public function updateCorrectorStatus(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return redirect('/')->with('error', 'Доступ запрещен');
        }
        
        $user = User::findOrFail($id);
        
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Нельзя изменять права суперадминистратора');
        }
        
        $user->corrector = $request->input('corrector') == '1' || $request->has('corrector') ? 1 : 0;
        $user->save();
        
        $message = $user->corrector ? 'Статус корректора назначен' : 'Статус корректора снят';
        return back()->with('success', $message);
    }
    
    public function destroy($id)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return redirect('/')->with('error', 'Доступ запрещен');
        }
        
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Вы не можете удалить самого себя');
        }
        
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Нельзя удалить суперадминистратора');
        }
        
        $userName = $user->name;
        $user->delete();
        
        return back()->with('success', "Пользователь {$userName} успешно удален");
    }
}
