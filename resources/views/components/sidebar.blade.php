<div>
    <button
        data-drawer-target="default-sidebar"
        data-drawer-toggle="default-sidebar"
        aria-controls="default-sidebar"
        type="button"
        class="inline-flex items-center p-2 mt-2 ml-3 text-slate-600 font-medium text-sm rounded-lg sm:hidden hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-200"
    >
        <span class="sr-only">Open sidebar</span>
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd"
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
            </path>
        </svg>
    </button>

    <aside id="default-sidebar"
        class="sticky top-10 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
        aria-label="Sidenav"
    >
        <div class="overflow-y-auto py-5 px-3 h-full shadow-xl">

            {{-- Logo (selalu tampil) --}}
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center text-2xl font-bold">
                    <img src="https://actconsulting.co/wp-content/uploads/2023/10/ACT-AT-Landscape-BLUE.png" class="p-1" alt="ACT Logo">
                </a>
            </div>
            <hr class="py-4 border-gray-200">

            @php
                $user = Auth::user();
                $role = $user->groups->first()->id ?? null;
                $isMaster = request()->routeIs('master_data.*');
            @endphp

            <ul class="space-y-2">
                {{-- ROLE 1 (Superadmin) --}}
                @if($role == 1)
                <li>
                        <a href="{{ url('monitoring') }}"
                           @class([
                             'flex items-center p-2 rounded-lg transition group',
                             request()->is('monitoring*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-600 hover:text-white',
                           ])>
                            <div @class([
                                'p-2 rounded-md transition',
                                request()->is('monitoring*') ? 'bg-white' : 'bg-gray-100 group-hover:bg-white',
                            ])>
                                <img src="{{ asset('img/icon/monitoring.png') }}" alt="Monitoring">
                            </div>
                            <span @class([
                                'ml-3 font-medium transition',
                                request()->is('monitoring*') ? 'text-white' : 'text-slate-700 group-hover:text-white',
                            ])>Monitoring</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.index') }}"
                           @class([
                             'flex items-center p-2 rounded-lg transition group',
                             'bg-blue-600 text-white shadow-sm' => request()->routeIs('account.*'),
                             'text-slate-600 hover:bg-blue-600 hover:text-white' => !request()->routeIs('account.*'),
                           ])>
                            <div @class([
                                'p-2 rounded-md transition',
                                'bg-white' => request()->routeIs('account.*'),
                                'bg-gray-100 group-hover:bg-white' => !request()->routeIs('account.*'),
                            ])>
                                <img src="{{ asset('img/icon/monitoring.png') }}" alt="">
                            </div>
                            <span @class([
                                'ml-3 font-medium transition',
                                'text-white' => request()->routeIs('account.*'),
                                'text-slate-700 group-hover:text-white' => !request()->routeIs('account.*'),
                            ])>List Akun</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('event.index') }}"
                           @class([
                             'flex items-center p-2 rounded-lg transition group',
                             'bg-blue-600 text-white shadow-sm' => request()->routeIs('event.*'),
                             'text-slate-600 hover:bg-blue-600 hover:text-white' => !request()->routeIs('event.*'),
                           ])>
                            <div @class([
                                'p-2 rounded-md transition',
                                'bg-white' => request()->routeIs('event.*'),
                                'bg-gray-100 group-hover:bg-white' => !request()->routeIs('event.*'),
                            ])>
                                <img src="{{ asset('img/icon/monitoring.png') }}" alt="">
                            </div>
                            <span @class([
                                'ml-3 font-medium transition',
                                'text-white' => request()->routeIs('event.*'),
                                'text-slate-700 group-hover:text-white' => !request()->routeIs('event.*'),
                            ])>Event Akun</span>
                        </a>
                    </li>

                    {{-- Variabel (route placeholder tetap seperti semula) --}}
{{-- Variabel --}}
<li>
    <a href="{{ route('master_data.variabel') }}"
       @class([
         'flex items-center p-2 rounded-lg transition group',
         // active
         'bg-blue-600 text-white shadow-sm' => request()->routeIs('master_data.variabel'),
         // default + hover
         'text-slate-600 hover:bg-blue-600 hover:text-white' => !request()->routeIs('master_data.variabel'),
       ])>
        <div @class([
            'p-2 rounded-md transition',
            // chip icon saat aktif & hover
            'bg-white' => request()->routeIs('master_data.variabel'),
            'bg-gray-100 group-hover:bg-white' => !request()->routeIs('master_data.variabel'),
        ])>
            <img src="{{ asset('img/icon/monitoring.png') }}" alt="Variabel">
        </div>
        <span @class([
            'ml-3 font-medium transition',
            'text-white' => request()->routeIs('master_data.variabel'),
            'text-slate-700 group-hover:text-white' => !request()->routeIs('master_data.variabel'),
        ])>Variabel</span>
    </a>
</li>


    {{-- Item pertanyaan --}}
