<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    //protected $table = 'foobar';
    // можем указать название таблицы в БД для этой модели 
   //protected $primaryKey - 'uuid' ;
 // можем указать название первичного ключа

    public $incrementing = false;
    // отключаем автоинкремент для первичного ключа
    // для того чтобы у нас отображалась строка валюты
  //  protected $connection ='second'; // здесь мы можем указать к какой базе данных подключаемся 
  protected $fillable = [   // указываем доступные поля в таблице 

    'id', 'name',   'price',
    'active',  'sort',

  ];


  protected $guarded =[]; // здесь указываем поля которые мы не должны пропускать
  protected $hidden =[];  // указывем скрытые поля

 protected $casts = [  // указываем какие типы для полей 
 
      'price' => 'float' ,
      'active'=> 'boolean' ,
       'sort'=> 'integer' ,
       // 'secret'=> 'encrypted' , // этот тип данных будет автоматически зашифровыватся и разсшыфровыватся 
  ];
 // protected $dates = [] // 
 // здесь если мы укажем поля то они потом будут автоматом преобразованы в обьекты класса Carbon  
}
