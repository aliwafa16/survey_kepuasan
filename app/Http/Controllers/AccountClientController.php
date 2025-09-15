<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TrnSurvey;
use App\Models\UserGroups;
use Str;
use App\Models\User;
use App\Models\ListDemo;
use Illuminate\Http\Request;
use App\Models\AccountClient;
use App\Models\ListMonitoring;
use App\Models\SurveySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountClientController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Management Account Client',
            'btn_add' => "Tambah data"
        ];


        $data['account_client'] = AccountClient::paginate();

        return view('account_client.account_client', $data);
    }


    public function add()
    {
        $data = [
            'title' => 'Add Account Client',
            'btn_add' => "Tambah data"
        ];

        return view('account_client.add', $data);
    }



    public function store(Request $request)
    {

        try {

            DB::beginTransaction();
            $request->validate([
                'f_account_name' => 'required|string|max:255',
                'f_account_contact' => 'required|string|max:255',
                'f_account_phone' => 'required|string|max:20',
                'f_account_email' => 'required|email|max:255',
                'f_user_password' => 'required',
            ]);

            $dataAccout = AccountClient::create([
                'f_account_name' => $request->input('f_account_name'),
                'f_account_contact' => $request->input('f_account_contact'),
                'f_account_phone' => $request->input('f_account_phone'),
                'f_account_email' => $request->input('f_account_email'),
                'f_account_noacc' => "ASSESMENTSKPPP" . Str::random(10),
                // 'f_account_logo'        => $request->input('f_account_logo'),
                // 'f_account_created_on'  => now(),
                'f_account_created_by' => "administrator",
                // 'f_account_updated_on'  => now(),
                'f_account_updated_by' => "administrator",
                'f_account_status' => 1,
                // 'f_account_token' => $request->input('f_account_token'),
            ]);


            // Create ke user
$dataUser = User::create([
    'ip_address'   => $request->ip(), // bisa ambil IP dari request
    'username'     => $request->input('f_account_name'),
    'email'        => $request->input('f_account_email'),
    'password'     => Hash::make($request->input('f_user_password')),
    'f_account_id' => $dataAccout->f_account_id,
    'active'       => 1,
    'created_on'   => now(),
    'updated_on'   => now(),
]);


// Crate ke group
UserGroups::create([
         'user_id'=>$dataUser->id,
        'group_id'=>2,
]);


            // Create demografi setting
            $dataDemografi = ListDemo::create([
                'f_account_id' => $dataAccout->f_account_id,
                'f_gender' => 1,
                'f_age' => 1,
                'f_masakerja' => 1,
                'f_region' => 1,
                'f_level_of_work' => 1,
                'f_level1' => 1,
                'f_level2' => 1,
                'f_level3' => 1,
                'f_level4' => 1,
                'f_level5' => 1,
                'f_level6' => 1,
                'f_level7' => 1,
                'f_custom1' => 1,
                'f_custom2' => 1,
                'f_custom3' => 1,
                'f_custom4' => 1,
                'f_custom5' => 1,
                'f_custom6' => 1,
                'f_custom7' => 1,
                'f_custom8' => 1,
                'f_custom9' => 1,
                'f_custom10' => 1,
                'f_pendidikan' => 1,
            ]);

            // // Create demografi setting
            // $dataMonitoring = ListMonitoring::create([
            //     'f_account_id' => $dataAccout->f_account_id,
            //     'f_gender' => 1,
            //     'f_age' => 1,
            //     'f_masakerja' => 1,
            //     'f_region' => 1,
            //     'f_level_of_work' => 1,
            //     'f_level1' => 1,
            //     'f_level2' => 1,
            //     'f_level3' => 1,
            //     'f_level4' => 1,
            //     'f_level5' => 1,
            //     'f_level6' => 1,
            //     'f_level7' => 1,
            //     'f_custom1' => 1,
            //     'f_custom2' => 1,
            //     'f_custom3' => 1,
            //     'f_custom4' => 1,
            //     'f_custom5' => 1,
            //     'f_custom6' => 1,
            //     'f_custom7' => 1,
            //     'f_custom8' => 1,
            //     'f_custom9' => 1,
            //     'f_custom10' => 1,
            //     'f_pendidikan' => 1,
            // ]);


            // Create sruvey setting
            $template = SurveySetting::where('f_account_id', 99999)->first(); // ambil dari template

            $surveySetting = $template->replicate(); // Buat salinan semua field
            $surveySetting->f_account_id = $dataAccout->f_account_id; // Ubah akun ID target
            $surveySetting->save(); // Simpan sebagai data baru


            // Buat profile
            Setting::create([
                'logo' => NULL,
                'banner' => NULL,
                'color_primary' => NULL,
                'tangline' => NULL,
                'id_corporate' => $dataAccout->f_account_id,
            ]);

            DB::commit();
            return redirect()->route('account.index')->with('success', 'Berhasil tambah data');
        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }

    }


    public function edit($id)
    {
        $dataAccout = AccountClient::where('f_account_id', $id)->first();
        $data = [
            'title' => 'Add Account Client',
            'btn_add' => "Tambah data",
            'data_account' => $dataAccout
        ];

        return view('account_client.edit', $data);
    }


    public function update(Request $request)
    {

        try {

            DB::beginTransaction();
            $request->validate([
                'f_account_name' => 'required|string|max:255',
                'f_account_contact' => 'required|string|max:255',
                'f_account_phone' => 'required|string|max:20',
                'f_account_email' => 'required|email|max:255',
                // 'f_user_password' => 'required',
                'f_account_token' => 'required|string|max:255',
            ]);

            $dataAccout = AccountClient::where('f_account_id', $request->input('f_account_id'))->update([
                'f_account_name' => $request->input('f_account_name'),
                'f_account_contact' => $request->input('f_account_contact'),
                'f_account_phone' => $request->input('f_account_phone'),
                'f_account_email' => $request->input('f_account_email'),
                // 'f_account_noacc' => "TDNACORPORATE" . Str::random(10),
                // 'f_account_logo'        => $request->input('f_account_logo'),
                // 'f_account_created_on'  => now(),
                // 'f_account_created_by' => "administrator",
                // 'f_account_updated_on'  => now(),
                // 'f_account_updated_by' => "administrator",
                // 'f_account_status' => 1,
                'f_account_token' => $request->input('f_account_token'),
                // 'is_corporate' => 1
            ]);

            DB::commit();
            return redirect()->route('account.index')->with('success', 'Berhasil edit data');
        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }

    }


    public function destroy($id)
    {
        // Cek apakah ada transaksi di dalamnya
        $hasResponden = TrnSurvey::where('f_corporate_id', $id)->exists();

        if ($hasResponden) {
            return response()->json([
                'status' => false,
                'kode' => 403,
                'message' => 'Data tidak bisa dihapus. Masih ada transaksi survey'
            ]);
        }

        // Jika tidak ada transaksi, hapus akun corporate
        $akun = AccountClient::where('is_corporate', 1)
            ->where('f_account_id', $id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data corporate berhasil dihapus.'
        ]);
    }
}
