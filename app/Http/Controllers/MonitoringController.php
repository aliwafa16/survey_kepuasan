<?php

namespace App\Http\Controllers;

use App\Models\Usia;
use App\Models\Level1;
use App\Models\Level2;
use App\Models\Level3;
use App\Models\Level4;
use App\Models\Level5;
use App\Models\Setting;
use App\Models\Wilayah;
use App\Models\ListDemo;
use App\Models\LevelWork;
use App\Models\MasaKerja;
use App\Models\TrnSurvey;
use App\Models\EventClient;
use App\Models\JenisKelamin;
use Illuminate\Http\Request;
use App\Models\AccountClient;
use App\Models\SurveySetting;
use App\Models\ListMonitoring;
use App\Models\TingkatPekerjaan;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\ItemPernyataanModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($kode)
    {
        // echo Auth::user()->f_account_id;die();

        $event = EventClient::where('f_event_kode', $kode)->first();


        $req = request(); // atau injeksikan di method controller

$q = TrnSurvey::query()
    ->where('f_event_id', $event->f_event_id);

// ====== FILTER DEMOGRAFI (single select) ======
$q->when($req->filled('gender'),        fn($x) => $x->where('f_gender',           $req->input('gender')));
$q->when($req->filled('age'),           fn($x) => $x->where('f_age',              $req->input('age')));
$q->when($req->filled('masa_kerja'),    fn($x) => $x->where('f_length_of_service',$req->input('masa_kerja')));
$q->when($req->filled('region'),        fn($x) => $x->where('f_region',           $req->input('region')));
$q->when($req->filled('level_of_work'), fn($x) => $x->where('f_level_of_work',    $req->input('level_of_work')));
$q->when($req->filled('pendidikan'),    fn($x) => $x->where('f_pendidikan',       $req->input('pendidikan')));

// ====== FILTER LEVEL HIRARKI (jika ada field dinamis di form) ======
// Misal di form ada: level1, level2, ... level7 (single select semua)
foreach (range(1,7) as $i) {
    $param = "level{$i}";
    $col   = "f_level{$i}";
    $q->when($req->filled($param), fn($x) => $x->where($col, $req->input($param)));
}

// ====== RANGE TANGGAL RESPON ======
$q->when($req->filled('date_start'), fn($x) => $x->whereDate('f_survey_created_on', '>=', $req->date('date_start')));
$q->when($req->filled('date_end'),   fn($x) => $x->whereDate('f_survey_created_on', '<=', $req->date('date_end')));

// ====== KEYWORD (nama/email/IP) ======
$q->when($req->filled('q'), function ($x) use ($req) {
    $kw = $req->input('q');
    $x->where(function ($w) use ($kw) {
        $w->where('f_survey_username', 'like', "%{$kw}%")
          ->orWhere('f_survey_email',   'like', "%{$kw}%")
          ->orWhere('f_ip_address',     'like', "%{$kw}%");
    });
});

// ====== EKSEKUSI QUERY ======
$trnSurvey = $q->get();


        $itemPertanyaan = ItemPernyataanModel::all();

        $chartData = [];
        foreach (['Q2', 'Q3', 'Q4', 'Q5', 'Q6'] as $kode) {
            if ($data = $this->buildChartData($kode, $itemPertanyaan, $trnSurvey)) {
                $chartData[] = $data;
            }
        }

        // 1. Ambil pertanyaan open-ended (type=2)
        $openQuestions = $itemPertanyaan->where('type', 2);

        // 2. Kelompokkan jawaban responden per kode (Q7..Q12)
        $answersByCode = [];
        foreach ($trnSurvey as $row) {
            $payload = is_string($row->f_survey) ? json_decode($row->f_survey, true) : $row->f_survey;
            if (!is_array($payload))
                continue;

            foreach ($payload['distribusi_jawaban'] ?? [] as $ans) {
                if ((int) ($ans['jenis'] ?? 0) !== 2)
                    continue; // hanya type=2
                $code = $ans['code'] ?? null;
                $answer = trim((string) ($ans['nilai'] ?? ''));

                if ($code && $answer !== '') {
                    $answersByCode[$code][] = [
                        'user' => $row->f_survey_username ?? '-',
                        'email' => $row->f_survey_email ?? '-',
                        'answer' => $answer,
                    ];
                }
            }
        }
        $akun = AccountClient::where('f_account_id', $event->f_account_id)->first();



        $list_demografi = ListDemo::where('f_account_id', $akun->f_account_id)->first();

        $surveySetting = SurveySetting::where('f_account_id', $akun->f_account_id)->first();
        $setting_profile = Setting::where('id_corporate', $akun->f_account_id)->first();
        $demografi = array();
        $label_others = json_decode($surveySetting->f_label_others, true);

        if ($list_demografi->f_nama == 1) {
            $demografi['nama']["label"] = $label_others['nama'];
        } else {
            $demografi['nama']["label"] = NULL;
            $demografi['nama']["value"] = NULL;
        }

        if ($list_demografi->f_email == 1) {
            $demografi['email']["label"] = $label_others['email'];

        } else {
            $demografi['email']["label"] = NULL;
            $demografi['email']["value"] = NULL;

        }

        if ($list_demografi->f_nip == 1) {
            $demografi['nip']["label"] = $label_others['nip'];
        } else {
            $demografi['nip']["label"] = NULL;
            $demografi['nip']["value"] = NULL;
        }


        if ($list_demografi->f_gender == 1) {
            $demografi['gender']["label"] = $label_others['gender'];
            $demografi['gender']["value"] = JenisKelamin::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $demografi['gender']["label"] = NULL;
            $demografi['gender']["value"] = NULL;
        }

        if ($list_demografi->f_age == 1) {
            $demografi['age']['label'] = $label_others['age'];

            $demografi['age']['value'] = Usia::where('f_account_id', $akun->f_account_id)->get();
        } else {

            $demografi['age']["label"] = NULL;
            $demografi['age']["value"] = NULL;
        }

        if ($list_demografi->f_masakerja == 1) {
            $demografi['masa_kerja']['label'] = $label_others['mk'];
            $demografi['masa_kerja']['value'] = MasaKerja::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $demografi['masa_kerja']["label"] = NULL;
            $demografi['masa_kerja']["value"] = NULL;
        }

        if ($list_demografi->f_region == 1) {
            $demografi['region']['label'] = $label_others['region'];
            $demografi['region']['value'] = Wilayah::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $demografi['region']["label"] = NULL;
            $demografi['region']["value"] = NULL;
        }

        if ($list_demografi->f_level_of_work == 1) {
            $demografi['level_of_work']['label'] = $label_others['work'];
            $demografi['level_of_work']['value'] = TingkatPekerjaan::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $demografi['level_of_work']["label"] = NULL;
            $demografi['level_of_work']["value"] = NULL;
        }


        // if ($list_demografi->f_pendidikan == 1) {
        //     $demografi['pendidikan']['label'] = $label_others['education'];
        //     $demografi['pendidikan']['value'] = Pendidikan::where('f_account_id', $akun->f_account_id)->where('f_aktif', 1)->get();
        // } else {
        //     $demografi['pendidikan']["label"] = NULL;
        //     $demografi['pendidikan']["value"] = NULL;
        // }

        if ($list_demografi->f_level1 == 1) {
            $level['label_level1']['label'] = json_decode($surveySetting['f_label_level1'], true);
            $level['label_level1']['level'] = 1;
            $level['label_level1']['value'] = Level1::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $level['label_level1']["label"] = NULL;
            $level['label_level1']['level'] = NULL;
            $level['label_level1']["value"] = NULL;
        }

        if ($list_demografi->f_level2 == 1) {
            $level['label_level2']['label'] = json_decode($surveySetting['f_label_level2'], true);
            $level['label_level2']['level'] = 2;
            $level['label_level2']['value'] = Level2::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $level['label_level2']["label"] = NULL;
            $level['label_level2']['level'] = NULL;
            $level['label_level2']["value"] = NULL;
        }


        if ($list_demografi->f_level3 == 1) {
            $level['label_level3']['label'] = json_decode($surveySetting['f_label_level3'], true);
            $level['label_level3']['level'] = 3;
            $level['label_level3']['value'] = Level3::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $level['label_level3']["label"] = NULL;
            $level['label_level3']['level'] = NULL;
            $level['label_level3']["value"] = NULL;
        }


        if ($list_demografi->f_level4 == 1) {
            $level['label_level4']['label'] = json_decode($surveySetting['f_label_level4'], true);
            $level['label_level4']['value'] = Level4::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $level['label_level4']["label"] = NULL;
            $level['label_level4']["value"] = NULL;
        }

        if ($list_demografi->f_level5 == 1) {
            $level['label_level5']['label'] = json_decode($surveySetting['f_label_level5'], true);
            $level['label_level5']['value'] = Level5::where('f_account_id', $akun->f_account_id)->get();
        } else {
            $level['label_level5']["label"] = NULL;
            $level['label_level5']["value"] = NULL;
        }

        // if ($list_demografi->f_level6 == 1) {
        //     $level['label_level6']['label'] = json_decode($surveySetting['f_label_level6'], true);
        //     $level['label_level6']['value'] = Level6::where('f_account_id', $akun->f_account_id)->get();
        // } else {
        //     $level['label_level6']["label"] = NULL;
        //     $level['label_level6']["value"] = NULL;
        // }

        // if ($list_demografi->f_level7 == 1) {
        //     $level['label_level7']['label'] = json_decode($surveySetting['f_label_level7'], true);
        //     $level['label_level7']['value'] = Level7::where('f_account_id', $akun->f_account_id)->get();
        // } else {
        //     $level['label_level7']["label"] = NULL;
        //     $level['label_level7']["value"] = NULL;
        // }




        return view('monitoring.index', compact('chartData', 'openQuestions', 'answersByCode', 'demografi', 'surveySetting', 'level', 'event'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    //     public function show(Request $request)
// {
//     $query = TrnSurvey::with('level1', 'level2', 'level3')
//         ->where('created_by', 8);

    //     if ($request->filled('f_level1')) {
//         $query->where('f_level1', $request->f_level1);
//     }
//     if ($request->filled('f_level2')) {
//         $query->where('f_level2', $request->f_level2);
//     }
//     if ($request->filled('f_level3')) {
//         $query->where('f_level3', $request->f_level3);
//     }

    //     $surveyUsers = $query->paginate(25); // ✅ Pake pagination

    //     // ✅ Caching master data
//     $level1Options = Cache::remember('level1_options', 3600, fn() => Level1::all());
//     $level2Options = Cache::remember('level2_options', 3600, fn() => Level2::all());
//     $level3Options = Cache::remember('level3_options', 3600, fn() => Level3::all());

    //     return view('monitoring.show', compact(
//         'surveyUsers',
//         'level1Options',
//         'level2Options',
//         'level3Options'
//     ));
// }

    // public function show(Request $request)
// {
//     if ($request->ajax()) {
//         $query = TrnSurvey::select([
//             'f_id',
//             'f_survey_username',
//             'f_email',
//             'f_level1',
//             'f_level2',
//             'f_level3',
//             'f_level_of_work',
//             'f_survey_valid',
//             'f_corporate_id'
//         ])->with(['level1', 'level2', 'level3', 'levelwork'])
//             ->where('f_corporate_id', Auth::user()->f_account_id);
//         if ($request->filled('levelwork')) {
//                 $query->where('f_level_of_work', $request->levelwork);
//         }
//         if ($request->filled('f_level1')) {
//             $query->where('f_level1', $request->f_level1);
//         }
//         if ($request->filled('f_level2')) {
//             $query->where('f_level2', $request->f_level2);
//         }
//         if ($request->filled('f_level3')) {
//             $query->where('f_level3', $request->f_level3);
//         }

    //         return DataTables::of($query)
//     ->addIndexColumn()
//     ->addColumn('checkbox', function ($row) {
//         return '<input type="checkbox" class="row-checkbox"
//             data-id="' . sha1(md5($row->f_id)) . '"
//             data-username="' . $row->f_survey_username . '"
//             data-email="' . $row->f_email . '"
//             data-level1="' . ($row->level1->f_position_desc ?? '') . '"
//             data-level2="' . ($row->level2->f_position_desc ?? '') . '"
//             data-level3="' . ($row->level3->f_position_desc ?? '') . '"
//             data-levelwork="' . ($row->levelwork->f_levelwork_desc ?? '') . '" />';
//     })
//     ->editColumn('levelwork.f_levelwork_desc', function ($row) {
//         return $row->levelwork->f_levelwork_desc ?? '';
//     })
//     ->editColumn('level1.f_position_desc', function ($row) {
//         return $row->level1->f_position_desc ?? '';
//     })
//     ->editColumn('level2.f_position_desc', function ($row) {
//         return $row->level2->f_position_desc ?? '';
//     })
//     ->editColumn('level3.f_position_desc', function ($row) {
//         return $row->level3->f_position_desc ?? '';
//     })

    //     // 🔍 Ini bagian penting: custom filter buat relasi
//     ->filterColumn('level1.f_position_desc', function($query, $keyword) {
//         $query->whereHas('level1', function($q) use ($keyword) {
//             $q->where('f_position_desc', 'like', "%{$keyword}%");
//         });
//     })
//     ->filterColumn('level2.f_position_desc', function($query, $keyword) {
//         $query->whereHas('level2', function($q) use ($keyword) {
//             $q->where('f_position_desc', 'like', "%{$keyword}%");
//         });
//     })
//     ->filterColumn('level3.f_position_desc', function($query, $keyword) {
//         $query->whereHas('level3', function($q) use ($keyword) {
//             $q->where('f_position_desc', 'like', "%{$keyword}%");
//         });
//     })

    //     ->addColumn('action', function ($row) {
//         return $row->f_survey_valid === 'yes'
//             ? '<a href="https://talentdna.me/tdna/trx_survey/fnTrx_surveyDetail/' . $row->f_email . '" class="text-blue-600 underline">Download</a>'
//             : '';
//     })
//     ->rawColumns(['checkbox', 'action'])
//     ->make(true);

    //     }

    //     $level1Options = Cache::remember('level1_options', 3600, fn() => Level1::where('f_account_id',Auth::user()->f_account_id)->get());
//     $level2Options = Cache::remember('level2_options', 3600, fn() => Level2::where('f_account_id',Auth::user()->f_account_id)->get());
//     $level3Options = Cache::remember('level3_options', 3600, fn() => Level3::where('f_account_id',Auth::user()->f_account_id)->get());
//     $levelworkOptions = LevelWork::where('f_account_id',Auth::user()->f_account_id)->get();

    //     return view('monitoring.show', compact(
//         'level1Options', 'level2Options', 'level3Options','levelworkOptions',
//     ));
// }

    public function show(Request $request)
    {
        // $kuota = DB::table('t_account')->select('*')->where('f_account_id', Auth::user()->f_account_id)->first();
        // $sisa = DB::table('trn_survey_empex')->where('f_corporate_id', Auth::user()->f_account_id)->count();
        // //  echo json_encode($kuota);die();

        // $settings = SurveySetting::where('f_account_id', Auth::user()->f_account_id)->first();
        // if ($request->ajax()) {
        //     $query = DB::table('trn_survey_empex')
        //         ->leftJoin('table_level_position1', 'trn_survey_empex.f_level1', '=', 'table_level_position1.f_id')
        //         ->leftJoin('table_level_position2', 'trn_survey_empex.f_level2', '=', 'table_level_position2.f_id')
        //         ->leftJoin('table_level_position3', 'trn_survey_empex.f_level3', '=', 'table_level_position3.f_id')
        //         ->leftJoin('table_level_work', 'trn_survey_empex.f_level_of_work', '=', 'table_level_work.f_id')
        //         ->where('trn_survey_empex.f_corporate_id', Auth::user()->f_account_id)
        //         ->select([
        //             'trn_survey_empex.f_id as survey_id',
        //             'trn_survey_empex.f_survey_username',
        //             'trn_survey_empex.f_email',
        //             'trn_survey_empex.f_level1',
        //             'trn_survey_empex.f_level2',
        //             'trn_survey_empex.f_level3',
        //             'trn_survey_empex.f_level_of_work',
        //             'trn_survey_empex.f_survey_valid',
        //             'table_level_position1.f_position_desc as level1_desc',
        //             'table_level_position2.f_position_desc as level2_desc',
        //             'table_level_position3.f_position_desc as level3_desc',
        //             'table_level_work.f_levelwork_desc as levelwork_desc',
        //         ]);

        //     if ($request->filled('levelwork')) {
        //         $query->where('trn_survey_empex.f_level_of_work', $request->levelwork);
        //     }
        //     if ($request->filled('f_level1')) {
        //         $query->where('trn_survey_empex.f_level1', $request->f_level1);
        //     }
        //     if ($request->filled('f_level2')) {
        //         $query->where('trn_survey_empex.f_level2', $request->f_level2);
        //     }
        //     if ($request->filled('f_level3')) {
        //         $query->where('trn_survey_empex.f_level3', $request->f_level3);
        //     }

        //     return DataTables::of($query)
        //         ->addIndexColumn()
        //         ->addColumn('checkbox', function ($row) {
        //             return '<input type="checkbox" class="row-checkbox"
        //             data-id="' . sha1(md5($row->survey_id)) . '"
        //             data-username="' . $row->f_survey_username . '"
        //             data-email="' . $row->f_email . '"
        //             data-level1="' . ($row->level1_desc ?? '') . '"
        //             data-level2="' . ($row->level2_desc ?? '') . '"
        //             data-level3="' . ($row->level3_desc ?? '') . '"
        //             data-levelwork="' . ($row->levelwork_desc ?? '') . '" />';
        //         })
        //         ->editColumn('level1_desc', fn($row) => $row->level1_desc ?? '')
        //         ->editColumn('level2_desc', fn($row) => $row->level2_desc ?? '')
        //         ->editColumn('level3_desc', fn($row) => $row->level3_desc ?? '')
        //         ->editColumn('levelwork_desc', fn($row) => $row->levelwork_desc ?? '')
        //         ->filterColumn('level1_desc', function ($query, $keyword) {
        //             $query->where('level1.f_position_desc', 'like', "%{$keyword}%");
        //         })
        //         ->filterColumn('level2_desc', function ($query, $keyword) {
        //             $query->where('level2.f_position_desc', 'like', "%{$keyword}%");
        //         })
        //         ->filterColumn('level3_desc', function ($query, $keyword) {
        //             $query->where('level3.f_position_desc', 'like', "%{$keyword}%");
        //         })
        //         ->addColumn('action', function ($row) {
        //             return $row->f_survey_valid === 'yes'
        //                 ? '<a href="' . url('reports/createPdf/'.$row->f_email) . '" class="text-blue-600 underline">Download</a>'
        //                 : '';
        //         })
        //         ->rawColumns(['checkbox', 'action'])
        //         ->make(true);
        // }

        // // Master options



        // $decode = json_decode(Auth::user()->nosj, true);

        // $level1Options = DB::table('table_level_position1')->where('f_account_id', Auth::user()->f_account_id)->get();
        // $level2Options = DB::table('table_level_position2')
        //     ->where('f_account_id', Auth::user()->f_account_id)
        //     // ->whereIn('f_id', $decode['f_level2'] ?? [])
        //     ->get();

        // $level3Options = DB::table('table_level_position3')
        //     ->where('f_account_id', Auth::user()->f_account_id)
        //     // ->whereIn('f_id', $decode['f_level3'] ?? [])
        //     ->get();
        // $levelworkOptions = DB::table('table_level_work')->where('f_account_id', Auth::user()->f_account_id)->get();

        return view('monitoring.show');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrnSurvey $trnSurvey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrnSurvey $trnSurvey)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrnSurvey $trnSurvey)
    {
        //
    }




    public function monitoring_user(Request $request, $id_corporate, $id_event)
    {
        // echo $id_corporate;die();   
        $event_client = EventClient::where('f_event_id', $id_event)->first();
        // echo json_encode($event_client);die();

        $id_corporate = sha1(md5($event_client->f_corporate_id));
        $kuota = DB::table('t_account')->select('*')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->first();
        $sisa = DB::table('trn_survey_empex')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->count();

        $list_demo = ListDemo::whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->first();
        $list_monitoring = ListMonitoring::whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->first();


        //   echo json_encode($list_demo);die();

        $settings = SurveySetting::whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->first();

        $appreance = Setting::whereRaw('sha1(md5(id_corporate)) = ?', [$id_corporate])->first();



        if ($request->ajax()) {
            $query = DB::table('trn_survey_empex')
                ->leftJoin('table_level_position1', 'trn_survey_empex.f_level1', '=', 'table_level_position1.f_id')
                ->leftJoin('table_level_position2', 'trn_survey_empex.f_level2', '=', 'table_level_position2.f_id')
                ->leftJoin('table_level_position3', 'trn_survey_empex.f_level3', '=', 'table_level_position3.f_id')
                ->leftJoin('table_level_work', 'trn_survey_empex.f_level_of_work', '=', 'table_level_work.f_id')
                ->leftJoin('table_pendidikan_account', 'trn_survey_empex.f_pendidikan_account', '=', 'table_pendidikan_account.f_id')

                // ->groupStart()
                //     ->whereRaw('sha1(md5(trn_survey_empex.f_corporate_id)) = ?', [$id_corporate])
                //     ->or_whereRaw('sha1(md5(trn_survey_empex.f_from_corporate_id)) = ?', [$id_corporate])
                // ->groupEnd()
                ->where(function ($q) use ($id_corporate) {
                    $q->whereRaw('sha1(md5(trn_survey_empex.f_corporate_id)) = ?', [$id_corporate])
                        ->orWhereRaw('sha1(md5(trn_survey_empex.f_from_corporate_id)) = ?', [$id_corporate]);
                })
                ->where('f_event_id', $event_client->f_event_id)

                ->select([
                    'trn_survey_empex.f_id as survey_id',
                    'trn_survey_empex.f_survey_username',
                    'trn_survey_empex.f_email',
                    'trn_survey_empex.f_level1',
                    'trn_survey_empex.f_level2',
                    'trn_survey_empex.f_level3',
                    'trn_survey_empex.f_level_of_work',
                    'trn_survey_empex.f_survey_valid',
                    'table_age.f_age_desc as age_desc',
                    'table_length_of_service.f_service_desc as service_desc',
                    'table_level_position1.f_position_desc as level1_desc',
                    'table_level_position2.f_position_desc as level2_desc',
                    'table_level_position3.f_position_desc as level3_desc',
                    'table_pendidikan_account.f_name as pendidikan_desc',
                    'table_level_work.f_levelwork_desc as level_work_desc'
                ])
                ->leftJoin('table_length_of_service', 'trn_survey_empex.f_length_of_service', '=', 'table_length_of_service.f_id')
                ->leftJoin('table_age', 'trn_survey_empex.f_age', '=', 'table_age.f_id');
            // ->leftJoin('table_level_work', 'trn_survey_empex.f_level_of_work', '=', 'table_level_work.f_id');

            if ($request->filled('levelwork')) {
                $query->where('trn_survey_empex.f_level_of_work', $request->levelwork);
            }
            if ($request->filled('f_level1')) {
                $query->where('trn_survey_empex.f_level1', $request->f_level1);
            }
            if ($request->filled('f_level2')) {
                $query->where('trn_survey_empex.f_level2', $request->f_level2);
            }
            if ($request->filled('f_level3')) {
                $query->where('trn_survey_empex.f_level3', $request->f_level3);
            }

            if ($request->filled('f_level3')) {
                $query->where('trn_survey_empex.f_level3', $request->f_level3);
            }

            if ($request->filled('f_level3')) {
                $query->where('trn_survey_empex.f_level3', $request->f_level3);
            }

            if ($request->filled('f_level3')) {
                $query->where('trn_survey_empex.f_level3', $request->f_level3);
            }

            if ($request->filled('service')) {
                $query->where('trn_survey_empex.f_length_of_service', $request->service);
            }

            if ($request->filled('age')) {
                $query->where('trn_survey_empex.f_age', $request->age);
            }


            if ($request->filled('education')) {
                $query->where('trn_survey_empex.f_pendidikan_account', $request->education);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="row-checkbox"
                    data-id="' . sha1(md5($row->survey_id)) . '"
                    data-username="' . ($row->f_survey_username ?? '') . '"
                    data-email="' . ($row->f_email ?? '') . '"
                    data-level1="' . ($row->level1_desc ?? '') . '"
                    data-level2="' . ($row->level2_desc ?? '') . '"
                    data-level3="' . ($row->level3_desc ?? '') . '"
                    data-levelwork="' . ($row->levelwork_desc ?? '') . '"
                    data-age="' . ($row->age_desc ?? '') . '"
                    data-education="' . ($row->pendidikan_desc ?? '') . '"
                    data-service="' . ($row->service_desc ?? '') . '" />';
                })
                ->editColumn('level1_desc', fn($row) => $row->level1_desc ?? '')
                ->editColumn('level2_desc', fn($row) => $row->level2_desc ?? '')
                ->editColumn('level3_desc', fn($row) => $row->level3_desc ?? '')
                ->editColumn('levelwork_desc', fn($row) => $row->levelwork_desc ?? '')
                ->editColumn('age_desc', fn($row) => $row->age_desc ?? '')
                ->editColumn('service_desc', fn($row) => $row->service_desc ?? '')
                ->editColumn('pendidikan_desc', fn($row) => $row->pendidikan_desc ?? '')
                ->filterColumn('level1_desc', function ($query, $keyword) {
                    $query->where('table_level_position1.f_position_desc', 'like', "%{$keyword}%");
                })
                ->filterColumn('level2_desc', function ($query, $keyword) {
                    $query->where('table_level_position2.f_position_desc', 'like', "%{$keyword}%");
                })
                ->filterColumn('level3_desc', function ($query, $keyword) {
                    $query->where('table_level_position3.f_position_desc', 'like', "%{$keyword}%");
                })
                ->filterColumn('pendidikan_desc', function ($query, $keyword) {
                    $query->where('table_pendidikan_account.pendidikan_desc', 'like', "%{$keyword}%");
                })
                ->addColumn('action', function ($row) {
                    return $row->f_survey_valid === 'yes'
                        ? '<a href="' . url('reports/createPdf/' . $row->f_email) . '" class="text-blue-600 underline">Download</a>'
                        : '';
                })
                ->rawColumns(['checkbox', 'action'])
                ->make(true);

        }

        // Master options



        // $decode = json_decode(Auth::user()->nosj,true);

        $level1Options = DB::table('table_level_position1')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $level2Options = DB::table('table_level_position2')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $level3Options = DB::table('table_level_position3')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $level4Options = DB::table('table_level_position4')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $level5Options = DB::table('table_level_position5')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();


        $levelworkOptions = DB::table('table_level_work')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $ageOptions = DB::table('table_age')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $lengthServiceOptions = DB::table('table_length_of_service')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $regionOptions = DB::table('table_region')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $pendidikanOptions = DB::table('table_pendidikan_account')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();



        // dd($appreance);

        return view('monitoring.guest_monitoring', compact(
            'ageOptions',
            'lengthServiceOptions',
            'level1Options',
            'level2Options',
            'level3Options',
            'level4Options',
            'level5Options',
            'levelworkOptions',
            'pendidikanOptions',
            'settings',
            'kuota',
            'id_corporate',
            'sisa',
            'list_demo',
            'appreance',
            'list_monitoring'
        ));
    }

    function event_monitoring(Request $request, $token_event)
    {

        $event_client = EventClient::where('f_event_kode', $token_event)->first();

        $id_corporate = sha1(md5($event_client->f_corporate_id));

        $kuota = $event_client->f_kuota;
        $sisa = $kuota - $event_client->f_sudah_isi;

        $list_demo = ListDemo::whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->first();

        $list_monitoring = ListMonitoring::whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->first();



        //   echo $kuota . " : ". $sisa;die();

        $settings = SurveySetting::whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->first();


        $appreance = Setting::whereRaw('sha1(md5(id_corporate)) = ?', [$id_corporate])->first();




        if ($request->ajax()) {
            $query = DB::table('trn_survey_empex')
                ->leftJoin('table_level_position1', 'trn_survey_empex.f_level1', '=', 'table_level_position1.f_id')
                ->leftJoin('table_level_position2', 'trn_survey_empex.f_level2', '=', 'table_level_position2.f_id')
                ->leftJoin('table_level_position3', 'trn_survey_empex.f_level3', '=', 'table_level_position3.f_id')
                ->leftJoin('table_level_work', 'trn_survey_empex.f_level_of_work', '=', 'table_level_work.f_id')
                // ->groupStart()
                //     ->whereRaw('sha1(md5(trn_survey_empex.f_corporate_id)) = ?', [$id_corporate])
                //     ->or_whereRaw('sha1(md5(trn_survey_empex.f_from_corporate_id)) = ?', [$id_corporate])
                // ->groupEnd()
                ->where(function ($q) use ($id_corporate) {
                    $q->whereRaw('sha1(md5(trn_survey_empex.f_corporate_id)) = ?', [$id_corporate])
                        ->orWhereRaw('sha1(md5(trn_survey_empex.f_from_corporate_id)) = ?', [$id_corporate]);
                })
                ->where('f_event_id', $event_client->f_event_id)
                ->select([
                    'trn_survey_empex.f_id as survey_id',
                    'trn_survey_empex.f_survey_username',
                    'trn_survey_empex.f_email',
                    'trn_survey_empex.f_level1',
                    'trn_survey_empex.f_level2',
                    'trn_survey_empex.f_level3',
                    'trn_survey_empex.f_level_of_work',
                    'trn_survey_empex.f_survey_valid',
                    'table_age.f_age_desc as age_desc',
                    'table_length_of_service.f_service_desc as service_desc',
                    'table_level_position1.f_position_desc as level1_desc',
                    'table_level_position2.f_position_desc as level2_desc',
                    'table_level_position3.f_position_desc as level3_desc',
                    'table_level_work.f_levelwork_desc as levelwork_desc',
                ])
                ->leftJoin('table_length_of_service', 'trn_survey_empex.f_length_of_service', '=', 'table_length_of_service.f_id')
                ->leftJoin('table_age', 'trn_survey_empex.f_age', '=', 'table_age.f_id');

            if ($request->filled('levelwork')) {
                $query->where('trn_survey_empex.f_level_of_work', $request->levelwork);
            }
            if ($request->filled('f_level1')) {
                $query->where('trn_survey_empex.f_level1', $request->f_level1);
            }
            if ($request->filled('f_level2')) {
                $query->where('trn_survey_empex.f_level2', $request->f_level2);
            }
            if ($request->filled('f_level3')) {
                $query->where('trn_survey_empex.f_level3', $request->f_level3);
            }

            if ($request->filled('f_level3')) {
                $query->where('trn_survey_empex.f_level3', $request->f_level3);
            }

            if ($request->filled('f_level3')) {
                $query->where('trn_survey_empex.f_level3', $request->f_level3);
            }

            if ($request->filled('f_level3')) {
                $query->where('trn_survey_empex.f_level3', $request->f_level3);
            }

            if ($request->filled('service')) {
                $query->where('trn_survey_empex.f_length_of_service', $request->service);
            }

            if ($request->filled('age')) {
                $query->where('trn_survey_empex.f_age', $request->age);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="row-checkbox"
                    data-id="' . sha1(md5($row->survey_id)) . '"
                    data-username="' . ($row->f_survey_username ?? '') . '"
                    data-email="' . ($row->f_email ?? '') . '"
                    data-level1="' . ($row->level1_desc ?? '') . '"
                    data-level2="' . ($row->level2_desc ?? '') . '"
                    data-level3="' . ($row->level3_desc ?? '') . '"
                    data-levelwork="' . ($row->levelwork_desc ?? '') . '"
                    data-age="' . ($row->age_desc ?? '') . '"
                    data-service="' . ($row->service_desc ?? '') . '" />';
                })
                ->editColumn('level1_desc', fn($row) => $row->level1_desc ?? '')
                ->editColumn('level2_desc', fn($row) => $row->level2_desc ?? '')
                ->editColumn('level3_desc', fn($row) => $row->level3_desc ?? '')
                ->editColumn('levelwork_desc', fn($row) => $row->levelwork_desc ?? '')
                ->editColumn('age_desc', fn($row) => $row->age_desc ?? '')
                ->editColumn('service_desc', fn($row) => $row->service_desc ?? '')
                ->filterColumn('level1_desc', function ($query, $keyword) {
                    $query->where('table_level_position1.f_position_desc', 'like', "%{$keyword}%");
                })
                ->filterColumn('level2_desc', function ($query, $keyword) {
                    $query->where('table_level_position2.f_position_desc', 'like', "%{$keyword}%");
                })
                ->filterColumn('level3_desc', function ($query, $keyword) {
                    $query->where('table_level_position3.f_position_desc', 'like', "%{$keyword}%");
                })
                ->addColumn('action', function ($row) {
                    return $row->f_survey_valid === 'yes'
                        ? '<a href="' . url('reports/createPdf/' . $row->f_email) . '" class="text-blue-600 underline">Download</a>'
                        : '';
                })
                ->rawColumns(['checkbox', 'action'])
                ->make(true);

        }

        // Master options



        // $decode = json_decode(Auth::user()->nosj,true);

        $level1Options = DB::table('table_level_position1')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $level2Options = DB::table('table_level_position2')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $level3Options = DB::table('table_level_position3')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $level4Options = DB::table('table_level_position4')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $level5Options = DB::table('table_level_position5')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();


        $levelworkOptions = DB::table('table_level_work')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $ageOptions = DB::table('table_age')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $lengthServiceOptions = DB::table('table_length_of_service')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $regionOptions = DB::table('table_region')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();
        $pendidikanOptions = DB::table('table_pendidikan_account')->whereRaw('sha1(md5(f_account_id)) = ?', [$id_corporate])->get();



        // dd($appreance);

        // echo json_encode($settings);die();

        return view('monitoring.guest_monitoring', compact(
            'ageOptions',
            'lengthServiceOptions',
            'level1Options',
            'level2Options',
            'level3Options',
            'level4Options',
            'level5Options',
            'levelworkOptions',
            'pendidikanOptions',
            'settings',
            'kuota',
            'id_corporate',
            'sisa',
            'list_demo',
            'appreance',
            'event_client',
            'list_monitoring'
        ));
    }

    function quick_count_monitoring(Request $request, $token_event)
    {

        $event_client = EventClient::where('f_event_kode', $token_event)->first();

        $id_corporate = sha1(md5($event_client->f_corporate_id));


        // $sudah_isi = $event_client->f_sudah_isi;
        $sudah_isi = $event_client->f_sudah_isi_temp;
        $code_event = $token_event;

        $appreance = Setting::whereRaw('sha1(md5(id_corporate)) = ?', [$id_corporate])->first();



        // dd($appreance);

        // echo json_encode($level1Options);die();

        return view('monitoring.quickcount', compact(
            'sudah_isi',
            'appreance',
            'code_event'
        ));
    }

    public function get_quick_count($id)
    {
        // Fetch the count of f_event_type based on the provided ID
        // $count = EventClient::where('f_event_kode', $id)->first()->f_sudah_isi;
        $count = EventClient::where('f_event_kode', $id)->first()->f_sudah_isi_temp;

        return response()->json([
            'count' => $count
        ]);
    }

    public function update_quick_count($id)
    {
        // Fetch the count of f_event_type based on the provided ID
        $event = EventClient::where('f_event_kode', $id)->first();

        $sudah_isi = $event->f_sudah_isi;
        $sudah_isi_temp = $event->f_sudah_isi_temp;

        $random = RAND(1, 10);

        // $random = 1150;

        $final_sudah_isi = $sudah_isi_temp + $random;

        if ($final_sudah_isi <= $sudah_isi) {
            //update
            // EventClient::where('f_event_kode', $id)->update()
            EventClient::where('f_event_kode', $id)
                ->update(['f_sudah_isi_temp' => $final_sudah_isi]);


            return response()->json([
                'code' => 200,
                'status' => 'success update to ' . $final_sudah_isi
            ]);
        } else {
            return response()->json([
                'code' => 201,
                'status' => 'failed update to ' . $final_sudah_isi . ' max limit reached ' . $sudah_isi
            ]);
        }

    }

    public function get_child($level = NULL, $id = NULL)
    {
        $table = 'table_level_position' . ($level + 1);
        $field_id = 'f_id' . ($level);

        // echo $table." - ".$field_id;

        $results = DB::select("SELECT * FROM {$table} WHERE $field_id = ?", [$id]);

        // echo json_encode($results);

        return response()->json([
            'code' => 200,
            'data' => $results
        ]);

        // $data['child'] = Level2::where('f_account_id', Auth::user()->f_account_id)->with('relasi_level1')->get();



    }

    function buildChartData($kode, $itemPertanyaan, $trnSurvey)
    {
        $q = $itemPertanyaan->firstWhere('f_kode', $kode);
        if (!$q)
            return null;

        $title = $q->f_item ?? $kode;
        $options = is_string($q->f_answer) ? json_decode($q->f_answer, true) : ($q->f_answer ?? []);
        $labels = array_map(fn($o) => $o['label'], $options);
        $values = array_map(fn($o) => (int) $o['value'], $options);

        $valueToIndex = [];
        foreach ($values as $i => $val)
            $valueToIndex[$val] = $i;

        $counts = array_fill(0, count($labels), 0);

        foreach ($trnSurvey as $row) {
            $payload = is_string($row->f_survey) ? json_decode($row->f_survey, true) : $row->f_survey;
            if (!is_array($payload))
                continue;

            foreach (($payload['distribusi_jawaban'] ?? []) as $ans) {
                if (($ans['jenis'] ?? null) != 1)
                    continue;
                if (($ans['code'] ?? null) !== $kode)
                    continue;
                $nilai = (int) ($ans['nilai'] ?? 0);
                if (!isset($valueToIndex[$nilai]))
                    continue;
                $counts[$valueToIndex[$nilai]]++;
            }
        }

        return [
            'code' => $kode,
            'title' => $title,
            'categories' => $labels,
            'series' => $counts,
        ];
    }
}

