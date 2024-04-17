<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersoneroMesa extends Model
{
    use HasFactory;
    protected $table = "personero_mesa";
    protected $primaryKey = 'id';
}