<li>
    <a href="{{ route('master_data.item_pernyataan') }}"
       @class([
         'flex items-center p-2 rounded-lg transition group',
         // active
         'bg-blue-600 text-white shadow-sm' => request()->routeIs('master_data.item_pernyataan'),
         // default + hover
         'text-slate-600 hover:bg-blue-600 hover:text-white' => !request()->routeIs('master_data.item_pernyataan'),
       ])>
        <div @class([
            'p-2 rounded-md transition',
            'bg-white' => request()->routeIs('master_data.item_pernyataan'),
            'bg-gray-100 group-hover:bg-white' => !request()->routeIs('master_data.item_pernyataan'),
        ])>
            <img src="{{ asset('img/icon/monitoring.png') }}" alt="Item pertanyaan">
        </div>
        <span @class([
            'ml-3 font-medium transition',
            'text-white' => request()->routeIs('master_data.item_pernyataan'),
            'text-slate-700 group-hover:text-white' => !request()->routeIs('master_data.item_pernyataan'),
        ])>Item pertanyaan</span>
    </a>
</li>

                @endif

                {{-- ROLE 2 (Admin Corporate) --}}
                @if ($role == 2)
                    {{-- Monitoring (wajib tampil) --}}
                    <li>
                        <a href="{{ url('monitoring') }}"
                           @class([
                             'flex items-center p-2 rounded-lg transition group',
                             request()->is('monitoring*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-600 hover:text-white',
                           ])>
                            <div @class([
                                'p-2 rounded-md transition',
                                request()->is('monitoring*') ? 'bg-white' : 'bg-gray-100 group-hover:bg-white',
                            ])>
                                <img src="{{ asset('img/icon/monitoring.png') }}" alt="Monitoring">
                            </div>
                            <span @class([
                                'ml-3 font-medium transition',
                                request()->is('monitoring*') ? 'text-white' : 'text-slate-700 group-hover:text-white',
                            ])>Monitoring</span>
                        </a>
                    </li>

                    {{-- Event Akun (wajib tampil) --}}
                    <li>
                        <a href="{{ route('event.index') }}"
                           @class([
                             'flex items-center p-2 rounded-lg transition group',
                             request()->routeIs('event.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-600 hover:text-white',
                           ])>
                            <div @class([
                                'p-2 rounded-md transition',
                                request()->routeIs('event.*') ? 'bg-white' : 'bg-gray-100 group-hover:bg-white',
                            ])>
                                <img src="{{ asset('img/icon/monitoring.png') }}" alt="Event">
                            </div>
                            <span @class([
                                'ml-3 font-medium transition',
                                request()->routeIs('event.*') ? 'text-white' : 'text-slate-700 group-hover:text-white',
                            ])>Event Akun</span>
                        </a>
                    </li>

                    {{-- Dropdown Master Data --}}
                    <li>
                        <button type="button"
                            @class([
                              'flex items-center p-2 w-full rounded-lg transition group',
                              $isMaster ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-600 hover:text-white',
                            ])
                            aria-controls="dropdown-authentication"
                            data-collapse-toggle="dropdown-authentication"
                            @if($isMaster) aria-expanded="true" @endif
                        >
                            <div @class([
                                'p-2 rounded-md transition',
                                $isMaster ? 'bg-white' : 'bg-gray-100 group-hover:bg-white',
                            ])>
                                <img src="{{ asset('img/icon/master_data.png') }}" alt="Master Data">
                            </div>

                            <span @class([
                                'flex-1 ml-3 font-medium text-left whitespace-nowrap transition',
                                $isMaster ? 'text-white' : 'text-slate-700 group-hover:text-white',
                            ])>Master data</span>

                            <svg aria-hidden="true" class="w-6 h-6 transition {{ $isMaster ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <ul id="dropdown-authentication" class="{{ $isMaster ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('master_data.level1.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.level1.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_level1']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level2.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.level2.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_level2']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level3.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.level3.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_level3']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level4.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.level4.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_level4']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level5.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.level5.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_level5']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.jenis_kelamin.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.jenis_kelamin.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_others']['gender']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.usia.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.usia.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_others']['age']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.masa_kerja.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.masa_kerja.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_others']['mk']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.wilayah.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.wilayah.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_others']['region']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.tingkat_pekerjaan.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.tingkat_pekerjaan.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_others']['work']['indonesian'] }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.pendidikan.index') }}"
                                   @class([
                                      'flex items-center p-2 pl-11 rounded-md transition border-l-4',
                                      request()->routeIs('master_data.pendidikan.*')
                                      ? 'bg-blue-50 text-blue-700 border-blue-600'
                                      : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-transparent',
                                   ])>
                                   {{ $sidebarData['label_others']['education']['indonesian'] }}
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Pengaturan Akun --}}
                    <li>
                        <a href="{{ route('setting.akun.index') }}"
                           @class([
                             'flex items-center p-2 rounded-lg transition group',
                             request()->routeIs('setting.akun.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-600 hover:text-white',
                           ])>
                            <div @class([
                                'p-2 rounded-md transition',
                                request()->routeIs('setting.akun.*') ? 'bg-white' : 'bg-gray-100 group-hover:bg-white',
                            ])>
                                <img src="{{ asset('img/icon/account_setting.png') }}" alt="">
                            </div>
                            <span @class([
                                'ml-3 font-medium transition',
                                request()->routeIs('setting.akun.*') ? 'text-white' : 'text-slate-700 group-hover:text-white',
                            ])>Pengaturan akun</span>
                        </a>
                    </li>
                @endif
            </ul>

        </div>
    </aside>
</div>
