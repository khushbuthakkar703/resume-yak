<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'job';
    protected $fillable = [
                            'id',
                            'user_id',
                            'position_id',
                            'title_id',
                            'company',
                            'position',
                            'city',
                            'state',
                            'country',
                            'start_month',
                            'start_year',
                            'end_month',
                            'end_year',
                            'created_at',
                            'updated_at',
                        ];

}
