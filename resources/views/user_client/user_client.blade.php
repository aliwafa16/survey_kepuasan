@extends('layout.app')
@section('content')
    <div class="bg-[#0075FF]/14 w-full rounded-md py-2 px-4">
        <h1 class="font-semibold text-2xl font-sans text-white">{{ $title }}</h1>
        <div class="flex my-2">
            @if (session('success'))
                <h3 class="bg-emerald-600 px-4 py-2 text-white font-semibold rounded-md text-center">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}

                </h3>
            @endif
            @if (session('error'))
                <h3 class="px-4 bg-rose-600 py-2 text-white font-semibold rounded-md text-center">
                    <i class="fas fa-times-circle"></i>
                    {{ session('error') }}
                </h3>
            @endif
        </div>
        <div
            class="flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center lg:justify-end md:space-y-0 md:space-x-3 mb-3">
            <a href="{{ route('user_client.add') }}"
                class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                </svg>
                {{ $btn_add }}
            </a>
            <button type="button" href="#" data-modal-target="importModal" data-modal-toggle="importModal"
            class="flex items-center justify-center flex-shrink-0 px-3 py-2 text-sm font-medium bg-orange-500 text-white border border-gray-200 rounded-lg focus:outline-none hover:bg-orange-600 focus:z-10 focus:ring-1 focus:ring-gray-20">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
           <path stroke-linecap="round" stroke-linejoin="round"
                 d="M4 4h16v16H4V4zm8 4v5m0 0l-3-3m3 3l3-3" />
       </svg>
            Import
        </button>
            <button type="button"
                class="flex items-center justify-center flex-shrink-0 px-3 py-2 text-sm font-medium bg-green-500 text-white border border-gray-200 rounded-lg focus:outline-none hover:bg-green-600 focus:z-10 focus:ring-1 focus:ring-gray-20">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Export
            </button>
        </div>

        <table id="search-table">
            <thead>
                <tr>
                    <th class="">No.</th>
                    <th class="text-center">Nama Akun</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Level Organisasi</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user_client as $key => $user)

                @php
                $levels = json_decode($user->nosj, true);
                $level1Id = $levels['f_level1'] ?? null;
                $level1Name = $level1Id && isset($level1_map[$level1Id])
                    ? $level1_map[$level1Id]->f_position_desc
                    : '-';
            @endphp
                    <tr>

                        <td>{{ $key + 1 }}</td>
                        <td>
                            {{ $user->username }}
                        </td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">{{ $level1Name }}</td>
                        <td class="px-4 py-2 font-medium text-gray-600 whitespace-nowrap">
                            <div class="flex justify-center gap-1">
                                <button class="bg-red-500 text-white px-6 py-1 rounded-md"
                                    onclick="hapus_data(`{{ $user->f_id }}`)">Hapus</button>
                                <button class="bg-yellow-500 text-white px-6 py-1 rounded-md" type="button"
                                    data-modal-target="editLevel1hModal" data-modal-toggle="editLevel1hModal"
                                    onclick="openEditModal('{{ $user->f_id }}', '{{ $user->f_position_desc }}')">Edit</button>

                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
