<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\UsiaController;
use App\Http\Controllers\Level1Controller;
use App\Http\Controllers\Level2Controller;
use App\Http\Controllers\Level3Controller;
use App\Http\Controllers\Level4Controller;
use App\Http\Controllers\Level5Controller;
use App\Http\Controllers\Level6Controller;
use App\Http\Controllers\Level7Controller;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasaKerjaController;
use App\Http\Controllers\MasterNipController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PendidikanController;
use App\Http\Controllers\UserClientController;
use App\Http\Controllers\EventClientController;
use App\Http\Controllers\JenisKelaminController;
use App\Http\Controllers\Reports;
use App\Http\Controllers\AccountClientController;
use App\Http\Controllers\PengaturanAkunController;
use App\Http\Controllers\TingkatPekrjaanController;
use App\Http\Controllers\AppearanceSettingController;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



    Route::get('monitoring-event/{id}', [MonitoringController::class, 'event_monitoring'])->name('monitoring.event');

    Route::get('quick-count-event/{id}', [MonitoringController::class, 'quick_count_monitoring'])->name('quick.event');
    Route::post('get-quick-count/{id}', [MonitoringController::class, 'get_quick_count'])->name('getquick.event');
    Route::get('update-count-event/{id}', [MonitoringController::class, 'update_quick_count'])->name('updatequick.event');

    

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Route::get('monitoring', [MonitoringController::class,'index']);
    Route::get('monitoring', [MonitoringController::class, 'show'])->name('monitoring.show');



    Route::put('/settings/appearance', [AppearanceSettingController::class, 'update'])->name('settings.appearance.update');
    Route::get('/settings/appearance', [AppearanceSettingController::class, 'index'])->name('settings.appearance.index');



});
Route::get('monitoring/{id}/{id_event}', [MonitoringController::class, 'monitoring_user'])->name('monitoring.monitoring_user');
;
// Route::get('monitoring', [MonitoringController::class,'show'])->name('monitoring.show');;
// Route::get('/monitoring/show', [MonitoringController::class, 'show'])


Route::get('/survey/{token}', [SurveyController::class, 'showSurvey'])->name('survey.show');

Route::post('/check_pengisian', [SurveyController::class, 'checkPengisian'])->name('survey.check');

Route::post('/survey/submit', [SurveyController::class, 'saveSurveyCorporate'])->name('survey.submit');

Route::get('/check-nip', [MasterNipController::class, 'checkNip'])->name('check.nip');



Route::post('/get_level', [SurveyController::class, 'getLevel'])->name('survey.getLevel');
// Route::get('monitoring/{id}', [MonitoringController::class,'show']);


Route::prefix('/settings')->group(function () {
    Route::get('akun', [PengaturanAkunController::class, 'akun'])->name('setting.akun.index');
    Route::post('save_setting', [PengaturanAkunController::class, 'save_setting'])->name('setting.akun.save');
    Route::post('save_halaman_setting', [PengaturanAkunController::class, 'save_halaman_setting'])->name('setting.akun.save_halaman_setting');
    Route::post('import_responden', [PengaturanAkunController::class, 'import_responden'])->name('setting.akun.import_responden');
    Route::get('responden/{id}', [PengaturanAkunController::class, 'responden'])->name('setting.akun.responden');
    Route::get('responden_import', [PengaturanAkunController::class, 'download_template'])->name('setting.responden.download_format');

});


