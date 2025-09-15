<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Level1;
use App\Models\Level2;
use App\Models\Level3;
use App\Models\Level4;
use App\Models\Level5;
use App\Models\Level6;
use App\Models\Level7;
use Hash;
use Illuminate\Http\Request;
use App\Models\SurveySetting;
use App\Models\DemografiSetting;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserClientController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Management User Client',
            'btn_add' => "Tambah data"
        ];


        $data['user_client'] = User::where('f_account_id', Auth::user()->f_account_id)
        ->where('f_role', 2)
        ->get();
    
    // Ambil semua Level1 yang digunakan di user_client (berdasarkan f_level1 di nosj)
    $usedLevel1Ids = $data['user_client']
        ->map(function ($user) {
            $nosj = json_decode($user->nosj, true);
            return $nosj['f_level1'] ?? null;
        })
        ->filter()
        ->unique()
        ->values()
        ->toArray();
    
    $data['level1_map'] = Level1::whereIn('f_id', $usedLevel1Ids)
        ->get()
        ->keyBy('f_id'); // supaya bisa diakses cepat lewat ID

        
        return view('user_client.user_client', $data);
    }


    public function add()
    {
        $data = [
            'title' => 'Add User Client',
            'btn_add' => "Tambah data"
        ];

        // Ambil data setting demografi
        $data['setting_demografi'] = DemografiSetting::where('f_account_id', Auth::user()->f_account_id)->first();

        $data['level1'] = $data['setting_demografi']['f_level1'] ? Level1::where('f_account_id', Auth::user()->f_account_id)->get() : collect();
        $data['level2'] = $data['setting_demografi']['f_level2'] ? Level2::where('f_account_id', Auth::user()->f_account_id)->get() : collect();
        $data['level3'] = $data['setting_demografi']['f_level3'] ? Level3::where('f_account_id', Auth::user()->f_account_id)->get() : collect();
        $data['level4'] = $data['setting_demografi']['f_level4'] ? Level4::where('f_account_id', Auth::user()->f_account_id)->get() : collect();
        $data['level5'] = $data['setting_demografi']['f_level5'] ? Level5::where('f_account_id', Auth::user()->f_account_id)->get() : collect();
        $data['level6'] = $data['setting_demografi']['f_level6'] ? Level6::where('f_account_id', Auth::user()->f_account_id)->get() : collect();
        $data['level7'] = $data['setting_demografi']['f_level7'] ? Level7::where('f_account_id', Auth::user()->f_account_id)->get() : collect();


        // Ambil data setting bahasa / label survey
        $surveySetting = SurveySetting::where('f_account_id', Auth::user()->f_account_id)->first();

        $data['label_others'] = json_decode($surveySetting->f_label_others, true);
        $data['label_level1'] = json_decode($surveySetting->f_label_level1, true);
        $data['label_level2'] = json_decode($surveySetting->f_label_level2, true);
        $data['label_level3'] = json_decode($surveySetting->f_label_level3, true);
        $data['label_level4'] = json_decode($surveySetting->f_label_level4, true);
        $data['label_level5'] = json_decode($surveySetting->f_label_level5, true);
        $data['label_level6'] = json_decode($surveySetting->f_label_level6, true);
        $data['label_level7'] = json_decode($surveySetting->f_label_level7, true);



        return view('user_client.add', $data);
    }


    public function store(Request $request)
    {

        try {
            DB::transaction(function () use ($request) {
                $request->validate([
                    'username' => 'required|string|max:255',
                    'email' => 'required|string|max:255',
                    'password' => 'required|string|max:20',
                ]);


                $levels = [
                    'f_level1' => $request->f_level1 ?? null,
                    'f_level2' => array_filter($request->input('f_level2', [])),
                    'f_level3' => array_filter($request->input('f_level3', [])),
                    'f_level4' => array_filter($request->input('f_level4', [])),
                    'f_level5' => array_filter($request->input('f_level5', [])),
                    'f_level6' => array_filter($request->input('f_level6', [])),
                    'f_level7' => array_filter($request->input('f_level7', [])),
                ];
                
                // Konversi ke JSON untuk disimpan
                $levelJson = json_encode($levels);

                $createUser = User::create([
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'f_account_id' => Auth::user()->f_account_id,
                    'nosj' => $levelJson,
                    'active' => 1,
                    'f_role' => 2,
                ]);

            });
            return redirect()->back()->with('success', 'Berhasil tambah data');
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }

    }
}
