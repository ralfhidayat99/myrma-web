<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    use HasFactory;
    protected $table = 'cuti_jenis';

    protected $fillable = [
        'name',
        'jml_cuti',
    ];
}
