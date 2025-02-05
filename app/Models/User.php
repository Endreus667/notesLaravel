<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function notes() {
        Return $this->hasMany(Note::class);
    }
}
