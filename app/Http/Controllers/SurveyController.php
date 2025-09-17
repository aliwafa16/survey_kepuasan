<?php

namespace App\Http\Controllers;
use App\Models\Age;
use App\Models\EventClient;
use App\Models\Usia;
use App\Models\Gender;
use App\Models\Level1;
use App\Models\Level2;
use App\Models\Level3;
use App\Models\Level4;
use App\Models\Level5;
use App\Models\Level6;
use App\Models\Level7;

use App\Models\Region;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Variabel;
use App\Models\Wilayah;
use App\Models\ListDemo;
use App\Models\MasaKerja;
use App\Models\MasterNip;
use App\Models\TrnSurvey;
use App\Models\Pendidikan;
use Illuminate\Support\Str;
use App\Models\JenisKelamin;
use Illuminate\Http\Request;
use App\Jobs\SubmitSurveyJob;
use App\Models\AccountClient;
use App\Models\SurveySetting;
use App\Models\TrnSurveyEmpex;
use Illuminate\Support\Carbon;
use App\Models\TingkatPekerjaan;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessCorporateSurvey;
use App\Models\ItemPernyataanModel;
use Illuminate\Support\Facades\Hash;



//     use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class SurveyController extends Controller
{
    public function showSurvey($event_client)
    {
        $check_event_token = EventClient::where('f_event_kode', $event_client)->first();


        $setting_profile = Setting::where('id_corporate', $check_event_token->f_account_id)->first();



        if ($check_event_token) {
            // Gabungkan tanggal & jam mulai dan selesai ke objek Carbon
            $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $check_event_token->f_event_start);
            $end = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $check_event_token->f_event_end);





            // Cek waktu pengisian
            if (now()->between($start, $end)) {
                $jumlah_pengisian = TrnSurvey::where('f_account_id', $check_event_token->f_account_id)->where('f_event_id', $check_event_token->f_event_id)->count();
                if ($jumlah_pengisian >= $check_event_token->f_event_min_respon) {
                    return view('survey.notfound', [
                        'setting' => $setting_profile,
                        'msg' => "Kuota sudah habis"
                    ]);
                } else {


                    $data = ItemPernyataanModel::all();
                    if ($data) {
                        // Ambil akun terlebih dahulu
                        $akun = AccountClient::where('f_account_id', $check_event_token->f_account_id)->first();


                        // Membagi data menjadi 3 bagian
                        $chunks = collect(value: $data)->chunk(ceil(count($data) / 2));
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


                        return view('survey.show', [
                            'sections' => $chunks,
                            'demografi' => $demografi,
                            'level' => $level,
                            'account_id' => $akun->f_account_id,
                            'setting' => $setting_profile,
                            'surveySetting' => $surveySetting,
                            'events' => $check_event_token,
                            'event_id' => $check_event_token->f_event_id
                        ]);
                    }

                }
            } else {

                $msg = (now() > $start) ? "Link sudah kadaluarsa" : "Link belum tersedia";
                return view('survey.notfound', [
                    'setting' => $setting_profile,
                    'msg' => $msg
                ]);
            }
        } else {
            return view('survey.notfound', [
                'setting' => $setting_profile,
                'msg' => 'Link tidak ditemukan'
            ]);
        }

    }

    public function getLevel(Request $request)
    {
        // echo json_encode($request->all());
        $level_awal = $request->get('level');
        $level = ($level_awal + 1);

        // echo $level;
        $table = 'table_level_position' . $level;
        $where_id = 'f_id' . $level_awal;

        $data = DB::table($table)->where($where_id, $request->id)->get();

        // echo $table;
        echo json_encode($data);
    }

    public function checkPengisian(Request $request)
    {
        $input = $request->all();

        $data = TrnSurvey::where('f_survey_email', $input['email'])->first();




        if ($data) {
            return response()->json([
                'status' => 200,
                'survey_valid' => false,
                'msg' => '',
            ]);
        }

        $data_corp = DB::table('t_account')
            ->select('*')
            ->whereRaw('sha1(f_account_id) = ?', [$request->account_id])
            ->first();


        $data_event = EventClient::where('f_event_id', $request->event_id)->first();

        // if (!$data_event || !$data_corp) {
        //     return response()->json([
        //         'status' => 400,
        //         'msg' => 'Data event atau akun tidak ditemukan.',
        //     ]);
        // }

        $setting_akun = SurveySetting::where('f_account_id', $data_event->f_account_id)->first();
        $id_corp = $data_corp->f_account_id;



        // dd($data);
        $master_nip = NULL;
        $from_nip = false;

        $list_demografi = NULL;
        $events = EventClient::where('f_event_id', $request->event_id)->first();

        $akun = AccountClient::where('f_account_id', $data_event->f_account_id)->first();



        if (optional($setting_akun)->f_demo_view == 2 && $events->f_event_type == 1) {

            $from_nip = true;
            $responden = MasterNip::where('id_account', $events->f_account_id)
                ->where('nip', $request->nip)
                ->first();

            $list_demografi = ListDemo::where('f_account_id', $akun->f_account_id)->first();


            $setting_profile = Setting::where('id_corporate', $akun->f_account_id)->first();
            $demografi = array();
            $label_others = json_decode($setting_akun->f_label_others, true);

            if ($responden->f_name) {
                $demografi["label"][] = $label_others['nama']['indonesian'];
                $demografi["value"][] = $responden->f_name;
            }


            if ($responden->f_gender) {
                $demografi["label"][] = $label_others['gender']['indonesian'];
                $demografi["value"][] = JenisKelamin::where('f_gender_id', $responden->f_gender)->first()->f_gender_name;
            }

            if ($responden->f_age) {
                $demografi['label'][] = $label_others['age']['indonesian'];

                $demografi['value'][] = Usia::where('f_id', $responden->f_age)->first()->f_age_desc;
            }

            if ($responden->f_length_of_service) {
                $demografi['label'][] = $label_others['mk']['indonesian'];
                $demografi['value'][] = MasaKerja::where('f_id', $responden->f_length_of_service)->first()->f_service_desc;
            }

            if ($responden->f_region) {
                $demografi['label'][] = $label_others['region']['indonesian'];
                $demografi['value'][] = Wilayah::where('f_id', $responden->f_region)->first()->f_region_name;
            }

            if ($responden->f_level_of_work) {
                $demografi['label'][] = $label_others['work']['indonesian'];
                $demografi['value'][] = TingkatPekerjaan::where('f_id', $responden->f_level_of_work)->first()->f_levelwork_desc;
            }


            if ($responden->f_pendidikan) {
                $demografi['label'][] = $label_others['education']['indonesian'];
                $demografi['value'][] = Pendidikan::where('f_id', $responden->f_pendidikan)->first()->f_name;
            }

            if ($responden->f_level1) {
                $demografi['label'][] = json_decode($setting_akun['f_label_level1'], true)['indonesian'];
                $demografi['value'][] = Level1::where('f_id', $responden->f_level1)->first()->f_position_desc;
            }
            if ($responden->f_level2) {
                $demografi['label_level2']['label'] = json_decode($setting_akun['f_label_level2'], true)['indonesian'];
                $demografi['label_level2']['value'] = Level2::where('f_id', $responden->f_level2)->first()->f_position_desc;
            }
            if ($responden->f_level3) {
                $demografi['label'][] = json_decode($setting_akun['f_label_level3'], true)['indonesian'];
                $demografi['value'][] = Level3::where('f_id', $responden->f_level3)->first()->f_position_desc;
            }
            if ($responden->f_level4) {
                $demografi['label'][] = json_decode($setting_akun['f_label_level4'], true)['indonesian'];
                $demografi['value'][] = Level4::where('f_id', $responden->f_level4)->first()->f_position_desc;
            }
            if ($responden->f_level5) {
                $demografi['label'][] = json_decode($setting_akun['f_label_level5'], true)['indonesian'];
                $demografi['value'][] = Level5::where('f_id', $responden->f_level5)->first()->f_position_desc;
            }
            if ($responden->f_level6) {
                $demografi['label'][] = json_decode($setting_akun['f_label_level6'], true)['indonesian'];
                $demografi['value'][] = Level6::where('f_id', $responden->f_level6)->first()->f_position_desc;
            }
            if ($responden->f_level7) {
                $demografi['label'][] = json_decode($setting_akun['f_label_level1'], true)['indonesian'];
                $demografi['value'][] = Level7::where('f_id', $responden->f_level7)->first()->f_position_desc;
            }

            // echo json_encode($responden)."<br><br>";
            // echo json_encode($list_demografi)."<br><br>";
            // echo json_encode($demografi)."<br><br>";die();

            $master_nip = $demografi;

        }

        if (!$data) {
            return response()->json([
                'status' => 200,
                'survey_valid' => false,
                'msg' => '',
                'from_nip' => $from_nip,
                'data_nip' => $master_nip,
            ]);
        }

        // Jika survey sudah valid
        if ($data->f_survey_valid === "yes") {
            $data_update = [
                'f_event_id' => $input['event_id'] ?? 0,
                'f_survey_username' => $input['name'] ?? "",
                // 'f_email' => $input['email'],
                'f_age' => $input['age'] ?? null,
                'f_gender' => $input['gender'] ?? null,
                'f_pendidikan_account' => $input['pendidikan'] ?? null,
                'f_level1' => $input['label_level1'] ?? null,
                'f_level2' => $input['label_level2'] ?? null,
                'f_level3' => $input['label_level3'] ?? null,
                'f_level4' => $input['label_level4'] ?? null,
                'f_level5' => $input['label_level5'] ?? null,
                'f_level6' => $input['label_level6'] ?? null,
                'f_level7' => $input['label_level7'] ?? null,
                // 'level_work' => $input['level_of_work'] ?? null,
                'f_account_id' => $data_event->f_account_id,
                'f_from_corporate_id' => $data_event->f_account_id,
                'f_length_of_service' => $input['masa_kerja'] ?? null,
                'f_level_of_work' => $input['level_of_work'] ?? null,
                'f_region' => $input['region'] ?? null,
                'f_survey_updated_on' => now(),
                'f_nip' => $input['nip'] ?? null,
            ];

            if ($data->f_report_type < $data_event->f_report_type) {
                $data_update['f_report_type'] = $data_event->f_report_type;
                $data_update['f_report_status'] = $data_event->f_report_type;
            }

            // Cek apakah ambil data dari tabel master_nip
            if (optional($setting_akun)->f_demo_view == 2 && $data_event->f_event_type == 1) {
                $responden = MasterNip::where('id_account', $data_event->f_account_id)
                    ->where('nip', $input['nip'])
                    ->first();

                if ($responden) {
                    $data_update = array_merge($data_update, [
                        'f_survey_username' => $responden->f_name ?? $data_update['f_survey_username'],
                        // 'f_email' => $responden->f_email ?? $data_update['f_email'],
                        'f_age' => $responden->f_age ?? $data_update['f_age'],
                        'f_gender' => $responden->f_gender ?? $data_update['f_gender'],
                        'f_pendidikan_account' => $responden->f_pendidikan ?? $data_update['f_pendidikan'],
                        'f_level1' => $responden->f_level1 ?? $data_update['f_level1'],
                        'f_level2' => $responden->f_level2 ?? $data_update['f_level2'],
                        'f_level3' => $responden->f_level3 ?? $data_update['f_level3'],
                        'f_level4' => $responden->f_level4 ?? $data_update['f_level4'],
                        'f_level5' => $responden->f_level5 ?? $data_update['f_level5'],
                        'f_level6' => $responden->f_level6 ?? $data_update['f_level6'],
                        'f_level7' => $responden->f_level7 ?? $data_update['f_level7'],
                        'f_level_of_work' => $responden->f_level_of_work ?? $data_update['f_level_of_work'],
                        // 'level_work' => $responden->f_level_of_work ?? $data_update['level_work'],
                        'f_length_of_service' => $responden->f_length_of_service ?? $data_update['f_length_of_service'],
                        'f_region' => $responden->f_region ?? $data_update['f_region'],
                        'f_nip' => $responden->nip ?? $data_update['f_nip'],
                    ]);


                    // Update ke responden
                    $responden->f_survey_valid = "yes";
                    $responden->f_survey_date = now();
                    $responden->save();
                }
            }

            $data->update($data_update);

            return response()->json([
                'status' => 403,
                'msg' => "Email telah digunakan, email otomatis ter-link dengan perusahaan Anda. Terimakasih atas partisipasinya.",
                'survey_valid' => true,
            ]);
        }

        // Jika status survey "no"
        return response()->json([
            'status' => 200,
            'survey_valid' => false,
            'from_nip' => $from_nip,
            'data_nip' => $master_nip,
            'msg' => 'Email sudah terdata didatabase, akan dilakukan update transaksi sesuai dengan event perusahaan anda.',
        ]);
    }


    public function submitSurvey(Request $request)
    {
        // $this->submitSurveyManual($request->all());

        // if($request->account_id == 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f'){
        //     $this->submitSurveyManual($request->all());
        // }else{
        // try {

        //     $job = new SubmitSurveyJob($request->all());
        //     $jobId = app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch($job);


        //     // Simpan ke session
        //     session([
        //         'job_id' => $jobId,
        //         'insert_date' => Carbon::now()->format('Y-m-d'),
        //     ]);

        //     return response()->json([
        //         'status' => 'queued',
        //         'msg' => 'Jawaban Anda masuk dalam antrian, Survey telah selesai. Laporan TalentDNA akan dikirimkan ke email Anda dalam kurun waktu maksimal 3-5 jam ke depan'
        //     ]);
        // } catch (\Exception $e) {
        //     // Log error dari dispatch queue
        //     Log::error('Dispatch failed, running manually: ' . $e->getMessage());

        //     // Jalankan manual sebagai fallback
        //     try {
        //         $this->submitSurveyManual($request->all());

        //         return response()->json([
        //             'status' => 'manual',
        //             'msg' => 'Jawaban Anda masuk dalam antrian, Survey telah selesai'
        //         ]);
        //     } catch (\Exception $ex) {
        //         Log::error('Manual submission also failed: ' . $ex->getMessage());

        //         return response()->json([
        //             'status' => 'error',
        //             'msg' => 'Terjadi Kesalahan.'
        //         ], 500);
        //     }
        // }

        $job = new SubmitSurveyJob($request->all());
        $jobId = app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch($job);

        // Cek apakah job berhasil dimasukkan ke antrian
        if ($jobId) {
            // Simpan ke session
            session([
                'job_id' => $jobId,
                'insert_date' => Carbon::now()->format('Y-m-d'),
            ]);

            return response()->json([
                'status' => 'success',
                'msg' => 'Jawaban Anda masuk dalam antrian, Survey telah selesai. Laporan TalentDNA akan dikirimkan ke email Anda dalam kurun waktu maksimal 3-5 jam ke depan'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'msg' => 'Gagal memasukkan ke antrian. Silakan coba beberapa saat lagi.'
            ], 500);
        }
        // }
    }

    public function saveSurveyCorporate(Request $request)
    {

        $distribusi_jawaban   = [];
$total_jawaban_semua  = 0;
$total_score          = 0.0;
$jumlah_skala         = 0;

$jawaban_skala = []; // pertanyaan_{id} => float
$jawaban_teks  = []; // pertanyaan_{id} => string

$list_pernyataan = ItemPernyataanModel::get();
$answers = $request->input('answers', []);

foreach ($list_pernyataan as $it) {
    $id    = $it->f_id;
    $type  = (int) ($it->type ?? 1);
    $kode  = $it->f_kode ?? null;
    $vari  = $it->f_variabel_id ?? null;

    $key   = 'ex' . $id;
    $raw   = $answers[$key] ?? null;

    if ($type === 1) {
        // numeric/scale
        $nilai = $raw;
        $jawaban_skala['pertanyaan_' . $id] = $nilai;
        $total_score += $nilai;
        $jumlah_skala++;
    } elseif ($type === 2) {
        // text/open-ended
        // opsional: sanitasi ringan
        $text = trim((string) $raw);
        $text = strip_tags($text); // buang HTML
        $jawaban_teks['pertanyaan_' . $id] = $text;
    } else {
        // tipe lain (kalau ada)
        $jawaban_teks['pertanyaan_' . $id] = is_scalar($raw) ? (string)$raw : json_encode($raw);
    }

    $distribusi_jawaban[] = [
        'id'       => $id,
        'code'     => $kode,
        'variabel' => $vari,
        'jenis'    => $type,
        'nilai'    => $type === 1 ? ($jawaban_skala['pertanyaan_'.$id] ?? null)
                                  : ($jawaban_teks['pertanyaan_'.$id] ?? null),
    ];

    $total_jawaban_semua++;
}

$rata2_skala = $jumlah_skala > 0 ? round($total_score / $jumlah_skala, 2) : 0.0;


$distribusi_by_variabel = [];
$variabels = Variabel::select('f_id', 'f_variabel_name')->get()->keyBy('f_id');

foreach ($distribusi_jawaban as $jawaban) {
    $variabelId = $jawaban['variabel'];
    $nilai      = $jawaban['nilai'] ?? 0;

    if (!isset($distribusi_by_variabel[$variabelId])) {
        $variabelName = $variabels[$variabelId]->f_variabel_name ?? 'Unknown';

        $distribusi_by_variabel[$variabelId] = [
            'id'             => $variabelId,
            'variabel'       => $variabelName,
            'total_nilai'    => 0,
            'jumlah_jawaban' => 0,
        ];
    }

    $distribusi_by_variabel[$variabelId]['total_nilai']    += (is_numeric($nilai) ? (float)$nilai : 0);
    $distribusi_by_variabel[$variabelId]['jumlah_jawaban'] += 1;
}

// optional: hitung rata-rata per variabel
foreach ($distribusi_by_variabel as &$row) {
    $row['rata_rata'] = $row['jumlah_jawaban'] > 0
        ? round($row['total_nilai'] / $row['jumlah_jawaban'], 2)
        : 0;
}


  $bodySurvey = json_encode(['distribusi_jawaban' => $distribusi_jawaban, 'jawaban_by_variabel' => $distribusi_by_variabel]);


//   Event

$event = EventClient::where('f_event_id',$request->event_id)->first();

        $insert = [
            'f_account_id' => $event->f_account_id,
            'f_event_id' => $event->f_event_id,
            'f_survey_password'=> sha1($request->email . Str::random(10)),
            'f_survey_username' => $request->name,
            'f_survey_email' => $request->email,
            'f_survey_telp' => $request->telp,
            'f_gender' => $request->gender,
            'f_age' => $request->age,
            'f_length_of_service' => $request->masa_kerja,
            'f_region' => $request->region,
            'f_survey' => $bodySurvey,
            'f_level_of_work' => $request->level_of_work,
            'f_pendidikan' => $request->pendidikan,
            'f_level1' => $request->label_level1,
            'f_level2' => $request->label_level2,
            'f_level3' => $request->label_level3,
            'f_level4' => $request->label_level4,
            'f_level5' => $request->label_level5,
            'f_level6' => $request->label_level6,
            'f_level7' => $request->label_level7,
            'f_survey_created_on' => now(),
            'f_survey_created_by' => auth()->id() ?? null,
            'f_ip_address' => $request->ip(),
        ];

        $survey = TrnSurvey::create($insert);


                    return response()->json([
                'status' => 'success',
                'msg' => 'Survey berhasil disubmit'
            ]);

    }


    public function submitSurveyManual($data)
    {
        // echo json_encode($request->all());
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://talentdna.me/tdna/corporate_api/save_survey_corporate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',

            CURLOPT_MAXREDIRS => 5,                         // lebih aman
            CURLOPT_TIMEOUT => 30,                          // batas total request
            CURLOPT_CONNECTTIMEOUT => 10,                   // koneksi awal maksimal 10 detik
            CURLOPT_NOSIGNAL => true,                       // hindari error di PHP-FPM

            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,         // hindari delay karena IPv6

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),

            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: ci_session=6be22251ff26aac8f57f345d8484a5a0405a338e'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
}