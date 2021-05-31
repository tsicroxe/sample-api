<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'dob', 'phone'];


    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

}
