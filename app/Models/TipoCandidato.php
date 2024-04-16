<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCandidato extends Model
{
    use HasFactory;

    protected $table = "tipo_candidato";
    protected $primaryKey = 'id';
}
