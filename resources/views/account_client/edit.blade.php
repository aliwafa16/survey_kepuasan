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


        <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')  
            <div class="flex gap-3 justify-center">
                <input type="hidden" name="f_account_id" value="{{ $data_account->f_account_id }}">
                <div class="w-full">
                    <div class="mb-3 w-1/2">
                        <label for="f_account_name" class="block mb-2 text-sm">Nama
                            Account</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_account_name" id="f_account_name"
                            value="{{ old('f_account_name', $data_account->f_account_name) }}">
                    </div>
                    <div class="mb-3 w-1/2">
                        <label for="f_account_email" class="block mb-2 text-sm">Email</label>
                        <input type="email" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_account_email" id="f_account_email"
                            value="{{ old('f_account_email', $data_account->f_account_email) }}">
                    </div>
                    <div class="mb-3 w-1/2">
                        <label for="f_account_contact" class="block mb-2 text-sm">PIC</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_account_contact" id="f_account_contact"
                            value="{{ old('f_account_contact', $data_account->f_account_contact) }}">
                    </div>
                    <div class="mb-3 w-1/2">
                        <label for="f_account_token" class="block mb-2 text-sm">Jumlah Token /
                            Responden</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_account_token" id="f_account_token" maxlength="8"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            value="{{ old('f_account_token', $data_account->f_account_token) }}">
                    </div>
                    <div class="mb-3 w-1/2">
                        <label for="f_account_phone" class="block mb-2 text-sm">No.Telp</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_account_phone" id="f_account_phone"
                            value="{{ old('f_account_phone', $data_account->f_account_phone) }}" maxlength="14"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>

                </div>



            </div>

            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('account.index') }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-400 rounded-lg bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                    type="button">Batal</a>
                <button
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                    type="submit">Edit</button>
            </div>
        </form>
    </div>
@endsection
