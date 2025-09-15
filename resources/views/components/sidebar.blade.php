<div>
    <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar"
        type="button"
        class="inline-flex items-center p-2 mt-2 ml-3 text-white text-sm text-gray-500 rounded-lg sm:hidden hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-gray-200">
        <span class="sr-only">Open sidebar</span>
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd"
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
            </path>
        </svg>
    </button>

    <aside id="default-sidebar"
        class="stikcy top-10 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
        aria-label="Sidenav">
        <div class="overflow-y-auto py-5 px-3 h-full bg-gradient-to-bl from-[#050C27] to-[#01215A] ">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center text-2xl text-white font-bold">
                    <img src="{{ asset('img/logo.png') }}" width="75px"> </img> Corporate
                </a>
            </div>
            <hr class="py-4 border-gray-500">
            <ul class="space-y-2">


                @php
       $roles =  Auth::user();

                    $role = $roles->groups->first()->id ?? null;
                @endphp
                @if($role == 1)
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center p-2 text-base font-normal text-white rounded-lg group">
                            <div class="bg-gray-100 p-2 rounded-md"><img src="{{ asset('img/icon/monitoring.png') }}"
                                    alt=""></div>
                            <span class="ml-3 text-white">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.index') }}"
                            class="flex items-center p-2 text-base font-normal text-white rounded-lg group">
                            <div class="bg-gray-100 p-2 rounded-md"><img src="{{ asset('img/icon/monitoring.png') }}"
                                    alt=""></div>
                            <span class="ml-3 text-white">List Akun</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('event.index') }}"
                            class="flex items-center p-2 text-base font-normal text-white rounded-lg group">
                            <div class="bg-gray-100 p-2 rounded-md"><img src="{{ asset('img/icon/monitoring.png') }}"
                                    alt=""></div>
                            <span class="ml-3 text-white">Event Akun</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('event.index') }}"
                            class="flex items-center p-2 text-base font-normal text-white rounded-lg group">
                            <div class="bg-gray-100 p-2 rounded-md"><img src="{{ asset('img/icon/monitoring.png') }}"
                                    alt=""></div>
                            <span class="ml-3 text-white">Dimensi</span>
                        </a>
                    </li>
                             <li>
                        <a href="{{ route('event.index') }}"
                            class="flex items-center p-2 text-base font-normal text-white rounded-lg group">
                            <div class="bg-gray-100 p-2 rounded-md"><img src="{{ asset('img/icon/monitoring.png') }}"
                                    alt=""></div>
                            <span class="ml-3 text-white">Item pertanyaan</span>
                        </a>
                    </li>
                @endif
                {{-- <li>
                    <a href="#"
                        class="flex items-center p-2 text-base font-normal text-white rounded-lg hover:bg-blue-600 group">
                        <svg aria-hidden="true"
                            class="w-6 h-6 text-gray-400 transition duration-75 group-hover:text-white"
                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                        </svg>
                        <span class="ml-3 text-white">Overview</span>
                    </a>
                </li> --}}


                {{-- Jika admin corporate munculkan --}}

                @if ($role == 2)
                    <li>
                        <a href="{{ url('monitoring') }}"
                            class="flex items-center p-2 text-base font-normal text-white rounded-lg group">
                            <div class="bg-gray-100 p-2 rounded-md"><img src="{{ asset('img/icon/monitoring.png') }}"
                                    alt=""></div>
                            <span class="ml-3 text-white">Monitoring</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('event.index') }}"
                            class="flex items-center p-2 text-base font-normal text-white rounded-lg group">
                            <div class="bg-gray-100 p-2 rounded-md"><img src="{{ asset('img/icon/monitoring.png') }}"
                                    alt=""></div>
                            <span class="ml-3 text-white">Event Akun</span>
                        </a>
                    </li>
                    <li>
                        <button type="button"
                            class="flex items-center p-2 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600"
                            aria-controls="dropdown-authentication" data-collapse-toggle="dropdown-authentication">
                            <div class="bg-gray-100 p-2 rounded-md"><img src="{{ asset('img/icon/master_data.png') }}"
                                    alt=""></div>

                            <span class="flex-1 ml-3 text-white text-left whitespace-nowrap">Master data</span>
                            <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <ul id="dropdown-authentication" class="hidden py-2 space-y-2">
                            <li>
                                <a href="{{ route('master_data.level1.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_level1']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level2.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_level2']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level3.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_level3']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level4.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_level4']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level5.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_level5']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level6.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_level6']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.level7.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_level7']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.jenis_kelamin.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_others']['gender']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.usia.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_others']['age']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.masa_kerja.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_others']['mk']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.wilayah.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_others']['region']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.tingkat_pekerjaan.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_others']['work']['indonesian'] }}</a>
                            </li>
                            <li>
                                <a href="{{ route('master_data.pendidikan.index') }}"
                                    class="flex items-center p-2 pl-11 w-full text-base font-normal text-white rounded-lg transition duration-75 group hover:bg-blue-600">{{ $sidebarData['label_others']['education']['indonesian'] }}</a>
                            </li>

                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('setting.akun.index') }}"
                            class="flex items-center p-2 text-base font-normal text-white rounded-lg transition duration-75 hover:bg-blue-600 group">
                            <div class="bg-gray-100 p-2 rounded-md"><img
                                    src="{{ asset('img/icon/account_setting.png') }}" alt=""></div>
                            <span class="ml-3 text-white">Pengaturan akun</span>
                        </a>
                    </li>
                @endif
            </ul>

        </div>
    </aside>
</div>