Route::prefix('/master_data')->middleware('role:2')->group(function () {

    Route::get('level1', [Level1Controller::class, 'index'])->name('master_data.level1.index');
    Route::post('level1_store', [Level1Controller::class, 'store'])->name('master_data.level1.store');
    Route::put('level1_update', [Level1Controller::class, 'update'])->name('master_data.level1.update');
    Route::delete('level1_hapus/{id}', [Level1Controller::class, 'hapus'])->name('master_data.level1.hapus');
    Route::get('export_level1', [Level1Controller::class, 'export'])->name('master_data.level1.export');
    Route::post('import_level1', [Level1Controller::class, 'import'])->name('master_data.level1.import');
    Route::get('export_value_level1', [Level1Controller::class, 'export_value'])->name('master_data.export_value_level1');


    Route::get('level2', [Level2Controller::class, 'index'])->name('master_data.level2.index');
    Route::post('level2_store', [Level2Controller::class, 'store'])->name('master_data.level2.store');
    Route::put('level2_update', [Level2Controller::class, 'update'])->name('master_data.level2.update');
    Route::delete('level2_hapus/{id}', [Level2Controller::class, 'hapus'])->name('master_data.level2.hapus');
    Route::get('export_level2', [Level2Controller::class, 'export'])->name('master_data.level2.export');
    Route::post('import_level2', [Level2Controller::class, 'import'])->name('master_data.level2.import');
    Route::get('export_value_level2', [Level2Controller::class, 'export_value'])->name('master_data.export_value_level2');


    Route::get('level3', [Level3Controller::class, 'index'])->name('master_data.level3.index');
    Route::post('level3_store', [Level3Controller::class, 'store'])->name('master_data.level3.store');
    Route::put('level3_update', [Level3Controller::class, 'update'])->name('master_data.level3.update');
    Route::delete('level3_hapus/{id}', [Level3Controller::class, 'hapus'])->name('master_data.level3.hapus');
    Route::get('export_level3', [Level3Controller::class, 'export'])->name('master_data.level3.export');
    Route::post('import_level3', [Level3Controller::class, 'import'])->name('master_data.level3.import');
    Route::get('export_value_level3', [Level3Controller::class, 'export_value'])->name('master_data.export_value_level3');


    Route::get('level4', [Level4Controller::class, 'index'])->name('master_data.level4.index');
    Route::post('level4_store', [Level4Controller::class, 'store'])->name('master_data.level4.store');
    Route::put('level4_update', [Level4Controller::class, 'update'])->name('master_data.level4.update');
    Route::delete('level4_hapus/{id}', [Level4Controller::class, 'hapus'])->name('master_data.level4.hapus');
    Route::get('export_level4', [Level4Controller::class, 'export'])->name('master_data.level4.export');
    Route::post('import_level4', [Level4Controller::class, 'import'])->name('master_data.level4.import');
    Route::get('export_value_level4', [Level4Controller::class, 'export_value'])->name('master_data.export_value_level4');


    Route::get('level5', [Level5Controller::class, 'index'])->name('master_data.level5.index');
    Route::post('level5_store', [Level5Controller::class, 'store'])->name('master_data.level5.store');
    Route::put('level5_update', [Level5Controller::class, 'update'])->name('master_data.level5.update');
    Route::delete('level5_hapus/{id}', [Level5Controller::class, 'hapus'])->name('master_data.level5.hapus');
    Route::get('export_level5', [Level5Controller::class, 'export'])->name('master_data.level5.export');
    Route::post('import_level5', [Level5Controller::class, 'import'])->name('master_data.level5.import');
    Route::get('export_value_level5', [Level5Controller::class, 'export_value'])->name('master_data.export_value_level5');

    Route::get('level6', [Level6Controller::class, 'index'])->name('master_data.level6.index');
    Route::post('level6_store', [Level6Controller::class, 'store'])->name('master_data.level6.store');
    Route::put('level6_update', [Level6Controller::class, 'update'])->name('master_data.level6.update');
    Route::delete('level6_hapus/{id}', [Level6Controller::class, 'hapus'])->name('master_data.level6.hapus');
    Route::get('export_level6', [Level6Controller::class, 'export'])->name('master_data.level6.export');
    Route::post('import_level6', [Level6Controller::class, 'import'])->name('master_data.level6.import');
    Route::get('export_value_level6', [Level6Controller::class, 'export_value'])->name('master_data.export_value_level6');


    Route::get('level7', [Level7Controller::class, 'index'])->name('master_data.level7.index');
    Route::post('level7_store', [Level7Controller::class, 'store'])->name('master_data.level7.store');
    Route::put('level7_update', [Level7Controller::class, 'update'])->name('master_data.level7.update');
    Route::delete('level7_hapus/{id}', [Level7Controller::class, 'hapus'])->name('master_data.level7.hapus');
    Route::get('export_level7', [Level7Controller::class, 'export'])->name('master_data.level7.export');
    Route::post('import_level7', [Level7Controller::class, 'import'])->name('master_data.level7.import');
    Route::get('export_value_level7', [Level7Controller::class, 'export_value'])->name('master_data.export_value_level7');


    Route::get('jenis_kelamin', [JenisKelaminController::class, 'index'])->name('master_data.jenis_kelamin.index');
    Route::post('jenis_kelamin_store', [JenisKelaminController::class, 'store'])->name('master_data.jenis_kelamin.store');
    Route::put('jenis_kelamin_update', [JenisKelaminController::class, 'update'])->name('master_data.jenis_kelamin.update');
    Route::delete('jenis_kelamin_hapus/{id}', [JenisKelaminController::class, 'hapus'])->name('master_data.jenis_kelamin.hapus');
    Route::get('export_jenis_kelamin', [JenisKelaminController::class, 'export'])->name('master_data.jenis_kelamin.export');
    Route::post('import_jenis_kelamin', [JenisKelaminController::class, 'import'])->name('master_data.jenis_kelamin.import');
    Route::get('export_value_jenis_kelamin', [JenisKelaminController::class, 'export_value'])->name('master_data.export_value_jenis_kelamin');


    Route::get('usia', [UsiaController::class, 'index'])->name('master_data.usia.index');
    Route::post('usia_store', [UsiaController::class, 'store'])->name('master_data.usia.store');
    Route::put('usia_update', [UsiaController::class, 'update'])->name('master_data.usia.update');
    Route::delete('usia_hapus/{id}', [UsiaController::class, 'hapus'])->name('master_data.usia.hapus');
    Route::get('export_usia', [UsiaController::class, 'export'])->name('master_data.usia.export');
    Route::post('import_usia', [UsiaController::class, 'import'])->name('master_data.usia.import');
    Route::get('export_value_usia', [UsiaController::class, 'export_value'])->name('master_data.export_value_usia');


    Route::get('masa_kerja', [MasaKerjaController::class, 'index'])->name('master_data.masa_kerja.index');
    Route::post('masa_kerja_store', [MasaKerjaController::class, 'store'])->name('master_data.masa_kerja.store');
    Route::put('masa_kerja_update', [MasaKerjaController::class, 'update'])->name('master_data.masa_kerja.update');
    Route::delete('masa_kerja_hapus/{id}', [MasaKerjaController::class, 'hapus'])->name('master_data.masa_kerja.hapus');
    Route::get('export_masa_kerja', [MasaKerjaController::class, 'export'])->name('master_data.masa_kerja.export');
    Route::post('import_masa_kerja', [MasaKerjaController::class, 'import'])->name('master_data.masa_kerja.import');
    Route::get('export_value_masa_kerja', [MasaKerjaController::class, 'export_value'])->name('master_data.export_value_masa_kerja');



    Route::get('wilayah', [WilayahController::class, 'index'])->name('master_data.wilayah.index');
    Route::post('wilayah_store', [WilayahController::class, 'store'])->name('master_data.wilayah.store');
    Route::put('wilayah_update', [WilayahController::class, 'update'])->name('master_data.wilayah.update');
    Route::delete('wilayah_hapus/{id}', [WilayahController::class, 'hapus'])->name('master_data.wilayah.hapus');
    Route::get('export_wilayah', [WilayahController::class, 'export'])->name('master_data.wilayah.export');
    Route::post('import_wilayah', [WilayahController::class, 'import'])->name('master_data.wilayah.import');
    Route::get('export_value_wilayah', [WilayahController::class, 'export_value'])->name('master_data.export_value_wilayah');



    Route::get('tingkat_pekerjaan', [TingkatPekrjaanController::class, 'index'])->name('master_data.tingkat_pekerjaan.index');
    Route::post('tingkat_pekerjaan_store', [TingkatPekrjaanController::class, 'store'])->name('master_data.tingkat_pekerjaan.store');
    Route::put('tingkat_pekerjaan_update', [TingkatPekrjaanController::class, 'update'])->name('master_data.tingkat_pekerjaan.update');
    Route::delete('tingkat_pekerjaan_hapus/{id}', [TingkatPekrjaanController::class, 'hapus'])->name('master_data.tingkat_pekerjaan.hapus');
    Route::get('export_tingkat_pekerjaan', [TingkatPekrjaanController::class, 'export'])->name('master_data.tingkat_pekerjaan.export');
    Route::post('import_tingkat_pekerjaan', [TingkatPekrjaanController::class, 'import'])->name('master_data.tingkat_pekerjaan.import');
    Route::get('export_value_tingkat_pekerjaan', [TingkatPekrjaanController::class, 'export_value'])->name('master_data.export_value_tingkat_pekerjaan');


    Route::get('pendidikan', [PendidikanController::class, 'index'])->name('master_data.pendidikan.index');
    Route::post('pendidikan_store', [PendidikanController::class, 'store'])->name('master_data.pendidikan.store');
    Route::put('pendidikan_update', [PendidikanController::class, 'update'])->name('master_data.pendidikan.update');
    Route::delete('pendidikan_hapus/{id}', [PendidikanController::class, 'hapus'])->name('master_data.pendidikan.hapus');
    Route::get('export_pendidikan', [PendidikanController::class, 'export'])->name('master_data.pendidikan.export');
    Route::post('import_pendidikan', [PendidikanController::class, 'import'])->name('master_data.pendidikan.import');
    Route::get('export_value_pendidikan', [PendidikanController::class, 'export_value'])->name('master_data.export_value_pendidikan');





});

