@extends('layout.app')
@section('content')
<div class="grid grid-cols-3 gap-4">
   <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-full space-x-4">
            <!-- Icon -->
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_corporate.png') }}" alt="">
            </div>

            <!-- Text content -->
            <div>
                <h2 class="text-lg font-semibold">Jumlah Akun </h2>
                <p class="text-sm text-gray-400">{{ number_format($jumlah_akun) }}</p>
            </div>
    </div>

     <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-full space-x-4">
            <!-- Icon -->
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_corporate.png') }}" alt="">
            </div>

            <!-- Text content -->
            <div>
                <h2 class="text-lg font-semibold">Jumlah Event </h2>
                <p class="text-sm text-gray-400">{{ number_format($jumlah_event) }}</p>
            </div>
    </div>

    <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-full space-x-4">
            <!-- Icon -->
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_corporate.png') }}" alt="">
            </div>

            <!-- Text content -->
            <div>
                <h2 class="text-lg font-semibold">Jumlah Responden </h2>
                <p class="text-sm text-gray-400">{{ number_format($jumlah_responden) }}</p>
            </div>
    </div>
</div>
        <table id="search-table">
            <thead>
                <tr>
                    <th class="">No.</th>
                    <th class="text-center">Nama Akun</th>
                    <th class="text-center">Nama Event</th>
                    <th class="text-center">Start date</th>
                    <th class="text-center">End Date</th>
                    <th class="text-center">Report Type</th>
                    <th class="text-center">Tipe survey</th>
                    <th class="text-center">Min Responden</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($event_client as $key => $user)
                    <tr>

                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="text-center">
                            {{ $user->akun_client->f_account_name }}
                        </td>
                        <td class="text-center">
                            {{ $user->f_event_name }}
                        </td>
                        <td class="text-center">{{ $user->f_event_start }} {{ $user->f_event_start_time }}</td>
                        <td class="text-center">{{ $user->f_event_end }} {{ $user->f_event_end_time }}</td>
                        <td class="text-center">{{ $user->f_report_type }}</td>
                        <td class="text-center">
                            @if($user->f_event_type == 1)
                                Pengecekan kata kunci
                                @else
                                Tanpa pengecekan kunci
                            @endif
                     </td>
                        <td class="text-center">{{ $user->f_event_min_respon }}</td>
                        <td class="px-4 py-2 font-medium text-gray-600 whitespace-nowrap">
                            <div class="flex justify-center gap-1">
                                <a href="{{ route('survey.show', ['token'=> $user->f_event_kode]) }}" class="bg-green-500 text-white px-6 py-1 rounded-md">
                                    Link Survey
                                </a>
                            </div>
                             <div class="flex justify-center gap-1">
                            <a href="{{ route('monitoring.event', ['id'=> $user->f_event_kode]) }}" class="bg-red-500 text-white px-6 py-1 rounded-md">
                                    Link Monitoring
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
@endsection