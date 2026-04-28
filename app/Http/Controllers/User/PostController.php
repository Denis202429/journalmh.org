<?php

namespace App\Http\Controllers\User;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->latest('created_at')
            ->paginate(12);

        return view('user.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('user.posts.create');
    }

    public function store(Request $request)
    {
        // $validated = validate($request->all(), Post::$rules);

        // $post = (new Post)->fillAttributes($validated);
        // $post->user_id = User::query()->value('id');
        // $post->save();
       
       // $validator =validator ($request->all(),);
       //if  ($validator->fails()){  } // можно так
       $validated =validator($request->all(),[
        'title'=>['required','string','max:100'],
        'content'=>['required','string','max:10000'],
        'published_at'=>['required','string','date'],
        'published'=>['nullable','boolean']
       ]);
    //  $post = Post::query()->firstOrCreate([  //firstOrCreate - ищем первые два поля в БД и если их нет то создаем запись 
    //     'user_id' => User::query()->value('id'),
    //     'title' => $validated['title'],[
    //         'content' => $validated['content'],
    //          'published_at' => new Carbon($validated['published_at']) ?? null, // значение по умолчанию
    //          'published' => $validated['published'] ?? false, // значение по умолчанию
    //     ]] );
    
//     use App\Models\Post;
//     use App\Models\User;
// for ($i=0;$i<99;$i++) {
//    Post::query()->create([   
//   'user_id' => User::query()->value('id'),
//   'title' => fake()->sentence(),
//   'content' =>fake()->paragraph(),
//   'published' => true, // значение по умолчанию
//   'published_at' => fake()->dateTimeBetween(now()->subYear(),now()), // значение по умолчанию
//        ] );
//    }   
//    echo 'ok' ;
  
    // $post = Post::query()->Create([  //firstOrCreate - ищем первые два поля в БД и если их нет то создаем запись 
    //     'user_id' => User::query()->value('id'),
    //     'title' => $validated['title'],
    //     'content' => $validated['content'],
    //     'published_at' => new Carbon($validated['published_at']) ?? null, // значение по умолчанию
    //     'published' => $validated['published'] ?? false, // значение по умолчанию
    //     ])->validate();
        // $validatedData = $validator->validate();
    // $validated = $request->validate([   // можно сделать валидацию через оюьект реквест
    //         'title'=>['required','string','max:100'],
    //      'content'=>['required','string','max:10000']
    // ]);
     //  dd($post->toArray());
       //  alert(__('Сохранено!'));

        // return redirect()->route('user.posts.show', $post);
     //  $title = $request->input('title');
      // $content = $request->input('content');
      return redirect()->route('user.posts.show',123);
      //  dd($title,$content);
    }

    public function show(Post $post)
    {
        return view('user.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('user.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = validate($request->all(), Post::$rules);

        $post->fillAttributes($validated)->save();

        alert(__('Сохранено!'));

        return back();
    }

    public function delete($post)
    {
        return redirect()->route('user.posts');
    }
}
