<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        // $validated = $request->validate([
        //     'search' => ['nullable', 'string', 'max:50'],
        //     'from_date' => ['nullable', 'string', 'date'],
        //     'to_date' => ['nullable', 'string', 'date', 'after:from_date'],
        //     'tag' => ['nullable', 'string', 'max:10'],
        // ]);

        // $query = Post::query()
        //     ->where('published', true)
        //     ->whereNotNull('published_at');

        // if ($search = $validated['search'] ?? null) {
        //     $query->where('title', 'like', "%{$search}%");
        // }

        // if ($fromDate = $validated['from_date'] ?? null) {
        //     $query->where('published_at', '>=', new Carbon($fromDate));
        // }

        // if ($toDate = $validated['to_date'] ?? null) {
        //     $query->where('published_at', '<=', new Carbon($toDate));
        // }

        // if ($tag = $validated['tag'] ?? null) {
        //     $query->whereJsonContains('tags', $tag);
        // }
      
         //$posts =Post::all();
        // $page = $validated['page'] ?? 1;
        // $limit = $validated['limit'] ?? 12;
        // $offset = $limit *($page -1);
        // $posts = Post::query()->limit($limit)->offset($offset)->get();

        // select * from posts order by published_at
       //  $posts = Post::query()->orderBy('published_at','desc')->paginate(12);
       $categories= [
        null => __('Все категории'),
        1 => __('Первая категории'),
        2 => __('Вторая категория'),
       ];

       $posts = Post::query()->latest('published_at')->paginate(12);

//        $posts = Post::query()
//         ->where('id','=',5)
//        ->paginate(12);

//        $posts = Post::query()
//        ->where('published','=',true) 
//        ->where('published',true) // или так  
//       ->paginate(12);

//       $posts = Post::query()
//       ->whereColumn('title','content') // выведет только посты где две колонки равны
//       ->paginate(12);

//       $posts = Post::query()
//       ->where('id','>',5)
//      ->paginate(12);

//      $search = 'dolor';
       
//       $posts = Post::query()
//        ->where('title','like',"%{$search}%")   // выведет только посты где 'title' содержит $search
//        ->paginate(12);       // 'like' ищет не учитывая регистр слов

//     //    $posts = Post::query()
//     //  //  ->where('published_at','=',null) // где поле пустое
//     //   // ->whereNull('published_at')     // где поле пустое
//     //    ->whereNotNull('published_at')     // где поле не пустое
//     //    ->paginate(12)     
//     //    ->toSql(); //возвращает строку эскюэл запроса

//     //   $posts = Post::query()
//     //   ->where('title','like',"dolor")   // выведет только посты где 'title' содержит $search
//     //   ->paginate(12);       // 'like' ищет не учитывая регистр слов

// // where id in {1,2,4}
//       $posts = Post::query()
//     //  ->where('published_at','=',null)
//      // ->whereNull('published_at')   
//    //   ->whereIn('id',[2,3,4,5,6,7])    
//       ->whereNotIn('id',[2,3,4,5,6,7])    
//       ->paginate(12);     
    
//     //   $posts = Post::query()
//        // ->whereDate('published_at', new Carbon('2023-01-17'))    
//         //->whereDate('published_at', '2023-01-17')    
//     //    ->whereYear('published_at', 2022 )    
//     //    ->whereMonth('published_at', 2)
//     //    ->whereDay('published_at', 12)
//     //     ->paginate(12);     

//     $posts = Post::query()
//      //->whereBetween('id',[1,5])  // выбираем посты которые между [1,5])

//      ->whereBetween('published_at',[new Carbon('2023-01-17'),new Carbon('2023-03-17')])  
//       ->paginate(12);     
 
//       $posts = Post::query()
//         ->where('published',true)
//         ->whereNotNull('published_at')   // два оператора соединятся AND а третий оператор с OR
//         ->orWhere('id',10)    
//         ->paginate(12);     
// // далее идет реализация скобок
//       $posts = Post::query()
//         ->where('published',true)
//         ->where( function (Builder $query) {
//             $query->Where('id',10)          
//                   ->whereNotNull('published_at');
//         })         
//                                            // два оператора соединятся AND а третий оператор с OR
//           // "select * from `posts` where `published` = ? and (`id` = ? and `published_at` is not null)"
//         ->toSql();     
//        dd( $posts);

      // $posts = Post::query()->oldest('published_at')->paginate(12);
        // $posts = Post::query()->paginate($limit);
        //  $posts = Post::query()->limit(12)->get();
        //     ->paginate(12);
        // select * from posts
        //$post = Post::all(); 
       // $post = Post::query()->get(); // то же самое что Post::all(); 
        // select id, title from posts
   //  $post = Post::all(['id','title']);  // получаем только те данные которые указаны  в массиве

    //  dd($post->toArray()); 
      // dd($post->getAttribute('title')); 
      // dd($post->title);
        return view('blog.index', compact('posts','categories'));
    }

    public function show(Request $request, Post $post)
    {
 //  мы все что ниже вообще можем не писать а просто привязать модел к посту
 //  этот метод называется роут модель биндинг

        //$post = Post::query()->latest('id')->first();  
        //  при использовании >first() надо всегда указывать сортировку
        // first() возвращает первый попавшийся пост из базы данных
    //     $post = Post::query()
    //   //  ->where('user_id',1234)
    //     ->oldest('published_at') 
    //     ->firstOrFail(['id','title']);  // если запись не найдена то выдает ошибку 
     // $post = Post::query()->find($post,['id','title','content']);
      // ищем посты с идентификатором $post и с полями перечисленными во втором параметре
    //  $post = Post::query()->findOrFail($post,['id','title','content']);

      // $post = Post::query()->find([20,33,44]); // вернет массив постов с идентификаторами [2,3,4]
       //$post = Post::query()->findorFail($post);
       // if (is_null($post)) // если записей не найдено то возвращаем ошибку 
        // {abort(404);}
      // dd($post->toArray());
        return view('blog.show', compact('post'));
    }

    public function like($post)
    {
        return 'Поставить лайк';
    }
}