Route::get('get_child/{level}/{id}', [MonitoringController::class, 'get_child'])->name('get_child');




Route::controller(AccountClientController::class)->group(function () {
    Route::get('/account_client', 'index')->name('account.index');
    Route::get('/account_client/add', 'add')->name('account.add');
    Route::get('/account_client/edit/{id}', 'edit')->name('account.edit');
    Route::post('/account_client/store', 'store')->name('account.store');
    Route::put('/account_client/update', 'update')->name('account.update');
    Route::delete('/account_client/destroy/{id}', 'destroy')->name('account.destroy');
    
});


Route::controller(EventClientController::class)->group(function () {
    Route::get('/event_client', 'index')->name('event.index');
    Route::get('/event_client/add', 'add')->name('event.add');
    Route::get('/event_client/edit/{id}', 'edit')->name('event.edit');
    Route::post('/event_client/store', 'store')->name('event.store');
    Route::put('/event_client/update', 'update')->name('event.update');
    Route::delete('/event_client/destroy/{id}', 'destroy')->name('event.destroy');
});


Route::controller(UserClientController::class)->group(function () {
    Route::get('/user_client', 'index')->name('user_client.index');
    Route::get('/user_client/add', 'add')->name('user_client.add');
    Route::post('/user_client/store', 'store')->name('user_client.store');
});


