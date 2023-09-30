<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function country() : BelongsTo
    {
      return $this->BelongsTo(Country::class);
    }

    public function state() : BelongsTo
    {
      return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
      return $this->belongsTo(City::class);
    }

    public function department() : BelongsTo
    {
      return $this->belongsTo(Department::class);  
    }
}
