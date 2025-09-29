@extends('layout.app')

@section('content')
<div class="bg-white shadow-xl w-full rounded-md py-2 pt-4 px-4">
    <h1 class="font-semibold text-2xl font-sans text-slate-700">{{ $title ?? 'Variabel' }}</h1>

    <div class="flex my-2">
        @if (session('message'))
            <h3 class="bg-emerald-600 px-4 py-2 text-white font-semibold rounded-md text-center">
                <i class="fas fa-check-circle"></i> {{ session('message') }}
            </h3>
        @endif
        @if (session('error'))
            <h3 class="px-4 bg-rose-600 py-2 text-white font-semibold rounded-md text-center">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </h3>
        @endif
    </div>


    <table id="search-table" class="w-full">
        <thead>
            <tr>
                <th>No.</th>
                <th class="text-center">Nama Variabel</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $row)
                <tr>
                    <td class="text-center">{{ ($data->currentPage()-1)*$data->perPage() + $i + 1 }}</td>
                    <td class="text-center">{{ $row->f_variabel_name }}</td>
                    <td class="text-center">
                        @if($row->f_aktif)
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600">Tidak</span>
                        @endif
                    </td>
                   
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-6 text-slate-500">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $data->links() }}
    </div>
</div>

<script>
function hapusData(el) {
    event.preventDefault();
    const url = el.getAttribute('data-url');

    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: url,
            type: 'POST',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.status || res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message || 'Data dihapus', timer: 1300, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal menghapus.' });
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan saat menghapus.' });
            }
        });
    });
}
</script>
@endsection
