<?php
// app/Models/SurveySetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveySetting extends Model
{
    protected $table = 'survey_setting';

    protected $primaryKey = 'f_account_id';

    protected $fillable = [
        'f_account_id',
        'f_setting',
        'f_page_welcome',
        'f_page_howto',
        'f_page_thanks',
        'f_page_personal_leader',
        'f_page_desire_leader',
        'f_page_personal',
        'f_page_current',
        'f_page_desire',
        'f_page_current2',
        'f_page_desire2',
        'f_page_current3',
        'f_page_desire3',
        'f_page_leaderc',
        'f_page_leaderd',
        'f_page_qopen',
        'f_page_logo',
        'f_demo_view',
        'f_label_level1',
        'f_label_level2',
        'f_label_level3',
        'f_label_level4',
        'f_label_level5',
        'f_label_level6',
        'f_label_level7',
        'f_label_others',
        'f_template',
        'url',
        'f_language',
        'sesi',
        'f_page_created_on',
        'f_page_created_by',
        'f_page_updated_on',
        'f_page_updated_by',
    ];


    public $timestamps = false;

}
