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
            <button href="#" data-modal-target="addLevel2Modal" data-modal-toggle="addLevel2Modal"
                type="button"
                class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                </svg>
                {{ $btn_add }}
            </button>
            <button type="button" href="#" data-modal-target="importModal" data-modal-toggle="importModal"
                class="flex items-center justify-center flex-shrink-0 px-3 py-2 text-sm font-medium bg-orange-500 text-white border border-gray-200 rounded-lg focus:outline-none hover:bg-orange-600 focus:z-10 focus:ring-1 focus:ring-gray-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
               <path stroke-linecap="round" stroke-linejoin="round"
                     d="M4 4h16v16H4V4zm8 4v5m0 0l-3-3m3 3l3-3" />
           </svg>
                Import
            </button>
            <a href="{{ route('master_data.export_value_level2') }}"
                class="flex items-center justify-center flex-shrink-0 px-3 py-2 text-sm font-medium bg-green-500 text-white border border-gray-200 rounded-lg focus:outline-none hover:bg-green-600 focus:z-10 focus:ring-1 focus:ring-gray-20">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Export
            </a>
        </div>
        <table id="search-table">
            <thead>
                <tr>
                    <th class="">No.</th>
                    <th class="text-center">Level 1</th>
                    <th class="text-center">Level 2</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($level2 as $key => $user)
                    <tr>

                        <td>{{ $key + 1 }}</td>
                        <td>{{ $user->relasi_level1->f_position_desc }}</td>
                        <td>{{ $user->f_position_desc }}</td>
                        <td class="px-4 py-2 font-medium text-gray-600 whitespace-nowrap">
                            <div class="flex justify-center gap-1">
                                <button class="bg-red-500 text-white px-6 py-1 rounded-md"
                                    onclick="hapus_data(`{{ $user->f_id }}`)">Hapus</button>
                                <button class="bg-yellow-500 text-white px-6 py-1 rounded-md" type="button"
                                    data-modal-target="editLevel2Modal" data-modal-toggle="editLevel2Modal"
                                    onclick="openEditModal('{{ $user->f_id }}', '{{ $user->f_position_desc }}', '{{ $user->f_id1 }}')">Edit</button>

                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    {{-- Add modal --}}
    <div id="addLevel2Modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow  sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between mb-4 rounded-t sm:mb-5">
                    <div class="text-lg text-gray-900 md:text-xl">
                        <h3 class="font-bold ">
                            Form tambah level 2
                        </h3>
                    </div>
                    <div>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-toggle="addLevel2Modal">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>
                <div class="">
                    <form action="{{ route('master_data.level2.store') }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="mb-3">
                            <select class="select select-bordered w-full rounded-lg text-gray-600" id="f_id1" name="f_id1">
                                <option selected value="" class="text-gray-600">-- Pilih level 1 --</option>
                                @foreach($level1 as $key => $value)
                                <option value="{{ $value->f_id }}" class="text-gray-600"> {{ $value->f_position_desc }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="f_position_desc" class="block mb-2 text-sm font-medium text-gray-900">Level 2</label>
                            <input type="text" id="text" aria-describedby="helper-text-explanation"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="" name="f_position_desc" id="f_position_desc" value="{{ old('ec') }}">
                        </div>

                        <button type="submit"
                            class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            <i class="fa fa-plus text-white font-bold mr-2"></i>
                            Tambah
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div id="editLevel2Modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow  sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between mb-4 rounded-t sm:mb-5">
                    <div class="text-lg text-gray-900 md:text-xl">
                        <h3 class="font-bold ">
                            Form edit Level 2
                        </h3>
                    </div>
                    <div>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-toggle="addLevel2Modal">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>
                <div class="">
                    <form action="{{ route('master_data.level2.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_f_position_desc" id="id_f_position_desc">
                        <div class="mb-3">
                            <select class="select select-bordered w-full rounded-lg text-gray-600" id="f_id1" name="f_id1">
                                <option selected value="" class="text-gray-600">-- Pilih level 1 --</option>
                                @foreach($level1 as $key => $value)
                                <option value="{{ $value->f_id }}" class="text-gray-600"> {{ $value->f_position_desc }}
                            </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="f_position_desc" class="block mb-2 text-sm font-medium text-gray-900">Level 2</label>
                            <input type="text" id="f_position_desc" aria-describedby="helper-text-explanation"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="" name="f_position_desc" id="f_position_desc" value="{{ old('ec') }}">
                        </div>

                        <button type="submit"
                            class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            <i class="fas fa-edit text-white font-bold mr-2"></i>
                            Edit
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div id="importModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
    <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative p-4 bg-white rounded-lg shadow  sm:p-5">
            <!-- Modal header -->
            <div class="flex justify-between mb-4 rounded-t sm:mb-5">
                <div class="text-lg text-gray-900 md:text-xl">
                    <h3 class="font-bold ">
                        ImportModal
                    </h3>
                </div>
                <div>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="importModal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
            </div>
            <div class="">
                <form action="{{ route('master_data.level2.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="mb-3">
                        <label for="file" class="block mb-2 text-sm font-medium text-gray-900">Level
                            2</label>
                        <input type="file" id="file" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="file" id="file" value="{{ old('ec') }}">
                    </div>

                    <button type="submit"
                        class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        <i class="fa fa-plus text-white font-bold mr-2"></i>
                        Upload
                    </button>
                </form>

                <div class="mt-4">

                <a href="{{ route('master_data.level2.export') }}" class="font-normal text-blue-500">Download format</a>
            </div>

            </div>
        </div>
    </div>
</div>

    <script>
        function openEditModal(id, f_position_desc, f_id1) {
            $('#editLevel2Modal #id_f_position_desc').val(id)
            $('#editLevel2Modal #f_position_desc').val(f_position_desc)
            $('#editLevel2Modal #f_id1').val(f_id1).trigger('change')

        }

        function hapus_data(id) {
            Swal.fire({
                title: "Yakin ingin menghapus data",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Hapus",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white' // Custom classes for confirm button
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('/master_data/level2_hapus/') }}` + '/' +
                            id, // Replace with your delete route
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')

                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: `${response.message}`,
                                    icon: 'success',
                                    customClass: {
                                        confirmButton: 'bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md' // Custom style for success confirm button
                                    },
                                    buttonsStyling: false // Disable default styling to apply custom classes

                                }).then(() => {
                                    // Reload the page or remove the deleted item from the table
                                    location.reload(); // Or use other methods to update the UI
                                });
                            } else {
                                Swal.fire(
                                    'Gagal!', `${response.message}`, 'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!', 'Terjadi kesalahan. Silakan coba lagi.', 'error'
                            );
                        }
                    });

                }
            });

        }
    </script>
@endsection
