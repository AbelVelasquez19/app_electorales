<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cantidato extends Model
{
    use HasFactory;
    protected $table = "candidatos";
    protected $primaryKey = 'id';

    protected $guarded = ['id'];




}
