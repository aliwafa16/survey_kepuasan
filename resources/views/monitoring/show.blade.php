@extends('layout.app')

@section('content')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>


    <h2 class="text-xl font-semibold mb-4">Monitoring Survey Users</h2>

    {{-- {{ json_encode($decode) }} --}}



    {{-- <div class="max-w-md flex mx-auto mb-6 space-x-6">
        @if ($isRole1)
        <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Kuota Corporate</h2>
            <p class="text-gray-600 text-base break-all">
                {{ $kuota->f_account_token }}
            </p>
        </div>
        @endif

        <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Kuota {{ json_decode($settings->f_label_level1,true)['indonesian'] }}</h2>
            <p id="kuota_level1" class="text-gray-600 text-base break-all">
            Pilih terlebih dahulu
            </p>
        </div>

    </div> --}}

    <div class="flex">


        {{-- <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-fit space-x-4 ml-4">
            <!-- Icon -->
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_opd.png') }}" alt="">
            </div>

            <!-- Text content -->
            <div>
            <h2 class="text-lg font-semibold">Kuota {{ json_decode($settings->f_label_level1,true)['indonesian'] }}</h2>
            <p id="kuota_level1" class="text-sm text-gray-400">Pilih terlebih dahulu</p>

            </div>
        </div> --}}

        {{-- <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-fit space-x-4 ml-4">
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_opd.png') }}" alt="">
            </div>

            <div>
                <a href="{{ url('survey', sha1(Auth::user()->f_account_id)) }}" class="text-lg font-semibold">Link
                    Pengisian</a>
            </div>
        </div>


        <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-fit space-x-4 ml-4">
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_opd.png') }}" alt="">
            </div>

            <div>
                <a href="{{ url('monitoring', sha1(md5(Auth::user()->f_account_id))) }}" class="text-lg font-semibold">Link
                    Monitoring</a>
            </div>
        </div> --}}
    </div>




@endsection
