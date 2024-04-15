<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corregimient extends Model
{
    use HasFactory;
    protected $table = "corregimiento";
    protected $primaryKey = 'id';
}
