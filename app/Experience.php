<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $table = 'experiences';
    protected $fillable = [ 'id', 'user_id', 'question_id',	'position_id', 'job_id', 'result_id', 'description', 'created_at', 'updated_at' ];

}
