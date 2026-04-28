<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        // dd(session()->all());
        // $foo = session('foo');
        // dd($foo);
        // $foo= session()->get('foo');
        // dd($foo);
        //dd(session()->all());
       // $foo= session('foo');
       // dd($foo);
        return view('login.index');
    }

    public function store(Request $request)
    {
        
       // $session= app()->make('session');
        $session= app('session');
        //$session->put('foo','bar'); // записываем данные  сессию
        session(['foo'=>'bar',
        'name'=>'Maks',]); // или такой вариант


        //dd($session);
        // $email =  $request->input('email');
        // $password =  $request->input('password');
        // $remember =  $request->boolean('remember');
        // dd( $email, $password, $remember );
        // // authenticate user
        //return  'Добро пожаловать!';
        //alert(__('Добро пожаловать!'));

        // if (true) {
            // return redirect()->back()->withInput();
        // }
        //return response()->redirecttoroute('user');
       // return redirect()->route('foo');
       return redirect('user');

    }
}
