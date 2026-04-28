<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('Auth.login');
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember') && $request->input('remember') == '1';

        if (Auth::attempt($credentials, $remember)) {
            return redirect()->intended('/')->withSuccess('Signed in');
        }
        
        return redirect("login")->withSuccess('Login details are not valid');
    }

    public function registration()
    {
        $organizations = config('organizations.organizations', []);
        return view('Auth.Registration', compact('organizations'));
    }

    public function customRegistration(Request $request)
    { 
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'organization' => 'required|string',
        ]);
        $data = $request->all();
        $check = $this->create($data);
        
        Auth::login($check);
        
        return redirect(url('/'))->withSuccess('Вы успешно зарегистрировались');
    }

    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'organization' => $data['organization'] ?? null,
      ]);
    }   
  
    public function dashboard()
    {
        if(Auth::check()){
            return view('Auth.Dashboard');
        }
        return redirect("login")->withSuccess('are not allowed to access');
    }

    public function signOut() {
        Auth::logout();
        return view('Auth.login');
    }

    public function welcome_user()
     {
        $userName = Auth::user()->name;
        return view('Auth.welcome_user', ['userName' => $userName]);
     }
    

}
