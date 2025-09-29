<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemPernyataanModel;

class ItemPernyataanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 10);
        $q = trim((string) $request->input('q', ''));

        $data = ItemPernyataanModel::query()
            ->with('variabel:f_id,f_variabel_name')
            ->when($q, function($s) use($q){
                $s->where('f_kode', 'like', "%{$q}%")
                  ->orWhere('f_item', 'like', "%{$q}%")
                  ->orWhere('f_item_eng', 'like', "%{$q}%")
                  ->orWhere('f_kategori', 'like', "%{$q}%");
            })
            ->orderByDesc('f_id')
            ->paginate($perPage)
            ->appends($request->only('q','perPage'));

        return view('master_data.item_pernyataan', [
            'title'    => 'Item Pernyataan',
            'data'     => $data,
            'q'        => $q,
            'perPage'  => $perPage,
        ]);
    }

    public function create()
    {
        return view('master_data.item_pernyataan.create', [
            'title'    => 'Tambah Item Pernyataan',
            'row'      => new ItemPernyataanModel(),
            'variabel' => Variabel::orderBy('f_variabel_name')->get(['f_id','f_variabel_name']),
        ]);
    }

    public function store(Request $request)
    {
        // Validasi langsung di controller
        $validated = $request->validate([
            'f_kode'        => 'required|string|max:50',
            'f_item'        => 'required|string|max:2000',
            'f_item_eng'    => 'nullable|string|max:2000',
            'f_answer'      => 'nullable|string|max:1000',
            'type'          => 'required|string|max:50',
            'f_variabel_id' => 'required|integer|exists:t_variabel,f_id',
            'f_dimensi_id'  => 'nullable|integer',
            'f_kategori'    => 'nullable|string|max:255',
            'free'          => 'nullable|string|max:255',
        ], [
            'f_kode.required'         => 'Kode wajib diisi.',
            'f_item.required'         => 'Teks item (Indonesia) wajib diisi.',
            'type.required'           => 'Tipe wajib diisi.',
            'f_variabel_id.required'  => 'Variabel wajib dipilih.',
            'f_variabel_id.exists'    => 'Variabel tidak ditemukan.',
        ]);

        ItemPernyataanModel::create($validated);

        return redirect()
            ->route('master_data.item-pernyataan')
            ->with('message', 'Item pernyataan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = ItemPernyataanModel::where('f_id', $id)->firstOrFail();

        return view('master_data.item_pernyataan.edit', [
            'title'    => 'Edit Item Pernyataan',
            'row'      => $row,
            'variabel' => Variabel::orderBy('f_variabel_name')->get(['f_id','f_variabel_name']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'f_kode'        => 'required|string|max:50',
            'f_item'        => 'required|string|max:2000',
            'f_item_eng'    => 'nullable|string|max:2000',
            'f_answer'      => 'nullable|string|max:1000',
            'type'          => 'required|string|max:50',
            'f_variabel_id' => 'required|integer|exists:t_variabel,f_id',
            'f_dimensi_id'  => 'nullable|integer',
            'f_kategori'    => 'nullable|string|max:255',
            'free'          => 'nullable|string|max:255',
        ]);

        $row = ItemPernyataanModel::where('f_id', $id)->firstOrFail();
        $row->update($validated);

        return redirect()
            ->route('master_data.item-pernyataan')
            ->with('message', 'Item pernyataan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $row = ItemPernyataanModel::where('f_id', $id)->firstOrFail();
        $row->delete();

        // Jika request AJAX (hapus via Swal), balas JSON
        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => 'Item berhasil dihapus.']);
        }
        return back()->with('message', 'Item berhasil dihapus.');
    }
}
