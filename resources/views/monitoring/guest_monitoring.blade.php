{{-- @extends('layouts.guest') --}}

<x-monitoring :appreance="$appreance">



    @section('content')

        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>



        <style>
            /* Teks di seluruh tabel */
            table.dataTable,
            table.dataTable thead th,
            table.dataTable tbody td {
                color: white !important;
                /* Tailwind gray-800 */
            }


            /* Pagination tombol */
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                color: white !important;
                background-color: transparent !important;
                border: 1px solid #4b5563 !important;
                /* gray-600 */
                margin: 2px;
            }

            /* Tombol pagination saat hover */
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background-color: #4b5563 !important;
                color: white !important;
            }

            /* Tombol pagination aktif */
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background-color: #2563eb !important;
                /* blue-600 */
                color: white !important;
                border: none;
            }

            /* Info teks di bawah */
            .dataTables_wrapper .dataTables_info {
                color: white !important;
            }

            /* Dropdown jumlah entri dan search input */
            .dataTables_wrapper .dataTables_length select,
            .dataTables_wrapper .dataTables_filter input {
                color: white !important;
                border: 1px solid #4b5563 !important;
            }

            /* Label di samping select dan input */
            .dataTables_wrapper .dataTables_length label,
            .dataTables_wrapper .dataTables_filter label {
                color: white !important;
            }
        </style>

        <style>
