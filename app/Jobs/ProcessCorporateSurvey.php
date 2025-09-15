<?php

namespace App\Jobs;

use App\Models\EventClient;
use App\Models\MasterNip;
use App\Models\SurveySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCorporateSurvey implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $account;
    public $answer;


    public $name_responden;
    public $email_responden;

    public function __construct(array $data, object $account, array $answer)
    {
        $this->data = $data;
        $this->account = $account;
        $this->answer = $answer;
    }

    public function handle()
    {
        $data = $this->data;
        $account = $this->account;
        $answer = $this->answer;



        // Ambil data event
        $events = EventClient::where('f_event_id', $data['event_id'])->first();
        $setting_akun = SurveySetting::where('f_account_id', $events->f_corporate_id)->first();

        $existing = DB::table('trn_survey_empex')->where('f_email', $data['email'])->first();




        if ($existing) {

            // Cek tipe report
            $report_type = max(10, $existing->f_report_type, $events->f_report_type);

            if ($existing->f_survey_valid == "no") {
                    $this->name_responden = $data['name'] ?? '';
                    $this->email_responden = $data['email'] ?? '';

                // Kondisi normal ambil dari inputan
                $update_survey = [
                    'f_account_id' => 0,
                    'f_event_id' => $data['event_id'] ?? 0,
                    'f_survey_username' => $this->name_responden,
                    'f_email' => $this->email_responden,
                    'f_age' => $data['age'] ?? NULL,
                    'f_gender' => $data['gender'] ?? NULL,
                    'f_report_status' => $report_type,
                    'f_report_type' => $report_type,
                    // 'f_survey_password' => sha1($data['email']) ?? NULL,
                    'f_survey' => json_encode($answer, JSON_NUMERIC_CHECK),
                    'f_survey_valid' => "yes",
                    'f_pendidikan_account' => $data['pendidikan'] ?? NULL,
                    'f_level1' => $data['label_level1'] ?? NULL,
                    'f_level2' => $data['label_level2'] ?? NULL,
                    'f_level3' => $data['label_level3'] ?? NULL,
                    'f_level4' => $data['label_level4'] ?? NULL,
                    'f_level5' => $data['label_level5'] ?? NULL,
                    'f_level6' => $data['label_level6'] ?? NULL,
                    'f_level7' => $data['label_level7'] ?? NULL,
                    // 'level_work' => $data['level_of_work'] ?? NULL,
                    'negara' => "Indonesia",
                    'f_bahasa' => "id-ID",
                    'f_report' => 1,
                    'status_mail' => NULL,
                    'created_by' => 1,
                    'f_survey_created_by' => 'corporate',
                    'f_status_bayar' => 0,
                    'f_corporate_id' => $account->f_account_id,
                    'f_from_corporate_id' => $account->f_account_id,
                    'f_length_of_service' => $data['masa_kerja'] ?? NULL,
                    'f_level_of_work' => $data['level_of_work'] ?? NULL,
                    'f_survey_created_on' => now(),
                    'f_survey_updated_on' => now(),
                    'f_nip' => $data['nip'] ?? NULL,
                    'f_region' => $data['region'] ?? NULL,
                ];


                // Ambil settingan survey untuk emastikan jika harus mengamvik dari responden
                $setting_akun = SurveySetting::where('f_account_id', $events->f_corporate_id)->first();
                if ($setting_akun->f_demo_view == 2 && $events->f_event_type == 1) {

                    $responden = MasterNip::where('id_account', $events->f_corporate_id)
                        ->where('nip', $data['nip'])
                        ->first();

                    if ($responden) {
                        $update_survey = array_merge($update_survey, [
                            'f_survey_username' => $responden->f_name ?? $update_survey['f_survey_username'],
                            // 'f_email' => $responden->f_email ?? $update_survey['f_email'],
                            'f_age' => $responden->f_age ?? NULL,
                            'f_gender' => $responden->f_gender ?? null,
                            'f_pendidikan_account' => $responden->f_pendidikan ?? NULL,
                            'f_level1' => $responden->f_level1 ?? NULL,
                            'f_level2' => $responden->f_level2 ?? NULL,
                            'f_level3' => $responden->f_level3 ?? NULL,
                            'f_level4' => $responden->f_level4 ?? NULL,
                            'f_level5' => $responden->f_level5 ?? NULL,
                            'f_level6' => $responden->f_level6 ?? NULL,
                            'f_level7' => $responden->f_level7 ?? NULL,
                            'f_level_of_work' => $responden->f_level_of_work ?? NULL,
                            // 'level_work' => $responden->f_level_of_work ?? NULL,
                            'f_nip' => $responden->nip ?? NULL,
                            'f_length_of_service' => $responden->f_length_of_service ?? $update_survey['f_length_of_service'],
                            'f_region' => $responden->f_region ?? $update_survey['f_region'],
                        ]);


                        // Update data responden
                        $responden->f_survey_valid = "yes";
                        $responden->f_survey_date = now();
                        $responden->save();


                        $this->name_responden = $responden->f_name ?? $this->name_responden;
                        $this->email_responden = $responden->f_email ?? $this->email_responden;
                    }
                }
                DB::table('trn_survey_empex')->where('f_id', $existing->f_id)->update($update_survey);
            }
        } else {
            $insertData = [
                'f_account_id' => 0,
                'f_event_id' => $data['event_id'] ?? 0,
                'f_survey_username' => $data['name'] ?? "",
                'f_email' => $data['email'],
                'f_age' => $data['age'] ?? NULL,
                'f_gender' => $data['gender'] ?? NULL,
                'f_survey_password' => sha1($data['email']) ?? NULL,
                'f_survey' => json_encode($answer, JSON_NUMERIC_CHECK),
                'f_survey_valid' => "yes",
                'f_report_status' => $events->f_report_type,
                'f_pendidikan_account' => $data['pendidikan'] ?? NULL,
                'f_level1' => $data['label_level1'] ?? NULL,
                'f_level2' => $data['label_level2'] ?? NULL,
                'f_level3' => $data['label_level3'] ?? NULL,
                'f_level4' => $data['label_level4'] ?? NULL,
                'f_level5' => $data['label_level5'] ?? NULL,
                'f_level6' => $data['label_level6'] ?? NULL,
                'f_level7' => $data['label_level7'] ?? NULL,
                // 'level_work' => $data['level_of_work'] ?? NULL,
                'negara' => "Indonesia",
                'f_bahasa' => "id-ID",
                'f_report' => 1,
                'f_report_type' => $events->f_report_type,
                'status_mail' => NULL,
                'created_by' => 1,
                'f_survey_created_by' => 'corporate',
                'f_status_bayar' => 0,
                'f_corporate_id' => $account->f_account_id,
                'f_from_corporate_id' => NULL,
                'f_length_of_service' => $data['masa_kerja'] ?? NULL,
                'f_level_of_work' => $data['level_of_work'] ?? NULL,
                'f_survey_created_on' => now(),
                'f_survey_updated_on' => now(),
                'f_nip' => $data['nip'] ?? NULL,
                'f_region' => $data['region'] ?? NULL,
            ];


        $this->name_responden = $data['name'] ?? '';
        $this->email_responden = $data['email'] ?? '';

            // Setting untuk demografi disinii 
            $nama = $insertData['f_survey_username'];
            if ($setting_akun->f_demo_view == 2 && $events->f_event_type == 1) {
                $responden = MasterNip::where('id_account', $events->f_corporate_id)
                    ->where('nip', $data['nip'])
                    ->first();

                if ($responden) {
                    $insertData['f_survey_username'] = $responden->f_name ?? $insertData['f_survey_username'];
                    $insertData['f_email'] = $responden->f_email ?? $insertData['f_email'];
                    $insertData['f_age'] = $responden->f_age ?? $insertData['f_age'];
                    $insertData['f_gender'] = $responden->f_gender ?? $insertData['f_gender'];
                    $insertData['f_pendidikan_account'] = $responden->f_pendidikan ?? $insertData['f_pendidikan_account'];
                    $insertData['f_level1'] = $responden->f_level1 ?? $insertData['f_level1'];
                    $insertData['f_level2'] = $responden->f_level2 ?? $insertData['f_level2'];
                    $insertData['f_level3'] = $responden->f_level3 ?? $insertData['f_level3'];
                    $insertData['f_level4'] = $responden->f_level4 ?? $insertData['f_level4'];
                    $insertData['f_level5'] = $responden->f_level5 ?? $insertData['f_level5'];
                    $insertData['f_level6'] = $responden->f_level6 ?? $insertData['f_level6'];
                    $insertData['f_level7'] = $responden->f_level7 ?? $insertData['f_level7'];
                    $insertData['f_level_of_work'] = $responden->f_level_of_work ?? $insertData['f_level_of_work'];
                    // $insertData['level_work'] = $responden->f_level_of_work ?? $insertData['level_work'];
                    $insertData['f_nip'] = $responden->nip ?? $insertData['f_nip'];
                    $insertData['f_length_of_service'] = $responden->f_length_of_service ?? $insertData['f_length_of_service'];
                    $insertData['f_region'] = $responden->f_region ?? $insertData['f_region'];


                    $nama = $responden->f_name ?? $insertData['f_survey_username'];


                    $this->name_responden = $responden->f_name ?? $this->name_responden;
$this->email_responden = $responden->f_email ?? $this->email_responden;

                }
            }

            DB::table('trn_survey_empex')->insertOrIgnore([$insertData]);
            if ($events->f_event_type == 1) {
                $updated = DB::table('t_master_nip')
                    ->where('nip', $data['nip'])
                    ->whereRaw('sha1(id_account) = ?', [$data['account_id']])
                    ->update([
                        'f_survey_valid' => "yes",
                        "f_survey_date" => now()
                    ]);
            }

        }


            // Buat akun user jika belum ada
            $user = DB::table('users')->where('email', $data['email'])->first();
            if (!$user) {
                $hashed = Hash::make($data['email']);
                $user_id = DB::table('users')->insertGetId([
                    'ip_address' => request()->ip(),
                    'username' => $this->name_responden,
                    'first_name' => $this->name_responden,
                    'password' => $hashed,
                    'email' => $data['email'],
                    'created_on' => time(),
                    'active' => 1,
                    'phone' => null,
                    'f_account_id' => $account->f_account_id,
                ]);
                DB::table('users_groups')->insert(['user_id' => $user_id, 'group_id' => 3]);

                $user_info = ['user' => $this->name_responden, 'email' => $data['email'], 'pass' => $data['email'], 'status' => 'edit'];
            } else {
                $user_info = ['user' => $user->username, 'email' => $data['email'], 'pass' => '(gunakan password sebelumnya)', 'status' => 'edit'];
            }

        $user_info['link_isi'] = 'https://app.talentdna.me/';

        Mail::send('vw_email_akses_quota', $user_info, function ($m) use ($data) {
            $m->from('info@talentdna.me', 'Talent DNA')
                ->to($data['email'])
                ->bcc('esqtraining2@esq165.co.id')
                ->subject('Selamat! Keunikan TalentDNA Anda Telah Tersedia dalam Report');
        });


    }
}
