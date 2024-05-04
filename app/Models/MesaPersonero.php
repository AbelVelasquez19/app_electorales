<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesaPersonero extends Model
{
    use HasFactory;
    protected $table = "personero_mesa";
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