.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input { color : #3F51B5 !important}
</style>

        <h2 class="text-xl font-semibold text-white mb-4">Monitoring Survey Users</h2>



        <form method="GET" action="{{ route('monitoring.show') }}" class="w-full px-20">
            <div class="flex flex-wrap">

                <!-- Filter Eselon / Jabatan -->
                @if ( $list_monitoring->f_level1)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_level1, true)['indonesian'] }}
                        </label>
                        <select id="f_level1"
                            data-level="1"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md level">
                            <option value="">-- Semua --</option>
                            @foreach ($level1Options as $lvl1)
                                <option data-kuota="{{ $lvl1->f_token }}" value="{{ $lvl1->f_id }}">
                                    {{ $lvl1->f_position_desc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif


                <!-- Filter Level 2 -->
                @if ( $list_monitoring->f_level2)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_level2, true)['indonesian'] }}
                        </label>
                        <select id="f_level2"
                            data-level="2"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md level">
                            <option value="">-- Semua --</option>
                            @foreach ($level2Options as $lvl2)
                                <option value="{{ $lvl2->f_id }}">{{ $lvl2->f_position_desc }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Level 3 -->
                @if ( $list_monitoring->f_level3)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_level3, true)['indonesian'] }}
                        </label>
                        <select id="f_level3"
                            data-level="3"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md level">
                            <option value="">-- Semua --</option>
                            @foreach ($level3Options as $lvl3)
                                <option value="{{ $lvl3->f_id }}">{{ $lvl3->f_position_desc }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ( $list_monitoring->f_level4)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_level4, true)['indonesian'] }}
                        </label>
                        <select id="f_level4"
                            data-level="4"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md level">
                            <option value="">-- Semua --</option>
                            @foreach ($level4Options as $lvl4)
                                <option value="{{ $lvl4->f_id }}">{{ $lvl4->f_position_desc }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif


                @if ( $list_monitoring->f_level5)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_level5, true)['indonesian'] }}
                        </label>
                        <select id="f_level5"
                            data-level="5"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md level">
                            <option value="">-- Semua --</option>
                            @foreach ($level5Options as $lvl5)
                                <option value="{{ $lvl5->f_id }}">{{ $lvl5->f_position_desc }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Level of Work -->
                @if ( $list_monitoring->f_level_of_work)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_others, true)['work']['indonesian'] }}
                        </label>
                        <select id="levelwork"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md">
                            <option value="">-- Semua
                                {{ json_decode($settings->f_label_others, true)['work']['indonesian'] }} --</option>
                            @foreach ($levelworkOptions as $level_work)
                                <option data-kuota="{{ $level_work->f_id }}" value="{{ $level_work->f_id }}">
                                    {{ $level_work->f_levelwork_desc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Umur -->
                @if ( $list_monitoring->f_age)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_others, true)['age']['indonesian'] }}
                        </label>
                        <select id="age"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md">
                            <option value="">-- Semua
                                {{ json_decode($settings->f_label_others, true)['age']['indonesian'] }} --</option>
                            @foreach ($ageOptions as $age)
                                <option data-kuota="{{ $age->f_id }}" value="{{ $age->f_id }}">
                                    {{ $age->f_age_desc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Masa Kerja -->
                @if ( $list_monitoring->f_masakerja)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_others, true)['mk']['indonesian'] }}
                        </label>
                        <select id="service"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md">
                            <option value="">-- Semua
                                {{ json_decode($settings->f_label_others, true)['mk']['indonesian'] }} --</option>
                            @foreach ($lengthServiceOptions as $ls)
                                <option data-kuota="{{ $ls->f_id }}" value="{{ $ls->f_id }}">
                                    {{ $ls->f_service_desc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif


                <!-- Filter Negara -->
                @if ( $list_monitoring->f_region)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_others, true)['mk']['indonesian'] }}
                        </label>
                        <select id="service"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md">
                            <option value="">-- Semua
                                {{ json_decode($settings->f_label_others, true)['mk']['indonesian'] }} --</option>
                            @foreach ($lengthServiceOptions as $ls)
                                <option data-kuota="{{ $ls->f_id }}" value="{{ $ls->f_id }}">
                                    {{ $ls->f_service_desc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Negara -->
                @if ( $list_monitoring->f_pendidikan)
                    <div class="w-full md:w-1/2 px-4 py-2">
                        <label class="block mb-1 text-sm font-semibold text-white">
                            Filter {{ json_decode($settings->f_label_others, true)['education']['indonesian'] }}
                        </label>
                        <select id="education"
                            class="w-full text-white border-none px-3 py-1 pr-[20px] text-sm bg-[#022256] rounded-md">
                            <option value="">-- Semua
                                {{ json_decode($settings->f_label_others, true)['education']['indonesian'] }} --</option>
                            @foreach ($pendidikanOptions as $p)
                                <option data-kuota="{{ $p->f_id }}" value="{{ $p->f_id }}">
                                    {{ $p->f_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif


            </div>
        </form>

        <div class="flex gap-2 mb-4 mt-4">
            <button onclick="exportToExcel()" class="bg-green-600 text-white flex px-4 py-2 rounded">
                <img src="{{ asset('img/icon/excel.png') }}" class="w-6 h-6 mr-2" alt=""> Export ke Excel
            </button>
            <button onclick="downloadCheckedReports()" class="bg-blue-600 text-white flex px-4 py-2 rounded">
                <img src="{{ asset('img/icon/download.png') }}" class="w-6 h-6 mr-2" alt=""> Download Report
            </button>
        </div>

        <div class="max-w-lg mb-4 mx-auto p-6 bg-white rounded-lg shadow-md">
            <!-- Title -->
            <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Pengisian</h2>

            <!-- Display Kuota -->
            <p class="text-gray-600 text-center"><span class="font-bold text-green-500">{{ $kuota - $sisa }}</span> / <span class="font-bold text-gray-800">{{ $kuota }}</span></p>
        </div>


        <div class="overflow-x-auto">

            <table id="search-tables" class="table-auto w-full p-4 bg-[#022256] text-sm text-left text-gray-700">
                <thead class="text-white">
                    <tr>
                        <th><input type="checkbox" id="select-all" class="accent-blue-600" /></th>
                        <th>No</th>
                        <th>Username</th>
                        <th>Email</th>

                        @if ($list_monitoring->f_level1)
                            <th>{{ json_decode($settings->f_label_level1, true)['indonesian'] }}</th>
                        @endif

                        @if ($list_monitoring->f_level2)
                            <th>{{ json_decode($settings->f_label_level2, true)['indonesian'] }}</th>
                        @endif


                        @if ($list_monitoring->f_level3)
                            <th>{{ json_decode($settings->f_label_level3, true)['indonesian'] }}</th>
                        @endif


                        @if ($list_monitoring->f_age)
                            <th>{{ json_decode($settings->f_label_others, true)['age']['indonesian'] }}</th>
                        @endif


                        @if ($list_monitoring->f_masakerja)
                            <th>{{ json_decode($settings->f_label_others, true)['mk']['indonesian'] }}</th>
                        @endif

                        @if ($list_monitoring->f_level_of_work)
                            <th>{{ json_decode($settings->f_label_others, true)['work']['indonesian'] }}</th>
                        @endif

                        @if ($list_monitoring->f_pendidikan)
                            <th>{{ json_decode($settings->f_label_others, true)['education']['indonesian'] }}</th>
                        @endif

                        
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-white"></tbody>
            </table>
        </div>

        <script>
            let selectedRows = [];

            function updateSelectedRows() {
                selectedRows = [];
                document.querySelectorAll(".row-checkbox:checked").forEach(cb => {
                    console.log(cb);
                    selectedRows.push({
                        id: cb.dataset.id,
                        Username: cb.dataset.username,
                        Email: cb.dataset.email,
                        Jabatan: cb.dataset.levelwork,
                        Level1: cb.dataset.level1,
                        Level2: cb.dataset.level2,
                        Level3: cb.dataset.level3,
                        "{{ json_decode($settings->f_label_others, true)['age']['indonesian'] }}": cb.dataset
                            .age,
                        "{{ json_decode($settings->f_label_others, true)['mk']['indonesian'] }}": cb.dataset
                            .service
                    });
                });
                console.log("Selected Rows:", selectedRows);
            }

            $(document).ready(function() {

                var selectedOption = $('#f_level1').find('option:selected');
                var kuota = selectedOption.data('kuota'); // ambil data-kuota dari option terpilih
                console.log("Kuota dari option terpilih:", kuota);

                $('#kuota_level1').html(kuota);

                const table = $('#search-tables').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('monitoring.monitoring_user', [$id_corporate, $event_client->f_event_id]) }}',
                        data: d => {
                            d.f_level1 = $('#f_level1').val();
                            d.levelwork = $('#levelwork').val();
                            d.f_level2 = $('#f_level2').val();
                            d.f_level3 = $('#f_level3').val();
                            d.age = $('#age').val();
                            d.service = $('#service').val();
                            d.education = $('#education').val();
                        }
                    },
                    lengthMenu: [
                        [10, 25, 50, 100, 1000, -1],
                        [10, 25, 50, 100, 1000, "All"]
                    ],

                    // columns: [
                    //     { data: 'checkbox', orderable: false, searchable: false },
                    //     { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    //     { data: 'f_survey_username' },
                    //     { data: 'f_email' },
                    //     { data: 'levelwork.f_levelwork_desc', defaultContent: '' },
                    //     { data: 'level1.f_position_desc', defaultContent: '' },
                    //     { data: 'level2.f_position_desc', defaultContent: '' },
                    //     { data: 'level3.f_position_desc', defaultContent: '' },
                    //     { data: 'action', orderable: false, searchable: false }
                    // ],
                    columns: [{
                            data: 'checkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            searchable: false
                        },
                        {
                            data: 'f_survey_username'
                        },
                        {
                            data: 'f_email'
                        },
                        // {
                        //     data: 'levelwork_desc',
                        //     name: 'levelwork_desc',
                        //     defaultContent: '',
                        //     searchable: false
                        // },
                        @if ( $list_monitoring->f_level1)
                            {
                                data: 'level1_desc',
                                defaultContent: '',
                                searchable: false
                            },
                        @endif

                        @if ( $list_monitoring->f_level2)
                            {
                                data: 'level2_desc',
                                defaultContent: '',
                                searchable: false
                            },
                        @endif

                        @if ( $list_monitoring->f_level3)
                            {
                                data: 'level3_desc',
                                defaultContent: '',
                                searchable: false
                            },
                        @endif

                        @if ( $list_monitoring->f_age)
                            {
                                data: 'age_desc',
                                defaultContent: '',
                                searchable: false
                            },
                        @endif

                        @if ( $list_monitoring->f_masakerja)
                            {
                                data: 'service_desc',
                                defaultContent: '',
                                searchable: false
                            },
                        @endif

                        @if ( $list_monitoring->f_level_of_work)
                            {
                                data: 'level_work_desc',
                                defaultContent: '',
                                searchable: false
                            },
                        @endif

                        @if ( $list_monitoring->f_pendidikan)
                            {
                                data: 'pendidikan_desc',
                                defaultContent: '',
                                searchable: false
                            },
                        @endif

                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    drawCallback: () => {
                        $('#select-all').prop('checked', false);
                        bindCheckboxEvents();
                    }
                });

                $('#f_level1, #f_level2, #f_level3,#levelwork, #service, #age, #education').change(() => table.ajax.reload());

                $('#select-all').on('change', function() {
                    const checked = this.checked;
                    $('.row-checkbox').prop('checked', checked);
                    updateSelectedRows();
                });
                $('#f_level1').on('change', function() {
                    var selectedOption = $(this).find('option:selected');
                    var kuota = selectedOption.data('kuota'); // ambil data-kuota dari option terpilih
                    console.log("Kuota dari option terpilih:", kuota);

                    $('#kuota_level1').html(kuota);
                });

            });

            var old_val_level = [];
            $('.level').each(function(index) {
                    var level = $(this).data('level');                    
                    var id_next = '#f_level' + level;
                    old_val_level.push($(id_next).html());

                });
                

            $('.level').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var curr_level = $(this).data('level');

                // Reset the next and all subsequent level dropdowns
                $('.level').each(function(index) {
                    var level = $(this).data('level');
                    if (level > curr_level) {
                        var id_next = '#f_level' + level;
                        $(id_next).html(old_val_level[index]); // Reset it to a default option
                    }
                });

                // Construct the link based on the current level and selected value
                var link = "{{ Url('get_child') }}/" + curr_level + "/" + selectedOption.val();
                console.log(link);

                // Make the AJAX request
                $.ajax({
                    "url": link,
                    "method": "GET",
                    "dataType": "JSON",
                    success: function(e) {
                        // Create the HTML for the new options
                        var html = ' <option value="">-- Semua --</option>';
                        $.each(e["data"], function(key, val) {
                            html += '<option value="' + val["f_id"] + '">' + val["f_position_desc"] + '</option>';
                        });

                        // Populate the next level dropdown
                        var id_next = '#f_level' + (curr_level + 1);
                        $(id_next).html(html);
                    }
                });
            });

            function bindCheckboxEvents() {
                $('.row-checkbox').off('change').on('change', updateSelectedRows);
            }

            function exportToExcel() {
                if (selectedRows.length === 0) {
                    alert("Pilih minimal satu baris untuk diekspor.");
                    return;
                }

                console.log(selectedRows);

                const worksheet = XLSX.utils.json_to_sheet(selectedRows);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Selected Users");
                XLSX.writeFile(workbook, "selected-survey-users.xlsx");
            }

            function downloadCheckedReports() {
                const checkedIds = selectedRows.map(row => row.id);
                if (checkedIds.length === 0) {
                    alert("Pilih minimal satu baris untuk download report.");
                    return;
                }

                if (checkedIds.length > 50) {
                    alert("Maksimal download report adalah 50 report");
                    return;
                }
                const url = `{{ url('generate-zip') }}?id=${checkedIds.join(",")}`;
                // const url = `https://talentdna.me/tdna/trx_survey/fnTrx_surveyDR?id=${checkedIds.join(",")}`;
                window.open(url, '_blank');
            }
        </script>

        {{-- @endsection --}}
    </x-monitoring>
