{{-- @extends('layouts.guest') --}}

<x-monitoring :appreance="$appreance">



    @section('content')

        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>



        <style>
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

        <h2 class=" font-semibold text-white mb-4 text-6xl">Responden</h2>


        <div class="w-xl mb-4 mx-auto p-6 mt-4 rounded-lg shadow-md" style="background-color: rgba(255, 255, 255, 0.8);">

            <!-- Display Kuota -->
            <p class="text-gray-600 text-center"><span class="font-bold text-blue-800 text-8xl" id="jumlah">{{ $sudah_isi }}</span></p>
        </div>


        <script>
             $(document).ready(function() {
               function get_count(){
                    var eventId = '{{ $code_event }}'; // Get the ID from the button's data-id attribute

                    $.ajax({
                        url: '{{ route("getquick.event", ":id") }}'.replace(':id', eventId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}', // Include the CSRF token for protection
                        },
                        success: function(response) {
                            if($('#jumlah').html() != response.count){
                            // Fade out the current number
                                $('#jumlah').fadeOut(300, function() {
                                    // Update the number
                                    $(this).text(response.count).fadeIn(300); // Fade it back in with new count
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("Error: " + error);
                        }
                    });
                }

                setInterval(get_count, 5000);
            });
        </script>
    </x-monitoring>
