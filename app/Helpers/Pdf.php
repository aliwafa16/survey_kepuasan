<?php
namespace App\Helpers;

use TCPDF;

class Pdf extends TCPDF
{
    // Override Header method
    public function Header()
    {
		$bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
		//$img_file = FCPATH.'assets/images/covernew_polos.png'; // set bacground image
        //$this->Image($img_file, 0, 0, 148, 210, '', '', '', false, 300, '', false, false, 0);

        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
        //$pages = $this->getPage();
        $lastpages = $this->getAliasNbPages();

    }

    public function HeaderCostum($row) {
		$report_type = $row['name_report_type'];
		//if($row['report_type'] == 10) $report_type = 'TOP 10';
		//if($row['report_type'] == 45 || $row['report_type'] == 65) $report_type = 'FULL 45';

		$tgl_selesai = tgl_indo1($row['tgl_selesai']);

		$this->setTextColor(0,0,0);
		$this->setCellHeightRatio(1.1);
		$this->SetFont('helvetica','',10);
		$img_file = public_path('assets/images/tdnanew.png');
		$this->Image($img_file, 75, 10, 20, 12, '', '', 'M', false, 300, 'L', false, false, 0);
		$html = '<p style="font-size:18pt;font-weight:700;color:'.$row['color_text'].';">'.$report_type.'<br>'.$row['f_survey_username'].'<br><span style="font-size:7pt;">'.$row['tgl_survey'].': '.$tgl_selesai.'</span></p>';
		$this->writeHTMLCell(148, 0, 0, 8, $html, 0, 0, 0, true, 'C', true);
	}

    public function HeaderCareer($row) {
		$report_type = 'Career';

		$tgl_selesai = tgl_indo1($row['tgl_selesai']);

		$this->setTextColor(0,0,0);
		$this->setCellHeightRatio(1.1);
		$this->SetFont('helvetica','',10);
		$img_file = public_path('assets/images/tdnanew.png');
		$this->Image($img_file, 75, 10, 20, 12, '', '', 'M', false, 300, 'L', false, false, 0);
		$html = '<p style="font-size:18pt;font-weight:700;color:'.$row['color_text'].';">'.$report_type.' REPORT<br>'.$row['f_survey_username'].'<br><span style="font-size:7pt;">'.$row['tgl_survey'].': '.$tgl_selesai.'</span></p>';
		$this->writeHTMLCell(148, 0, 0, 8, $html, 0, 0, 0, true, 'C', true);
	}

    public function Header2024($row) {
		$report_type = 'Career';

		$tgl_selesai = tgl_indo1($row['tgl_selesai']);

		$this->setTextColor(0,0,0);
		$this->setCellHeightRatio(1.1);
		$this->SetFont('helvetica','',10);
		$img_file = public_path('assets/images/tdnanew.png');
		$this->Image($img_file, 75, 10, 20, 12, '', '', 'M', false, 300, 'L', false, false, 0);
		$html = '<p style="font-size:18pt;font-weight:700;color:'.$row['color_text'].';">'.$report_type.' REPORT<br>'.$row['f_survey_username'].'<br><span style="font-size:7pt;">'.$row['tgl_survey'].': '.$tgl_selesai.'</span></p>';
		$this->writeHTMLCell(148, 0, 0, 8, $html, 0, 0, 0, true, 'C', true);
	}

    public function PageOver($bahasa = 'id-ID') {
		$this->AddPage();
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->getAutoPageBreak();
		// disable auto-page-break
		$this->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = public_path('assets/images/v3/PAGE_AKHIR_ID.png');
		//if($bahasa != 'id-ID')$img_file = FCPATH.'assets/images/v3/PAGE_AKHIR_ENGLISH.png';
		if($bahasa != 'id-ID')$img_file = public_path('assets/images/v3/PAGE_AKHIR_ENG.png');
		$this->Image($img_file, 0, 0, 150, 210, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
	}

	public function Footer() {
		// Position at 15 mm from bottom
		$style = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));

        //if ($this->page > 1)
        //{
			////$this->Line(10, 142, 200, 142, $style);
			////$this->setXY(10.5,-5);
			//$this->setFont('arial', '', 6);
			//// Page number
			//$html = '<p style="font-weight:700;color:#FFF;">Copyright © 2023 ESQ. All Rights Reserved</p> ';//.$this->page.' '.$this->getAliasNbPages();
			//$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);
		//}
		//else
		//{
			//$this->Line(35, 139, 135, 139, $style);
			//$this->setXY(35,-6);
			$this->setY(-6);
			// Set font
			$this->setFont('robotothin', 'B', 7);
			// Page number
			 $color = '#000';
			if($this->page == 1) $color = '#FFF';
			//$html = '<p style="font-weight:700;color:#FFF;">Copyright © 2023 ESQ. All Rights Reserved</p> ';//.$this->page.' '.$this->getAliasNbPages();
			$html = '<p style="font-weight:700;color:'.$color.';">Copyright © 2023 ESQ. All Rights Reserved</p> ';//.$this->page.' '.$this->getAliasNbPages();
			$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);
		//}
	}
}
