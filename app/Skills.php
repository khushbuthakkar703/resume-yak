<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skills extends Model
{
    protected $table = 'skills';
    protected $fillable = ['name', 'unspsc_category', 'weak', 'is_active'];
}
