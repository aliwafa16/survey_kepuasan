<?php

namespace App\Http\Controllers;

use App\Models\Level3;
use App\Models\Level4;
use Illuminate\Http\Request;
use App\Exports\Level4Export;
use App\Imports\Level4Import;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Level4Controller extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Level 4',
            'btn_add' => 'Tambah Level 4'
        ];
        $data['level4'] = Level4::where('f_account_id', Auth::user()->f_account_id)->with('relasi_level3')->get();
        $data['level3'] = Level3::where('f_account_id', Auth::user()->f_account_id)->get();

        return view('master_data.level4', $data);
    }


    public function store(Request $request)
    {


        $request->validate([
            'f_position_desc' => 'required|string',
            'f_id3'=>'required'
        ]);

        $data = Level4::create([
            'f_position_desc' => $request->input('f_position_desc'),
            'f_id3'=> $request->input('f_id3'),
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
        $data = Level4::where('f_id', $id)->first();

        if ($data && $data->delete()) {
            return response()->json(['status' => true, 'message' => 'Data berhasil dihapus.']);
        }

        return response()->json(['status' => false, 'message' => 'Gagal menghapus data.']);
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'f_position_desc' => 'required|string|max:255',
            'f_id3'=>'required'
        ]);

        $data = Level4::find($request->input('id_f_position_desc'));

        if ($data) {
            $data->f_position_desc = $validatedData['f_position_desc'];
            $data->f_id3 = $validatedData['f_id3'];
            $data->save();

            return redirect()->back()->with('success', 'Data berhasil diperbarui');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }


    public function export()
    {
        return Excel::download(new Level4Export, 'template_level4.xlsx');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new Level4Import, $request->file('file'));

        return back()->with('success', 'Data berhasil diimport!');
    }


        public function export_value(): StreamedResponse
{
    // Ambil semua data
    $data = Level4::where('f_account_id', Auth::user()->f_account_id)->get();

    if ($data->isEmpty()) {
        abort(404, 'Tidak ada data untuk diekspor.');
    }

    // Ambil nama kolom dari item pertama
    $header = array_keys($data->first()->getAttributes());

    // Inisialisasi spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('level4');

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
    $filename = 'data-level4.xlsx';

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}

    

}
