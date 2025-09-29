@extends('layout.app')

@section('content')
<div class="space-y-6">

    {{-- Header & Toolbar --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold">Monitoring</h1>
            <p class="text-base text-slate-500">Daftar event beserta tautan Survey & Monitoring</p>
        </div>
    </div>

    <div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="search-table" class="min-w-full table-auto">
                <thead class="bg-slate-50 sticky top-0 z-10">
                    <tr class="text-left text-sm text-slate-600">
                        <th class="px-4 py-3 w-14 text-center">No.</th>
                        <th class="px-4 py-3 text-center">Nama Akun</th>
                        <th class="px-4 py-3 text-center">Nama Event</th>
                        <th class="px-4 py-3 text-center">Nama</th>
                        <th class="px-4 py-3 text-center">Email</th>
                        <th class="px-4 py-3 text-center">Tanggal submit</th>
                        <th class="px-4 py-3 text-center w-80">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse ($trnSurvey as $key => $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-center">{{ $key + 1 }}</td>
                            <td class="px-4 py-3 text-center">
                                {{ $user->account->f_account_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $user->events->f_event_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">{{ $user->f_survey_username }}</td>
                            <td class="px-4 py-3 text-center">{{ $user->f_survey_email }}</td>
                            <td class="px-4 py-3 text-center">{{ $user->f_survey_created_on }}</td>
                           <td class="px-4 py-3">
    <a href="{{ route('monitoring.event', ['id' => $user->events->f_event_kode, 'q' => $user->f_survey_username]) }}"
       class="bg-blue-500 px-2 py-1 rounded-sm text-white">
       Detail
    </a>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-slate-500" colspan="9">
                                Belum ada data event.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
