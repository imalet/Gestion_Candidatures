<?php

namespace App\Models;

use App\Models\Formation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    use HasFactory;

    public function formation(){
        return $this->belongsTo(Formation::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
