<?php

namespace App\Http\Controllers;

use App\Models\EventClient;
use App\Models\TrnSurvey;
use Illuminate\Http\Request;
use App\Models\AccountClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Str;

class EventClientController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Management Event Client',
            'btn_add' => "Tambah data"
        ];
        $data['event_client'] = EventClient::with('akun_client')->paginate(10);
        return view('event_client.event_client', $data);
    }


    public function add()
    {
        $data = [
            'title' => 'Add Event Client',
            'btn_add' => "Tambah data"
        ];


        $data['akun'] = AccountClient::get();

        // dd($data['akun']);
        return view('event_client.add', $data);
    }



    public function store(Request $request)
    {

        try {

            DB::beginTransaction();
            $validated = $request->validate([
                // 'f_corporate_id' => 'required',
                'f_event_name' => 'required|string|max:255',
                'f_event_start' => 'required|date',
                'f_event_start_time' => 'required|date_format:H:i',
                'f_event_end' => 'required|date|after_or_equal:f_event_start',
                'f_event_end_time' => 'required|date_format:H:i',
                'f_event_status' => 'nullable|boolean',
                'f_event_min_respon' => 'required'
            ],[
                // Custom error messages
                'f_event_start.required' => 'Tanggal mulai event wajib diisi.',
                'f_event_start.date' => 'Format tanggal mulai tidak valid.',
                'f_event_end.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
                'f_event_start_time.required' => 'Jam mulai event wajib diisi.',
                'f_event_start_time.date_format' => 'Format jam mulai harus HH:ii (contoh: 08:00).',
                'f_event_min_respon.required' => 'Jumlah minimal responden harus diisi.',
                'f_event_min_respon.integer' => 'Jumlah minimal responden harus berupa angka.',
                // Tambah lainnya sesuai kebutuhan
            ]);

            // Konversi checkbox menjadi boolean
            $validated['f_event_status'] = $request->has('f_event_status') ? 1 : 0;
            $validated['f_event_kode'] = sha1("ASSESMENTSKPPP" . Str::random(10));
            $validated['f_event_created_by'] = Auth::user()->username;
            $validated['f_event_respon'] = 0; //Default


            // // Cek apakah kuota masih tersedia dari yang sudah digunakan
            // $kuota = AccountClient::where('f_account_id', $validated['f_corporate_id'])->value('f_account_token');
            // $kuota_digunakan = TrnSurvey::where('f_corporate_id', $validated['f_corporate_id'])->count();


            // // Hitung
            // $sisa_kuota = $kuota - $kuota_digunakan;

            // // Validasi sisa kuota
            // if ($sisa_kuota < $validated['f_event_min_respon']) {
            //     throw new \Exception('Sisa kuota hanya ' . $sisa_kuota . '. Tidak cukup untuk minimal respon ' . $validated['f_event_min_respon']);
            // }


            $dataAccout = EventClient::create($validated);

            DB::commit();
            return redirect()->route('event.index')->with('success', 'Berhasil tambah data');
        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }

    }


    public function edit($id)
    {
        $dataAccout = EventClient::where('f_event_id', $id)->first();
        $data = [
            'title' => 'Add Account Client',
            'btn_add' => "Tambah data",
            'data_account' => $dataAccout
        ];

        $data['akun'] = AccountClient::where('is_corporate', 1)
            ->when(Auth::user()->f_role !== 1, function ($query) {
                $query->where('f_account_id', Auth::user()->f_account_id);
            })
            ->get();

        return view('event_client.edit', $data);
    }


    public function update(Request $request)
    {

        try {

            DB::beginTransaction();
            $validated = $request->validate([
                'f_corporate_id' => 'required',
                'f_event_name' => 'required|string|max:255',
                'f_event_start' => 'required|date',
                'f_event_start_time' => 'required|date_format:H:i',
                'f_event_end' => 'required|date|after_or_equal:f_event_start',
                'f_event_end_time' => 'required|date_format:H:i',
                'f_event_status' => 'nullable|boolean',
                'f_event_min_respon' => 'required'
            ]);

            // Konversi checkbox menjadi boolean
            $validated['f_event_status'] = $request->has('f_event_status') ? 1 : 0;
            // $validated['f_event_kode'] = sha1("TDNACORPORATE" . Str::random(10));
            $validated['f_event_updated_by'] = Auth::user()->username;
            // $validated['is_corporate'] = 1; //Wajib 1
            // $validated['f_event_respon'] = 0; //Default

            $dataAccout = EventClient::where('f_event_id', $request->input('f_event_id'))->update($validated);

            DB::commit();
            return redirect()->route('event.index')->with('success', 'Berhasil edit data');
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
        $akun = EventClient::where('is_corporate', 1)
            ->where('f_event_id', $id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data corporate berhasil dihapus.'
        ]);
    }
}
