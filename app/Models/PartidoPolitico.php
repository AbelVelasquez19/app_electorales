<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidoPolitico extends Model
{
    use HasFactory;
    protected $table = "partido_politico";
    protected $primaryKey = 'id';
}
