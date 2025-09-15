<?php

namespace App\Http\Controllers;

use App\Models\AccountClient;
use App\Models\EventClient;
use App\Models\TrnSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AccountClientController;

class DashboardController extends Controller
{
    public function index(){
         $user = Auth::user();


         $data = [];
        if ($user->f_role == 1) {



            $data['jumlah_akun'] = AccountClient::where('is_corporate',1)->count();
            $data['jumlah_event'] = EventClient::where('is_corporate',1)->count();
            $data['jumlah_responden'] = TrnSurvey::whereNotNull('f_corporate_id')->count();


            $data['event_client'] = EventClient::where('is_corporate', 1)->when(Auth::user()->f_role !== 1, function ($query) {
            $query->where('f_corporate_id', Auth::user()->f_account_id);
        })->with('akun_client')->whereHas('akun_client',function($query){
           return $query->where('is_corporate', 1);
        }
        )->get();
            return view('dashboard', $data); // view untuk role 1
        }

        return redirect('/monitoring'); // redirect untuk role selain 1
    }
}
