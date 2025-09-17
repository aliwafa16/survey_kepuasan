@extends('monitoring.guest_monitoring')

@section('content')

    {{-- <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script> --}}

    <div class="grid grid-cols-3 gap-4">
        <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-full space-x-4">
            <!-- Icon -->
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_corporate.png') }}" alt="">
            </div>

            <!-- Text content -->
            <div>
                <h2 class="text-lg font-semibold">Jumlah Responden </h2>
                <p class="text-sm text-gray-400">100</p>
            </div>
        </div>


        <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-full space-x-4">
            <!-- Icon -->
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_corporate.png') }}" alt="">
            </div>

            <!-- Text content -->
            <div>
                <h2 class="text-lg font-semibold">Nama Event </h2>
                <p class="text-sm text-gray-400">Pelatihan ms.office</p>
            </div>
        </div>

        <div class="flex items-center bg-[#0B1F4C] text-white px-6 py-4 mb-4 rounded-2xl shadow-md w-full space-x-4">
            <!-- Icon -->
            <div class="text-4xl">
                <img src="{{ asset('img/icon/quota_corporate.png') }}" alt="">
            </div>

            <!-- Text content -->
            <div>
                <h2 class="text-lg font-semibold">Nama Perusahaan </h2>
                <p class="text-sm text-gray-400">Universitas Ary Ginanjar</p>
            </div>
        </div>
    </div>


    @php
        $language = 'indonesian'; // atau 'english', 'malaysia' sesuai pilihan user
    @endphp
    <form id="filterForm" action="" method="GET" class="mb-6">
        @csrf

        {{-- (Opsional) identitas context --}}
        <input type="hidden" name="account_id" value="{{ sha1($account_id ?? '') }}">
        <input type="hidden" name="event_id" value="{{ $event_id ?? '' }}">

        {{-- DEMOGRAFI --}}
        <div id="filterDemografi" class="space-y-4 grid grid-cols-3 gap-4">

            @if (isset($demografi['gender']['label'][$language]))
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">
                    {{ $demografi['gender']['label'][$language] }}
                </label>
                <select name="gender" id="gender"
                    class="w-full bg-white border border-slate-300 shadow px-4 py-2 rounded ">
                    <option value="">-- Pilih Gender --</option>
                    @foreach ($demografi['gender']['value'] as $value)
                        <option value="{{ $value['f_gender_id'] }}" @selected(request('gender') == $value['f_gender_id'])>
                            {{ $value['f_gender_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if (isset($demografi['age']['label'][$language]))
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">
                    {{ $demografi['age']['label'][$language] }}
                </label>
                <select name="age" id="age"
                    class="w-full bg-white border border-slate-300 shadow px-4 py-2 rounded ">
                    <option value="">-- Pilih Usia --</option>
                    @foreach ($demografi['age']['value'] as $value)
                        <option value="{{ $value['f_id'] }}" @selected(request('age') == $value['f_id'])>
                            {{ $value['f_age_desc'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if (isset($demografi['masa_kerja']['label'][$language]))
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">
                    {{ $demografi['masa_kerja']['label'][$language] }}
                </label>
                <select name="masa_kerja" id="masa_kerja"
                    class="w-full bg-white border border-slate-300 shadow px-4 py-2 rounded ">
                    <option value="">-- Pilih Masa Kerja --</option>
                    @foreach ($demografi['masa_kerja']['value'] as $value)
                        <option value="{{ $value['f_id'] }}" @selected(request('masa_kerja') == $value['f_id'])>
                            {{ $value['f_service_desc'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if (isset($demografi['region']['label'][$language]))
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">
                    {{ $demografi['region']['label'][$language] }}
                </label>
                <select name="region" id="region"
                    class="w-full bg-white border border-slate-300 shadow px-4 py-2 rounded ">
                    <option value="">-- Pilih Region --</option>
                    @foreach ($demografi['region']['value'] as $value)
                        <option value="{{ $value['f_id'] }}" @selected(request('region') == $value['f_id'])>
                            {{ $value['f_region_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if (isset($demografi['level_of_work']['label'][$language]))
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">
                    {{ $demografi['level_of_work']['label'][$language] }}
                </label>
                <select name="level_of_work" id="level_of_work"
                    class="w-full bg-white border border-slate-300 shadow px-4 py-2 rounded ">
                    <option value="">-- Pilih Level Pekerjaan --</option>
                    @foreach ($demografi['level_of_work']['value'] as $value)
                        <option value="{{ $value['f_id'] }}" @selected(request('level_of_work') == $value['f_id'])>
                            {{ $value['f_levelwork_desc'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if (isset($demografi['pendidikan']['label'][$language]))
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">
                    {{ $demografi['pendidikan']['label'][$language] }}
                </label>
                <select name="pendidikan" id="pendidikan"
                    class="w-full bg-white border border-slate-300 shadow px-4 py-2 rounded ">
                    <option value="">-- Pilih Pendidikan --</option>
                    @foreach ($demografi['pendidikan']['value'] as $value)
                        <option value="{{ $value['f_id'] }}" @selected(request('pendidikan') == $value['f_id'])>
                            {{ $value['f_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif


            {{-- LEVEL HIERARKI (multi-select) --}}
            @foreach ($level as $key => $field)
                @if (isset($field['label'][$language]))
                <div>
                    <label class="text-[#0B1F4C] mb-2 text-lg font-bold">
                        {{ $field['label'][$language] }}
                    </label>
                    <select name="{{ $key }}" id="{{ $key }}"
                        class="w-full bg-white border border-slate-300 shadow   px-4 py-2 rounded ">
                        <option value="">-- Pilih --</option>
                        @foreach ($field['value'] as $value)
                            <option value="{{ $value['f_id'] }}" @selected(request($key) == $value['f_id'])>
                                {{ $value['f_position_desc'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            @endforeach

        </div>

        {{-- RANGE TANGGAL RESPON --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">Tanggal Mulai</label>
                <input type="date" name="date_start" value="{{ request('date_start') }}"
                    class="w-full bg-white border border-slate-300 shadow  px-4 py-2 rounded ">
            </div>
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">Tanggal Akhir</label>
                <input type="date" name="date_end" value="{{ request('date_end') }}"
                    class="w-full bg-white border border-slate-300 shadow  px-4 py-2 rounded ">
            </div>
            <div>
                <label class="text-[#0B1F4C] mb-2 text-lg font-bold">Kata Kunci (Nama/Email/IP)</label>
                <input type="text" name="q" value="{{ request('q') }}"
                    class="w-full bg-white border border-slate-300 shadow  px-4 py-2 rounded "
                    placeholder="cari responden...">
            </div>
        </div>



        {{-- TOMBOL AKSI --}}
        <div class="flex items-center gap-3 mt-6">
            <button type="submit" style="background-color: {{ $setting->color_primary ?? '#000165' }};"
                class="text-white py-2 px-4 rounded-[20px]">
                Terapkan Filter
            </button>

            <a href="{{ route('monitoring.event', ['id' => $event->f_event_kode]) }}"
                class="bg-white border px-4 py-2 rounded-[20px]">
                Reset
            </a>
        </div>
    </form>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($chartData as $c)
            <div class="border border-slate-300 shadow-sm rounded-xl p-4">
                <h1 class="font-semibold">{{ $c['title'] }}</h1>
                <div id="chart{{ $c['code'] }}"></div>
            </div>
        @endforeach
    </div>

    <div class="space-y-8 gap-6 mt-6">
        @foreach ($openQuestions as $q)
            @php
                $rows = $answersByCode[$q->f_kode] ?? [];
            @endphp

            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-4 bg-[#0B1F4C] text-white">
                    <h3 class="font-semibold">{{ $q->f_kode }} — {{ $q->f_item }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border-collapse border-slate-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border border-slate-200 text-left">Jawaban</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $i => $it)
                                <tr>
                                    <td class="px-3 py-2 border border-slate-200 whitespace-pre-wrap">{{ $it['answer'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-gray-500 py-4 border">Belum ada jawaban
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        (function() {
            const allData = {!! json_encode($chartData, JSON_UNESCAPED_UNICODE) !!};

            const renderBar = (el, data) => {
                const options = {
                    series: [{
                        name: 'Jumlah Jawaban',
                        data: data.series
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            borderRadiusApplication: 'end',
                            horizontal: true
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: data.categories
                    },
                    tooltip: {
                        y: {
                            formatter: (val) => `${val} responden`
                        }
                    }
                };
                new ApexCharts(document.querySelector(el), options).render();
            };

            allData.forEach((c) => {
                renderBar(`#chart${c.code}`, c);
            });
        })();
    </script>

@endsection
