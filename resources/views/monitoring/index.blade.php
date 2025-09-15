@extends('layout.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>

<div>


    <table id="search-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Level 1</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>
            @foreach($surveyUsers as $key => $user)
            <tr>

                <td>{{ $key +1 }}</td>
                <td>{{ $user->f_position_desc}}</td>
                <td><a href="{{ url('monitoring',$user->f_id) }}" >Lihat</a></td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
if (document.getElementById("search-table") && typeof simpleDatatables.DataTable !== 'undefined') {
    const dataTable = new simpleDatatables.DataTable("#search-table", {
        searchable: true,
        sortable: false
    });
}
</script>

@endsection
