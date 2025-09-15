<?php

namespace App\Http\Controllers;

use App\Models\Level6;
use App\Models\Level7;
use Illuminate\Http\Request;
use App\Exports\Level7Export;
use App\Imports\Level7Import;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Level7Controller extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Level 7',
            'btn_add' => 'Tambah Level 7'
        ];
        $data['level7'] = Level7::where('f_account_id', Auth::user()->f_account_id)->with('relasi_level6')->get();
        $data['level6'] = Level6::where('f_account_id', Auth::user()->f_account_id)->get();

        return view('master_data.level7', $data);
    }


    public function store(Request $request)
    {


        $request->validate([
            'f_position_desc' => 'required|string',
            'f_id6'=>'required'
        ]);

        $data = Level7::create([
            'f_position_desc' => $request->input('f_position_desc'),
            'f_id6'=> $request->input('f_id6'),
            'f_account_id' => Auth::user()->f_account_id,
        ]);

        if ($data) {
            return redirect()->back()->with('success', 'Berhasil tambah data');
        } else {
            return redirect()->back()->with('error', 'Gagal tambah data');
        }
    }

    public function hapus($id)
    {
        $data = Level7::where('f_id', $id)->first();

        if ($data && $data->delete()) {
            return response()->json(['status' => true, 'message' => 'Data berhasil dihapus.']);
        }

        return response()->json(['status' => false, 'message' => 'Gagal menghapus data.']);
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'f_position_desc' => 'required|string|max:255',
            'f_id6'=>'required'
        ]);

        $data = Level7::find($request->input('id_f_position_desc'));

        if ($data) {
            $data->f_position_desc = $validatedData['f_position_desc'];
            $data->f_id6 = $validatedData['f_id6'];
            $data->save();

            return redirect()->back()->with('success', 'Data berhasil diperbarui');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }

    public function export()
    {
        return Excel::download(new Level7Export, 'template_level7.xlsx');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new Level7Import, $request->file('file'));

        return back()->with('success', 'Data berhasil diimport!');
    }

    public function export_value(): StreamedResponse
    {
        // Ambil semua data
        $data = Level7::where('f_account_id', Auth::user()->f_account_id)->get();

        if ($data->isEmpty()) {
            abort(404, 'Tidak ada data untuk diekspor.');
        }

        // Ambil nama kolom dari item pertama
        $header = array_keys($data->first()->getAttributes());

        // Inisialisasi spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('level7');

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
        $filename = 'data-level7.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

}
