<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    protected $table = 'resumes';
    protected $fillable = [
        'user_id',
        'position_id',
        'title_id',
        'name',
        'middle_initial',
        'address_display',
        'email',
        'address',
        'phone',
        'profile',
        'website',
        'headline_type',
        'hide_headline',
        'headline_endorsement_id',
        'employment_date',
        'employment_locat',
        'employment_desc',
        'employment_emp',
        'education_date',
        'education_location',
        'education_emphas',
        'expand_experienc',
        'type_assessment',
        'chronological_scor',
        'type',
        'last_step',
        'template_id',
        'color_id',
        'font_id',
        'heading_font_size',
        'subheading_font_s',
        'body_font_size',
        'top_bottom_margi',
        'left_right_margin',
        'heading_spacing',
        'subheading_spacin',
        'body_text_spacing',
        'indent_spacing',
    ];

}
