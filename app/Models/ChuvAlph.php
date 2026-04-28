<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChuvAlph extends Model
{
    use HasFactory;

    protected $table = 'chuv_alph';

    // Укажите заполняемые поля
    protected $fillable = ['Word', 'Pos', 'Perv_sl'];
}
