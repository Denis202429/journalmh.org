<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Parallel;

class TestController extends Controller
{
    public function index(Request $request) 
    {

        $parallel = Parallel::paginate(3);
      
      
        if($request->ajax()){
            return view('Test.article-pagination ', compact('parallel')); 
        } 
        return view('Test.article ', compact('parallel'));

       // return view('Test.index', compact('parallel') );
    }

    // public function __construct()
    // {
    //     $this->middleware('throttle:10');
    // }

    // public function __invoke(Request $request)
    // {
    //     // return 'Test;
    //     // return response('Test');

    //      return 'Test';
        
    //     //// return ['foo' => 'bar'];
    //    // return response()->json(['foo' => 'bar']);
    // }
}
