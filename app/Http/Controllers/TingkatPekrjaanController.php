<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TingkatPekerjaan;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TingkatPekerjaanExport;
use App\Imports\TingkatPekerjaanImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TingkatPekrjaanController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Tingkat Pekerjaan',
            'btn_add' => 'Tambah Tingkat Pekerjaan'
        ];
        $data['data'] = TingkatPekerjaan::where('f_account_id', Auth::user()->f_account_id)->get();
        return view('master_data.tingkat_pekerjaan', $data);
    }


    public function store(Request $request)
    {


        $request->validate([
            'f_levelwork_desc' => 'required|string'
        ]);

        $data = TingkatPekerjaan::create([
            'f_levelwork_desc' => $request->input('f_levelwork_desc'),
            'f_account_id' => Auth::user()->f_account_id,
            'f_age_aktif' =>1,
        ]);

        if ($data) {
            return redirect()->back()->with('success', 'Berhasil tambah data');
        } else {
            return redirect()->back()->with('error', 'Gagal tambah data');
        }
    }

    public function hapus($id)
    {
        $data = TingkatPekerjaan::where('f_id', $id)->first();

        if ($data && $data->delete()) {
            return response()->json(['status' => true, 'message' => 'Data berhasil dihapus.']);
        }

        return response()->json(['status' => false, 'message' => 'Gagal menghapus data.']);
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'f_levelwork_desc' => 'required|string|max:255',
        ]);

        $data = TingkatPekerjaan::find($request->input('id_f_levelwork_desc'));

        if ($data) {
            $data->f_levelwork_desc = $validatedData['f_levelwork_desc'];
            $data->save();

            return redirect()->back()->with('success', 'Data berhasil diperbarui');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }

    public function export(){
        return Excel::download(new TingkatPekerjaanExport, 'template_tingkat_pekerjaan.xlsx');
    }


    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls',
    ]);

    Excel::import(new TingkatPekerjaanImport, $request->file('file'));

    return back()->with('success', 'Data berhasil diimport!');
}


public function export_value(): StreamedResponse
{
    // Ambil semua data
    $data = TingkatPekerjaan::where('f_account_id', Auth::user()->f_account_id)->get();

    if ($data->isEmpty()) {
        abort(404, 'Tidak ada data untuk diekspor.');
    }

    // Ambil nama kolom dari item pertama
    $header = array_keys($data->first()->getAttributes());

    // Inisialisasi spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('level1');

    // Isi header di baris pertama
    $sheet->fromArray($header, null, 'A1');

    // Ubah isi data ke array baris-baris
    $rows = $data->map(function ($item) {
        return array_values($item->getAttributes());
    })->toArray();

    // Masukkan ke baris ke-2 ke bawah
    $sheet->fromArray($rows, null, 'A2');

    // Simpan dan download
    $writer = new Xlsx($spreadsheet);
    $filename = 'data-tingkat-pekerjaan.xlsx';

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}
}
