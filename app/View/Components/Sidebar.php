<?php

namespace App\View\Components;

use Closure;
use App\Models\SurveySetting;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }

    public function compose(View $view){



        $data = [];
       $roles =  Auth::user();

// Contoh: ambil nama group pertama
$role = $roles->groups->first()->id ?? null;



       if($role != 1){
        $surveySetting = SurveySetting::where('f_account_id', Auth::user()->f_account_id)
        ->first(['f_label_others', 'f_label_level1', 'f_label_level2', 'f_label_level3', 'f_label_level4', 'f_label_level5','f_label_level6','f_label_level7']);
    
        $data['label_others'] = json_decode($surveySetting->f_label_others, true);
        $data['label_level1'] = json_decode($surveySetting->f_label_level1, true);
        $data['label_level2'] = json_decode($surveySetting->f_label_level2, true);
        $data['label_level3'] = json_decode($surveySetting->f_label_level3, true);
        $data['label_level4'] = json_decode($surveySetting->f_label_level4, true);
        $data['label_level5'] = json_decode($surveySetting->f_label_level5, true);
        $data['label_level6'] = json_decode($surveySetting->f_label_level6, true);
        $data['label_level7'] = json_decode($surveySetting->f_label_level7, true);
       }

        $view->with('sidebarData', $data);
    }


}
