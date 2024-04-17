<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroVotacion extends Model
{
    use HasFactory;


    protected $table = "centro_votacion";
    protected $primaryKey = 'id';


}
