<?php
use App\Models\Variabel;
use App\Models\Dimensi;
use App\Helpers\Pdf;

use Illuminate\Support\Facades\DB;

if (!function_exists('plugin_asset')) {
    /**
     * Generate a URL for assets stored in the plugins directory.
     *
     * @param string $path
     * @return string
     */
    function plugin_asset($path)
    {
        // Pastikan path dimulai dengan "/"
        $path = ltrim($path, '/');

        // Misalnya, asumsikan plugin disimpan di public/plugins
        return url("plugins/{$path}");
    }

}

function createPDF10($row,$mode = 'I')
{
	$nama_file = 'Result_'.str_replace(" ","_",$row['f_survey_username']).'-'.str_replace(" ","_",$row['f_email']).'.pdf';
    $cekbhs = get_bahasa($row['f_bahasa']);

    $kata2 = file_get_contents(public_path('language/'.$row['f_bahasa'].'/report.json'));
	$kata2 = json_decode($kata2, true);
	$tgl_survey = $kata2['tanggal_survey'];

	$tgl_selesai = tgl_indo1($row['tgl_selesai']);

    $vcolor = array(
        1 => array( 'rgb1' => 124, 'rgb2' => 125, 'rgb3' => 255, 'color' => '#7c7dff', 'nama' => 'drive', ),
        2 => array( 'rgb1' => 255, 'rgb2' =>  132, 'rgb3' => 0, 'color' => '#ff8400', 'nama' => 'network', ),
        3 => array( 'rgb1' => 221, 'rgb2' => 62, 'rgb3' => 58, 'color' => '#dd3e3a', 'nama' => 'action', ),
    );

    // Cek Variabel
    $variabels = Variabel::select('a.f_id', 'a.f_variabel_name', 'c.f_bahasa')
        ->from('t_variabel as a')
        ->join('variabel_bahasa as c', 'c.f_id', '=', 'a.f_id')
        ->where('c.f_negara', $row['f_bahasa'])
        ->get();

    $detail_variabel = [];
    foreach ($variabels as $r) {
        $d = json_decode($r->f_bahasa, true);
        foreach ($d as $e => $f) {
            $detail_variabel[$r->f_id][$e] = $f;
        }
    }

    // Cek Variabel Dimensi
    $dimensies = Dimensi::select('a.f_id', 'a.f_dimensi_name', 'a.f_name_indo', 'a.f_variabel_id', 'b.f_variabel_name', 'c.f_bahasa')
        ->from('t_dimensi as a')
        ->join('t_variabel as b', 'b.f_id', '=', 'a.f_variabel_id')
        ->join('dimensi_bahasa as c', 'c.f_id', '=', 'a.f_id')
        ->where('c.f_negara', $row['f_bahasa'])
        ->get();

    $detail_dimensi = [];
    $combo_dimensi = [];
    $color_dimensi = []; // Pastikan $vcolor didefinisikan sebelumnya
    foreach ($dimensies as $r) {
        $detail_dimensi[$r->f_id] = $r;
        $combo_dimensi[$r->f_id] = $r->f_dimensi_name;
        $color_dimensi[$r->f_id] = $vcolor[$r->f_variabel_id]; // Pastikan $vcolor didefinisikan sebelumnya
    }

    $total_dimensi = $row['total_dimensi'];
    $top_ten = $row['topten'];
    $topten = [];
    foreach ($top_ten as $c) {
        $topten[$c['id']] = $c['nama'];
    }

    $rata_dimensi = [];
    foreach ($row['total_dimensi'] as $k => $v) {
        $total_soal = $row['soal_perdimensi'][$k];
        $hitung = round($v / $total_soal, 2);
        $name_indo = ucwords(strtolower($detail_dimensi[$k]['f_name_indo']));
        $rata_dimensi[] = [
            'id' => $k,
            'nama' => $combo_dimensi[$k],
            //'hc' => $combo_hc[$k], // Pastikan $combo_hc didefinisikan sebelumnya
            'total' => $hitung,
            'color' => $color_dimensi[$k],
            'nama_lain' => $name_indo,
        ];
    }

    // Mengurutkan berdasarkan total
    usort($rata_dimensi, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    $output = array_slice($rata_dimensi, 0, 10);
    $topten_id = [];
    foreach ($output as $r) {
        $topten_id[] = $r['id'];
    }

    $total_akhir = $output[9]['total'];
    foreach ($rata_dimensi as $r) {
        if ($r['total'] == $total_akhir && !in_array($r['id'], $topten_id)) {
            array_push($output, $r);
        }
    }

    if (count($output) > 10) {
        $temp_bukan_akhir = [];
        $temp_akhir = [];
        foreach ($output as $r) {
            if ($r['total'] == $total_akhir) {
                $temp_akhir[] = $r;
            } else {
                $temp_bukan_akhir[] = $r;
            }
        }
        $output = $temp_bukan_akhir;
        $selisih = 10 - count($temp_bukan_akhir);

        // Mengurutkan temp_akhir berdasarkan nama
        usort($temp_akhir, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        foreach ($temp_akhir as $r) {
            array_push($output, $r);
        }
    }

    // CEK ID OUTPUT SEKARANG
    $combo_id_output = [];
    foreach ($output as $r) {
        $combo_id_output[] = $r['id'];
    }

    // CEK YANG TIDAK MASUK KE OUTPUT UNTUK DI URUTKAN
    $temp_bukan_output = [];
    foreach ($rata_dimensi as $r) {
        if (!in_array($r['id'], $combo_id_output)) {
            $temp_bukan_output[] = $r;
        }
    }

    // Mengurutkan temp_bukan_output berdasarkan total
    usort($temp_bukan_output, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    foreach ($temp_bukan_output as $r) {
        array_push($output, $r);
    }

    $output_talent = $output;

    // $pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false);
    $pdf = new Pdf('P', 'mm', 'A5', true, 'UTF-8', false);
	$pdf->SetCreator('Copyright © 2023 ESQ. All Rights Reserved');
	$pdf->SetAuthor('ESQ');
	$pdf->SetTitle('TalentDNA® '.$row['f_survey_username']);
	$pdf->SetSubject('TalentDNA® by ESQ');

    $margin = 17.5;
	$color_text = '#FFF';
	$row['color_text'] = $color_text;
	$row['tgl_survey'] = $tgl_survey;
	$fcolor1 = '#000';
	$pdf->SetHeaderMargin($margin);
	$pdf->SetFooterMargin($margin);
	$pdf->SetAutoPageBreak(TRUE, $margin);

	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

    // COVER
	$pdf->AddPage();

	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/10/COVER.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();


	$pdf->SetFont('roboto','',20);
	$report_type = $kata2['5report'];
	if($row['report_type'] == 10) $report_type = $kata2['10report'];
	if($row['report_type'] == 45 || $row['report_type'] == 65) $report_type = $kata2['45report'];
	$row['name_report_type'] = $report_type;
	//$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$report_type.'</p>';
	//$pdf->writeHTMLCell(148, 0, 0, 45, $html, 0, 0, 0, true, 'C', true);

	$pdf->SetY(131);
	//$html = '<p style="font-size:18pt;font-weight:700;background: linear-gradient(45deg, #11a8ff, #FF00FF, #9e5aff);-webkit-background-clip: text;color: transparent;">'.$row['f_survey_username'].'</p>';
	$pdf->SetFont('robotob', 'B', 10);
	$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$row['f_survey_username'].'</p>';
	$pdf->writeHTMLCell(105, 0, 30, '', $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('robotomedium', '', 10);
	$pdf->SetY(141);
	$html = '<p style="font-size:12pt;font-weight:700;color:'.$color_text.';">'.$kata2['tanggal_survey'].': '.$tgl_selesai.'</p>';
	$pdf->writeHTMLCell(125, 0, 35, '', $html, 0, 0, 0, true, 'L', true);

	// PAGE 1
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_01_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$pdf->SetFont('roboto', '', 10);
	$html = '<p style="font-size:14pt;color:#1717b6;">'.$kata2['page2_karir1'].'</p>';
	$pdf->writeHTMLCell(145, 0, 40, 40, $html, 0, 0, 0, true, 'L', true);

	$pdf->SetY(52.5);
	$nama = explode(" ",$row['f_survey_username']);
	//echo count($nama);
	// dd($nama);
    // die();
	if(count($nama) == 1){ $nama = $nama[0];}
	else if(count($nama) == 2) { $nama = $nama[0].'<br>'.$nama[1]; }
	else if(count($nama) == 3) { $nama = $nama[0].' '.$nama[1].'<br>'.$nama[2]; }
	else if(count($nama) == 4) {
		$nama_belakang = '';
		for($i=2;$i<count($nama);$i++) {
			$nama_belakang .= $nama[$i].' ';
		}
		$nama = $nama[0].' '.$nama[1].'<br>';
		$nama .= trim($nama_belakang," ");
	}
	else if(count($nama) > 4) {
		$nama_belakang = '';
		for($i=3;$i<count($nama);$i++) {
			$nama_belakang .= $nama[$i].' ';
		}
		$nama = $nama[0].' '.$nama[1].' '.$nama[2].'<br>';
		$nama .= trim($nama_belakang," ");
	}
	//echo $nama;die();

	$pdf->SetFont('robotob', 'B', 10);
	$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$nama.'</p>';
	$pdf->writeHTMLCell(145, 0, 40, '', $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('roboto', '', 10);
	$html = $kata2['page1_10'];
	$pdf->writeHTMLCell(145, 0, 40, 80, $html, 0, 0, 0, true, 'L', true);

	// PAGE 2
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_02_KOSONG-02.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['page2_pk'];
	$pdf->SetXY(27,12);
	$pdf->SetFont('roboto', '', 10);
	$pdf->SetTextColor(255,255,255);
	$pdf->writeHTMLCell(145, 0, 13, 12.5, $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('roboto','',8);
	$pdf->SetTextColor(0,0,0);
	$html =$kata2['page2_pk1'];
	$pdf->writeHTMLCell(105, 0, 24, 23.5, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk2'];
	$pdf->writeHTMLCell(105, 0, 24, 41.5, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk3'];
	$pdf->writeHTMLCell(105, 0, 24, 57, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk4'];
	$pdf->writeHTMLCell(105, 0, 24, 73, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk5'];
	$pdf->writeHTMLCell(105, 0, 24, 92.5, $html, 0, 0, 0, true, 'J', true);

	$html =$kata2['page2_bt'];
	$pdf->writeHTMLCell(145, 0, 15, 117.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt1_old'];
	$pdf->writeHTMLCell(145, 0, 15, 127, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_phone_old'];
	$pdf->writeHTMLCell(145, 0, 72, 140, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt2'];
	$pdf->writeHTMLCell(145, 0, 15, 155, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt3'];
	$pdf->writeHTMLCell(145, 0, 15, 165, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt4'];
	$pdf->writeHTMLCell(145, 0, 20, 170.6, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt5'];
	$pdf->writeHTMLCell(145, 0, 15, 179, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_in'];
	$pdf->writeHTMLCell(145, 0, 19, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_ig'];
	$pdf->writeHTMLCell(145, 0, 43, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_yt'];
	$pdf->writeHTMLCell(145, 0, 69.5, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_tt'];
	$pdf->writeHTMLCell(145, 0, 96, 185.5, $html, 0, 0, 0, true, 'L', true);


	// PAGE 2-3
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 5);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_03P.png');
	//if($row['f_bahasa'] == 'id-ID') $img_file = FCPATH.'assets/images/v3/PAGE_03P_id.png';
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	//$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	$pdf->SetAutoPageBreak(true, 5);
	// set the starting point for the page content
	$pdf->setPageMark();
	$html =$kata2['text_3N_judul1'];
	$pdf->SetXY(27,12);
	$pdf->SetTextColor(25, 27, 189);
	$pdf->writeHTMLCell(135, 0, 12, 100, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_judul2'];
	$pdf->writeHTMLCell(135, 0, 12, 120, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['text_3N_judul3'];
	$pdf->writeHTMLCell(135, 0, 12, 136, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text31'];
	$pdf->writeHTMLCell(100, 0, 19, 147.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text32'];
	$pdf->writeHTMLCell(100, 0, 19, 155, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text33'];
	$pdf->writeHTMLCell(100, 0, 19, 158.75, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text34'];
	$pdf->writeHTMLCell(100, 0, 19, 162.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text35'];
	$pdf->writeHTMLCell(100, 0, 19, 166.35, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text35_1'];
	$pdf->writeHTMLCell(100, 0, 19, 170.35, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text36'];
	$pdf->writeHTMLCell(100, 0, 19, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text37'];
	$pdf->writeHTMLCell(100, 0, 19, 189.25, $html, 0, 0, 0, true, 'L', true);

	// PAGE 3
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_TIPE_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['npage3'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',16);
	$pdf->SetTextColor(255,255,255);
	$pdf->writeHTMLCell(145, 0, 12, 11, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['npage30'];
	$pdf->SetFont('roboto','',9);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 12, 22, $html, 0, 0, 0, true, 'L', true);

	// DRIVE
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[1]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=30, $h=0, $x=15, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="line-height:1.5;font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[1]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=15, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

	// NETWORK
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[2]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=30, $h=0, $x=56, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[2]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=56, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

	// ACTION
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[3]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=35, $h=0, $x=99, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="line-height:1.25;font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[3]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=99, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

    // PAGE 4
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/10/PAGE_04_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$pdf->StartTransform();
	// set clipping mask
	$pdf->StarPolygon(75.5, 50.85, 15.5, 50, 3, 0, 0, 'CNZ');
	$img_file = public_path('assets/images/v3/'.$row['image_profil']);
	$pdf->Image($img_file, 54.5, 29.85, 42, 42, '', 'URL', '', true, 300);
	$pdf->StopTransform();

	$pdf->SetFont('roboto', 'B', 7.25);
	$i = 0; $no = 1;
	// #1
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 45, 12.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #2
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 27.5, 20, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #3
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 21.5, 28, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #4
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 17.25, 36.25, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #5
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 14.35, 44.25, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;

	// #6
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 14.35, 52.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #7
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 17.25, 60.75, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #8
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 21.5, 70.75, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #9
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 27.5, 79.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #10
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 45, 87.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;

	for($i=0;$i<10;$i++) {
		$colortext= $output[$i]['color']['color'];
		//$bgtext= ($no < 11) ? '#ECECFF':'#FFF';
		$html .= '<p style="bottom:-5px;font-weight:700;font-size:11pt;color:'.$colortext.'"><b>'.$no.' '.$output[$i]['nama'].'</b></p>';
		$bahas[$i] = json_decode($detail_dimensi[$output[$i]['id']]['f_bahasa'],true);
		$no++;
	}

	$html =$kata2['penjelasan_top'];
	$pdf->SetFont('roboto','',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(58, 0, 12.5, 104, $html, 0, 0, 0, true, 'C', true);

	$html =$kata2['penjelasan_bottom'];
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(58, 0, 75, 104, $html, 0, 0, 0, true, 'C', true);

	$find=array('{BAHAS_1}','{BAHAS_2}','{BAHAS_3}','{BAHAS_4}','{BAHAS_5}','{BAHAS_6}','{BAHAS_7}','{BAHAS_8}','{BAHAS_9}','{BAHAS_10}');
	$replace = array(str_replace('.','',$bahas[0]['deskripsi_paragraf']),str_replace('.','',$bahas[1]['deskripsi_paragraf']),str_replace('.','',$bahas[2]['deskripsi_paragraf']),str_replace('.','',$bahas[3]['deskripsi_paragraf']),str_replace('.','',$bahas[4]['deskripsi_paragraf']),str_replace('.','',$bahas[5]['deskripsi_paragraf']),str_replace('.','',$bahas[6]['deskripsi_paragraf']),str_replace('.','',$bahas[7]['deskripsi_paragraf']),str_replace('.','',$bahas[8]['deskripsi_paragraf']),str_replace('.','',$bahas[9]['deskripsi_paragraf']));
	$gmbr_text = str_replace($find,$replace,$kata2['gambar_diri10']);

	$fontsize = 7.5;
	if(strlen($gmbr_text) > 815) $fontsize = 7.25;
	$pdf->SetFont('roboto','',$fontsize);
    $pdf->writeHTMLCell($w=65, $h=0, $x=7.5, $y=120, $gmbr_text, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);

	// PAGE 5
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PENJELASAN.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['penjelasan1'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 20, 48, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['penjelasantop10'];
	$pdf->writeHTMLCell(145, 0, 22, 87, $html, 0, 0, 0, true, 'L', true);

	// PENJELASAN TOP TALENT
	$pdf->AddPage();

	$no = 1;$y= 8;$y1= 9;$y2= 22.5;;$y3= 30;
	for($i=0;$i<10;$i++) {
		$pdf->SetFont('roboto','B',10);
		$type_talent = $output_talent[$i]['color']['nama'];
		$bahas = json_decode($detail_dimensi[$output_talent[$i]['id']]['f_bahasa'],true);
		$img_file = public_path('assets/images/v3/bar/'.strtolower($type_talent).'.png');
		$pdf->Image($img_file, 0, $y, 150, 12, '', 'URL', '', true, 300);
		$judul_talent = ucwords(strtolower($output_talent[$i]['nama']));
		if($row['f_bahasa'] == 'id-ID')
		{
			$spasi = '&nbsp;&nbsp;&nbsp;&nbsp;';
			if($no > 9) $spasi .= '&nbsp;&nbsp;&nbsp;';
			$judul_talent .= '<br>'.$spasi.'<span style="margin-top:-5px;font-size:12pt;">('.$output_talent[$i]['nama_lain'].')</span>';
			$html = '<p style="font-size:14pt;font-weight:700;color:#FFF;line-height:0.9;">'.$no.'. '.$judul_talent.'</p>';
		} else {
			$html = '<p style="font-size:20pt;font-weight:700;color:#FFF;">'.$no.'. '.$judul_talent.'</p>';
		}
		$pdf->writeHTMLCell(135, 10, 10, $y1, $html, 0, 0, 0, true, 'L', true);


		$pdf->SetFont('roboto','',8.65);
		$html = '<p style="text-align:justify;line-height:1;">'.$bahas['deskripsi'].'</p>';
		$pdf->writeHTMLCell(75, 10, 10, $y2, $html, 0, 0, 0, true, 'L', true);
		$pdf->SetFont('roboto','B',10);
		$html = '<p style="font-size:12pt;text-align:justify"><strong>'.$kata2['text_3_1'].'</strong></p>';
		$pdf->writeHTMLCell(75, 10, 87.5, $y2, $html, 0, 0, 0, true, 'L', true);
		$html = '<ul style="font-size:8.65pt;text-align:justify">';
		foreach($bahas['keterangan_kuat'] as $c) {
			$html .= '<li>'.$c.'</li>';
		}
		$html .= '</ul>';
		$pdf->SetFont('roboto','',9);
		$pdf->writeHTMLCell(62.5, 10, 80, $y3, $html, 0, 0, 0, true, 'L', true);

		$y+=100;$y1+=100;$y2+=100;$y3+=100;
		if($no%2 ==0) {
			if($no < 10) {
				$pdf->AddPage();
				$y= 8;$y1= 9;$y2= 22.5;$y3= 30;
			}
		}
		$no++;
	}

	// PAGE 10
	$pdf->PageOver($row['f_bahasa']);

    $publicPath = public_path('assets/pdf/' . $nama_file);
    $pdf->Output($publicPath,$mode);
    chmod($publicPath,0777);
}
function createPDF45($row,$mode = 'I')
{
	$nama_file = 'Result_'.str_replace(" ","_",$row['f_survey_username']).'-'.str_replace(" ","_",$row['f_email']).'.pdf';
    $cekbhs = get_bahasa($row['f_bahasa']);

    $kata2 = file_get_contents(public_path('language/'.$row['f_bahasa'].'/report.json'));
	$kata2 = json_decode($kata2, true);
	$tgl_survey = $kata2['tanggal_survey'];

	$tgl_selesai = tgl_indo1($row['tgl_selesai']);

    $vcolor = array(
        1 => array( 'rgb1' => 124, 'rgb2' => 125, 'rgb3' => 255, 'color' => '#7c7dff', 'nama' => 'drive', ),
        2 => array( 'rgb1' => 255, 'rgb2' =>  132, 'rgb3' => 0, 'color' => '#ff8400', 'nama' => 'network', ),
        3 => array( 'rgb1' => 221, 'rgb2' => 62, 'rgb3' => 58, 'color' => '#dd3e3a', 'nama' => 'action', ),
    );

    // Cek Variabel
    $variabels = Variabel::select('a.f_id', 'a.f_variabel_name', 'c.f_bahasa')
        ->from('t_variabel as a')
        ->join('variabel_bahasa as c', 'c.f_id', '=', 'a.f_id')
        ->where('c.f_negara', $row['f_bahasa'])
        ->get();

    $detail_variabel = [];
    foreach ($variabels as $r) {
        $d = json_decode($r->f_bahasa, true);
        foreach ($d as $e => $f) {
            $detail_variabel[$r->f_id][$e] = $f;
        }
    }

    // Cek Variabel Dimensi
    $dimensies = Dimensi::select('a.f_id', 'a.f_dimensi_name', 'a.f_name_indo', 'a.f_variabel_id', 'b.f_variabel_name', 'c.f_bahasa')
        ->from('t_dimensi as a')
        ->join('t_variabel as b', 'b.f_id', '=', 'a.f_variabel_id')
        ->join('dimensi_bahasa as c', 'c.f_id', '=', 'a.f_id')
        ->where('c.f_negara', $row['f_bahasa'])
        ->get();

    $detail_dimensi = [];
    $combo_dimensi = [];
    $color_dimensi = []; // Pastikan $vcolor didefinisikan sebelumnya
    foreach ($dimensies as $r) {
        $detail_dimensi[$r->f_id] = $r;
        $combo_dimensi[$r->f_id] = $r->f_dimensi_name;
        $color_dimensi[$r->f_id] = $vcolor[$r->f_variabel_id]; // Pastikan $vcolor didefinisikan sebelumnya
    }

    $total_dimensi = $row['total_dimensi'];
    $top_ten = $row['topten'];
    $topten = [];
    foreach ($top_ten as $c) {
        $topten[$c['id']] = $c['nama'];
    }

    $rata_dimensi = [];
    foreach ($row['total_dimensi'] as $k => $v) {
        $total_soal = $row['soal_perdimensi'][$k];
        $hitung = round($v / $total_soal, 2);
        $name_indo = ucwords(strtolower($detail_dimensi[$k]['f_name_indo']));
        $rata_dimensi[] = [
            'id' => $k,
            'nama' => $combo_dimensi[$k],
            //'hc' => $combo_hc[$k], // Pastikan $combo_hc didefinisikan sebelumnya
            'total' => $hitung,
            'color' => $color_dimensi[$k],
            'nama_lain' => $name_indo,
        ];
    }

    // Mengurutkan berdasarkan total
    usort($rata_dimensi, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    $output = array_slice($rata_dimensi, 0, 10);
    $topten_id = [];
    foreach ($output as $r) {
        $topten_id[] = $r['id'];
    }

    $total_akhir = $output[9]['total'];
    foreach ($rata_dimensi as $r) {
        if ($r['total'] == $total_akhir && !in_array($r['id'], $topten_id)) {
            array_push($output, $r);
        }
    }

    if (count($output) > 10) {
        $temp_bukan_akhir = [];
        $temp_akhir = [];
        foreach ($output as $r) {
            if ($r['total'] == $total_akhir) {
                $temp_akhir[] = $r;
            } else {
                $temp_bukan_akhir[] = $r;
            }
        }
        $output = $temp_bukan_akhir;
        $selisih = 10 - count($temp_bukan_akhir);

        // Mengurutkan temp_akhir berdasarkan nama
        usort($temp_akhir, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        foreach ($temp_akhir as $r) {
            array_push($output, $r);
        }
    }

    // CEK ID OUTPUT SEKARANG
    $combo_id_output = [];
    foreach ($output as $r) {
        $combo_id_output[] = $r['id'];
    }

    // CEK YANG TIDAK MASUK KE OUTPUT UNTUK DI URUTKAN
    $temp_bukan_output = [];
    foreach ($rata_dimensi as $r) {
        if (!in_array($r['id'], $combo_id_output)) {
            $temp_bukan_output[] = $r;
        }
    }

    // Mengurutkan temp_bukan_output berdasarkan total
    usort($temp_bukan_output, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    foreach ($temp_bukan_output as $r) {
        array_push($output, $r);
    }

    $output_talent = $output;

    // $pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false);
    $pdf = new Pdf('P', 'mm', 'A5', true, 'UTF-8', false);
	$pdf->SetCreator('Copyright © 2023 ESQ. All Rights Reserved');
	$pdf->SetAuthor('ESQ');
	$pdf->SetTitle('TalentDNA® '.$row['f_survey_username']);
	$pdf->SetSubject('TalentDNA® by ESQ');

    $margin = 17.5;
	$color_text = '#FFF';
	$row['color_text'] = $color_text;
	$row['tgl_survey'] = $tgl_survey;
	$fcolor1 = '#000';
	$pdf->SetHeaderMargin($margin);
	$pdf->SetFooterMargin($margin);
	$pdf->SetAutoPageBreak(TRUE, $margin);

	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

    // COVER
	$pdf->AddPage();

	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/45/COVER.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();


	$pdf->SetFont('roboto','',20);
	$report_type = $kata2['5report'];
	if($row['report_type'] == 10) $report_type = $kata2['10report'];
	if($row['report_type'] == 45 || $row['report_type'] == 65) $report_type = $kata2['45report'];
	$row['name_report_type'] = $report_type;
	//$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$report_type.'</p>';
	//$pdf->writeHTMLCell(148, 0, 0, 45, $html, 0, 0, 0, true, 'C', true);

	$pdf->SetY(131);
	//$html = '<p style="font-size:18pt;font-weight:700;background: linear-gradient(45deg, #11a8ff, #FF00FF, #9e5aff);-webkit-background-clip: text;color: transparent;">'.$row['f_survey_username'].'</p>';
	$pdf->SetFont('robotob', 'B', 10);
	$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$row['f_survey_username'].'</p>';
	$pdf->writeHTMLCell(105, 0, 30, '', $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('robotomedium', '', 10);
	$pdf->SetY(141);
	$html = '<p style="font-size:12pt;font-weight:700;color:'.$color_text.';">'.$kata2['tanggal_survey'].': '.$tgl_selesai.'</p>';
	$pdf->writeHTMLCell(125, 0, 35, '', $html, 0, 0, 0, true, 'L', true);

	// PAGE 1
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_01_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$pdf->SetFont('roboto', '', 10);
	$html = '<p style="font-size:14pt;color:#1717b6;">'.$kata2['page2_karir1'].'</p>';
	$pdf->writeHTMLCell(145, 0, 40, 40, $html, 0, 0, 0, true, 'L', true);

	$pdf->SetY(52.5);
	$nama = explode(" ",$row['f_survey_username']);
	//echo count($nama);
	//opn($nama);
	if(count($nama) == 1){ $nama = $nama[0];}
	else if(count($nama) == 2) { $nama = $nama[0].'<br>'.$nama[1]; }
	else if(count($nama) == 3) { $nama = $nama[0].' '.$nama[1].'<br>'.$nama[2]; }
	else if(count($nama) == 4) {
		$nama_belakang = '';
		for($i=2;$i<count($nama);$i++) {
			$nama_belakang .= $nama[$i].' ';
		}
		$nama = $nama[0].' '.$nama[1].'<br>';
		$nama .= trim($nama_belakang," ");
	}
	else if(count($nama) > 4) {
		$nama_belakang = '';
		for($i=3;$i<count($nama);$i++) {
			$nama_belakang .= $nama[$i].' ';
		}
		$nama = $nama[0].' '.$nama[1].' '.$nama[2].'<br>';
		$nama .= trim($nama_belakang," ");
	}
	//echo $nama;die();

	$pdf->SetFont('robotob', 'B', 10);
	$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$nama.'</p>';
	$pdf->writeHTMLCell(145, 0, 40, '', $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('roboto', '', 10);
	$html = $kata2['page1_45'];
	$pdf->writeHTMLCell(145, 0, 40, 80, $html, 0, 0, 0, true, 'L', true);

	// PAGE 2
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_02_KOSONG-02.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['page2_pk'];
	$pdf->SetXY(27,12);
	$pdf->SetFont('roboto', '', 10);
	$pdf->SetTextColor(255,255,255);
	$pdf->writeHTMLCell(145, 0, 13, 12.5, $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('roboto','',8);
	$pdf->SetTextColor(0,0,0);
	$html =$kata2['page2_pk1'];
	$pdf->writeHTMLCell(105, 0, 24, 23.5, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk2'];
	$pdf->writeHTMLCell(105, 0, 24, 41.5, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk3'];
	$pdf->writeHTMLCell(105, 0, 24, 57, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk4'];
	$pdf->writeHTMLCell(105, 0, 24, 73, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk5'];
	$pdf->writeHTMLCell(105, 0, 24, 92.5, $html, 0, 0, 0, true, 'J', true);

	$html =$kata2['page2_bt'];
	$pdf->writeHTMLCell(145, 0, 15, 117.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt1_old'];
	$pdf->writeHTMLCell(145, 0, 15, 127, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_phone_old'];
	$pdf->writeHTMLCell(145, 0, 72, 140, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt2'];
	$pdf->writeHTMLCell(145, 0, 15, 155, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt3'];
	$pdf->writeHTMLCell(145, 0, 15, 165, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt4'];
	$pdf->writeHTMLCell(145, 0, 20, 170.6, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt5'];
	$pdf->writeHTMLCell(145, 0, 15, 179, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_in'];
	$pdf->writeHTMLCell(145, 0, 19, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_ig'];
	$pdf->writeHTMLCell(145, 0, 43, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_yt'];
	$pdf->writeHTMLCell(145, 0, 69.5, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_tt'];
	$pdf->writeHTMLCell(145, 0, 96, 185.5, $html, 0, 0, 0, true, 'L', true);


	// PAGE 2-3
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 5);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_03P.png');
	//if($row['f_bahasa'] == 'id-ID') $img_file = FCPATH.'assets/images/v3/PAGE_03P_id.png';
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	//$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	$pdf->SetAutoPageBreak(true, 5);
	// set the starting point for the page content
	$pdf->setPageMark();
	$html =$kata2['text_3N_judul1'];
	$pdf->SetXY(27,12);
	$pdf->SetTextColor(25, 27, 189);
	$pdf->writeHTMLCell(135, 0, 12, 100, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_judul2'];
	$pdf->writeHTMLCell(135, 0, 12, 120, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['text_3N_judul3'];
	$pdf->writeHTMLCell(135, 0, 12, 136, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text31'];
	$pdf->writeHTMLCell(100, 0, 19, 147.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text32'];
	$pdf->writeHTMLCell(100, 0, 19, 155, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text33'];
	$pdf->writeHTMLCell(100, 0, 19, 158.75, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text34'];
	$pdf->writeHTMLCell(100, 0, 19, 162.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text35'];
	$pdf->writeHTMLCell(100, 0, 19, 166.35, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text35_1'];
	$pdf->writeHTMLCell(100, 0, 19, 170.35, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text36'];
	$pdf->writeHTMLCell(100, 0, 19, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text37'];
	$pdf->writeHTMLCell(100, 0, 19, 189.25, $html, 0, 0, 0, true, 'L', true);

	// PAGE 3
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_TIPE_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['npage3'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',16);
	$pdf->SetTextColor(255,255,255);
	$pdf->writeHTMLCell(145, 0, 12, 11, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['npage30'];
	$pdf->SetFont('roboto','',9);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 12, 22, $html, 0, 0, 0, true, 'L', true);

	// DRIVE
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[1]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=30, $h=0, $x=15, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="line-height:1.5;font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[1]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=15, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

	// NETWORK
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[2]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=30, $h=0, $x=56, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[2]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=56, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

	// ACTION
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[3]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=35, $h=0, $x=99, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="line-height:1.25;font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[3]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=99, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

    // PAGE 4
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/45/PAGE_04_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$pdf->StartTransform();
	// set clipping mask
	$pdf->StarPolygon(75.5, 50.85, 15.5, 50, 3, 0, 0, 'CNZ');
	$img_file = public_path('assets/images/v3/'.$row['image_profil']);
	$pdf->Image($img_file, 54.5, 29.85, 42, 42, '', 'URL', '', true, 300);
	$pdf->StopTransform();

	$pdf->SetFont('roboto', 'B', 7.25);
	$i = 0; $no = 1;
	// #1
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 45, 12.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #2
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 27.5, 20, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #3
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 21.5, 28, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #4
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 17.25, 36.25, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #5
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 14.35, 44.25, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;

	// #6
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 14.35, 52.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #7
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 17.25, 60.75, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #8
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 21.5, 70.75, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #9
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 27.5, 79.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #10
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 45, 87.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;

	$i = 40; $no = 41;
	if($row['report_type'] == 45 || $row['report_type'] == 65) {
		// #41
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 104, 32.5, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
		// #42
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 108.5, 40.75, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
		// #43
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 110.5, 49, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
		// #44
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 109, 57, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
		// #45
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 104, 65.5, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
	}

	for($i=0;$i<10;$i++) {
		$colortext= $output[$i]['color']['color'];
		//$bgtext= ($no < 11) ? '#ECECFF':'#FFF';
		$html .= '<p style="bottom:-5px;font-weight:700;font-size:11pt;color:'.$colortext.'"><b>'.$no.' '.$output[$i]['nama'].'</b></p>';
		$bahas[$i] = json_decode($detail_dimensi[$output[$i]['id']]['f_bahasa'],true);
		$no++;
	}

	$html =$kata2['penjelasan_top'];
	$pdf->SetFont('roboto','',10);
	//$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(58, 0, 12.5, 104, $html, 0, 0, 0, true, 'C', true);

	$html =$kata2['penjelasan_bottom'];
	//$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(58, 0, 75, 104, $html, 0, 0, 0, true, 'C', true);

	$find=array('{BAHAS_1}','{BAHAS_2}','{BAHAS_3}','{BAHAS_4}','{BAHAS_5}','{BAHAS_6}','{BAHAS_7}','{BAHAS_8}','{BAHAS_9}','{BAHAS_10}');
	$replace = array(str_replace('.','',$bahas[0]['deskripsi_paragraf']),str_replace('.','',$bahas[1]['deskripsi_paragraf']),str_replace('.','',$bahas[2]['deskripsi_paragraf']),str_replace('.','',$bahas[3]['deskripsi_paragraf']),str_replace('.','',$bahas[4]['deskripsi_paragraf']),str_replace('.','',$bahas[5]['deskripsi_paragraf']),str_replace('.','',$bahas[6]['deskripsi_paragraf']),str_replace('.','',$bahas[7]['deskripsi_paragraf']),str_replace('.','',$bahas[8]['deskripsi_paragraf']),str_replace('.','',$bahas[9]['deskripsi_paragraf']));
	$gmbr_text = str_replace($find,$replace,$kata2['gambar_diri10']);

	$fontsize = 7.5;
	if(strlen($gmbr_text) > 815) $fontsize = 7.25;
	$pdf->SetFont('roboto','',$fontsize);
    $pdf->writeHTMLCell($w=65, $h=0, $x=7.5, $y=120, $gmbr_text, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);

	$no = 41;
	$bahas_bottom = array();
	//opn($detail_dimensi);
	for($i=40;$i<45;$i++) {
		$colortext= $output[$i]['color']['color'];
		//$bgtext= ($no < 11) ? '#ECECFF':'#FFF';
		$html .= '<p style="bottom:-5px;font-weight:700;font-size:10pt;color:'.$colortext.'"><b>'.$no.'. '.$output[$i]['nama'].'</b></p>';
		$bahas_bottom[$i] = json_decode($detail_dimensi[$output[$i]['id']]['f_bahasa'],true);
		$no++;
	}
	$find_btm=array('{BTM_41}','{BTM_42}','{BTM_43}','{BTM_44}','{BTM_45}');
	$replace_btm = array(str_replace('.','',$bahas_bottom[40]['deskripsi_paragraf']),str_replace('.','',$bahas_bottom[41]['deskripsi_paragraf']),str_replace('.','',$bahas_bottom[42]['deskripsi_paragraf']),str_replace('.','',$bahas_bottom[43]['deskripsi_paragraf']),str_replace('.','',$bahas_bottom[44]['deskripsi_paragraf']));

	$gmbr_text = str_replace($find_btm,$replace_btm,$kata2['bawah_diri5']);
	$pdf->SetFont('roboto','',$fontsize);
    $pdf->writeHTMLCell($w=62.5, $h=0, $x=80, $y=120, $gmbr_text, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);

	// PAGE 5
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/45/PAGE_05_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['page5_judul'];
	$pdf->SetFont('robotob','B',10);
	$pdf->writeHTMLCell(100, 0, 20, 13, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['page5_desc'];
	$pdf->SetFont('roboto','',10);
	$pdf->writeHTMLCell(115, 0, 20, 25, $html, 0, 0, 0, true, 'J', true);


	$pdf->SetFont('roboto', 'B', 11);
	$html_y = 45.5;
	$html_1 = '';$no = 1;
	for($i=0;$i<15;$i++) {
		$colortext= $output[$i]['color']['color'];
		$bgtext= ($no < 11) ? '#ECECFF':'#FFF';
		$bgtext_spasi= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if($no < 9) $bgtext_spasi=  '&nbsp;&nbsp;&nbsp;&nbsp;';
		//$html_1 .= '<p style="margin-top:25px;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b></p>';
		$html_1 .= '<p style="margin-top:10px;line-height:100%;font-size:9pt;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b><br><span style="font-size:8pt;">'.$bgtext_spasi.'('.$output[$i]['nama_lain'].')</span></p>';
		$no++;
	}
	$pdf->writeHTMLCell(40, 0, 20, $html_y, $html_1, 0, 0, 0, true, 'L', false);

	$html_1 = '';$no = 16;
	for($i=15;$i<30;$i++) {
		$colortext= $output[$i]['color']['color'];
		$bgtext= ($no < 6) ? '#ECECFF':'#FFF';
		$bgtext_spasi= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		//$html_1 .= '<p style="margin-top:25px;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b></p>';
		$html_1 .= '<p style="margin-top:10px;line-height:100%;font-size:9pt;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b><br><span style="font-size:8pt;">'.$bgtext_spasi.'('.$output[$i]['nama_lain'].')</span></p>';
		$no++;
	}
	$pdf->writeHTMLCell(40, 0, 60, $html_y, $html_1, 0, 0, 0, true, 'L', false);

	$html_1 = '';$no = 31;
	for($i=30;$i<45;$i++) {
		$colortext= $output[$i]['color']['color'];
		$bgtext= ($no > 40) ? '#ECECFF':'#FFF';
		$bgtext_spasi= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		//$html_1 .= '<p style="margin-top:25px;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b></p>';
		$html_1 .= '<p style="margin-top:10px;line-height:100%;font-size:9pt;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b><br><span style="font-size:8pt;">'.$bgtext_spasi.'('.$output[$i]['nama_lain'].')</span></p>';
		$no++;
	}
	$pdf->writeHTMLCell(40, 0, 100, $html_y, $html_1, 0, 0, 0, true, 'L', false);

	// PAGE 6
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PENJELASAN.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['penjelasan1'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 20, 48, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['penjelasantop10'];
	$pdf->writeHTMLCell(145, 0, 22, 87, $html, 0, 0, 0, true, 'L', true);

	// PENJELASAN TOP TALENT
	$pdf->AddPage();

	$no = 1;$y= 8;$y1= 9;$y2= 22.5;;$y3= 30;
	for($i=0;$i<10;$i++) {
		$pdf->SetFont('roboto','B',10);
		$type_talent = $output_talent[$i]['color']['nama'];
		$bahas = json_decode($detail_dimensi[$output_talent[$i]['id']]['f_bahasa'],true);
		$img_file = public_path('assets/images/v3/bar/'.strtolower($type_talent).'.png');
		$pdf->Image($img_file, 0, $y, 150, 12, '', 'URL', '', true, 300);
		$judul_talent = ucwords(strtolower($output_talent[$i]['nama']));
		if($row['f_bahasa'] == 'id-ID')
		{
			$spasi = '&nbsp;&nbsp;&nbsp;&nbsp;';
			if($no > 9) $spasi .= '&nbsp;&nbsp;&nbsp;';
			$judul_talent .= '<br>'.$spasi.'<span style="margin-top:-5px;font-size:12pt;">('.$output_talent[$i]['nama_lain'].')</span>';
			$html = '<p style="font-size:14pt;font-weight:700;color:#FFF;line-height:0.9;">'.$no.'. '.$judul_talent.'</p>';
		} else {
			$html = '<p style="font-size:20pt;font-weight:700;color:#FFF;">'.$no.'. '.$judul_talent.'</p>';
		}
		$pdf->writeHTMLCell(135, 10, 10, $y1, $html, 0, 0, 0, true, 'L', true);


		$pdf->SetFont('roboto','',8.65);
		$html = '<p style="text-align:justify;line-height:1;">'.$bahas['deskripsi'].'</p>';
		$pdf->writeHTMLCell(75, 10, 10, $y2, $html, 0, 0, 0, true, 'L', true);
		$pdf->SetFont('roboto','B',10);
		$html = '<p style="font-size:12pt;text-align:justify"><strong>'.$kata2['text_3_1'].'</strong></p>';
		$pdf->writeHTMLCell(75, 10, 87.5, $y2, $html, 0, 0, 0, true, 'L', true);
		$html = '<ul style="font-size:8.65pt;text-align:justify">';
		foreach($bahas['keterangan_kuat'] as $c) {
			$html .= '<li>'.$c.'</li>';
		}
		$html .= '</ul>';
		$pdf->SetFont('roboto','',9);
		$pdf->writeHTMLCell(62.5, 10, 80, $y3, $html, 0, 0, 0, true, 'L', true);

		$y+=100;$y1+=100;$y2+=100;$y3+=100;
		if($no%2 ==0) {
			if($no < 10) {
				$pdf->AddPage();
				$y= 8;$y1= 9;$y2= 22.5;$y3= 30;
			}
		}
		$no++;
	}

	// PAGE 8
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PENJELASAN.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['penjelasan2'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 20, 48, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['penjelasanbottom5'];
	$pdf->writeHTMLCell(145, 0, 22, 87, $html, 0, 0, 0, true, 'L', true);

	// PENJELASAN BOTTOM TALENT
	$pdf->AddPage();

	$no = 41;$y= 8;$y1= 9;$y2= 22.5;;$y3= 35;
	for($i=40;$i<45;$i++) {
		$pdf->SetFont('roboto','B',10);
		$type_talent = $output_talent[$i]['color']['nama'];
		$bahas = json_decode($detail_dimensi[$output_talent[$i]['id']]['f_bahasa'],true);
		$img_file = public_path('assets/images/v3/bar/'.strtolower($type_talent).'.png');
		$pdf->Image($img_file, 0, $y, 150, 12, '', 'URL', '', true, 300);
		$judul_talent = ucwords(strtolower($output_talent[$i]['nama']));
		if($row['f_bahasa'] == 'id-ID')
		{
			$judul_talent .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="margin-top:-5px;font-size:12pt;">('.$output_talent[$i]['nama_lain'].')</span>';
			$html = '<p style="font-size:14pt;font-weight:700;color:#FFF;line-height:0.9;">'.$no.'. '.$judul_talent.'</p>';
		} else {
			$html = '<p style="font-size:20pt;font-weight:700;color:#FFF;">'.$no.'. '.$judul_talent.'</p>';
		}
		$pdf->writeHTMLCell(135, 10, 10, $y1, $html, 0, 0, 0, true, 'L', true);

		$pdf->SetFont('roboto','',8.65);
		$html = '<p style="text-align:justify;line-height:1;">'.$bahas['deskripsi'].'</p>';
		$pdf->writeHTMLCell(75, 10, 10, $y2, $html, 0, 0, 0, true, 'L', true);
		$pdf->SetFont('roboto','B',10);
		$html = '<p style="font-size:12pt;text-align:justify"><strong>'.$kata2['text_3_2'].'</strong></p>';
		$pdf->writeHTMLCell(75, 10, 87.5, $y2, $html, 0, 0, 0, true, 'L', true);
		$html = '<ul style="font-size:8.65pt;text-align:justify">';
		foreach($bahas['keterangan_lemah'] as $c) {
			$html .= '<li>'.$c.'</li>';
		}
		$html .= '</ul>';
		$pdf->SetFont('roboto','',9);
		$pdf->writeHTMLCell(62.5, 10, 80, $y3, $html, 0, 0, 0, true, 'L', true);

		$y+=100;$y1+=100;$y2+=100;$y3+=100;
		if($no%2 ==0) {
			if($no < 45) {
				$pdf->AddPage();
				$y= 8;$y1= 9;$y2= 22.5;$y3= 35;
			}
		}
		$no++;
	}

	// PAGE 10
	$pdf->PageOver($row['f_bahasa']);

    $publicPath = public_path('assets/pdf/' . $nama_file);
    $pdf->Output($publicPath,$mode);
    chmod($publicPath,0777);

    //* ---------------------------------- */
    // $pdf->AddPage();
    // $pdf->SetFont('helvetica', '', 12);
    // $pdf->Write(0, 'Hello World');
    // $pdf->Output('example.pdf', $mode);
	// $pdf->Output(FCPATH.'assets/pdf/'.$nama_file, $mode);
	// chmod(FCPATH.'assets/pdf/'.$nama_file,0777);

}

function createPDF10Path($row, $path, $mode = 'I')
{
	$nama_file = 'Result_'.str_replace(" ","_",$row['f_survey_username']).'-'.str_replace(" ","_",$row['f_email']).'.pdf';
    $cekbhs = get_bahasa($row['f_bahasa']);

    $kata2 = file_get_contents(public_path('language/'.$row['f_bahasa'].'/report.json'));
	$kata2 = json_decode($kata2, true);
	$tgl_survey = $kata2['tanggal_survey'];

	$tgl_selesai = tgl_indo1($row['tgl_selesai']);

    $vcolor = array(
        1 => array( 'rgb1' => 124, 'rgb2' => 125, 'rgb3' => 255, 'color' => '#7c7dff', 'nama' => 'drive', ),
        2 => array( 'rgb1' => 255, 'rgb2' =>  132, 'rgb3' => 0, 'color' => '#ff8400', 'nama' => 'network', ),
        3 => array( 'rgb1' => 221, 'rgb2' => 62, 'rgb3' => 58, 'color' => '#dd3e3a', 'nama' => 'action', ),
    );

    // Cek Variabel
    $variabels = Variabel::select('a.f_id', 'a.f_variabel_name', 'c.f_bahasa')
        ->from('t_variabel as a')
        ->join('variabel_bahasa as c', 'c.f_id', '=', 'a.f_id')
        ->where('c.f_negara', $row['f_bahasa'])
        ->get();

    $detail_variabel = [];
    foreach ($variabels as $r) {
        $d = json_decode($r->f_bahasa, true);
        foreach ($d as $e => $f) {
            $detail_variabel[$r->f_id][$e] = $f;
        }
    }

    // Cek Variabel Dimensi
    $dimensies = Dimensi::select('a.f_id', 'a.f_dimensi_name', 'a.f_name_indo', 'a.f_variabel_id', 'b.f_variabel_name', 'c.f_bahasa')
        ->from('t_dimensi as a')
        ->join('t_variabel as b', 'b.f_id', '=', 'a.f_variabel_id')
        ->join('dimensi_bahasa as c', 'c.f_id', '=', 'a.f_id')
        ->where('c.f_negara', $row['f_bahasa'])
        ->get();

    $detail_dimensi = [];
    $combo_dimensi = [];
    $color_dimensi = []; // Pastikan $vcolor didefinisikan sebelumnya
    foreach ($dimensies as $r) {
        $detail_dimensi[$r->f_id] = $r;
        $combo_dimensi[$r->f_id] = $r->f_dimensi_name;
        $color_dimensi[$r->f_id] = $vcolor[$r->f_variabel_id]; // Pastikan $vcolor didefinisikan sebelumnya
    }

    $total_dimensi = $row['total_dimensi'];
    $top_ten = $row['topten'];
    $topten = [];
    foreach ($top_ten as $c) {
        $topten[$c['id']] = $c['nama'];
    }

    $rata_dimensi = [];
    foreach ($row['total_dimensi'] as $k => $v) {
        $total_soal = $row['soal_perdimensi'][$k];
        $hitung = round($v / $total_soal, 2);
        $name_indo = ucwords(strtolower($detail_dimensi[$k]['f_name_indo']));
        $rata_dimensi[] = [
            'id' => $k,
            'nama' => $combo_dimensi[$k],
            //'hc' => $combo_hc[$k], // Pastikan $combo_hc didefinisikan sebelumnya
            'total' => $hitung,
            'color' => $color_dimensi[$k],
            'nama_lain' => $name_indo,
        ];
    }

    // Mengurutkan berdasarkan total
    usort($rata_dimensi, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    $output = array_slice($rata_dimensi, 0, 10);
    $topten_id = [];
    foreach ($output as $r) {
        $topten_id[] = $r['id'];
    }

    $total_akhir = $output[9]['total'];
    foreach ($rata_dimensi as $r) {
        if ($r['total'] == $total_akhir && !in_array($r['id'], $topten_id)) {
            array_push($output, $r);
        }
    }

    if (count($output) > 10) {
        $temp_bukan_akhir = [];
        $temp_akhir = [];
        foreach ($output as $r) {
            if ($r['total'] == $total_akhir) {
                $temp_akhir[] = $r;
            } else {
                $temp_bukan_akhir[] = $r;
            }
        }
        $output = $temp_bukan_akhir;
        $selisih = 10 - count($temp_bukan_akhir);

        // Mengurutkan temp_akhir berdasarkan nama
        usort($temp_akhir, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        foreach ($temp_akhir as $r) {
            array_push($output, $r);
        }
    }

    // CEK ID OUTPUT SEKARANG
    $combo_id_output = [];
    foreach ($output as $r) {
        $combo_id_output[] = $r['id'];
    }

    // CEK YANG TIDAK MASUK KE OUTPUT UNTUK DI URUTKAN
    $temp_bukan_output = [];
    foreach ($rata_dimensi as $r) {
        if (!in_array($r['id'], $combo_id_output)) {
            $temp_bukan_output[] = $r;
        }
    }

    // Mengurutkan temp_bukan_output berdasarkan total
    usort($temp_bukan_output, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    foreach ($temp_bukan_output as $r) {
        array_push($output, $r);
    }

    $output_talent = $output;

    // $pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false);
    $pdf = new Pdf('P', 'mm', 'A5', true, 'UTF-8', false);
	$pdf->SetCreator('Copyright © 2023 ESQ. All Rights Reserved');
	$pdf->SetAuthor('ESQ');
	$pdf->SetTitle('TalentDNA® '.$row['f_survey_username']);
	$pdf->SetSubject('TalentDNA® by ESQ');

    $margin = 17.5;
	$color_text = '#FFF';
	$row['color_text'] = $color_text;
	$row['tgl_survey'] = $tgl_survey;
	$fcolor1 = '#000';
	$pdf->SetHeaderMargin($margin);
	$pdf->SetFooterMargin($margin);
	$pdf->SetAutoPageBreak(TRUE, $margin);

	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

    // COVER
	$pdf->AddPage();

	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/10/COVER.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();


	$pdf->SetFont('roboto','',20);
	$report_type = $kata2['5report'];
	if($row['report_type'] == 10) $report_type = $kata2['10report'];
	if($row['report_type'] == 45 || $row['report_type'] == 65) $report_type = $kata2['45report'];
	$row['name_report_type'] = $report_type;
	//$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$report_type.'</p>';
	//$pdf->writeHTMLCell(148, 0, 0, 45, $html, 0, 0, 0, true, 'C', true);

	$pdf->SetY(131);
	//$html = '<p style="font-size:18pt;font-weight:700;background: linear-gradient(45deg, #11a8ff, #FF00FF, #9e5aff);-webkit-background-clip: text;color: transparent;">'.$row['f_survey_username'].'</p>';
	$pdf->SetFont('robotob', 'B', 10);
	$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$row['f_survey_username'].'</p>';
	$pdf->writeHTMLCell(105, 0, 30, '', $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('robotomedium', '', 10);
	$pdf->SetY(141);
	$html = '<p style="font-size:12pt;font-weight:700;color:'.$color_text.';">'.$kata2['tanggal_survey'].': '.$tgl_selesai.'</p>';
	$pdf->writeHTMLCell(125, 0, 35, '', $html, 0, 0, 0, true, 'L', true);

	// PAGE 1
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_01_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$pdf->SetFont('roboto', '', 10);
	$html = '<p style="font-size:14pt;color:#1717b6;">'.$kata2['page2_karir1'].'</p>';
	$pdf->writeHTMLCell(145, 0, 40, 40, $html, 0, 0, 0, true, 'L', true);

	$pdf->SetY(52.5);
	$nama = explode(" ",$row['f_survey_username']);
	//echo count($nama);
	// dd($nama);
    // die();
	if(count($nama) == 1){ $nama = $nama[0];}
	else if(count($nama) == 2) { $nama = $nama[0].'<br>'.$nama[1]; }
	else if(count($nama) == 3) { $nama = $nama[0].' '.$nama[1].'<br>'.$nama[2]; }
	else if(count($nama) == 4) {
		$nama_belakang = '';
		for($i=2;$i<count($nama);$i++) {
			$nama_belakang .= $nama[$i].' ';
		}
		$nama = $nama[0].' '.$nama[1].'<br>';
		$nama .= trim($nama_belakang," ");
	}
	else if(count($nama) > 4) {
		$nama_belakang = '';
		for($i=3;$i<count($nama);$i++) {
			$nama_belakang .= $nama[$i].' ';
		}
		$nama = $nama[0].' '.$nama[1].' '.$nama[2].'<br>';
		$nama .= trim($nama_belakang," ");
	}
	//echo $nama;die();

	$pdf->SetFont('robotob', 'B', 10);
	$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$nama.'</p>';
	$pdf->writeHTMLCell(145, 0, 40, '', $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('roboto', '', 10);
	$html = $kata2['page1_10'];
	$pdf->writeHTMLCell(145, 0, 40, 80, $html, 0, 0, 0, true, 'L', true);

	// PAGE 2
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_02_KOSONG-02.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['page2_pk'];
	$pdf->SetXY(27,12);
	$pdf->SetFont('roboto', '', 10);
	$pdf->SetTextColor(255,255,255);
	$pdf->writeHTMLCell(145, 0, 13, 12.5, $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('roboto','',8);
	$pdf->SetTextColor(0,0,0);
	$html =$kata2['page2_pk1'];
	$pdf->writeHTMLCell(105, 0, 24, 23.5, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk2'];
	$pdf->writeHTMLCell(105, 0, 24, 41.5, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk3'];
	$pdf->writeHTMLCell(105, 0, 24, 57, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk4'];
	$pdf->writeHTMLCell(105, 0, 24, 73, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk5'];
	$pdf->writeHTMLCell(105, 0, 24, 92.5, $html, 0, 0, 0, true, 'J', true);

	$html =$kata2['page2_bt'];
	$pdf->writeHTMLCell(145, 0, 15, 117.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt1_old'];
	$pdf->writeHTMLCell(145, 0, 15, 127, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_phone_old'];
	$pdf->writeHTMLCell(145, 0, 72, 140, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt2'];
	$pdf->writeHTMLCell(145, 0, 15, 155, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt3'];
	$pdf->writeHTMLCell(145, 0, 15, 165, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt4'];
	$pdf->writeHTMLCell(145, 0, 20, 170.6, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt5'];
	$pdf->writeHTMLCell(145, 0, 15, 179, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_in'];
	$pdf->writeHTMLCell(145, 0, 19, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_ig'];
	$pdf->writeHTMLCell(145, 0, 43, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_yt'];
	$pdf->writeHTMLCell(145, 0, 69.5, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_tt'];
	$pdf->writeHTMLCell(145, 0, 96, 185.5, $html, 0, 0, 0, true, 'L', true);


	// PAGE 2-3
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 5);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_03P.png');
	//if($row['f_bahasa'] == 'id-ID') $img_file = FCPATH.'assets/images/v3/PAGE_03P_id.png';
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	//$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	$pdf->SetAutoPageBreak(true, 5);
	// set the starting point for the page content
	$pdf->setPageMark();
	$html =$kata2['text_3N_judul1'];
	$pdf->SetXY(27,12);
	$pdf->SetTextColor(25, 27, 189);
	$pdf->writeHTMLCell(135, 0, 12, 100, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_judul2'];
	$pdf->writeHTMLCell(135, 0, 12, 120, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['text_3N_judul3'];
	$pdf->writeHTMLCell(135, 0, 12, 136, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text31'];
	$pdf->writeHTMLCell(100, 0, 19, 147.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text32'];
	$pdf->writeHTMLCell(100, 0, 19, 155, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text33'];
	$pdf->writeHTMLCell(100, 0, 19, 158.75, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text34'];
	$pdf->writeHTMLCell(100, 0, 19, 162.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text35'];
	$pdf->writeHTMLCell(100, 0, 19, 166.35, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text35_1'];
	$pdf->writeHTMLCell(100, 0, 19, 170.35, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text36'];
	$pdf->writeHTMLCell(100, 0, 19, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text37'];
	$pdf->writeHTMLCell(100, 0, 19, 189.25, $html, 0, 0, 0, true, 'L', true);

	// PAGE 3
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_TIPE_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['npage3'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',16);
	$pdf->SetTextColor(255,255,255);
	$pdf->writeHTMLCell(145, 0, 12, 11, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['npage30'];
	$pdf->SetFont('roboto','',9);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 12, 22, $html, 0, 0, 0, true, 'L', true);

	// DRIVE
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[1]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=30, $h=0, $x=15, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="line-height:1.5;font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[1]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=15, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

	// NETWORK
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[2]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=30, $h=0, $x=56, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[2]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=56, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

	// ACTION
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[3]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=35, $h=0, $x=99, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="line-height:1.25;font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[3]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=99, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

    // PAGE 4
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/10/PAGE_04_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$pdf->StartTransform();
	// set clipping mask
	$pdf->StarPolygon(75.5, 50.85, 15.5, 50, 3, 0, 0, 'CNZ');
	$img_file = public_path('assets/images/v3/'.$row['image_profil']);
	$pdf->Image($img_file, 54.5, 29.85, 42, 42, '', 'URL', '', true, 300);
	$pdf->StopTransform();

	$pdf->SetFont('roboto', 'B', 7.25);
	$i = 0; $no = 1;
	// #1
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 45, 12.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #2
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 27.5, 20, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #3
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 21.5, 28, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #4
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 17.25, 36.25, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #5
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 14.35, 44.25, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;

	// #6
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 14.35, 52.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #7
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 17.25, 60.75, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #8
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 21.5, 70.75, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #9
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 27.5, 79.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #10
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 45, 87.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;

	for($i=0;$i<10;$i++) {
		$colortext= $output[$i]['color']['color'];
		//$bgtext= ($no < 11) ? '#ECECFF':'#FFF';
		$html .= '<p style="bottom:-5px;font-weight:700;font-size:11pt;color:'.$colortext.'"><b>'.$no.' '.$output[$i]['nama'].'</b></p>';
		$bahas[$i] = json_decode($detail_dimensi[$output[$i]['id']]['f_bahasa'],true);
		$no++;
	}

	$html =$kata2['penjelasan_top'];
	$pdf->SetFont('roboto','',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(58, 0, 12.5, 104, $html, 0, 0, 0, true, 'C', true);

	$html =$kata2['penjelasan_bottom'];
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(58, 0, 75, 104, $html, 0, 0, 0, true, 'C', true);

	$find=array('{BAHAS_1}','{BAHAS_2}','{BAHAS_3}','{BAHAS_4}','{BAHAS_5}','{BAHAS_6}','{BAHAS_7}','{BAHAS_8}','{BAHAS_9}','{BAHAS_10}');
	$replace = array(str_replace('.','',$bahas[0]['deskripsi_paragraf']),str_replace('.','',$bahas[1]['deskripsi_paragraf']),str_replace('.','',$bahas[2]['deskripsi_paragraf']),str_replace('.','',$bahas[3]['deskripsi_paragraf']),str_replace('.','',$bahas[4]['deskripsi_paragraf']),str_replace('.','',$bahas[5]['deskripsi_paragraf']),str_replace('.','',$bahas[6]['deskripsi_paragraf']),str_replace('.','',$bahas[7]['deskripsi_paragraf']),str_replace('.','',$bahas[8]['deskripsi_paragraf']),str_replace('.','',$bahas[9]['deskripsi_paragraf']));
	$gmbr_text = str_replace($find,$replace,$kata2['gambar_diri10']);

	$fontsize = 7.5;
	if(strlen($gmbr_text) > 815) $fontsize = 7.25;
	$pdf->SetFont('roboto','',$fontsize);
    $pdf->writeHTMLCell($w=65, $h=0, $x=7.5, $y=120, $gmbr_text, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);

	// PAGE 5
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PENJELASAN.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['penjelasan1'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 20, 48, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['penjelasantop10'];
	$pdf->writeHTMLCell(145, 0, 22, 87, $html, 0, 0, 0, true, 'L', true);

	// PENJELASAN TOP TALENT
	$pdf->AddPage();

	$no = 1;$y= 8;$y1= 9;$y2= 22.5;;$y3= 30;
	for($i=0;$i<10;$i++) {
		$pdf->SetFont('roboto','B',10);
		$type_talent = $output_talent[$i]['color']['nama'];
		$bahas = json_decode($detail_dimensi[$output_talent[$i]['id']]['f_bahasa'],true);
		$img_file = public_path('assets/images/v3/bar/'.strtolower($type_talent).'.png');
		$pdf->Image($img_file, 0, $y, 150, 12, '', 'URL', '', true, 300);
		$judul_talent = ucwords(strtolower($output_talent[$i]['nama']));
		if($row['f_bahasa'] == 'id-ID')
		{
			$spasi = '&nbsp;&nbsp;&nbsp;&nbsp;';
			if($no > 9) $spasi .= '&nbsp;&nbsp;&nbsp;';
			$judul_talent .= '<br>'.$spasi.'<span style="margin-top:-5px;font-size:12pt;">('.$output_talent[$i]['nama_lain'].')</span>';
			$html = '<p style="font-size:14pt;font-weight:700;color:#FFF;line-height:0.9;">'.$no.'. '.$judul_talent.'</p>';
		} else {
			$html = '<p style="font-size:20pt;font-weight:700;color:#FFF;">'.$no.'. '.$judul_talent.'</p>';
		}
		$pdf->writeHTMLCell(135, 10, 10, $y1, $html, 0, 0, 0, true, 'L', true);


		$pdf->SetFont('roboto','',8.65);
		$html = '<p style="text-align:justify;line-height:1;">'.$bahas['deskripsi'].'</p>';
		$pdf->writeHTMLCell(75, 10, 10, $y2, $html, 0, 0, 0, true, 'L', true);
		$pdf->SetFont('roboto','B',10);
		$html = '<p style="font-size:12pt;text-align:justify"><strong>'.$kata2['text_3_1'].'</strong></p>';
		$pdf->writeHTMLCell(75, 10, 87.5, $y2, $html, 0, 0, 0, true, 'L', true);
		$html = '<ul style="font-size:8.65pt;text-align:justify">';
		foreach($bahas['keterangan_kuat'] as $c) {
			$html .= '<li>'.$c.'</li>';
		}
		$html .= '</ul>';
		$pdf->SetFont('roboto','',9);
		$pdf->writeHTMLCell(62.5, 10, 80, $y3, $html, 0, 0, 0, true, 'L', true);

		$y+=100;$y1+=100;$y2+=100;$y3+=100;
		if($no%2 ==0) {
			if($no < 10) {
				$pdf->AddPage();
				$y= 8;$y1= 9;$y2= 22.5;$y3= 30;
			}
		}
		$no++;
	}

	// PAGE 10
	$pdf->PageOver($row['f_bahasa']);

    $publicPath = public_path('assets/pdf/'.$path .'/'. $nama_file);
    $pdf->Output($publicPath,$mode);
    chmod($publicPath,0777);
}

function createPDF45Path($row, $path ,$mode = 'I')
{
	$nama_file = 'Result_'.str_replace(" ","_",$row['f_survey_username']).'-'.str_replace(" ","_",$row['f_email']).'.pdf';
    $cekbhs = get_bahasa($row['f_bahasa']);

    $kata2 = file_get_contents(public_path('language/'.$row['f_bahasa'].'/report.json'));
	$kata2 = json_decode($kata2, true);
	$tgl_survey = $kata2['tanggal_survey'];

	$tgl_selesai = tgl_indo1($row['tgl_selesai']);

    $vcolor = array(
        1 => array( 'rgb1' => 124, 'rgb2' => 125, 'rgb3' => 255, 'color' => '#7c7dff', 'nama' => 'drive', ),
        2 => array( 'rgb1' => 255, 'rgb2' =>  132, 'rgb3' => 0, 'color' => '#ff8400', 'nama' => 'network', ),
        3 => array( 'rgb1' => 221, 'rgb2' => 62, 'rgb3' => 58, 'color' => '#dd3e3a', 'nama' => 'action', ),
    );

    // Cek Variabel
    $variabels = Variabel::select('a.f_id', 'a.f_variabel_name', 'c.f_bahasa')
        ->from('t_variabel as a')
        ->join('variabel_bahasa as c', 'c.f_id', '=', 'a.f_id')
        ->where('c.f_negara', $row['f_bahasa'])
        ->get();

    $detail_variabel = [];
    foreach ($variabels as $r) {
        $d = json_decode($r->f_bahasa, true);
        foreach ($d as $e => $f) {
            $detail_variabel[$r->f_id][$e] = $f;
        }
    }

    // Cek Variabel Dimensi
    $dimensies = Dimensi::select('a.f_id', 'a.f_dimensi_name', 'a.f_name_indo', 'a.f_variabel_id', 'b.f_variabel_name', 'c.f_bahasa')
        ->from('t_dimensi as a')
        ->join('t_variabel as b', 'b.f_id', '=', 'a.f_variabel_id')
        ->join('dimensi_bahasa as c', 'c.f_id', '=', 'a.f_id')
        ->where('c.f_negara', $row['f_bahasa'])
        ->get();

    $detail_dimensi = [];
    $combo_dimensi = [];
    $color_dimensi = []; // Pastikan $vcolor didefinisikan sebelumnya
    foreach ($dimensies as $r) {
        $detail_dimensi[$r->f_id] = $r;
        $combo_dimensi[$r->f_id] = $r->f_dimensi_name;
        $color_dimensi[$r->f_id] = $vcolor[$r->f_variabel_id]; // Pastikan $vcolor didefinisikan sebelumnya
    }

    $total_dimensi = $row['total_dimensi'];
    $top_ten = $row['topten'];
    $topten = [];
    foreach ($top_ten as $c) {
        $topten[$c['id']] = $c['nama'];
    }

    $rata_dimensi = [];
    foreach ($row['total_dimensi'] as $k => $v) {
        $total_soal = $row['soal_perdimensi'][$k];
        $hitung = round($v / $total_soal, 2);
        $name_indo = ucwords(strtolower($detail_dimensi[$k]['f_name_indo']));
        $rata_dimensi[] = [
            'id' => $k,
            'nama' => $combo_dimensi[$k],
            //'hc' => $combo_hc[$k], // Pastikan $combo_hc didefinisikan sebelumnya
            'total' => $hitung,
            'color' => $color_dimensi[$k],
            'nama_lain' => $name_indo,
        ];
    }

    // Mengurutkan berdasarkan total
    usort($rata_dimensi, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    $output = array_slice($rata_dimensi, 0, 10);
    $topten_id = [];
    foreach ($output as $r) {
        $topten_id[] = $r['id'];
    }

    $total_akhir = $output[9]['total'];
    foreach ($rata_dimensi as $r) {
        if ($r['total'] == $total_akhir && !in_array($r['id'], $topten_id)) {
            array_push($output, $r);
        }
    }

    if (count($output) > 10) {
        $temp_bukan_akhir = [];
        $temp_akhir = [];
        foreach ($output as $r) {
            if ($r['total'] == $total_akhir) {
                $temp_akhir[] = $r;
            } else {
                $temp_bukan_akhir[] = $r;
            }
        }
        $output = $temp_bukan_akhir;
        $selisih = 10 - count($temp_bukan_akhir);

        // Mengurutkan temp_akhir berdasarkan nama
        usort($temp_akhir, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        foreach ($temp_akhir as $r) {
            array_push($output, $r);
        }
    }

    // CEK ID OUTPUT SEKARANG
    $combo_id_output = [];
    foreach ($output as $r) {
        $combo_id_output[] = $r['id'];
    }

    // CEK YANG TIDAK MASUK KE OUTPUT UNTUK DI URUTKAN
    $temp_bukan_output = [];
    foreach ($rata_dimensi as $r) {
        if (!in_array($r['id'], $combo_id_output)) {
            $temp_bukan_output[] = $r;
        }
    }

    // Mengurutkan temp_bukan_output berdasarkan total
    usort($temp_bukan_output, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    foreach ($temp_bukan_output as $r) {
        array_push($output, $r);
    }

    $output_talent = $output;

    // $pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false);
    $pdf = new Pdf('P', 'mm', 'A5', true, 'UTF-8', false);
	$pdf->SetCreator('Copyright © 2023 ESQ. All Rights Reserved');
	$pdf->SetAuthor('ESQ');
	$pdf->SetTitle('TalentDNA® '.$row['f_survey_username']);
	$pdf->SetSubject('TalentDNA® by ESQ');

    $margin = 17.5;
	$color_text = '#FFF';
	$row['color_text'] = $color_text;
	$row['tgl_survey'] = $tgl_survey;
	$fcolor1 = '#000';
	$pdf->SetHeaderMargin($margin);
	$pdf->SetFooterMargin($margin);
	$pdf->SetAutoPageBreak(TRUE, $margin);

	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

    // COVER
	$pdf->AddPage();

	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/45/COVER.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();


	$pdf->SetFont('roboto','',20);
	$report_type = $kata2['5report'];
	if($row['report_type'] == 10) $report_type = $kata2['10report'];
	if($row['report_type'] == 45 || $row['report_type'] == 65) $report_type = $kata2['45report'];
	$row['name_report_type'] = $report_type;
	//$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$report_type.'</p>';
	//$pdf->writeHTMLCell(148, 0, 0, 45, $html, 0, 0, 0, true, 'C', true);

	$pdf->SetY(131);
	//$html = '<p style="font-size:18pt;font-weight:700;background: linear-gradient(45deg, #11a8ff, #FF00FF, #9e5aff);-webkit-background-clip: text;color: transparent;">'.$row['f_survey_username'].'</p>';
	$pdf->SetFont('robotob', 'B', 10);
	$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$row['f_survey_username'].'</p>';
	$pdf->writeHTMLCell(105, 0, 30, '', $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('robotomedium', '', 10);
	$pdf->SetY(141);
	$html = '<p style="font-size:12pt;font-weight:700;color:'.$color_text.';">'.$kata2['tanggal_survey'].': '.$tgl_selesai.'</p>';
	$pdf->writeHTMLCell(125, 0, 35, '', $html, 0, 0, 0, true, 'L', true);

	// PAGE 1
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_01_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$pdf->SetFont('roboto', '', 10);
	$html = '<p style="font-size:14pt;color:#1717b6;">'.$kata2['page2_karir1'].'</p>';
	$pdf->writeHTMLCell(145, 0, 40, 40, $html, 0, 0, 0, true, 'L', true);

	$pdf->SetY(52.5);
	$nama = explode(" ",$row['f_survey_username']);
	//echo count($nama);
	//opn($nama);
	if(count($nama) == 1){ $nama = $nama[0];}
	else if(count($nama) == 2) { $nama = $nama[0].'<br>'.$nama[1]; }
	else if(count($nama) == 3) { $nama = $nama[0].' '.$nama[1].'<br>'.$nama[2]; }
	else if(count($nama) == 4) {
		$nama_belakang = '';
		for($i=2;$i<count($nama);$i++) {
			$nama_belakang .= $nama[$i].' ';
		}
		$nama = $nama[0].' '.$nama[1].'<br>';
		$nama .= trim($nama_belakang," ");
	}
	else if(count($nama) > 4) {
		$nama_belakang = '';
		for($i=3;$i<count($nama);$i++) {
			$nama_belakang .= $nama[$i].' ';
		}
		$nama = $nama[0].' '.$nama[1].' '.$nama[2].'<br>';
		$nama .= trim($nama_belakang," ");
	}
	//echo $nama;die();

	$pdf->SetFont('robotob', 'B', 10);
	$html = '<p style="font-size:18pt;font-weight:700;color:'.$color_text.';">'.$nama.'</p>';
	$pdf->writeHTMLCell(145, 0, 40, '', $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('roboto', '', 10);
	$html = $kata2['page1_45'];
	$pdf->writeHTMLCell(145, 0, 40, 80, $html, 0, 0, 0, true, 'L', true);

	// PAGE 2
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_02_KOSONG-02.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['page2_pk'];
	$pdf->SetXY(27,12);
	$pdf->SetFont('roboto', '', 10);
	$pdf->SetTextColor(255,255,255);
	$pdf->writeHTMLCell(145, 0, 13, 12.5, $html, 0, 0, 0, true, 'L', true);

	$pdf->SetFont('roboto','',8);
	$pdf->SetTextColor(0,0,0);
	$html =$kata2['page2_pk1'];
	$pdf->writeHTMLCell(105, 0, 24, 23.5, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk2'];
	$pdf->writeHTMLCell(105, 0, 24, 41.5, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk3'];
	$pdf->writeHTMLCell(105, 0, 24, 57, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk4'];
	$pdf->writeHTMLCell(105, 0, 24, 73, $html, 0, 0, 0, true, 'J', true);
	$html =$kata2['page2_pk5'];
	$pdf->writeHTMLCell(105, 0, 24, 92.5, $html, 0, 0, 0, true, 'J', true);

	$html =$kata2['page2_bt'];
	$pdf->writeHTMLCell(145, 0, 15, 117.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt1_old'];
	$pdf->writeHTMLCell(145, 0, 15, 127, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_phone_old'];
	$pdf->writeHTMLCell(145, 0, 72, 140, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt2'];
	$pdf->writeHTMLCell(145, 0, 15, 155, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt3'];
	$pdf->writeHTMLCell(145, 0, 15, 165, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt4'];
	$pdf->writeHTMLCell(145, 0, 20, 170.6, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt5'];
	$pdf->writeHTMLCell(145, 0, 15, 179, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_in'];
	$pdf->writeHTMLCell(145, 0, 19, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_ig'];
	$pdf->writeHTMLCell(145, 0, 43, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_yt'];
	$pdf->writeHTMLCell(145, 0, 69.5, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['page2_bt_tt'];
	$pdf->writeHTMLCell(145, 0, 96, 185.5, $html, 0, 0, 0, true, 'L', true);


	// PAGE 2-3
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 5);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_03P.png');
	//if($row['f_bahasa'] == 'id-ID') $img_file = FCPATH.'assets/images/v3/PAGE_03P_id.png';
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	//$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	$pdf->SetAutoPageBreak(true, 5);
	// set the starting point for the page content
	$pdf->setPageMark();
	$html =$kata2['text_3N_judul1'];
	$pdf->SetXY(27,12);
	$pdf->SetTextColor(25, 27, 189);
	$pdf->writeHTMLCell(135, 0, 12, 100, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_judul2'];
	$pdf->writeHTMLCell(135, 0, 12, 120, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['text_3N_judul3'];
	$pdf->writeHTMLCell(135, 0, 12, 136, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text31'];
	$pdf->writeHTMLCell(100, 0, 19, 147.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text32'];
	$pdf->writeHTMLCell(100, 0, 19, 155, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text33'];
	$pdf->writeHTMLCell(100, 0, 19, 158.75, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text34'];
	$pdf->writeHTMLCell(100, 0, 19, 162.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text35'];
	$pdf->writeHTMLCell(100, 0, 19, 166.35, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text35_1'];
	$pdf->writeHTMLCell(100, 0, 19, 170.35, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text36'];
	$pdf->writeHTMLCell(100, 0, 19, 185.5, $html, 0, 0, 0, true, 'L', true);
	$html =$kata2['text_3N_text37'];
	$pdf->writeHTMLCell(100, 0, 19, 189.25, $html, 0, 0, 0, true, 'L', true);

	// PAGE 3
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PAGE_TIPE_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['npage3'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',16);
	$pdf->SetTextColor(255,255,255);
	$pdf->writeHTMLCell(145, 0, 12, 11, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['npage30'];
	$pdf->SetFont('roboto','',9);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 12, 22, $html, 0, 0, 0, true, 'L', true);

	// DRIVE
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[1]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=30, $h=0, $x=15, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="line-height:1.5;font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[1]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=15, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

	// NETWORK
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[2]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=30, $h=0, $x=56, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[2]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=56, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

	// ACTION
	$html='<p style="text-align:left;font-weight:700;font-size:10pt;color:'.$fcolor1.';"><b>'.$detail_variabel[3]['subjudul'].'</b></p>';
	$pdf->SetFont('robotob','',10);
    $pdf->writeHTMLCell($w=35, $h=0, $x=99, $y=77.5, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);
	$html='<p style="line-height:1.25;font-size:8pt;font-family:roboto;color:'.$fcolor1.';">'.$detail_variabel[3]['deskripsi'].'</p>';
    $pdf->writeHTMLCell($w=35, $h=0, $x=99, $y=94, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='L', $autopadding=true);

    // PAGE 4
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/45/PAGE_04_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$pdf->StartTransform();
	// set clipping mask
	$pdf->StarPolygon(75.5, 50.85, 15.5, 50, 3, 0, 0, 'CNZ');
	$img_file = public_path('assets/images/v3/'.$row['image_profil']);
	$pdf->Image($img_file, 54.5, 29.85, 42, 42, '', 'URL', '', true, 300);
	$pdf->StopTransform();

	$pdf->SetFont('roboto', 'B', 7.25);
	$i = 0; $no = 1;
	// #1
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 45, 12.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #2
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 27.5, 20, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #3
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 21.5, 28, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #4
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 17.25, 36.25, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #5
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 14.35, 44.25, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;

	// #6
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 14.35, 52.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #7
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 17.25, 60.75, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #8
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 21.5, 70.75, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #9
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 27.5, 79.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;
	// #10
	$colortext= $output_talent[$i]['color']['color'];
	$bgtext= '#FFF';
	$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
	$pdf->writeHTMLCell(45, 0, 45, 87.5, $html, 0, 0, 0, true, 'L', false);
	$i++;$no++;

	$i = 40; $no = 41;
	if($row['report_type'] == 45 || $row['report_type'] == 65) {
		// #41
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 104, 32.5, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
		// #42
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 108.5, 40.75, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
		// #43
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 110.5, 49, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
		// #44
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 109, 57, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
		// #45
		$colortext= $output_talent[$i]['color']['color'];
		$bgtext= '#FFF';
		$html = '<p style="margin-top:25px;color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output_talent[$i]['nama'])).'</b></p>';
		$pdf->writeHTMLCell(45, 0, 104, 65.5, $html, 0, 0, 0, true, 'L', false);
		$i++;$no++;
	}

	for($i=0;$i<10;$i++) {
		$colortext= $output[$i]['color']['color'];
		//$bgtext= ($no < 11) ? '#ECECFF':'#FFF';
		$html .= '<p style="bottom:-5px;font-weight:700;font-size:11pt;color:'.$colortext.'"><b>'.$no.' '.$output[$i]['nama'].'</b></p>';
		$bahas[$i] = json_decode($detail_dimensi[$output[$i]['id']]['f_bahasa'],true);
		$no++;
	}

	$html =$kata2['penjelasan_top'];
	$pdf->SetFont('roboto','',10);
	//$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(58, 0, 12.5, 104, $html, 0, 0, 0, true, 'C', true);

	$html =$kata2['penjelasan_bottom'];
	//$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(58, 0, 75, 104, $html, 0, 0, 0, true, 'C', true);

	$find=array('{BAHAS_1}','{BAHAS_2}','{BAHAS_3}','{BAHAS_4}','{BAHAS_5}','{BAHAS_6}','{BAHAS_7}','{BAHAS_8}','{BAHAS_9}','{BAHAS_10}');
	$replace = array(str_replace('.','',$bahas[0]['deskripsi_paragraf']),str_replace('.','',$bahas[1]['deskripsi_paragraf']),str_replace('.','',$bahas[2]['deskripsi_paragraf']),str_replace('.','',$bahas[3]['deskripsi_paragraf']),str_replace('.','',$bahas[4]['deskripsi_paragraf']),str_replace('.','',$bahas[5]['deskripsi_paragraf']),str_replace('.','',$bahas[6]['deskripsi_paragraf']),str_replace('.','',$bahas[7]['deskripsi_paragraf']),str_replace('.','',$bahas[8]['deskripsi_paragraf']),str_replace('.','',$bahas[9]['deskripsi_paragraf']));
	$gmbr_text = str_replace($find,$replace,$kata2['gambar_diri10']);

	$fontsize = 7.5;
	if(strlen($gmbr_text) > 815) $fontsize = 7.25;
	$pdf->SetFont('roboto','',$fontsize);
    $pdf->writeHTMLCell($w=65, $h=0, $x=7.5, $y=120, $gmbr_text, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);

	$no = 41;
	$bahas_bottom = array();
	//opn($detail_dimensi);
	for($i=40;$i<45;$i++) {
		$colortext= $output[$i]['color']['color'];
		//$bgtext= ($no < 11) ? '#ECECFF':'#FFF';
		$html .= '<p style="bottom:-5px;font-weight:700;font-size:10pt;color:'.$colortext.'"><b>'.$no.'. '.$output[$i]['nama'].'</b></p>';
		$bahas_bottom[$i] = json_decode($detail_dimensi[$output[$i]['id']]['f_bahasa'],true);
		$no++;
	}
	$find_btm=array('{BTM_41}','{BTM_42}','{BTM_43}','{BTM_44}','{BTM_45}');
	$replace_btm = array(str_replace('.','',$bahas_bottom[40]['deskripsi_paragraf']),str_replace('.','',$bahas_bottom[41]['deskripsi_paragraf']),str_replace('.','',$bahas_bottom[42]['deskripsi_paragraf']),str_replace('.','',$bahas_bottom[43]['deskripsi_paragraf']),str_replace('.','',$bahas_bottom[44]['deskripsi_paragraf']));

	$gmbr_text = str_replace($find_btm,$replace_btm,$kata2['bawah_diri5']);
	$pdf->SetFont('roboto','',$fontsize);
    $pdf->writeHTMLCell($w=62.5, $h=0, $x=80, $y=120, $gmbr_text, $border=0, $ln=1, $fill=0, $reseth=true, $align='J', $autopadding=true);

	// PAGE 5
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/45/PAGE_05_KOSONG.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['page5_judul'];
	$pdf->SetFont('robotob','B',10);
	$pdf->writeHTMLCell(100, 0, 20, 13, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['page5_desc'];
	$pdf->SetFont('roboto','',10);
	$pdf->writeHTMLCell(115, 0, 20, 25, $html, 0, 0, 0, true, 'J', true);


	$pdf->SetFont('roboto', 'B', 11);
	$html_y = 45.5;
	$html_1 = '';$no = 1;
	for($i=0;$i<15;$i++) {
		$colortext= $output[$i]['color']['color'];
		$bgtext= ($no < 11) ? '#ECECFF':'#FFF';
		$bgtext_spasi= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if($no < 9) $bgtext_spasi=  '&nbsp;&nbsp;&nbsp;&nbsp;';
		//$html_1 .= '<p style="margin-top:25px;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b></p>';
		$html_1 .= '<p style="margin-top:10px;line-height:100%;font-size:9pt;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b><br><span style="font-size:8pt;">'.$bgtext_spasi.'('.$output[$i]['nama_lain'].')</span></p>';
		$no++;
	}
	$pdf->writeHTMLCell(40, 0, 20, $html_y, $html_1, 0, 0, 0, true, 'L', false);

	$html_1 = '';$no = 16;
	for($i=15;$i<30;$i++) {
		$colortext= $output[$i]['color']['color'];
		$bgtext= ($no < 6) ? '#ECECFF':'#FFF';
		$bgtext_spasi= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		//$html_1 .= '<p style="margin-top:25px;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b></p>';
		$html_1 .= '<p style="margin-top:10px;line-height:100%;font-size:9pt;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b><br><span style="font-size:8pt;">'.$bgtext_spasi.'('.$output[$i]['nama_lain'].')</span></p>';
		$no++;
	}
	$pdf->writeHTMLCell(40, 0, 60, $html_y, $html_1, 0, 0, 0, true, 'L', false);

	$html_1 = '';$no = 31;
	for($i=30;$i<45;$i++) {
		$colortext= $output[$i]['color']['color'];
		$bgtext= ($no > 40) ? '#ECECFF':'#FFF';
		$bgtext_spasi= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		//$html_1 .= '<p style="margin-top:25px;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b></p>';
		$html_1 .= '<p style="margin-top:10px;line-height:100%;font-size:9pt;background-color:'.$bgtext.';color:'.$colortext.'"><b>'.$no.'. '.ucwords(strtolower($output[$i]['nama'])).'</b><br><span style="font-size:8pt;">'.$bgtext_spasi.'('.$output[$i]['nama_lain'].')</span></p>';
		$no++;
	}
	$pdf->writeHTMLCell(40, 0, 100, $html_y, $html_1, 0, 0, 0, true, 'L', false);

	// PAGE 6
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PENJELASAN.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['penjelasan1'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 20, 48, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['penjelasantop10'];
	$pdf->writeHTMLCell(145, 0, 22, 87, $html, 0, 0, 0, true, 'L', true);

	// PENJELASAN TOP TALENT
	$pdf->AddPage();

	$no = 1;$y= 8;$y1= 9;$y2= 22.5;;$y3= 30;
	for($i=0;$i<10;$i++) {
		$pdf->SetFont('roboto','B',10);
		$type_talent = $output_talent[$i]['color']['nama'];
		$bahas = json_decode($detail_dimensi[$output_talent[$i]['id']]['f_bahasa'],true);
		$img_file = public_path('assets/images/v3/bar/'.strtolower($type_talent).'.png');
		$pdf->Image($img_file, 0, $y, 150, 12, '', 'URL', '', true, 300);
		$judul_talent = ucwords(strtolower($output_talent[$i]['nama']));
		if($row['f_bahasa'] == 'id-ID')
		{
			$spasi = '&nbsp;&nbsp;&nbsp;&nbsp;';
			if($no > 9) $spasi .= '&nbsp;&nbsp;&nbsp;';
			$judul_talent .= '<br>'.$spasi.'<span style="margin-top:-5px;font-size:12pt;">('.$output_talent[$i]['nama_lain'].')</span>';
			$html = '<p style="font-size:14pt;font-weight:700;color:#FFF;line-height:0.9;">'.$no.'. '.$judul_talent.'</p>';
		} else {
			$html = '<p style="font-size:20pt;font-weight:700;color:#FFF;">'.$no.'. '.$judul_talent.'</p>';
		}
		$pdf->writeHTMLCell(135, 10, 10, $y1, $html, 0, 0, 0, true, 'L', true);


		$pdf->SetFont('roboto','',8.65);
		$html = '<p style="text-align:justify;line-height:1;">'.$bahas['deskripsi'].'</p>';
		$pdf->writeHTMLCell(75, 10, 10, $y2, $html, 0, 0, 0, true, 'L', true);
		$pdf->SetFont('roboto','B',10);
		$html = '<p style="font-size:12pt;text-align:justify"><strong>'.$kata2['text_3_1'].'</strong></p>';
		$pdf->writeHTMLCell(75, 10, 87.5, $y2, $html, 0, 0, 0, true, 'L', true);
		$html = '<ul style="font-size:8.65pt;text-align:justify">';
		foreach($bahas['keterangan_kuat'] as $c) {
			$html .= '<li>'.$c.'</li>';
		}
		$html .= '</ul>';
		$pdf->SetFont('roboto','',9);
		$pdf->writeHTMLCell(62.5, 10, 80, $y3, $html, 0, 0, 0, true, 'L', true);

		$y+=100;$y1+=100;$y2+=100;$y3+=100;
		if($no%2 ==0) {
			if($no < 10) {
				$pdf->AddPage();
				$y= 8;$y1= 9;$y2= 22.5;$y3= 30;
			}
		}
		$no++;
	}

	// PAGE 8
	$pdf->AddPage();
	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = public_path('assets/images/v3/PENJELASAN.png');
	$pdf->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$html =$kata2['penjelasan2'];
	//$pdf->SetXY(25,15);
	$pdf->SetFont('robotob','B',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->writeHTMLCell(145, 0, 20, 48, $html, 0, 0, 0, true, 'L', true);

	$html =$kata2['penjelasanbottom5'];
	$pdf->writeHTMLCell(145, 0, 22, 87, $html, 0, 0, 0, true, 'L', true);

	// PENJELASAN BOTTOM TALENT
	$pdf->AddPage();

	$no = 41;$y= 8;$y1= 9;$y2= 22.5;;$y3= 35;
	for($i=40;$i<45;$i++) {
		$pdf->SetFont('roboto','B',10);
		$type_talent = $output_talent[$i]['color']['nama'];
		$bahas = json_decode($detail_dimensi[$output_talent[$i]['id']]['f_bahasa'],true);
		$img_file = public_path('assets/images/v3/bar/'.strtolower($type_talent).'.png');
		$pdf->Image($img_file, 0, $y, 150, 12, '', 'URL', '', true, 300);
		$judul_talent = ucwords(strtolower($output_talent[$i]['nama']));
		if($row['f_bahasa'] == 'id-ID')
		{
			$judul_talent .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="margin-top:-5px;font-size:12pt;">('.$output_talent[$i]['nama_lain'].')</span>';
			$html = '<p style="font-size:14pt;font-weight:700;color:#FFF;line-height:0.9;">'.$no.'. '.$judul_talent.'</p>';
		} else {
			$html = '<p style="font-size:20pt;font-weight:700;color:#FFF;">'.$no.'. '.$judul_talent.'</p>';
		}
		$pdf->writeHTMLCell(135, 10, 10, $y1, $html, 0, 0, 0, true, 'L', true);

		$pdf->SetFont('roboto','',8.65);
		$html = '<p style="text-align:justify;line-height:1;">'.$bahas['deskripsi'].'</p>';
		$pdf->writeHTMLCell(75, 10, 10, $y2, $html, 0, 0, 0, true, 'L', true);
		$pdf->SetFont('roboto','B',10);
		$html = '<p style="font-size:12pt;text-align:justify"><strong>'.$kata2['text_3_2'].'</strong></p>';
		$pdf->writeHTMLCell(75, 10, 87.5, $y2, $html, 0, 0, 0, true, 'L', true);
		$html = '<ul style="font-size:8.65pt;text-align:justify">';
		foreach($bahas['keterangan_lemah'] as $c) {
			$html .= '<li>'.$c.'</li>';
		}
		$html .= '</ul>';
		$pdf->SetFont('roboto','',9);
		$pdf->writeHTMLCell(62.5, 10, 80, $y3, $html, 0, 0, 0, true, 'L', true);

		$y+=100;$y1+=100;$y2+=100;$y3+=100;
		if($no%2 ==0) {
			if($no < 45) {
				$pdf->AddPage();
				$y= 8;$y1= 9;$y2= 22.5;$y3= 35;
			}
		}
		$no++;
	}

	// PAGE 10
	$pdf->PageOver($row['f_bahasa']);

    $publicPath = public_path('assets/pdf/' .$path.'/'. $nama_file);
    $pdf->Output($publicPath,$mode);
    chmod($publicPath,0777);

}

function get_bahasa($kode)
{
    // Melakukan query untuk mendapatkan data dari tabel set_bahasa
    $record = DB::table('set_bahasa')
        ->where('f_negara', $kode)
        ->first(); // Mengambil satu record
    $data = false; // Inisialisasi data sebagai false
    if ($record) {
        // Jika record ditemukan, decode JSON dari kolom f_bahasa
        $data = json_decode($record->f_bahasa, true);
    }
    return $data; // Mengembalikan data
}

function tgl_indo($tgl)
{
	$tanggal = substr($tgl, 8, 2);
	$bulan = getBulan(substr($tgl, 5, 2));
	$tahun = substr($tgl, 0, 4);
	return $tanggal . ' ' . $bulan . ' ' . $tahun;
}

function tgl_indo1($tgl)
{
	$tanggal = substr($tgl, 8, 2);
	$bulan = substr($tgl, 5, 2);
	$tahun = substr($tgl, 0, 4);
	return $tanggal . '-' . $bulan . '-' . $tahun;
}

function getBulan($bln)
{
	switch ($bln) {
		case 1:
			return "Januari";
			break;
		case 2:
			return "Februari";
			break;
		case 3:
			return "Maret";
			break;
		case 4:
			return "April";
			break;
		case 5:
			return "Mei";
			break;
		case 6:
			return "Juni";
			break;
		case 7:
			return "Juli";
			break;
		case 8:
			return "Agustus";
			break;
		case 9:
			return "September";
			break;
		case 10:
			return "Oktober";
			break;
		case 11:
			return "November";
			break;
		case 12:
			return "Desember";
			break;
	}
}
