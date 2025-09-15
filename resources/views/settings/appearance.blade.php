<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Appearance Settings</h1>

        <form id="update-form" action="{{ route('settings.appearance.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Logo Image -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo Image (931px x 205px)</label>
                <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-600">
                @if (isset($settings->logo))
                    <img src="{{ asset('storage/' .  $settings->logo) }}" alt="Current Logo" class="h-16 mt-2">
                @endif
            </div>

            <!-- Banner Image -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Background Image (Landscape)</label>
                <input type="file" name="banner" accept="image/*" class="block w-full text-sm text-gray-600">
                @if (isset($settings->banner))
                    <img src="{{ asset('storage/' .  $settings->banner) }}" alt="Current Banner" class="h-24 mt-2">
                @endif
            </div>

            <!-- Color Primary -->
            <div>
                <label for="color_primary" class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                <input type="color" name="color_primary" id="color_primary" value="{{ old('color_primary',  $settings->color_primary ?? '#1072f1') }}" class="w-16 h-10 border rounded">
            </div>

            <!-- Color Secondary -->
            <div>
                <label for="color_secondary" class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                <input type="color" name="color_secondary" id="color_secondary" value="{{ old('color_secondary',  $settings->color_secondary ?? '#67c5f7') }}" class="w-16 h-10 border rounded">
            </div>

            <!-- Tombol aksi sejajar -->


        </form>

        <div class="flex space-x-4 mt-6">
            <!-- Tombol Simpan: trigger form di atas -->
            <button type="submit" form="update-form" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
                Simpan Perubahan
            </button>

            <!-- Tombol Reset: form terpisah -->
            <form action="{{ route('settings.appearance.reset') }}" method="POST" onsubmit="return confirm('Yakin ingin mereset tampilan?')">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded shadow">
                    Reset Tampilan
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
