<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acta extends Model
{
    use HasFactory;
    protected $table = "acta";
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
