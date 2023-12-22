<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_cuti',
        'tgl_cuti',
        'tgl_kembali',
        'keperluan',
        'pengganti',
        'approve',
        'decline_reason',
        'approved_by',
        'is_known',
    ];
}
