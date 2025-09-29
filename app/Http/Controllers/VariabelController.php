<?php

namespace App\Http\Controllers;

use App\Models\Variabel;
use Illuminate\Http\Request;

class VariabelController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int)($request->input('perPage', 10));
        $q = trim((string)$request->input('q', ''));

        $data = Variabel::query()
            ->when($q, function ($s) use ($q) {
                $s->where('f_variabel_name', 'like', "%{$q}%")
                  ->orWhere('free', 'like', "%{$q}%");
            })
            ->orderByDesc('f_id')
            ->paginate($perPage)
            ->appends($request->only('q','perPage'));


        return view('master_data.variabel', [
            'data' => $data,
            'q' => $q,
            'perPage' => $perPage,
            'main_title' => 'Master Data',
            'title' => 'Variabel',
        ]);
    }

    public function create()
    {
        return view('master_data.variabel.create', [
            'main_title' => 'Master Data',
            'title' => 'Tambah Variabel',
            'row' => new Variabel(),
        ]);
    }

    public function store(Request $request)
    {
        // VALIDASI DI CONTROLLER
        $validated = $request->validate(
            [
                'f_variabel_name' => 'required|string|max:255',
                'f_aktif'         => 'required|in:0,1',
                'free'            => 'nullable|string|max:255',
            ],
            [
                'f_variabel_name.required' => 'Nama Variabel wajib diisi.',
                'f_variabel_name.max'      => 'Nama Variabel maksimal 255 karakter.',
                'f_aktif.required'         => 'Status wajib dipilih.',
                'f_aktif.in'               => 'Status tidak valid.',
                'free.max'                 => 'Keterangan maksimal 255 karakter.',
            ],
            [
                'f_variabel_name' => 'Nama Variabel',
                'f_aktif'         => 'Status',
                'free'            => 'Keterangan',
            ]
        );

        Variabel::create([
            'f_variabel_name' => $validated['f_variabel_name'],
            'f_aktif'         => (int)$validated['f_aktif'],
            'free'            => $validated['free'] ?? null,
            'f_created_on'    => now(),
        ]);

        return redirect()
            ->route('master_data.variabel.index')
            ->with('message', 'Variabel berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = Variabel::where('f_id', $id)->firstOrFail();

        return view('master_data.variabel.edit', [
            'main_title' => 'Master Data',
            'title' => 'Edit Variabel',
            'row' => $row,
        ]);
    }

    public function update(Request $request, $id)
    {
        // VALIDASI DI CONTROLLER
        $validated = $request->validate(
            [
                'f_variabel_name' => 'required|string|max:255',
                'f_aktif'         => 'required|in:0,1',
                'free'            => 'nullable|string|max:255',
            ],
            [
                'f_variabel_name.required' => 'Nama Variabel wajib diisi.',
                'f_variabel_name.max'      => 'Nama Variabel maksimal 255 karakter.',
                'f_aktif.required'         => 'Status wajib dipilih.',
                'f_aktif.in'               => 'Status tidak valid.',
                'free.max'                 => 'Keterangan maksimal 255 karakter.',
            ],
            [
                'f_variabel_name' => 'Nama Variabel',
                'f_aktif'         => 'Status',
                'free'            => 'Keterangan',
            ]
        );

        $row = Variabel::where('f_id', $id)->firstOrFail();

        $row->update([
            'f_variabel_name' => $validated['f_variabel_name'],
            'f_aktif'         => (int)$validated['f_aktif'],
            'free'            => $validated['free'] ?? null,
        ]);

        return redirect()
            ->route('master_data.variabel.index')
            ->with('message', 'Variabel berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $row = Variabel::where('f_id', $id)->firstOrFail();
        $row->delete();

        return back()->with('message', 'Variabel berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (!empty($ids)) {
            Variabel::whereIn('f_id', $ids)->delete();
            return back()->with('message', 'Variabel terpilih berhasil dihapus.');
        }
        return back()->with('error', 'Tidak ada data yang dipilih.');
    }
}