Route::put('/settings/appearance', [AppearanceSettingController::class, 'update'])->name('settings.appearance.update');
Route::get('/settings/appearance', [AppearanceSettingController::class, 'index'])->name('settings.appearance.index');


Route::get('/remove-session/{key}', function ($key) {
    if (Session::has($key)) {
        Session::forget($key);
        return response()->json([
            'success' => true,
            'message' => "Session '$key' berhasil dihapus."
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => "Session '$key' tidak ditemukan."
    ]);
})->name('remove.session');


Route::get('/coba_email', function () {
    $user_info = [
        'user' => "Aliwafa",
        'email' => "muhamad.aliwafa@esq165.co.id",
        'pass' => "esq12345",
        'status' => 'edit',
        'link_isi' => 'https://app.talentdna.me/'
    ];

    Mail::send('vw_email_akses_quota', $user_info, function ($message) use ($user_info) {
        $message->from(config('mail.from.address'), config('mail.from.name'))
            ->to($user_info['email'])
            ->bcc('esqtraining2@esq165.co.id')
            ->subject('Selamat! Keunikan TalentDNA Anda Telah Tersedia dalam Report');
    });

    return "✅ Email berhasil dikirim ke {$user_info['email']}";
});

Route::get('/reports/createPdf/{id}', [Reports::class, 'createPdf']);
Route::get('/generate-zip', [Reports::class, 'downloadPdf']);


require __DIR__ . '/auth.php';
