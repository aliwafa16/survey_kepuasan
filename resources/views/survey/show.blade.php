@extends('layout.frontend') @section('content')
    @php
        $language = 'indonesian'; // atau 'english', 'malaysia' sesuai pilihan user
    @endphp

    <style>
        .swal-text-left {
            text-align: left !important;
        }
    </style>



    <style>
        /* Customize the slider track */
        input[type="range"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 100%;
            height: 8px;
            background: {{ $setting->color_secondary ?? 'grey' }};
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
            background: {{ $setting->color_primary ?? '#000165' }};
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




    <div class="container p-6 mx-auto">
        <main class="h-auto mb-10">
            <div class="container mx-auto py-8 border-spacing-2">
                <div class="flex flex-col justify-center w-full text-center pb-6">
                    <p class="text-slate-600 md:text-2xl font-extrabold">{{ Str::replace('-', ' ', env('APP_NAME_FULL')) }}</p>
                    <p class="text-slate-600 md:text-base font-normal">{{ $events->f_event_name }}</p>
                </div>
                <div
                    class="container bg-[#7ea38d]/30 mx-auto px-6 py-4 md:px-16 md:py-8 border-1 border-slate-300 rounded-[20px] pengisian">
                    @if (optional($setting)->logo)
                        <center>
                            <img src="{{ asset('storage/' . $setting->logo) }}" class="mb-5 w-[200px]" alt="">
                        </center>
                    @endif
                    <!-- Form untuk submit survey -->
                    <form id="surveyForm" action="{{ Route('survey.submit') }}" method="POST">
                        @csrf

                        <input type="hidden" id="account_id" name="account_id" value="{{ sha1($account_id) }}"
                            class="w-full bg-white border border-slate-300 px-4 py-2 rounded">
                        <input type="hidden" id="event_id" name="event_id" value="{{ $event_id }}"
                            class="w-full bg-white border border-slate-300 px-4 py-2 rounded">
                        <div id="demografi">


                            @if (isset($demografi['nip']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="gender">{{ $demografi['nip']['label'][$language] }}</label>
                                <input type="number" inputmode="numeric" pattern="[0-9]" id="nip" name="nip"
                                    value=''
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                            @endif

                            @if (isset($demografi['nama']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="gender">{{ $demografi['nama']['label'][$language] }}</label>
                                <input type="text" id="name" name="name" value=''
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                            @endif

                            @if (isset($demografi['email']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="gender">{{ $demografi['email']['label'][$language] }}</label>
                                <input type="text" id="email" name="email" value=''
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                            @endif





                            @if (isset($demografi['gender']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="gender">{{ $demografi['gender']['label'][$language] }}</label>
                                <select name="gender" id="gender"
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                                    <option value=""></option>
                                    @foreach ($demografi['gender']['value'] as $value)
                                        <option value="{{ $value['f_gender_id'] }}">{{ $value['f_gender_name'] }}
                                        </option>
                                    @endforeach
                                </select><br>
                            @endif


                            @if (isset($demografi['age']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="age">{{ $demografi['age']['label'][$language] }}</label>
                                <select name="age" id="age"
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                                    <option value=""></option>
                                    @foreach ($demografi['age']['value'] as $value)
                                        <option value="{{ $value['f_id'] }}">{{ $value['f_age_desc'] }}</option>
                                    @endforeach
                                </select><br>
                            @endif


                            @if (isset($demografi['masa_kerja']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="masa_kerja">{{ $demografi['masa_kerja']['label'][$language] }}</label>
                                <select name="masa_kerja" id="masa_kerja"
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                                    <option value=""></option>
                                    @foreach ($demografi['masa_kerja']['value'] as $value)
                                        <option value="{{ $value['f_id'] }}">{{ $value['f_service_desc'] }}</option>
                                    @endforeach
                                </select><br>
                            @endif

                            @if (isset($demografi['region']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="region">{{ $demografi['region']['label'][$language] }}</label>
                                <select name="region" id="region"
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                                    <option value=""></option>
                                    @foreach ($demografi['region']['value'] as $value)
                                        <option value="{{ $value['f_id'] }}">{{ $value['f_region_name'] }}</option>
                                    @endforeach
                                </select><br>
                            @endif

                            @if (isset($demografi['level_of_work']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="level_of_work">{{ $demografi['level_of_work']['label'][$language] }}</label>
                                <select name="level_of_work" id="level_of_work"
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                                    <option value=""></option>
                                    @foreach ($demografi['level_of_work']['value'] as $value)
                                        <option value="{{ $value['f_id'] }}">{{ $value['f_levelwork_desc'] }}</option>
                                    @endforeach
                                </select><br>
                            @endif



                            @if (isset($demografi['pendidikan']['label'][$language]))
                                <label class="text-cyan-800 mb-2 text-lg font-bold "
                                    for="pendidikan">{{ $demografi['pendidikan']['label'][$language] }}</label>
                                <select name="pendidikan" id="pendidikan"
                                    class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4"
                                    required>
                                    <option value=""></option>
                                    @foreach ($demografi['pendidikan']['value'] as $value)
                                        <option value="{{ $value['f_id'] }}">{{ $value['f_name'] }}</option>
                                    @endforeach
                                </select><br>
                            @endif

                            @foreach ($level as $key => $field)
                                @if (isset($field['label'][$language]))
                                    <div>
                                        <label class="text-cyan-800 mb-2 text-lg font-bold "
                                            for="{{ $key }}">{{ $field['label'][$language] }}</label>
                                        <select name="{{ $key }}" id="{{ $key }}"
                                            data-level="{{ $field['level'] ?? 0 }}"
                                            class="w-full bg-white border border-slate-300 px-4 py-2 rounded demografi mb-4 level"
                                            onchange="changeLevel({{ $field['level'] ?? 0 }})" required>
                                            <option value=""></option>
                                            @foreach ($field['value'] as $value)
                                                <option value="{{ $value['f_id'] }}">{{ $value['f_position_desc'] }}
                                                </option>
                                            @endforeach
                                        </select><br>
                                    </div>
                                @endif
                            @endforeach
                            <br><br>

                            <button type="button" id="mulai"
                                style="background-color: {{ $setting->color_primary ?? '#000165' }};"
                                class="text-white py-2 px-4 rounded-[20px]">
                                Mulai Survey
                            </button>
                        </div>
                        <!-- Section Wrapper -->

                        <div id="surveySections" hidden>
                            <div>
                                <!-- Progress Bar -->
                                <div class="flex justify-between mb-4">
                                    <div class="text-cyan-800 font-bold text-1xl leading-4">Progres Tes:</div>
                                    <div class="">
                                        <span id="progressPercentage" class=" text-cyan-800 font-bold text-2xl leading-4"> 0%
                                        </span>
                                    </div>
                                </div>
                                <div class="relative w-full bg-gray-200 rounded-full h-4 mb-14">
                                    <div id="progressBar" class="h-4 rounded-full"
                                        style="width: 0%; box-shadow: 0 0 15px rgba(93, 152, 255, 0.8); background-color: {{ $setting->color_primary ?? '#000165' }};">
                                    </div>
                                </div>
                            </div>
                            @foreach ($sections as $sectionIndex => $section)
                                <div class="survey-section {{ $sectionIndex === 0 ? '' : 'hidden' }}"
                                    data-index="{{ $sectionIndex }}">
                                    @php
                                        $count_soal = count($section);
                                        // echo $count_soal;
                                        $total_persentase = $count_soal - ceil($count_soal * (25 / 100));
                                    @endphp
                                    @foreach ($section as $question)
                                        <div class="mb-6 pb-4">
                                            <p class="mb-2 text-cyan-800 font-medium">
                                                {{ $question['f_item'] }}
                                            </p>

                                            @if ($question['type'] == 1)
                                                @php
                                                    $answers = json_decode($question['f_answer'], true) ?? [];
                                                @endphp

                                                <div class="space-y-2">
                                                    @foreach ($answers as $answer)
                                                        <label class="flex items-center space-x-2">
                                                            <input type="radio"
                                                                name="answers[ex{{ $question['f_id'] }}]"
                                                                value="{{ $answer['value'] }}"
                                                                class="text-blue-600 focus:ring-blue-500">
                                                            <span class="text-cyan-800">{{ $answer['label'] }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif ($question['type'] == 2)
                                                <textarea name="answers[ex{{ $question['f_id'] }}]" rows="3"
                                                    class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
                                            @endif
                                        </div>
                                    @endforeach

                                </div>
                            @endforeach

                            <div class="flex justify-center mt-2">
                                <button type="button" id="prevBtn" onclick="previous();"
                                    style="background-color: {{ $setting->color_primary ?? '#000165' }};"
                                    class="text-white py-2 px-4 rounded-[20px]">Sebelumnya</button>
                                <button type="button" id="nextBtn"
                                    style="background-color: {{ $setting->color_primary ?? '#000165' }};"
                                    class="text-white py-2 px-4 rounded-[20px]">Selanjutnya</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
<script>
// ====== Setup umum ======
$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

const sections = $('.survey-section');
const prevBtn = $('#prevBtn');
const nextBtn = $('#nextBtn');
const progressBar = $('#progressBar');
const progressPercentage = $('#progressPercentage');
let currentSection = 0;

// ====== Util ======
const escapeHtml = (str) => String(str)
  .replace(/&/g,'&amp;').replace(/</g,'&lt;')
  .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');

function scrollTopFast() { $('html, body').animate({ scrollTop: 0 }, 'fast'); }

function markError($el, isError) {
  // Tambah/kurangi ring merah di elemen input atau wrapper-nya
  if ($el.is('textarea, input')) {
    $el.toggleClass('ring-2 ring-red-500', isError);
  } else {
    $el.toggleClass('ring-2 ring-red-500 rounded-md p-2', isError);
  }
}

// ====== Validasi Demografi ======
function validateDemografiNotEmpty() {
  let valid = true;
  $('#demografi').find('input:not([type="hidden"]), select').each(function() {
    const empty = ($(this).val() === '' || $(this).val() === null);
    $(this).toggleClass('border-red-500', empty);
    if (empty) valid = false;
  });
  return valid;
}

function collectDemografiData() {
  const data = {
    email: $('#email').val()?.trim() || '',
    account_id: $('#account_id').val(),
    event_id: $('#event_id').val(),
  };
  // Ambil semua input/select demografi yang diberi class .demografi
  $('.demografi').each(function() {
    const name = $(this).attr('name');
    if (!name) return;
    const value = $(this).is('select')
      ? $(this).find('option:selected').val()
      : $(this).val();
    data[name] = value;
  });
  return data;
}

function buildDemografiPreviewHtml(extraPairs = []) {
  let html = '';
  $('.demografi').each(function() {
    const $el = $(this);
    const label = $el.prev('label').text().trim();
    const valueText = $el.is('select') ? $el.find('option:selected').text().trim() : ($el.val()||'').trim();
    if (label && valueText) {
      html += `${escapeHtml(label)}:<br><small style="font-weight:normal;">${escapeHtml(valueText)}</small><br>`;
    }
  });
  extraPairs.forEach(([label, value]) => {
    html += `${escapeHtml(label)}:<br><small style="font-weight:normal;">${escapeHtml(value)}</small><br>`;
  });
  return `<div style="text-align:left;">${html}</div>`;
}

// ====== Validasi Section (radio & textarea) ======
function validateActiveSection() {
  const $active = $('.survey-section.aktif');
  let ok = true;

  // Bersihkan error dulu
  $active.find('.ring-red-500').removeClass('ring-2 ring-red-500');

  // 1) RADIO: pastikan tiap group memiliki checked
  const radioGroups = new Set();
  $active.find('input[type="radio"]').each(function() {
    radioGroups.add($(this).attr('name'));
  });
  radioGroups.forEach((groupName) => {
    const $group = $active.find(`input[type="radio"][name="${groupName}"]`);
    const anyChecked = $group.is(':checked');
    if (!anyChecked) {
      // Tandai wrapper dari radio (pakai parent terdekat yang wajar)
      markError($group.first().closest('.space-y-2').length ? $group.first().closest('.space-y-2') : $group.first().parent(), true);
      ok = false;
    }
  });

  // 2) TEXTAREA: wajib diisi minimal 3 karakter
  $active.find('textarea').each(function() {
    const val = ($(this).val() || '').trim();
    if (val.length < 3) {
      markError($(this), true);
      ok = false;
    }
  });

  if (!ok) {
    Swal.fire({
      icon: 'error',
      title: 'Lengkapi jawaban Anda',
      text: 'Harap isi semua pertanyaan pada section ini sebelum melanjutkan.'
    });
  }
  return ok;
}

// Validasi seluruh form (semua section) saat submit
function validateAllSectionsBeforeSubmit() {
  let ok = true;

  // Bersihkan semua error
  $('.survey-section').find('.ring-red-500').removeClass('ring-2 ring-red-500');

  // RADIO
  const allRadioGroups = new Set();
  $('input[type="radio"]').each(function() { allRadioGroups.add($(this).attr('name')); });
  allRadioGroups.forEach((groupName) => {
    const $group = $(`input[type="radio"][name="${groupName}"]`);
    const anyChecked = $group.is(':checked');
    if (!anyChecked) {
      markError($group.first().closest('.space-y-2').length ? $group.first().closest('.space-y-2') : $group.first().parent(), true);
      ok = false;
    }
  });

  // TEXTAREA
  $('textarea').each(function() {
    const val = ($(this).val() || '').trim();
    if (val.length < 3) {
      markError($(this), true);
      ok = false;
    }
  });

  if (!ok) {
    Swal.fire({
      icon: 'error',
      title: 'Jawaban belum lengkap',
      text: 'Masih ada pertanyaan yang belum diisi. Silakan lengkapi semua jawaban.'
    });
  }
  return ok;
}

// ====== Navigasi section ======
function updateSections() {
  sections.each(function(index) {
    $(this).toggleClass('hidden', index !== currentSection)
           .toggleClass('aktif', index === currentSection);
  });

  const progress = ((currentSection + 1) / sections.length) * 100;
  progressBar.css('width', progress + '%').attr('aria-valuenow', Math.round(progress));
  progressPercentage.text(`${currentSection + 1} / ${sections.length}`);

  prevBtn.prop('hidden', currentSection === 0);

  nextBtn.off('click');
  if (currentSection === sections.length - 1) {
    nextBtn.text('Kirim').attr('type', 'submit');
  } else {
    nextBtn.text('Selanjutnya').attr('type', 'button').on('click', function(e) {
      e.preventDefault();
      if (!validateActiveSection()) return;
      currentSection++;
      updateSections();
      scrollTopFast();
    });
  }
}

function previous() {
  if (currentSection > 0) {
    currentSection--;
    updateSections();
    scrollTopFast();
  }
}
window.previous = previous; // dipakai di onclick

// ====== changeLevel (jika dipakai) ======
function changeLevel(level) {
  const selector = '#'+($('#level_of_work').length ? 'level_of_work' : ('label_level' + level)); // fallback
  const id = $(selector).val();
  const nextSelector = '#label_level' + (level + 1);

  $(nextSelector).prop('disabled', true).html('<option>Loading...</option>');

  $.ajax({
    url: "{{ Route('survey.getLevel') }}",
    method: "POST",
    data: { level, id },
    dataType: "JSON",
    success: function(response) {
      let html = `<option value=""></option>`;
      $.each(response, function(_, v) {
        html += `<option value="${v['f_id']}">${escapeHtml(v['f_position_desc'])}</option>`;
      });
      $(nextSelector).html(html).prop('disabled', false);
    },
    error: function() {
      $(nextSelector).html('<option value="">Gagal memuat</option>').prop('disabled', false);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memuat level berikutnya.' });
    }
  });
}
window.changeLevel = changeLevel;

// ====== Mulai & Cek survey ======
$('#mulai').on('click', function() {
  if (!validateDemografiNotEmpty()) {
    Swal.fire({ title: "Semua field demografi harus diisi!", icon: "error", confirmButtonColor: "#3085d6", confirmButtonText: "OK" });
    return;
  }

  const data = collectDemografiData();
  if (!data.email) {
    Swal.fire({ title: "Email wajib diisi!", icon: "error", confirmButtonColor: "#3085d6", confirmButtonText: "OK" })
      .then(() => window.location.reload());
    return;
  }

  $.ajax({
    url: "{{ Route('survey.check') }}",
    method: "POST",
    data,
    dataType: "JSON",
    success: function(response) {
      if (response.survey_valid) {
        Swal.fire({ title: response.msg, icon: "info", confirmButtonColor: "#3085d6", confirmButtonText: "OK" })
          .then(() => $('.pengisian').html('<h1 class="text-2xl text-cyan-800 text-center">Terima Kasih atas partisipasi anda</h1>'));
        return;
      }

      const extra = [];
      if (response.from_nip) {
        (response.data_nip.label || []).forEach((label, i) => {
          extra.push([label, response.data_nip.value?.[i] ?? '']);
        });
      }

      const preview = buildDemografiPreviewHtml(extra);
      Swal.fire({
        title: '<center>Apakah benar data berikut?</center>',
        html: `${preview}<br><span>Data demografi tidak dapat diubah setelah submit dan survey hanya bisa dilakukan 1x</span><br><span class="text-red-500 text-sm">${escapeHtml(response.msg || '')}</span>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, benar"
      }).then((confirm) => {
        if (confirm.isConfirmed) {
          $('#demografi').attr('hidden', true);
          $('#surveySections').attr('hidden', false);
          scrollTopFast();
        }
      });
    },
    error: function() {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memeriksa status survey.' });
    }
  });
});

// ====== Submit form ======
$('#surveyForm').on('submit', function(e) {
  e.preventDefault();

  // Validasi section aktif (supaya feedbacknya terlihat di layar terakhir)
  if (!validateActiveSection()) return;

  // Validasi seluruh jawaban
  if (!validateAllSectionsBeforeSubmit()) return;

  const form = this;
  const formData = new FormData(form);
  const submitBtn = $(form).find('button[type="submit"]');
  const originalBtnText = submitBtn.html();

  submitBtn.prop('disabled', true).html('Loading...');

  $.ajax({
    url: $(form).attr('action'),
    type: $(form).attr('method') || 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: "JSON",
    success: function(response) {
      if (response.status === 'success') {
        Swal.fire({ icon: 'success', title: response.msg })
          .then(() => {
            $('.pengisian').html('<h1 class="text-2xl text-white text-center">Terima Kasih atas partisipasi anda</h1>');
          });
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: response.msg || 'Terjadi kesalahan.' });
      }
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat mengirim form.' });
    },
    complete: function() {
      submitBtn.prop('disabled', false).html(originalBtnText);
    }
  });
});

// ====== Init ======
updateSections();
</script>


        @php
            $welcome = json_decode($surveySetting->f_page_welcome, true);
            $howto = json_decode($surveySetting->f_page_howto, true);
        @endphp

        <script>
            @if (!empty($welcome['title']))
                const welcomeTitle = {!! json_encode($welcome['title']) !!};
                const welcomeContent = `{!! $welcome['content'] !!}`;
            @endif

            @if (!empty($howto['title']))
                const howtoTitle = {!! json_encode($howto['title']) !!};
                const howtoContent = `{!! $howto['content'] !!}`;
            @endif

            function showWelcomeAndHowTo() {
                @if (!empty($welcome['title']))
                    Swal.fire({
                        confirmButtonText: 'Mulai',
                        title: welcomeTitle,
                        html: welcomeContent,
                        customClass: {
                            title: 'swal-text-left',
                            htmlContainer: 'swal-text-left'
                        }
                    }).then(() => {
                        @if (!empty($howto['title']))
                            Swal.fire({
                                title: howtoTitle,
                                html: howtoContent,
                                customClass: {
                                    title: 'swal-text-left',
                                    htmlContainer: 'swal-text-left'
                                }
                            });
                        @endif
                    });
                @else
                    @if (!empty($howto['title']))
                        Swal.fire({
                            title: howtoTitle,
                            html: howtoContent
                        });
                    @endif
                @endif
            }

            // Panggil saat halaman dimuat
            showWelcomeAndHowTo();
        </script>

    </div>
@endsection
