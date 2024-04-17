<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroVotacionSupervisor extends Model
{
    use HasFactory;

    protected $table = "centro_votacion_supervisor";
    protected $primaryKey = 'id';
}
