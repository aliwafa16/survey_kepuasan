<?php

namespace App\Http\Controllers;
use App\Models\TrnSurveyEmpex; // Model untuk tabel trn_survey_empex
use App\Models\Variabel;
use App\Models\Dimensi;
use Illuminate\Support\Facades\DB;

use ZipArchive;

use Illuminate\Http\Request;
//use Elibyy\TCPDF\Facades\TCPDF;
use TCPDF; // langsung gunakan class TCPDF


class Reports extends Controller
{

    public function index()
    {


    }

    public function createPdf($id)
    {
        $email = strtolower($id);//'suzianieasiana@gmail.com';
        $row = TrnSurveyEmpex::where('f_email', $email)->first(); // Mengambil satu record
        $image_profil = 'SILHOUETTE.png'; // Inisialisasi image_profil
        $to_pdf = []; // Inisialisasi array untuk data PDF
        if ($row) {
            $jw_survey = json_decode($row->f_survey, true); // Decode JSON dari kolom f_survey
            $to_pdf = [
                'image_profil' => $image_profil,
                'f_survey_username' => $row->f_survey_username,
                'f_survey_password' => $row->f_survey_password,
                'f_email' => $row->f_email,
                'f_bahasa' => $row->f_bahasa,
                'tgl_selesai' => $row->f_survey_updated_on,
                'report_type' => $row->f_report_type,
                'topten' => $jw_survey['topten'] ?? null, // Menggunakan null jika tidak ada
                'total_dimensi' => $jw_survey['total_dimensi'] ?? null,
                'soal_perdimensi' => $jw_survey['soal_perdimensi'] ?? null,
            ];
            // dd($to_pdf);die();

            if($row->f_report_type == 10){
            createPDF10($to_pdf);

            }else{
            createPDF45($to_pdf);

            }
        }
    }

    public function downloadPdf(Request $request)
    {
        $id = $request->get('id');
        $surveys = DB::table('trn_survey_empex')
            // ->whereIn('f_email', $emails)
            // ->whereIn('sha1(md5(f_id))', explode(",",$id))
            ->whereIn(DB::raw('sha1(md5(f_id))'), explode(",", $id))
            ->where('f_survey_valid','yes')
            ->orderBy('f_id', 'asc')
            ->get();

        if ($surveys->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data'], 404);
        }

        // Nama folder dan file ZIP
        $nama_zip = 'report_' . now()->format('Ymd');
        $zipPath = public_path("assets/pdf/{$nama_zip}.zip");
        $pdfDir = public_path("assets/pdf/{$nama_zip}");

        // Buat direktori jika belum ada
        if (!file_exists($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }

        // Inisialisasi ZipArchive
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Gagal membuat file ZIP'], 500);
        }

        foreach ($surveys as $survey) {
            // Decode JSON survey
            $jw_survey = json_decode($survey->f_survey, true);

            // Data untuk PDF
            $image_profil = $survey->f_bahasa !== 'id-ID' ? 'SILHOUETTE_ENG.png' : 'SILHOUETTE.png';
            $to_pdf = [
                'image_profil' => $image_profil,
                'f_survey_username' => $survey->f_survey_username,
                'f_survey_password' => $survey->f_survey_password,
                'f_email' => $survey->f_email,
                'f_bahasa' => $survey->f_bahasa,
                'tgl_selesai' => $survey->f_survey_updated_on,
                'f_report_type' => $survey->f_report_type,
                'report_type' => $survey->f_report_type,
                'topten' => $jw_survey['topten'],
                'total_dimensi' => $jw_survey['total_dimensi'],
                'soal_perdimensi' => $jw_survey['soal_perdimensi'],
            ];

            // Generate PDF
            $nama_file = 'Result_' . str_replace(' ', '_', $survey->f_survey_username) . '-' . str_replace(' ', '_', $survey->f_email) . '.pdf';
            $pdfPath = "{$pdfDir}/{$nama_file}";

            // Tambahkan file PDF ke ZIP

            // Jika report_talent == 6 atau 11 atau 65, tambahkan file Career PDF
            $report_talent = $survey->f_report_type;
            if ($report_talent == 45 || $report_talent == 65) {
                createPDF45Path($to_pdf, $nama_zip, 'F');
            } else {
                createPDF10Path($to_pdf, $nama_zip, 'F');
            }
            $zip->addFile($pdfPath, $nama_file);

            sleep(0.5); // Simulasi delay
        }

        // Tutup arsip ZIP
        $zip->close();

        // Hapus folder sementara
        $this->deleteDirectory($pdfDir);

        // Kirim file ZIP ke browser
        return response()->download($zipPath)->deleteFileAfterSend(true);

    }

    private function deleteDirectory($dir)
    {
        foreach (glob("{$dir}/*") as $file) {
            if (is_dir($file)) {
                $this->deleteDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }

}
