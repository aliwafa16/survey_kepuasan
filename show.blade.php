@extends('layout.frontend') @section('content')
    @php
        $language = 'indonesian'; // atau 'english', 'malaysia' sesuai pilihan user
    @endphp



    <style>
        /* body {
          background-color: grey;
        } */

        /* Customize the slider track */
        input[type="range"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 100%;
            height: 8px;
            background: #bdc0c6;
            /* Light gray background */
            border-radius: 9999px;
            transition: background 0.3s;
        }

        /* Customize the slider thumb */
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #01215A;
            /* Tailwind green */
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
            box-shadow: 0 0 15px rgba(93, 152, 255, 0.8);
        }

        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #01215A;
            /* Tailwind green */
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
            box-shadow: 0 0 15px rgba(93, 152, 255, 0.8);
        }

        input[type="range"]::-ms-thumb {
            width: 20px;
            height: 20px;
            background: #01215A;
            /* Tailwind green */
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
            box-shadow: 0 0 15px rgba(93, 152, 255, 0.8);
        }

        /* Change thumb color on hover */
        input[type="range"]:hover::-webkit-slider-thumb {
            background: #01215A;
            /* Darker green */
        }

        input[type="range"]:hover::-moz-range-thumb {
            background: #01215A;
            /* Darker green */
        }

        input[type="range"]:hover::-ms-thumb {
            background: #01215A;
            /* Darker green */
        }
    </style>
    <div class="container p-6 mx-auto bg-transparent">
        <main class="  h-auto mb-10">
            <div class="container mx-auto px-4 py-8 border-spacing-2">
                <img src="{{ asset('img/BANNER.png') }}" class="mb-5" alt="">
                <div class="container bg-[#7ea38d]/30 mx-auto px-16 py-8 border-2 border-[#01215A] rounded-[20px] pengisian">
                    <!-- Form untuk submit survey -->
                    <form id="surveyForm" action="{{ Route('survey.submit') }}" method="POST">
                        @csrf
                        <div id="demografi">
                            <input type="text" id="account_id" name="account_id" value="{{ $account_id }}"
                                class="w-full bg-white border px-4 py-2 rounded" hidden>

                            <label class="text-white mb-2 font-bold text-lg" for="">Nama lengkap (Tanpa Jabatan dan Gelar)</label>
                            <input type="text" id="name" name="name" value=''
                                class="w-full bg-white border px-4 py-2 rounded demografi mb-4">
                            <label class="text-white mb-2 text-lg font-bold" for="">Email aktif</label>
                            <input type="text" id="email" name="email" value=''
                                class="w-full bg-white border px-4 py-2 rounded demografi mb-4">


                            @if (isset($demografi['gender']["label"][$language]))
                                <label class="text-white mb-2 text-lg font-bold"
                                    for="gender">{{ $demografi['gender']["label"][$language] }}</label>
                                <select name="gender" id="gender" class="w-full bg-white border px-4 py-2 rounded demografi mb-4">
                                    <option value=""></option>
                                    @foreach ($demografi['gender']["value"] as $value)
                                        <option value="{{ $value["f_gender_name"] }}">{{ $value["f_gender_name"] }}</option>
                                    @endforeach
                                </select><br>
                            @endif


                            @if (isset($demografi['age']["label"][$language]))
                                <label class="text-white mb-2 text-lg font-bold" for="age">{{ $demografi['age']["label"][$language] }}</label>
                                <select name="age" id="age" class="w-full bg-white border px-4 py-2 rounded demografi mb-4">
                                    <option value=""></option>
                                    @foreach ($demografi['age']["value"] as $value)
                                        <option value="{{ $value["f_id"] }}">{{ $value["f_age_desc"] }}</option>
                                    @endforeach
                                </select><br>
                            @endif


                            @if (isset($demografi['masa_kerja']["label"][$language]))
                                <label class="text-white mb-2 text-lg font-bold"
                                    for="masa_kerja">{{ $demografi['masa_kerja']["label"][$language] }}</label>
                                <select name="masa_kerja" id="masa_kerja"
                                    class="w-full bg-white border px-4 py-2 rounded demografi mb-4">
                                    <option value=""></option>
                                    @foreach ($demografi['masa_kerja']["value"] as $value)
                                        <option value="{{ $value["f_id"] }}">{{ $value["f_service_desc"] }}</option>
                                    @endforeach
                                </select><br>
                            @endif

                            @if (isset($demografi['region']["label"][$language]))
                                <label class="text-white mb-2 text-lg font-bold"
                                    for="region">{{ $demografi['region']["label"][$language] }}</label>
                                <select name="region" id="region" class="w-full bg-white border px-4 py-2 rounded demografi mb-4">
                                    <option value=""></option>
                                    @foreach ($demografi['region']["value"] as $value)
                                        <option value="{{ $value["f_id"] }}">{{ $value["f_region_name"] }}</option>
                                    @endforeach
                                </select><br>
                            @endif

                            @if (isset($demografi['level_of_work']["label"][$language]))
                                <label class="text-white mb-2 text-lg font-bold"
                                    for="level_of_work">{{ $demografi['level_of_work']["label"][$language] }}</label>
                                <select name="level_of_work" id="level_of_work"
                                    class="w-full bg-white border px-4 py-2 rounded demografi mb-4">
                                    <option value=""></option>
                                    @foreach ($demografi['level_of_work']["value"] as $value)
                                        <option value="{{ $value["f_id"] }}">{{ $value["f_levelwork_desc"] }}</option>
                                    @endforeach
                                </select><br>
                            @endif


                            @if (isset($demografi['pendidikan']["label"][$language]))
                                <label class="text-white mb-2 text-lg font-bold"
                                    for="pendidikan">{{ $demografi['pendidikan']["label"][$language] }}</label>
                                <select name="pendidikan" id="pendidikan"
                                    class="w-full bg-white border px-4 py-2 rounded demografi mb-4">
                                    <option value=""></option>
                                    @foreach ($demografi['pendidikan']["value"] as $value)
                                        <option value="{{ $value["f_kode"] }}">{{ $value["f_name"] }}</option>
                                    @endforeach
                                </select><br>
                            @endif
                            
                            @foreach ($level as $key => $field)
                                @if (isset($field["label"][$language]))
                                    <div>
                                        <label class="text-white mb-2 text-lg font-bold" for="{{ $key }}">{{ $field["label"][$language] }}</label>
                                        <select name="{{ $key }}" id="{{ $key }}" data-level="{{ $field['level']??0 }}"
                                            class="w-full bg-white border px-4 py-2 rounded demografi mb-4 level"
                                            onchange="changeLevel({{ $field['level']??0 }})">
                                            <option value=""></option>
                                                @foreach ($field["value"] as $value)
                                                    <option value="{{ $value["f_id"] }}">{{ $value["f_position_desc"] }}</option>
                                                @endforeach
                                        </select><br>
                                    </div>
                                @endif
                            @endforeach
                            <br><br>

                            <button type="button" id="mulai"
                                class="bg-gradient-to-r from-[#050C27] to-[#01215A]  text-white py-2 px-4 rounded-[20px]">Mulai
                                Survey</button>

                        </div>
                        <!-- Section Wrapper -->

                        <div id="surveySections"  hidden>
                            <div>
                                <!-- Progress Bar -->
                                <div class="flex justify-between mb-4">
                                    <div class="text-white font-bold text-1xl leading-4">Progres Tes:</div>
                                    <div class="">
                                        <span id="progressPercentage" class=" text-white font-bold text-2xl leading-4"> 0%
                                        </span>
                                    </div>
                                </div>
                                <div class="relative w-full bg-gray-200 rounded-full h-4 mb-14">
                                    <div id="progressBar" class="bg-[#01215A] h-4 rounded-full"
                                        style="width: 0%; box-shadow: 0 0 15px rgba(93, 152, 255, 0.8);"></div>
                                </div>
                            </div> @foreach ($sections as $sectionIndex => $section) <div
                                class="survey-section {{ $sectionIndex === 0 ? '' : 'hidden' }}"
                                data-index="{{ $sectionIndex }}">
                                @php
                                $count_soal = count($section);
                                // echo $count_soal;
                                $total_persentase = $count_soal - ceil($count_soal*(25/100));
                                @endphp
                                @foreach ($section as $question) <div class="mb-6  pb-4">
                                    <p class="mb-2 text-white font-medium">{{ $question['f_item_name'] }}</p>
                                    <input type="range" name="answers[ex{{ $question['f_id'] }}]" min="1" max="10"
                                        step="0.01" value="1" class="">
                                    <div class="flex justify-between w-full">
                                        <div class="text-white">Sangat Tidak Setuju</div>
                                        <div class="text-white">Sangat Setuju</div>
                                    </div>
                                </div> @endforeach
                            </div> @endforeach

                            <div class="flex justify-center mt-6">
                                <button type="button" id="prevBtn"
                                onclick="previous();"
                                    class="bg-gradient-to-r text-white from-[#050C27] to-[#01215A] py-2 px-4 rounded-[20px]">Sebelumnya</button>
                                <button type="button" id="nextBtn"
                                    class="bg-gradient-to-r from-[#050C27] to-[#01215A]  text-white py-2 px-4 rounded-[20px]">Selanjutnya</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <script>

            function previous(){
                // e.preventDefault();
                console.log(currentSection);
                if (currentSection > 0) {
                    currentSection--;
                    updateSections();

                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                }
            };

            $.each($('.level'), function (index, element) {
                // console.log(element);
                // console.log();
                // console.log(index);

                const level = $(element).data('level');

            })

            function changeLevel(level) {
                var selector = '#label_level' + level;
                $.ajax({
                    url: "{{ Route('survey.getLevel') }}",
                    method: "POST",
                    data: { level, id: $(selector).val() },
                    dataType: "JSON",
                    success: function (response) {
                        // console.log(response);

                        var html = `<option value=""></option>`;

                        $.each(response, function (index, value) {
                            // console.log(value);
                            html += `<option value="${value['f_id']}">${value['f_position_desc']}</option>`;
                        });
                        var selector_next = '#label_level' + (level + 1);
                        $(selector_next).html('').html(html);

                        // <option value="{{ $value["f_id"] }}">{{ $value["f_position_desc"] }}</option>
                    }
                });
            }




            const sections = $('.survey-section'); // Semua section
            const prevBtn = $('#prevBtn'); // Tombol Previous
            const nextBtn = $('#nextBtn'); // Tombol Next/Submit
            const progressBar = $('#progressBar'); // Progress Bar
            const progressPercentage = $('#progressPercentage'); // Teks persentase
            let currentSection = 0; // Index section aktif
            // Fungsi untuk memperbarui tampilan section

            $('#mulai').on('click', function () {
                let isValid = true;

                $('#demografi')
                    .find('input:not([type="hidden"]), select')
                    .each(function () {
                        if ($(this).val() === '') {
                            isValid = false;
                            $(this).addClass('border-red-500'); // Tambahkan efek merah
                        } else {
                            $(this).removeClass('border-red-500');
                        }
                    });

                if (!isValid) {
                    // alert('Semua field harus diisi!');
                    Swal.fire({
                        title: "Semua field harus diisi!",
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK"
                    })
                    return;
                }

                // Jika semua valid
                mulaiSurvey(); // fungsi kamu sendiri
            });

            function mulaiSurvey() {
                const email = $('#email').val().trim();
                const account_id = $('#account_id').val()


                if (!email) {
                    Swal.fire({
                        title: "Email wajib diisi!",
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.reload();
                    });;
                    return;
                }

                $.ajax({
                    url: "{{ Route('survey.check') }}",
                    method: "POST",
                    data: { "email": email, "account_id": account_id },
                    dataType: "JSON",
                    success: function (response) {
                        if (response) {
                            Swal.fire({
                                title: response['msg'],
                                icon: "info",
                                confirmButtonColor: "#3085d6",
                                confirmButtonText: "OK"
                            });
                        } else {
                            let result = '';
                            $('.demografi').each(function () {
                                let label = $(this).prev('label').text().trim();

                                if ($(this).is('select')) {
                                    // If it's a select element, get the selected option's text
                                    value = "<small style='font-weight:normal;'>" + $(this).find('option:selected').text().trim() + "</small>";
                                } else {
                                    // Otherwise, get the input value
                                    value = "<small style='font-weight:normal;'>" + $(this).val().trim() + "</small>";
                                }
                                if (value) {
                                    result += label + ': \n' + value + '\n';
                                }
                            });

                            if (result) {
                                Swal.fire({
                                    title: '<div style="text-align: left;"><center>Apakah benar data berikut?</center>\n' + result + '</div>\n',
                                    text: "Data demografi tidak dapat diubah setelah submit dan survey hanya bisa dilakukan 1x",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#3085d6",
                                    cancelButtonColor: "#d33",
                                    confirmButtonText: "Ya, benar"
                                }).then((confirm) => {
                                    if (confirm.isConfirmed) {
                                        $('#demografi').attr('hidden', true);
                                        $('#surveySections').attr('hidden', false);
                                        $('html, body').animate({ scrollTop: 0 }, 'fast');
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: "Harap lengkapi data demografi Anda!",
                                    icon: "error",
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "OK"
                                });
                            }
                        }
                    }
                });
            }

            function updateSections() {
                sections.each(function (index) {
                    if (index === currentSection) {
                        $(this).removeClass('hidden');
                        $(this).addClass('aktif');
                    } else {
                        $(this).addClass('hidden');
                        $(this).removeClass('aktif');
                    }
                });
                // Update progress bar dan persentase
                const progress = ((currentSection + 1) / sections.length) * 100;
                progressBar.css('width', progress + '%');
                progressPercentage.text(`${currentSection + 1} / ${sections.length}`);
                // Kontrol tombol navigasi
                prevBtn.prop('hidden', currentSection === 0);
                // Ubah tombol "Next" menjadi "Submit" pada section terakhir
                if (currentSection === sections.length - 1) {
                    nextBtn.text('Kirim');
                    nextBtn.off('click'); // Hapus event sebelumnya

                    $(nextBtn).attr('type', 'submit');
                    // nextBtn.on('click', function(e) {
                    //   e.preventDefault(); // Jangan submit otomatis
                    //   $('#surveyForm').submit(); // Kirim form
                    // });
                } else {
                    nextBtn.text('Selanjutnya');
                    nextBtn.off('click'); // Hapus event sebelumnya
                    nextBtn.on('click', function (e) {
                        e.preventDefault(); // Hentikan scroll otomatis
                        var total_value = 0;
                        var cek_total_value_min = 0;
                        var cek_total_value_max = 0;
                        
                        var inputs = $(".survey-section.aktif input[type='range']");
                        inputs.each(function() {
                            console.log($(this).val()); // Menampilkan nilai input di console
                            var r = $(this).val();
                            if(parseFloat(r) < 1.25) {
                                cek_total_value_min++;
                            }
                            if(parseFloat(r) > 9.75) {
                                cek_total_value_max++;
                            }
                            total_value += parseInt(r);
                        });			

                        console.log('min '+cek_total_value_min+' > @php echo $total_persentase; @endphp');
                        console.log('max '+cek_total_value_max+' > @php echo $total_persentase; @endphp');

                        // Ngiri
                        if (parseInt(cek_total_value_min) > @php echo $total_persentase;@endphp ) {
                            console.log('refresh')
                            swal.fire({
                                title: "Sistem mendeteksi bahwa Anda terlalu sering memilih jawaban pada bagian paling kiri pada skala penilaian. ",
                                text: 'Mohon untuk memberikan respons dengan lebih variatif dan objektif, sesuai dengan apa yang Anda rasakan terhadap masing-masing pernyataan.',
                                icon: 'error',
                                confirmButtonText: 'Close',
                                confirmButtonColor: '#FF0000'
                            })
                            // currentSection = 0;
                            // updateSections();
                            // prevBtn.trigger('click');

                            //console.log(currentSection);

                            return false;
                        }
                        // Nganan
                        if (parseInt(cek_total_value_max) > @php echo $total_persentase;@endphp ) {
                            console.log('refresh')
                            swal.fire({
                                title: "Sistem mendeteksi bahwa Anda terlalu sering memilih jawaban pada bagian paling kanan pada skala penilaian. ",
                                text: 'Mohon untuk memberikan respons dengan lebih variatif dan objektif, sesuai dengan apa yang Anda rasakan terhadap masing-masing pernyataan.',
                                icon: 'error',
                                confirmButtonText: 'Close',
                                confirmButtonColor: '#FF0000'
                            })
                            // currentSection = 0;
                            //updateSections();

                            // console.log(currentSection);

                            return false;
                        }

                        currentSection++;
                        updateSections();
                        // Scroll ke atas section baru
                        $('html, body').animate({
                            scrollTop: 0
                        }, 'fast');
                    });
                }
            }


           
            // Inisialisasi tampilan awal
            updateSections();


            $('#surveyForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this)[0];
                let formData = new FormData(form);
                let submitBtn = $(this).find('button[type="submit"]');
                let originalBtnText = submitBtn.html();

                // Disable and show loading
                submitBtn.prop('disabled', true).html('Loading...');
                var range = $('input[type="range"]');
                var total_value = 0;
				var cek_total_value_min = 0;
				var cek_total_value_max = 0;
				
				var inputs = $(".survey-section.aktif input[type='range']");
				inputs.each(function() {
				    console.log($(this).val()); // Menampilkan nilai input di console
				    var r = $(this).val();
				    if(parseFloat(r) < 1.25) {
						cek_total_value_min++;
					}
				    if(parseFloat(r) > 9.75) {
						cek_total_value_max++;
					}
				    total_value += parseInt(r);
				});			

				console.log('min '+cek_total_value_min+' > @php echo $total_persentase; @endphp');
				console.log('max '+cek_total_value_max+' > @php echo $total_persentase; @endphp');

				if (parseInt(cek_total_value_min) > @php echo $total_persentase;@endphp ) {
					console.log('refresh')
                    swal.fire({
                        title: "Sistem mendeteksi bahwa Anda terlalu sering memilih jawaban pada bagian paling kiri pada skala penilaian. ",
                        text: 'Mohon untuk memberikan respons dengan lebih variatif dan objektif, sesuai dengan apa yang Anda rasakan terhadap masing-masing pernyataan.',
                        icon: 'error',
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#FF0000'
                    })
                    // currentSection = 0;
                    // updateSections();

                    // console.log(currentSection);

					submitBtn.removeAttr('disabled').html('Kirim');
					return false;
				}
				// Nganan
				if (parseInt(cek_total_value_max) > @php echo $total_persentase;@endphp ) {
					console.log('refresh')
                    swal.fire({
                        title: "Sistem mendeteksi bahwa Anda terlalu sering memilih jawaban pada bagian paling kanan pada skala penilaian. ",
                        text: 'Mohon untuk memberikan respons dengan lebih variatif dan objektif, sesuai dengan apa yang Anda rasakan terhadap masing-masing pernyataan.',
                        icon: 'error',
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#FF0000'
                    })
                    // currentSection = 0;
                    // updateSections();

                    // console.log(currentSection);

					submitBtn.removeAttr('disabled').html('Kirim');
					return false;
				}

                // console.log(total_value);

                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method') || 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "JSON",
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: response['msg'],
                        }).then(() => {
                            $('.pengisian').html('<h1 class="text-2xl text-white text-center">Terima Kasih atas partisipasi anda</h1>')
                            // window.location.reload();
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengirim form.'
                        });
                    },
                    complete: function () {
                        // Re-enable button and restore original text
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });


        </script>
</div> @endsection
