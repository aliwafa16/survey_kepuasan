@extends('layout.app')
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
    <style>
        .ck-editor__editable_inline {
            min-height: 300px;
            font-size: 14px;
            line-height: 1.6;
            padding: 1rem;
            color: #111;
        }

        .ck.ck-editor__main>.ck-editor__editable {
            background-color: #f9f9f9;
            border-radius: 0.5rem;
        }

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

    <div class="bg-[#0075FF]/14 w-full rounded-md py-2 px-4">
        <div class="flex my-2">
            @if (session('success'))
                <h3 class="bg-emerald-600 px-4 py-2 text-white font-semibold rounded-md text-center">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}

                </h3>
            @endif
            @if (session('error') || ($setting_demografi->f_nip != 1 && $demografi_view == 2))
                <h3 class="px-4 bg-rose-600 py-2 text-white font-semibold rounded-md text-center">
                    <i class="fas fa-times-circle"></i>
                    {{ session('error') ?? 'Tipe data demografi & key tidak sesuai!' }}
                </h3>
            @endif
        </div>

        <div x-data="{ tab: 'demografi' }">
            <div class="flex gap-2 my-4">
                <button @click="tab = 'demografi'"
                    :class="tab === 'demografi' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-4 py-2 rounded">
                    Demografi
                </button>
                <button @click="tab = 'halaman-setting'"
                    :class="tab === 'halaman-setting' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-4 py-2 rounded">
                    Halaman Setting
                </button>
                <button @click="tab = 'appreance'"
                    :class="tab === 'appreance' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-4 py-2 rounded">
                    Appreance
                </button>
                {{-- <button @click="tab = 'responden'"
                    :class="tab === 'responden' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-4 py-2 rounded">
                    Responden
                </button> --}}
            </div>

            <div x-show="tab === 'demografi'">
                <form id="form_setting_akun" action="{{ route('setting.akun.save') }}" method="POST">

                    @method('post')
                    @csrf
                    <table class="w-full border border-collapse">
                        <thead class="">
                            <tr>
                                <th class="px-4 py-4 border border-slate-300 bg-blue-400 text-slate-50">Desc</th>
                                <th class="px-4 py-4 border border-slate-300 bg-blue-400 text-slate-50">Demografi Survey
                                </th>
                                {{-- <th class="px-4 py-4 border border-slate-300 bg-blue-400 text-slate-50">Buat user</th> --}}
                                <th class="px-4 py-4 border border-slate-300 bg-blue-400 text-slate-50">Indonesia</th>
                                <th class="px-4 py-4 border border-slate-300 bg-blue-400 text-slate-50">Inggris</th>
                                <th class="px-4 py-4 border border-slate-300 bg-blue-400 text-slate-50">Malaysia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <input class="text-slate-600" type="hidden" name="is_aktif_f_account_id"
                                value="{{ $account_id }}">
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Nama
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_nama"
                                        {{ $setting_demografi->f_nama ? 'checked' : '' }}></td>
                   
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center"></td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_id_nama" value="{{ $label_others['nama']['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_en_nama" value="{{ $label_others['nama']['english'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_my_nama" value="{{ $label_others['nama']['malaysia'] }}">
                                </td>
                            <tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Email
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_email"
                                        {{ $setting_demografi->f_email ? 'checked' : '' }}></td>

                         
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center"></td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_id_email" value="{{ $label_others['email']['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_en_email" value="{{ $label_others['email']['english'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_my_email" value="{{ $label_others['email']['malaysia'] }}">
                                </td>
                            <tr>
      
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Jenis kelamin
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_gender"
                                        {{ $setting_demografi->f_gender ? 'checked' : '' }}></td>

                                {{-- <td class="px-2 py-2 border border-slate-300 text-center"></td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_id_gender" value="{{ $label_others['gender']['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_en_gender" value="{{ $label_others['gender']['english'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_my_gender" value="{{ $label_others['gender']['malaysia'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Usia</td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_age"
                                        {{ $setting_demografi->f_age ? 'checked' : '' }}>
                                </td>
                                </td>
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center"></td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_id_age" value="{{ $label_others['age']['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_en_age" value="{{ $label_others['age']['english'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_my_age" value="{{ $label_others['age']['malaysia'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Masa kerja
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_masakerja"
                                        {{ $setting_demografi->f_masakerja ? 'checked' : '' }}></td>

                                {{-- <td class="px-2 py-2 border border-slate-300 text-center"></td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_id_mk" value="{{ $label_others['mk']['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_en_mk" value="{{ $label_others['mk']['english'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_my_mk" value="{{ $label_others['mk']['malaysia'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Pendidikan
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_pendidikan"
                                        {{ $setting_demografi->f_pendidikan ? 'checked' : '' }}></td>






                                {{-- <td class="px-2 py-2 border border-slate-300 text-center"></td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_id_education" value="{{ $label_others['education']['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_en_education" value="{{ $label_others['education']['english'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_my_education" value="{{ $label_others['education']['malaysia'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Wilayah</td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_region"
                                        {{ $setting_demografi->f_region ? 'checked' : '' }}></td>

                  
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_region" {{ $setting_create_user->f_region ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_id_region" value="{{ $label_others['region']['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_en_region" value="{{ $label_others['region']['english'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_my_region" value="{{ $label_others['region']['malaysia'] }}">
                                </td>
                            </tr>

                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Tingkat
                                    pekerjaan
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_level_of_work"
                                        {{ $setting_demografi->f_level_of_work ? 'checked' : '' }}></td>

                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_level_of_work" {{ $setting_create_user->f_level_of_work ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_id_work" value="{{ $label_others['work']['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_en_work" value="{{ $label_others['work']['english'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="label_my_work" value="{{ $label_others['work']['malaysia'] }}">
                                </td>
                            </tr>

                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Tingkat 1
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_level1"
                                        {{ $setting_demografi->f_level1 ? 'checked' : '' }}></td>

                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_level1" {{ $setting_create_user->f_level1 ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="id_label_level1" value="{{ $label_level1['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="my_label_level1" value="{{ $label_level1['malaysia'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="en_label_level1" value="{{ $label_level1['english'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Tingkat 2
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_level2"
                                        {{ $setting_demografi->f_level2 ? 'checked' : '' }}></td>
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_level2" {{ $setting_create_user->f_level2 ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="id_label_level2" value="{{ $label_level2['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="my_label_level2" value="{{ $label_level2['malaysia'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="en_label_level2" value="{{ $label_level2['english'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Tingkat 3
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_level3"
                                        {{ $setting_demografi->f_level3 ? 'checked' : '' }}></td>
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_level3" {{ $setting_create_user->f_level3 ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="id_label_level3" value="{{ $label_level3['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="my_label_level3" value="{{ $label_level3['malaysia'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="en_label_level3" value="{{ $label_level3['english'] }}">
                                </td>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Tingkat 4
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_level4"
                                        {{ $setting_demografi->f_level4 ? 'checked' : '' }}></td>

                        
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_level4" {{ $setting_create_user->f_level4 ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="id_label_level4" value="{{ $label_level4['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="my_label_level4" value="{{ $label_level4['malaysia'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="en_label_level4" value="{{ $label_level4['english'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Tingkat 5
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_level5"
                                        {{ $setting_demografi->f_level5 ? 'checked' : '' }}></td>

                 
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_level5" {{ $setting_create_user->f_level5 ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="id_label_level5" value="{{ $label_level5['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="my_label_level5" value="{{ $label_level5['malaysia'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="en_label_level5" value="{{ $label_level5['english'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Tingkat 6
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_level6"
                                        {{ $setting_demografi->f_level6 ? 'checked' : '' }}></td>
                   
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_level6" {{ $setting_create_user->f_level6 ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="id_label_level6" value="{{ $label_level6['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="my_label_level6" value="{{ $label_level6['malaysia'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="en_label_level6" value="{{ $label_level6['english'] }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 border border-slate-300 font-normal text-sm text-center">Tingkat 7
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="checkbox" name="is_aktif_f_level7"
                                        {{ $setting_demografi->f_level7 ? 'checked' : '' }}></td>
              
                                {{-- <td class="px-2 py-2 border border-slate-300 text-center">
                                        <input class="text-slate-600" type="checkbox"
                                name="is_create_user_f_level7" {{ $setting_create_user->f_level7 ? 'checked' : ''  }}>
                                </td> --}}
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="id_label_level7" value="{{ $label_level7['indonesian'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="my_label_level7" value="{{ $label_level7['malaysia'] }}">
                                </td>
                                <td class="px-2 py-2 border border-slate-300 text-center"><input class="text-slate-600"
                                        type="text"
                                        class="w-full rounded bg-slate-50 py-2 px-4 font-normal text-sm font-sans border-slate-300  "
                                        name="en_label_level7" value="{{ $label_level7['english'] }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="flex justify-center">
                        <button class="bg-green-600 text-white py-2 px-6 rounded-md my-4" type="submit">Simpan</button>
                    </div>
                </form>
            </div>


            <div x-show="tab === 'halaman-setting'">


                <form action="{{ route('setting.akun.save_halaman_setting') }}" method="POST">
                    @method('POST')
                    @csrf
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <div>
                                <label for="f_page_welcome_title" class="block text-sm font-medium mb-1">Judul</label>
                                <input type="text" name="f_page_welcome_title" id="f_page_welcome_title"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                                    placeholder="Masukkan judul..." value="{{ $page_welcome['title'] }}" readonly>
                            </div>
                            <div>
                                <label for="editor1" class="block text-sm font-medium mb-1">Konten</label>
                                <textarea name="f_page_welcome_content" id="editor1">{{ $page_welcome['content'] }}</textarea>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label for="f_page_howto_title" class="block text-sm font-medium mb-1">Judul</label>
                                <input type="text" name="f_page_howto_title" id="f_page_howto_title"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                                    placeholder="Masukkan judul..." value="{{ $page_howto['title'] }}" readonly>
                            </div>
                            <div>
                                <label for="editor2" class="block text-sm font-medium mb-1">Konten</label>
                                <textarea name="f_page_howto_content" id="editor2">{{ $page_howto['content'] }}</textarea>
                            </div>
                        </div>



                        <div class="space-y-3">
                            <div>
                                <label for="f_page_thanks_title" class="block text-sm font-medium mb-1">Judul</label>
                                <input type="text" name="f_page_thanks_title" id="f_page_thanks_title"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                                    placeholder="Masukkan judul..." value="{{ $page_thanks['title'] }}" readonly>
                            </div>
                            <div>
                                <label for="editor3" class="block text-sm font-medium mb-1">Konten</label>
                                <textarea name="f_page_thanks_content" id="editor3">{{ $page_thanks['content'] }}</textarea>
                            </div>
                        </div>


                        <div class="flex justify-center">
                            <button class="bg-green-600 text-white py-2 px-6 rounded-md my-4"
                                type="submit">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>

            <div x-show="tab === 'appreance'">
                <form action="{{ route('settings.appearance.update') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium mb-3">Logo Image</label>
                        <input type="file" name="logo" accept="image/*"
                            class="block w-full text-sm border border-white">
                        @if (isset($settings->logo))
                            <img src="{{ asset('storage/' . $settings->logo) }}" alt="Current Logo" class="h-16 mt-2">
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-3">Banner Image</label>
                        <input type="file" name="banner" accept="image/*"
                            class="block w-full text-sm border border-white">
                        @if (isset($settings->banner))
                            <img src="{{ asset('storage/' . $settings->banner) }}" alt="Current Banner"
                                class="h-24 mt-2">
                        @endif
                    </div>


                    <div>
                        <label for="color_primary" class="block text-sm font-medium mb-3">Primary Color</label>
                        <input type="color" name="color_primary" id="color_primary"
                            value="{{ old('color_primary', $settings->color_primary ?? '#1072f1') }}"
                            class="w-16 h-10 border rounded">
                    </div>

                    <div>
                        <label for="color_secondary" class="block text-sm font-medium mb-3">Secondary
                            Color</label>
                        <input type="color" name="color_secondary" id="color_secondary"
                            value="{{ old('color_secondary', $settings->color_secondary ?? '#67c5f7') }}"
                            class="w-16 h-10 border rounded">
                    </div>

                    <div>
                        <label for="tagline" class="block text-sm font-medium">Tagline</label>
                        <textarea type="text" name="tagline" id="tagline" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('tagline', $settings->tagline ?? '') }}</textarea>
                    </div>

                    <div class="flex justify-center">
                        <button class="bg-green-600 text-white py-2 px-6 rounded-md my-4" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
            {{-- <div x-show="tab === 'responden'">
                <form method="POST" enctype="multipart/form-data" class="my-6" id="import-form">
                    @csrf
                    @method('POST')
                    <div class="flex flex-row mb-3">
                        <input type="file" name="file" required class="border rounded" id="file">
                        <button type="submit"
                            class="bg-orange-500 text-white hover:bg-orange-700 ms-3 px-4 rounded">Import</button>
                    </div>
                    <a href="{{ route('setting.responden.download_format') }}" target="_blank" class="">Download
                        format</a>
                </form>


                <form action="">
                    <div class="grid grid-cols-8">
                        <div class="flex flex-col gap-1 mt-2">
                            <label class="text-paynes_gray-200 font-normal text-sm">Status survey</label>
                            <select
                                class="select select-bordered border-slate-400 rounded-lg h-10 w-full text-sm px-1 py-1 text-gray-500"
                                name="status_survey">
                                <option class="text-grey-500" value="" selected>--Pilih--</option>
                                <option class="text-grey-500" value="yes">Yes</option>
                                <option class="text-grey-500" value="no">No</option>
                            </select>
                            @error('status_survey')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-1 mt-2">
                            <label class="text-paynes_gray-200 font-normal text-sm">Jenis Kelamin</label>
                            <select
                                class="select select-bordered border-slate-400 rounded-lg h-10 w-full text-sm px-1 py-1 text-gray-500"
                                name="status_survey">
                                <option class="text-grey-500" value="" selected>--Pilih--</option>
                                <option class="text-grey-500" value="yes">Yes</option>
                                <option class="text-grey-500" value="no">No</option>
                            </select>
                            @error('status')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </form>
                <div class="overflow-x-auto mt-6 mb-10">
                    <table id="search-tables"
                        class="min-w-full text-sm text-left text-white bg-[#022256] border border-gray-700">
                        <thead class="bg-[#03316f] text-white uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-2 border border-gray-600">No</th>
                                <th class="px-4 py-2 border border-gray-600">Status Survey</th>
                                <th class="px-4 py-2 border border-gray-600">Tanggal Pengisian</th>
                                <th class="px-4 py-2 border border-gray-600">Tanggal Lahir</th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_others['nama']['indonesian'] }}
                                </th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_others['gender']['indonesian'] }}
                                </th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_others['age']['indonesian'] }}
                                </th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_others['mk']['indonesian'] }}</th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_others['region']['indonesian'] }}
                                </th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_others['work']['indonesian'] }}
                                </th>
                                         <th class="px-4 py-2 border border-gray-600">{{ $label_others['education']['indonesian'] }}
                                </th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_level1['indonesian'] }}</th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_level2['indonesian'] }}</th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_level3['indonesian'] }}</th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_level4['indonesian'] }}</th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_level5['indonesian'] }}</th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_level6['indonesian'] }}</th>
                                <th class="px-4 py-2 border border-gray-600">{{ $label_level7['indonesian'] }}</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            {{-- @foreach ($responden as $i => $item)
                                <tr class="hover:bg-[#03396c]">
                                    <td class="px-4 py-2 border border-gray-600">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2 border border-gray-600">{{ $item->f_survey_valid ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">{{ $item->f_survey_date ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">{{ $item->tanggal_lahir ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">{{ $item->f_name ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">{{ $item->nip ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_gender->f_gender_name ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_umur->f_age_desc ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_masa_kerja->f_service_desc ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_wilayah->f_region_name ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_jabatan->f_levelwork_desc ?? '-' }}</td>
                                                  <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_pendidikan->f_name ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_level1->f_position_desc ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_level2->f_position_desc ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_level3->f_position_desc ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_level4->f_position_desc ?? '-' }}</td>
                                    <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_level5->f_position_desc ?? '-' }}</td>
                                                                <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_level6->f_position_desc ?? '-' }}</td>
                                                                <td class="px-4 py-2 border border-gray-600">
                                        {{ $item->relasi_level7->f_position_desc ?? '-' }}</td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>

                    <div class="py-3 px-2 bg-white">
                        {{-- {{ $responden->onEachSide(5)->links('vendor.pagination.tailwind') }} --}}
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#editor1'), {
                //     ckfinder: {

                //     }
            })
            .catch(error => {
                console.error(error);
            });


        ClassicEditor
            .create(document.querySelector('#editor2'), {
                //     ckfinder: {

                //     }
            })
            .catch(error => {
                console.error(error);
            });


        ClassicEditor
            .create(document.querySelector('#editor3'), {
                //     ckfinder: {

                //     }
            })
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#tagline'), {
                //     ckfinder: {

                //     }
            })
            .catch(error => {
                console.error(error);
            });



        document.getElementById('import-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('file');
            const file = fileInput.files[0];
            if (!file) {
                Swal.fire('Gagal', 'Silakan pilih file terlebih dahulu.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            Swal.fire({
                title: 'Waiting...',
                text: 'Sedang memproses data.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch("{{ route('setting.akun.import_responden') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Selesai!',
                        text: 'Data berhasil diimpor.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat mengimpor.', 'error');
                }

            } catch (err) {
                Swal.fire('Gagal!', 'Terjadi kesalahan koneksi.', 'error');
            }
        });
    </script>
@endsection
