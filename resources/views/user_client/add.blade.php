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


        <form action="{{ route('user_client.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="mb-3">
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="username" id="username" value="{{ old('username') }}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="email" id="email" value="{{ old('email') }}">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="password" id="password" value="{{ old('password') }}">
                    </div>

                    <div class="mb-3">
                        <label for="f_user_repassword" class="block mb-2 text-sm font-medium text-gray-900">Re
                            Password</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_user_repassword" id="f_user_repassword"
                            value="{{ old('f_user_repassword') }}">
                    </div>
                </div>
                <div>
                    <h3 class="text-center font-bold text-xl">Level Organisasi</h3>

                    @if ($setting_demografi['f_level1'])
                        <div class="mb-3">
                            <select class="select select-bordered w-full rounded-lg border-slate-300 text-slate-600"
                                id="f_id1" name="f_level1">
                                <option selected value="">-- Pilih {{ $label_level1['indonesian'] }} --</option>
                                @foreach ($level1 as $key => $value)
                                    <option value="{{ $value->f_id }}"> {{ $value->f_position_desc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    @if ($setting_demografi['f_level2'])
                    <div x-data="{ items: [1] }">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="mb-3 flex items-center justify-center gap-1">
                                <!-- Dropdown Select -->
                                <select class="select select-bordered w-full rounded-lg border-slate-300 text-slate-600"
                                    :id="'f_level2_' + index" :name="'f_level2[]'">
                                    <option selected value="">-- Pilih {{ $label_level2['indonesian'] }} --</option>
                                    @foreach ($level2 as $value)
                                        <option value="{{ $value->f_id }}">{{ $value->f_position_desc }}</option>
                                    @endforeach
                                </select>

                                <!-- Tombol Tambah -->
                                <div x-show="index === items.length - 1" @click="items.push(Date.now())"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                    </svg>
                                </div>

                                <!-- Tombol Hapus -->
                                <div x-show="items.length > 1"
                                    @click="items.splice(index, 1)"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" />
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>

                    @endif
                    @if ($setting_demografi['f_level3'])
                    <div x-data="{ items3: [1] }">
                        <template x-for="(item, index) in items3" :key="index">
                            <div class="mb-3 flex items-center justify-center gap-1">
                                <!-- Dropdown Select -->
                                <select class="select select-bordered w-full rounded-lg border-slate-300 text-slate-600"
                                    :id="'f_level3_' + index" :name="'f_level3[]'">
                                    <option selected value="">-- Pilih {{ $label_level3['indonesian'] }} --</option>
                                    @foreach ($level3 as $value)
                                        <option value="{{ $value->f_id }}">{{ $value->f_position_desc }}</option>
                                    @endforeach
                                </select>

                                <!-- Tombol Tambah -->
                                <div x-show="index === items3.length - 1" @click="items3.push(Date.now())"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                    </svg>
                                </div>

                                <!-- Tombol Hapus -->
                                <div x-show="items3.length > 1"
                                    @click="items3.splice(index, 1)"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" />
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>
                    @endif
                    @if ($setting_demografi['f_level4'])
                    <div x-data="{ items4: [1] }">
                        <template x-for="(item, index) in items4" :key="index">
                            <div class="mb-3 flex items-center justify-center gap-1">
                                <!-- Dropdown Select -->
                                <select class="select select-bordered w-full rounded-lg border-slate-300 text-slate-600"
                                    :id="'f_level4_' + index" :name="'f_level4[]'">
                                    <option selected value="">-- Pilih {{ $label_level4['indonesian'] }} --</option>
                                    @foreach ($level4 as $value)
                                        <option value="{{ $value->f_id }}">{{ $value->f_position_desc }}</option>
                                    @endforeach
                                </select>

                                <!-- Tombol Tambah -->
                                <div x-show="index === items4.length - 1" @click="items4.push(Date.now())"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                    </svg>
                                </div>

                                <!-- Tombol Hapus -->
                                <div x-show="items4.length > 1"
                                    @click="items4.splice(index, 1)"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" />
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>

                    @endif
                    @if ($setting_demografi['f_level5'])
                    <div x-data="{ items5: [1] }">
                        <template x-for="(item, index) in items5" :key="index">
                            <div class="mb-3 flex items-center justify-center gap-1">
                                <!-- Dropdown Select -->
                                <select class="select select-bordered w-full rounded-lg border-slate-300 text-slate-600"
                                    :id="'f_level5_' + index" :name="'f_level5[]'">
                                    <option selected value="">-- Pilih {{ $label_level5['indonesian'] }} --</option>
                                    @foreach ($level5 as $value)
                                        <option value="{{ $value->f_id }}">{{ $value->f_position_desc }}</option>
                                    @endforeach
                                </select>

                                <!-- Tombol Tambah -->
                                <div x-show="index === items5.length - 1" @click="items5.push(Date.now())"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                    </svg>
                                </div>

                                <!-- Tombol Hapus -->
                                <div x-show="items5.length > 1"
                                    @click="items5.splice(index, 1)"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" />
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>

                    @endif
                    @if ($setting_demografi['f_level6'])
                    <div x-data="{ items6: [1] }">
                        <template x-for="(item, index) in items6" :key="index">
                            <div class="mb-3 flex items-center justify-center gap-1">
                                <!-- Dropdown Select -->
                                <select class="select select-bordered w-full rounded-lg border-slate-300 text-slate-600"
                                    :id="'f_level6_' + index" :name="'f_level6[]'">
                                    <option selected value="">-- Pilih {{ $label_level6['indonesian'] }} --</option>
                                    @foreach ($level6 as $value)
                                        <option value="{{ $value->f_id }}">{{ $value->f_position_desc }}</option>
                                    @endforeach
                                </select>

                                <!-- Tombol Tambah -->
                                <div x-show="index === items6.length - 1" @click="items6.push(Date.now())"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                    </svg>
                                </div>

                                <!-- Tombol Hapus -->
                                <div x-show="items6.length > 1"
                                    @click="items6.splice(index, 1)"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" />
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>
                    @endif
                    @if ($setting_demografi['f_level7'])
                    <div x-data="{ items7: [1] }">
                        <template x-for="(item, index) in items7" :key="index">
                            <div class="mb-3 flex items-center justify-center gap-1">
                                <!-- Dropdown Select -->
                                <select class="select select-bordered w-full rounded-lg border-slate-300 text-slate-600"
                                    :id="'f_level7_' + index" :name="'f_level7[]'">
                                    <option selected value="">-- Pilih {{ $label_level7['indonesian'] }} --</option>
                                    @foreach ($level7 as $value)
                                        <option value="{{ $value->f_id }}">{{ $value->f_position_desc }}</option>
                                    @endforeach
                                </select>

                                <!-- Tombol Tambah -->
                                <div x-show="index === items7.length - 1" @click="items7.push(Date.now())"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                    </svg>
                                </div>

                                <!-- Tombol Hapus -->
                                <div x-show="items7.length > 1"
                                    @click="items7.splice(index, 1)"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg text-center flex justify-center">
                                    <svg class="h-4 w-3.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" />
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>

                    @endif
                </div>
            </div>

            <div class="flex items-center justify-center">
                <button
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                    type="submit">Tambah</button>
            </div>
        </form>
    </div>
@endsection
