<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;

class Atasan extends Model implements Authenticatable
{
    use HasFactory, AuthenticatableTrait;

    protected $guarded = ['id'];

    public function changePassword($newPassword)
    {
        $this->password = bcrypt($newPassword);
        $this->save();
    }
}
