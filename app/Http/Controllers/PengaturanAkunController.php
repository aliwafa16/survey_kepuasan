<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ListLogin;
use App\Models\MasterNip;
use Illuminate\Http\Request;
use App\Models\SurveySetting;
use App\Models\ListMonitoring;
use App\Imports\MasterNipImport;
use App\Jobs\ImportMasterNipJob;
use App\Models\DemografiSetting;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengaturanAkunController extends Controller
{
    public function akun(Request $request)
    {
        // Cek session login
        $data = [
            'title' => "setting akun",
        ];

        // Ambil data setting demografi
        $data['setting_demografi'] = DemografiSetting::where('f_account_id', Auth::user()->f_account_id)->first();
        $data['setting_monitoring'] = ListMonitoring::where('f_account_id', Auth::user()->f_account_id)->first();
        $data['setting_create_user'] = ListLogin::where('f_account_id', Auth::user()->f_account_id)->first();

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


        $data['page_welcome'] = json_decode($surveySetting->f_page_welcome, true);
        $data['page_howto'] = json_decode($surveySetting->f_page_howto, true);
        $data['page_thanks'] = json_decode($surveySetting->f_page_thanks, true);

        $data['settings'] = Setting::where('id_corporate', Auth::user()->f_account_id)->first();


        $data['demografi_view'] = $surveySetting->f_demo_view;


        $data['account_id'] = Auth::user()->f_account_id;
        $data['responden'] = MasterNip::with([
            'relasi_gender',
            'relasi_umur',
            'relasi_masa_kerja',
            'relasi_wilayah',
            'relasi_jabatan',
            'relasi_pendidikan',
            'relasi_level1',
            'relasi_level2',
            'relasi_level3',
            'relasi_level4',
            'relasi_level5',
            'relasi_level6',
            'relasi_level7',
        ])->where('id_account', Auth::user()->f_account_id)->paginate(10);

        // echo json_encode($data['responden']);die();



        // Master data
        return view('survey_setting.akun', $data);
    }


    public function save_setting(Request $request)
    {

        // echo json_encode($request->all());die();


        // cek apakah tipe data demografi & key nya sudah sesuai
        if($request->input('f_demo_view') == 2 && $request->input('is_aktif_f_nip') == 0){
        return redirect()->back()->with('error', 'Data gagal disimpan. Tipe data demografi & key tidak sesuai!');

        }


        // Proses save data
        $settingDemografi = DemografiSetting::where('f_account_id', Auth::user()->f_account_id)->first();
        $columnsDemografi = array_keys($settingDemografi->getAttributes());
        $updateDemografi = [];
        foreach ($columnsDemografi as $key => $value) {
            $string = "is_aktif_" . $value;
            $updateDemografi[$value] = $request->has($string) ? 1 : 0;
        }

        $updateDemografi['f_account_id'] = $request->input('is_aktif_f_account_id');
        $settingDemografi->where('f_account_id', $updateDemografi['f_account_id'])->update($updateDemografi);



        $settingMonitoring = ListMonitoring::where('f_account_id', Auth::user()->f_account_id)->first();
        $columnsMonitoring = array_keys($settingMonitoring->getAttributes());
        $updateMonitoring = [];
        foreach ($columnsMonitoring as $key => $value) {
            $string = "is_aktif_" . $value."_monitoring";
            $updateMonitoring[$value] = $request->has($string) ? 1 : 0;
        }
        // echo json_encode($updateMonitoring);die();


        $updateMonitoring['f_account_id'] = $request->input('is_aktif_f_account_id');
        unset($updateMonitoring['id']); // hindari update ke kolom pencarian

        // echo json_encode($updateMonitoring);die();
        ListMonitoring::where('f_account_id',  $request->input('is_aktif_f_account_id'))->update($updateMonitoring);

        // $settingDemografi->where('f_account_id', $updateDemografi['f_account_id'])->update($updateDemografi);
        // ListMonitoring::updateOrCreate(
        //     ['f_account_id' => $updateMonitoring['f_account_id']],
        //     $updateMonitoring
        // );



        // Proses save data crateuser
        // $settingCreateUser = ListLogin::where('f_account_id', Auth::user()->f_account_id)->first();
        // $columnsCreateUser = array_keys($settingCreateUser->getAttributes());
        // $updateCreateUser = [];
        // foreach ($columnsCreateUser as $key => $value) {
        //     $string = "is_create_user_".$value;
        //     $updateCreateUser[$value] = $request->has($string) ? 1 : 0;
        // }

        // $updateCreateUser['f_account_id'] = $request->input('is_aktif_f_account_id');
        // $settingCreateUser->where('f_account_id', $updateCreateUser['f_account_id'])->update($updateCreateUser);


        // Proses save data survey setting label
        $settingSurvey = SurveySetting::where('f_account_id', Auth::user()->f_account_id)->first();
        $label_others = json_decode($settingSurvey->f_label_others, true);


        $labelOther = [];
        foreach ($label_others as $key => $value) {

            $string_label_id = 'label_id_' . $key;
            $string_label_en = 'label_en_' . $key;
            $string_label_my = 'label_my_' . $key;

            $labelOther[$key]["indonesian"] = $request->input($string_label_id);
            $labelOther[$key]["english"] = $request->input($string_label_en);
            $labelOther[$key]["malaysia"] = $request->input($string_label_my);


        }


        $label_level1 = [];
        $label_level2 = [];
        $label_level3 = [];
        $label_level4 = [];
        $label_level5 = [];
        $label_level6 = [];
        $label_level7 = [];


        for ($i = 1; $i <= 7; $i++) {
            $varName = 'label_level' . $i;

            $$varName = [
                'indonesian' => $request->input("id_label_level$i"),
                'english' => $request->input("en_label_level$i"),
                'malaysia' => $request->input("my_label_level$i"),
            ];
        }


        $updatedSurveySetting = [
            'f_label_others' => json_encode($labelOther),
            'f_label_level1' => json_encode($label_level1),
            'f_label_level2' => json_encode($label_level2),
            'f_label_level3' => json_encode($label_level3),
            'f_label_level4' => json_encode($label_level4),
            'f_label_level5' => json_encode($label_level5),
            'f_label_level6' => json_encode($label_level6),
            'f_label_level7' => json_encode($label_level7),
        ];
        
        $updatedSurveySetting['f_demo_view'] = $request->input('f_demo_view');
        $settingSurvey->update($updatedSurveySetting);

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan!')->with('tab', 'demografi');

    }

    public function save_halaman_setting(Request $request)
    {
        $settingSurvey = SurveySetting::where('f_account_id', Auth::user()->f_account_id)->first();



        $welcome = [
            'title' => $request->input('f_page_welcome_title'),
            'content' => $request->input('f_page_welcome_content'),
        ];

        $howto = [
            'title' => $request->input('f_page_howto_title'),
            'content' => $request->input('f_page_howto_content'),
        ];

        $thanks = [
            'title' => $request->input('f_page_thanks_title'),
            'content' => $request->input('f_page_thanks_content'),
        ];


        $settingSurvey->f_page_welcome = json_encode($welcome);
        $settingSurvey->f_page_howto = json_encode($howto);
        $settingSurvey->f_page_thanks = json_encode($thanks);

        $settingSurvey->save();
        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan!')->with('tab', 'halaman-setting');

    }

    public function import_responden(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        // Simpan file ke disk sementara
        // $filePath = $request->file('file')->store('private/imports', 'local');

        // // Dispatch job ke queue
        // ImportMasterNipJob::dispatch($filePath);

        Excel::import(new MasterNipImport, $request->file('file'));


        return response()->json([
            'success' => true
        ]);
    }

    public function responden(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('t_master_nip')
                ->leftJoin('table_level_position1', 't_master_nip.f_level1', '=', 'table_level_position1.f_id')
                ->leftJoin('table_level_position2', 't_master_nip.f_level2', '=', 'table_level_position2.f_id')
                ->leftJoin('table_level_position3', 't_master_nip.f_level3', '=', 'table_level_position3.f_id')
                ->leftJoin('table_level_position4', 't_master_nip.f_level4', '=', 'table_level_position4.f_id')
                ->leftJoin('table_level_position5', 't_master_nip.f_level5', '=', 'table_level_position5.f_id')
                ->leftJoin('table_level_position6', 't_master_nip.f_level6', '=', 'table_level_position6.f_id')
                ->leftJoin('table_level_position7', 't_master_nip.f_level7', '=', 'table_level_position7.f_id')



                ->leftJoin('table_level_work', 't_master_nip.f_level_of_work', '=', 'table_level_work.f_id') // Jabatan
                ->leftJoin('table_length_of_service', 't_master_nip.f_length_of_service', '=', 'table_level_work.f_id') // Masa kerja
                ->leftJoin('table_age', 't_master_nip.f_age', '=', 'table_age.f_id') // Umur
                ->leftJoin('table_gender', 't_master_nip.f_gender', '=', 'table_gender.f_gender_id') // Gender
                ->leftJoin('table_pendidikan_account', 't_master_nip.f_pendidikan', '=', 'table_pendidikan_account.f_id') // pendidikan
                ->leftJoin('table_region', 't_master_nip.f_region', '=', 'table_region.f_id') // Region


                ->where('t_master_nip.id_account', Auth::user()->f_account_id)
                ->select([
                    't_master_nip.id as id',
                    't_master_nip.nip',
                    't_master_nip.tanggal_lahir',
                    't_master_nip.f_survey_valid',
                    't_master_nip.f_survey_date',

                    'table_level_position1.f_position_desc as level1_desc',
                    'table_level_position2.f_position_desc as level2_desc',
                    'table_level_position3.f_position_desc as level3_desc',
                    'table_level_position4.f_position_desc as level4_desc',
                    'table_level_position5.f_position_desc as level5_desc',
                    'table_level_position6.f_position_desc as level6_desc',
                    'table_level_position7.f_position_desc as level7_desc',


                    'table_level_work.f_levelwork_desc as levelwork_desc',
                    'table_length_of_service.f_service_desc as f_service_desc',
                    'table_age.f_age_desc as f_age_desc',
                    'table_gender.f_gender_name as f_gender_desc',
                    'table_pendidikan_account.f_name as f_pendidikan_desc',
                    'table_region.f_region_name as f_region_desc',

                ]);



            return DataTables::of($query)
    ->addIndexColumn()

    // Kolom Checkbox
    ->addColumn('checkbox', function ($row) {
        return '<input type="checkbox" class="row-checkbox"
            data-id="' . sha1(md5($row->id)) . '"
            data-level1="' . e($row->level1_desc ?? '') . '"
            data-level2="' . e($row->level2_desc ?? '') . '"
            data-level3="' . e($row->level3_desc ?? '') . '"
            data-levelwork="' . e($row->levelwork_desc ?? '') . '" />';
    })

    // Kolom yang akan ditampilkan di frontend (cocok dengan `columns[]`)
    ->editColumn('nip', fn($row) => $row->nip ?? '-')
    ->editColumn('tanggal_lahir', fn($row) => $row->tanggal_lahir ?? '-')
    ->editColumn('f_survey_valid', fn($row) => $row->f_survey_valid ?? '-')
    ->editColumn('f_survey_date', fn($row) => $row->f_survey_date ?? '-')
    ->editColumn('f_gender_desc', fn($row) => $row->f_gender_desc ?? '-')
    ->editColumn('f_age_desc', fn($row) => $row->f_age_desc ?? '-')
    ->editColumn('f_service_desc', fn($row) => $row->f_service_desc ?? '-')
    ->editColumn('f_region_desc', fn($row) => $row->f_region_desc ?? '-')
    ->editColumn('f_pendidikan_desc', fn($row) => $row->f_pendidikan_desc ?? '-')
    ->editColumn('levelwork_desc', fn($row) => $row->levelwork_desc ?? '-')


    ->editColumn('level1_desc', fn($row) => $row->level1_desc ?? '-')
    ->editColumn('level2_desc', fn($row) => $row->level2_desc ?? '-')
    ->editColumn('level3_desc', fn($row) => $row->level3_desc ?? '-')
    ->editColumn('level4_desc', fn($row) => $row->level4_desc ?? '-')
    ->editColumn('level5_desc', fn($row) => $row->level5_desc ?? '-')
    ->editColumn('level6_desc', fn($row) => $row->level4_desc ?? '-')
    ->editColumn('level7_desc', fn($row) => $row->level5_desc ?? '-')

    // Filter kolom
    ->filterColumn('nip', function ($query, $keyword) {
        $query->where('t_master_nip.nip', 'like', "%{$keyword}%");
    })
    ->filterColumn('f_gender_desc', function ($query, $keyword) {
        $query->where('table_gender.f_gender_name', 'like', "%{$keyword}%");
    })
    ->filterColumn('f_age_desc', function ($query, $keyword) {
        $query->where('table_age.f_age_desc', 'like', "%{$keyword}%");
    })
        ->filterColumn('f_service_desc', function ($query, $keyword) {
        $query->where('table_length_of_service.f_service_desc', 'like', "%{$keyword}%");
    })
    ->filterColumn('f_region_desc', function ($query, $keyword) {
        $query->where('table_region.f_region_name', 'like', "%{$keyword}%");
    })
        ->filterColumn('levelwork_desc', function ($query, $keyword) {
        $query->where('table_level_work.f_levelwork_desc', 'like', "%{$keyword}%");
    })
    ->filterColumn('f_pendidikan_desc', function ($query, $keyword) {
        $query->where('table_pendidikan_account.f_name', 'like', "%{$keyword}%");
    })
    ->filterColumn('level1_desc', function ($query, $keyword) {
        $query->where('table_level_position1.f_position_desc', 'like', "%{$keyword}%");
    })
        ->filterColumn('level2_desc', function ($query, $keyword) {
        $query->where('table_level_position2.f_position_desc', 'like', "%{$keyword}%");
    })
        ->filterColumn('level3_desc', function ($query, $keyword) {
        $query->where('table_level_position3.f_position_desc', 'like', "%{$keyword}%");
    })
        ->filterColumn('level4_desc', function ($query, $keyword) {
        $query->where('table_level_position4.f_position_desc', 'like', "%{$keyword}%");
    })
        ->filterColumn('level5_desc', function ($query, $keyword) {
        $query->where('table_level_position5.f_position_desc', 'like', "%{$keyword}%");
    })
        ->filterColumn('level6_desc', function ($query, $keyword) {
        $query->where('table_level_position6.f_position_desc', 'like', "%{$keyword}%");
    })
        ->filterColumn('level7_desc', function ($query, $keyword) {
        $query->where('table_level_position7.f_position_desc', 'like', "%{$keyword}%");
    })


    // Kolom Aksi
    // ->addColumn('action', function ($row) {
    //     return $row->f_survey_valid === 'yes'
    //         ? '<a href="https://talentdna.me/tdna/trx_survey/fnTrx_surveyDetail/' . e($row->f_email) . '" class="text-blue-600 underline">Download</a>'
    //         : '';
    // })

    // Kolom HTML
    ->rawColumns(['checkbox', 'action'])

    ->make(true);

        }
    }


    public function download_template(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        $sheet->fromArray([
        'nip',
        'name',
        'tanggal_lahir',
        'survey_date',
        'type',
        'gender',
        'age',
        'length_of_service',
        'region',
        'level_of_work',
        'level1',
        'level2',
        'level3',
        'level4',
        'level5',
        'level6',
        'level7',
        'custom1',
        'custom2',
        'custom3',
        'custom4',
        'pendidikan'], null, 'A1');

        $writer = new Xlsx($spreadsheet);
        $filename = 'template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}