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


        <form action="{{ route('event.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <div class="mb-3">
                        <label for="f_corporate_id" class="block mb-2 text-sm">Akun</label>
                        <select id="f_corporate_id" name="f_corporate_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            @foreach($akun as $key => $value)
                                <option value="{{ $value->f_account_id }}" {{ old('f_corporate_id') == $value->f_account_id ? 'selected' : '' }}>{{ $value->f_account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="f_event_name" class="block mb-2 text-sm">Nama event</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_event_name" id="f_event_name" value="{{ old('f_event_name') }}">
                    </div>
                    <div class="mb-3">
                        <label for="f_event_start" class="block mb-2 text-sm">Event start date</label>
                        <input type="date" id="f_event_start" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_event_start" id="f_event_start" value="{{ old('f_event_start') }}">
                    </div>
                    <div class="mb-3">
                        <label for="f_event_start_time" class="block mb-2 text-sm">Event start time</label>
                        <input type="time" id="f_event_start_time" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_event_start_time" id="f_event_start_time"
                            value="{{ old('f_event_start_time') }}">
                    </div>
                    <div class="mb-3">
                        <label for="f_event_end" class="block mb-2 text-sm">Event end date</label>
                        <input type="date" id="f_event_end" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_event_end" id="f_event_end" value="{{ old('f_event_end') }}">
                    </div>
                    <div class="mb-3">
                        <label for="f_event_end_time" class="block mb-2 text-sm">Event end time</label>
                        <input type="time" id="f_event_end_time" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_event_end_time" id="f_event_end_time"
                            value="{{ old('f_event_end_time') }}">
                    </div>
                                        <div class="mb-3">
                        <label for="f_event_min_respon" class="block mb-2 text-sm">Minimal Responden</label>
                        <input type="text" id="text" aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="" name="f_event_min_respon" id="f_event_min_respon" value="{{ old('f_event_min_respon') }}">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('event.index') }}"
                    class="px-4 py-2 text-sm font-medium text-white  rounded-lg bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                    type="button">Batal</a>
                <button
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-400 rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                    type="submit">Tambah</button>
            </div>



        </form>
    </div>

    <script>
        $(document).ready(function() {
    $('input[type="time"]').on('click', function() {
        if (this.showPicker) {
            this.showPicker(); // hanya didukung Chrome & Edge
        }
    });
});
    </script>
@endsection
