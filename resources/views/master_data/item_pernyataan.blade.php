{{-- resources/views/master_data/item_pernyataan/index.blade.php --}}
@extends('layout.app')

@section('content')
<div class="bg-white shadow-xl w-full rounded-md py-2 pt-4 px-4">
    <h1 class="font-semibold text-2xl font-sans text-slate-700">{{ $title }}</h1>



    <table id="search-table" class="w-full">
        <thead>
            <tr>
                <th>No.</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Item (ID)</th>
                <th class="text-center">Tipe</th>
                <th class="text-center">Variabel</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $row)
                <tr>
                    <td class="text-center">{{ ($data->currentPage()-1)*$data->perPage() + $i + 1 }}</td>
                    <td class="text-center">{{ $row->f_kode }}</td>
                    <td class="text-center">{{ $row->f_item }}</td>
                    <td class="text-center">
                        
                        @if($row->type == 1)

                <span>Pilihan ganda</span>
                @else
                <span>Text area</span>    
                        @endif
                    </td>
                    <td class="text-center">{{ $row->variabel->f_variabel_name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-6 text-slate-500">Belum ada data.</td></tr>
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
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((res) => {
        if (!res.isConfirmed) return;

        $.ajax({
            url: url,
            type: 'POST',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function(resp){
                if (resp.status || resp.success) {
                    Swal.fire({icon:'success', title:'Berhasil!', text: resp.message || 'Data dihapus', timer:1200, showConfirmButton:false})
                        .then(()=> location.reload());
                } else {
                    Swal.fire({icon:'error', title:'Gagal', text: resp.message || 'Gagal menghapus.'});
                }
            },
            error: function(xhr){
                Swal.fire({icon:'error', title:'Gagal', text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan.'});
            }
        });
    });
}
</script>
@endsection
