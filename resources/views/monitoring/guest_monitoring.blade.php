<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Talent DNA - Corporate</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />


    <!-- Favicon-->
    <link rel="shortcut icon" href="https://talentdna.me/tdna/assets/images/fav_talent_dna.png" type="image/x-icon">
    <link rel="icon" type="image/*" href="https://talentdna.me/tdna/assets/images/fav_talent_dna.png">
    <link rel="apple-touch-icon" type="image/x-icon"
        href="https://talentdna.me/tdna/assets/survey/img/fav_talent_dna.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72"
        href="https://talentdna.me/tdna/assets/survey/img/fav_talent_dna.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114"
        href="https://talentdna.me/tdna/assets/survey/img/fav_talent_dna.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144"
        href="https://talentdna.me/tdna/assets/survey/img/fav_talent_dna.png">
    <meta property="og:image" content="https://talentdna.me/tdna/assets/survey/img/fav_talent_dna.png">
    <meta property="twitter:image" content="https://talentdna.me/tdna/assets/survey/img/fav_talent_dna.png">



    {{-- Sweetalert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- Jquery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>


    {{-- Datatables --}}
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>


    {{-- Alpine js --}}
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}




    <!-- Styles / Scripts -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>



{{-- <body class="h-screen overflow-hidden bg-gradient-to-l from-[#050C27] to-[#01215A] text-white"> --}}
<body class="h-screen overflow-hidden">


    {{-- <x-navbar /> --}}

    <style>
        /* Matikan semua style default dari Simple-DataTables */
        /* .datatable-wrapper,
        .datatable-container,
        .datatable-top,
        .datatable-bottom,
        .datatable-pagination,
        .datatable-info,
        .datatable-dropdown,
        .datatable-search,
        .datatable-selector,
        .datatable-input,
        .datatable-pagination-list {
          all: unset;
        } */

        /* Tabel biar tetap tampil baik */
        .datatable-table {
            /* all: unset; */
            width: 100%;
            /* border-collapse: collapse; */
        }

        .datatable-table th {
            background-color: transparent !important;
            background: transparent !important;
        }

        .datatable-wrapper thead th {
            background-color: transparent !important;
            background: transparent !important;
        }

        th {
            background-color: transparent !important;
        }

        .datatable-table td {
            color: white;
            padding: 0.75rem 1rem;
            text-align: left;
        }

        /* Tambahkan pagination custom jika diperlukan */
    </style>

    <style>
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            color: #3F51B5 !important
        }
    </style>


    <div class="flex h-screen pb-10">
        <main class="flex-1 overflow-auto ">

            @include('layouts.navigation')
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>

</html>
