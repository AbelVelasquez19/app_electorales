<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodigoPais extends Model
{
    use HasFactory;
    protected $table = "codigo_pais";
    protected $primaryKey = 'id';
}
