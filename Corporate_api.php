<?php
defined('BASEPATH') or exit('No direct script access allowed');
//require_once FCPATH.'vendor/autoload.php';
//use Mailgun\Mailgun;

class Corporate_api extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('mo_c');
		$this->load->helper('string');
	}

	function save_survey_corporate()//dari t_account
	{

		// error_reporting(-1);
		// ini_set('display_errors', 1);
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        
		
		$input		= json_decode(file_get_contents("php://input"),true);
        // echo json_encode($input);die();

        $corporate = $this->load->database('db_prod_corporate', true);

        $data_corp = $corporate->select('*')
        ->from('t_account a')
        ->where('sha1(f_account_id)', $input['account_id'])
        ->get()->row_array();

        // echo json_encode($data_corp);die();

        $row	= $corporate->get_where('trn_survey_empex', array('f_email' => $input['email']))->row_array();

        if($row){

            $id_corp = $data_corp['f_account_id'];

            $corporate->where('f_id', $row['f_id'])->update('trn_survey_empex',['f_from_corporate_id'=>$id_corp]);
            
            // echo $corporate->last_query();
            $response = array(
                'status' => 200,
                'msg' => 'Email telah digunakan, email otomatis ter-link dengan perusahaan Anda'
            );

            echo json_encode($response);

            die();
        }

        // $to_pdf = array(
        //     'image_profil' => 'SILHOUETTE.png',
        //     'f_survey_username' => html_escape($input['name'], true), //f_survey_email
        //     'f_survey_password' => $cek->f_survey_password,
        //     'f_email' => $email,
        //     'f_bahasa' => $cek->f_bahasa,
        // );
        $answer = array(
            'soal_semua' => 0,
            'soal_perkategori' => array(),
            'soal_perdimensi' => array(),
        );
        $dimensi = $corporate->get('t_dimensi')->result();
        $combo_dimensi = array();
        foreach ($dimensi as $r) {
            $combo_dimensi[$r->f_id] = $r->f_dimensi_name;
        }
        $pertanyaan = $corporate->get('t_item_pernyataan')->result();

        
        foreach ($pertanyaan as $key) {
            $id = $key->f_id;
            if($input['answers']["ex". $id])
            {
                $namepost = round($input['answers']["ex". $id], 2 );
                $kategori = $key->f_variabel_id;
                $dimensi = $key->f_dimensi_id;
                $answer['jawab'][$id] = $namepost;
                $answer['total_kategori'][$kategori] += $namepost;
                $answer['total_dimensi'][$dimensi] += $namepost;
                $answer['total__kategori_dimensi'][$kategori][$dimensi] += $namepost;

                $answer['soal_semua']++;
                $answer['soal_perkategori'][$kategori]++;
                $answer['soal_perdimensi'][$dimensi]++;
            }
        }

        $rata_dimensi = array();
        foreach ($answer['total_dimensi'] as $k => $v) {
            $total_soal = $answer['soal_perdimensi'][$k];
            $hitung = round($v / $total_soal, 2);
            $answer['rata_dimensi'][$k] = $hitung;
            $rata_dimensi[] = array(
                'id' => $k,
                'nama' => $combo_dimensi[$k],
                'total' => $hitung,
            );
        }

        $rata_dimensi = array_sort($rata_dimensi, 'total', SORT_DESC);
        $output = array_slice($rata_dimensi, 0, 10);
        $topten_id = array();
        foreach ($output as $r) {
            $topten_id[] = $r['id'];
        }

        // CEK TOTAL YANG SAMA ANTARA URUTAN ke 10 dengan URUTAN KE >10;
        $total_akhir =  $output[9]['total'];
        foreach ($rata_dimensi as $r) {
            if ($r['total'] == $total_akhir && !in_array($r['id'], $topten_id)) {
                array_push($output, $r);
            }
        }

        // JIKA ADA LEBIH 
        if (count($output) > 10) {
            $temp_bukan_akhir = array();
            $temp_akhir = array();
            foreach ($output as $r) {
                if ($r['total'] == $total_akhir)
                    $temp_akhir[] = $r;
                else
                    $temp_bukan_akhir[] = $r;
            }

            $output = $temp_bukan_akhir;
            $selisih = 10 - count($temp_bukan_akhir);
            //opn($temp_bukan_akhir);
            //opn($temp_akhir);

            $temp_akhir = array_sort($temp_akhir, 'nama', SORT_ASC);
            $temp_akhir_sisa = array_slice($temp_akhir, 0, $selisih);
            foreach ($temp_akhir_sisa as $r)	array_push($output, $r);
        }

        $answer['topten'] = $output;
        // $to_pdf['topten'] = $output;
        // $to_pdf['report_type'] = $cek->f_report_type;
        // $to_pdf['total_dimensi'] = $answer['total_dimensi'];
        // $to_pdf['soal_perdimensi'] = $answer['soal_perdimensi'];


		// opn($input);
        $data = [
            'f_account_id' => $data_corp['f_account_id'],
            'f_event_id' => 0,
            'f_survey_username' => html_escape($input['name'], true),
            'f_email' => $input['email'],
            'f_age' => $input['age'],
            'f_gender' => $input['gender'],
            'f_survey_password' => $input['email'],
            'f_survey' => json_encode($answer, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_HEX_APOS),
            'f_survey_valid' => "yes",
            'f_report_status' => 45,
            'f_pendidikan' => $input['pendidikan'],
            'f_level1' => $input['label_level1'],
            'f_level2' => $input['label_level2'],
            'f_level3' => $input['label_level3'],
            'level_work' => $input['level_of_work'],
            'negara' => "Indonesia",
            'f_bahasa' => "id-ID",
            'f_report' => 1,
            'f_report_type' => 45,
            'status_mail' => NULL,
            'created_by' => 1,
            'f_survey_created_by' => 'corporate',
            'f_status_bayar' => 0,
            'f_corporate_id' => $data_corp['f_account_id'], // kalau udah ada ambil dari id corporate t_acc nya
            'f_from_corporate_id'=> NULL, //kalau udah ada di isi pake account_id
            'f_length_of_service' => $input['masa_kerja'],
            'f_level_of_work' => $input['level_of_work'],
            'f_survey_created_on' => date('Y-m-d H:i:s'),
            'f_survey_updated_on' => date('Y-m-d H:i:s'),
        ];

        // echo json_encode($data);die();
		$insert_survey = $corporate->insert('trn_survey_empex', $data);

        if($insert_survey){
            // $corporate
            // ->where('sha1(f_id)', $input['idl1'])
            // ->update('table_level_position1', ['f_token' => '(f_token-1)']);
            $corporate->set('f_token', 'f_token - 1', false);
                $corporate->where('sha1(f_id)', $input['idl1']);
                $corporate->update('table_level_position1');

        }

        $row	= $corporate->get_where('trn_survey_empex', array('f_email' => $input['email']))->row_array();


        //send email
		$jawab = json_decode($row['f_survey'],true);
		// $image_profil = getFotoProfil($row['f_email'],$row['f_bahasa']);
		$to_pdf = array(
			'image_profil' => $image_profil,
			'f_survey_username' => $row['f_survey_username'],
			'f_survey_password' => $row['f_survey_password'],
			'f_email' => $row['f_email'],
			'f_bahasa' => $row['f_bahasa'],
			'report_type' => $row['f_report_type'],
			'topten' => $jawab['topten'],
			'total_dimensi' => $jawab['total_dimensi'],
			'soal_perdimensi' => $jawab['soal_perdimensi'],
			'tgl_selesai' => $row['f_survey_updated_on'],
		);

        // echo json_encode($to_pdf);die();
		// if($row['f_report_type'] == 5 || $row['f_report_type'] == 6)  {
		// 	createPDF5($to_pdf, 'F');
		// } else if($row['f_report_type'] == 10 || $row['f_report_type'] == 11)  {
		// 	createPDF10($to_pdf, 'F');
		// } else if($row['f_report_type'] == 45 || $row['f_report_type'] == 65)  {
		// 	createPDF45($to_pdf, 'F');
		// } else {
		// 	createPDF5($to_pdf, 'F');
		// 	//createPDF($to_pdf);
		// }

    // $html = $this->load->view('corporate/email_template_user',true);
    $email = $input['email'];
    // CEK USER APAKAH SUDAH ADA ATAU BELUM
    $corporate->where('email', $email);
    $cek_user = $corporate->get('users')->row_array();

    if (!$cek_user) {
        $group_id = 3;
        $params = ['cost' => ($group_id == 1) ? 12 : 10,];
        $gpassword = $input['email'];
        $password = password_hash($gpassword, PASSWORD_BCRYPT, $params);

        $data_user = array(
            'ip_address' => $this->input->ip_address(),
            'username' => html_escape($input['name'], true),
            'first_name' => html_escape($input['name'], true),
            'password' => $password,
            'email' => $email,
            'created_on' => time(),
            'active' => 1,
            'phone' => NULL,
            'f_account_id' => 0,
        );

        $corporate->insert('users', $data_user);
        $user_id = $corporate->insert_id();
        $group_user = array(
            'user_id' => $user_id,
            'group_id' => 3
        );
        $corporate->insert('users_groups', $group_user);


        $tmp_email = array(
            'user' => html_escape($this->input->post('f_survey_username', true)),
            'email' => $input['email'],
            'pass' => $gpassword,
            'status' => 'edit',
        );
    } else {
        $tmp_email = array(
            'user' => $cek_user['username'],
            'email' => $email,
            'pass' => '(gunakan password sebelumnya, silahkan reset password jika lupa)',
            'status' => 'edit',
        );
    }

    $tmp_email['link_isi'] = 'https://app.talentdna.me/';
    $html = $this->load->view('vw_email_akses_quota', $tmp_email, true);

    $this->email->clear();
    $this->email->set_newline("\r\n");
    $this->email->from('info@talentdna.me', 'Talent DNA');
    $this->email->to($input['email']);
    $this->email->bcc('esqtraining2@esq165.co.id');
    $this->email->subject('Selamat! Keunikan TalentDNA Anda Telah Tersedia dalam Report');
    $this->email->message($html);
    $this->email->send();
    // $nama_file = 'Result_' . str_replace(" ", "_", $to_pdf['f_survey_username']) . '-' . str_replace(" ", "_", $to_pdf['f_email']) . '.pdf';
    // chmod(FCPATH . 'assets/pdf/' . $nama_file, 0777);
    // $this->email->attach(FCPATH . 'assets/pdf/' . $nama_file, 'attachment', 'Result_' . str_replace(" ", "_", $to_pdf['f_survey_username']) . '.pdf');
    // if ($cek->f_report_type == 6 || $cek->f_report_type == 11 || $cek->f_report_type == 65) {
    //     $nama_file_career = 'Career_' . str_replace(" ", "_", $to_pdf['f_survey_username']) . '-' . str_replace(" ", "_", $to_pdf['f_email']) . '.pdf';
    //     chmod(FCPATH . 'assets/pdf/' . $nama_file_career, 0777);
    //     $this->email->attach(FCPATH . 'assets/pdf/' . $nama_file_career, 'attachment', 'Career_' . str_replace(" ", "_", $to_pdf['f_survey_username']) . '.pdf');
    // }
    // if ($this->email->send()) {
    //     unlink(FCPATH . 'assets/pdf/' . $nama_file);
    //     if ($cek->f_report_type == 6 || $cek->f_report_type == 11 || $cek->f_report_type == 65) unlink(FCPATH . 'assets/pdf/' . $nama_file_career);
    // }
        
    $response = array(
        'status' => 200,
        'msg' => 'Sukses melakukan input, terimakasih atas partisipasi Anda'
    );

    echo json_encode($response);
	}

    function fnTrx_surveyDetail($id)
	{

		// error_reporting(-1);
		// ini_set('display_errors', 1);
        $corporate = $this->load->database('db_prod_corporate', true);

		//$row	= $this->mo_trx_survey->getDataId($id);
		$row	= $corporate->get_where('trn_survey_empex', array('f_email' => $id))->row_array();
		//opn($row);die();
		$jawab = json_decode($row['f_survey'],true);
		//opn($jawab['topten']);
		$image_profil = getFotoProfil($row['f_email'],$row['f_bahasa']);
		$to_pdf = array(
			'image_profil' => $image_profil,
			'f_survey_username' => $row['f_survey_username'],
			'f_survey_password' => $row['f_survey_password'],
			'f_email' => $row['f_email'],
			'f_bahasa' => $row['f_bahasa'],
			'report_type' => $row['f_report_type'],
			'topten' => $jawab['topten'],
			'total_dimensi' => $jawab['total_dimensi'],
			'soal_perdimensi' => $jawab['soal_perdimensi'],
			'tgl_selesai' => $row['f_survey_updated_on'],
		);
		if($row['f_report_type'] == 5 || $row['f_report_type'] == 6)  {
			createPDF5($to_pdf);
		} else if($row['f_report_type'] == 10 || $row['f_report_type'] == 11)  {
			createPDF10($to_pdf);
		} else if($row['f_report_type'] == 45 || $row['f_report_type'] == 65)  {
			createPDF45($to_pdf);
		} else {
			createPDF5($to_pdf);
			//createPDF($to_pdf);
		}
		//createPDF($to_pdf);
	}

	function myReport($id)
	{
		$row	= $corporate->get_where('trn_survey_empex', array('f_survey_password' => html_escape($id)))->row_array();
		//opn($row);die();
		$jawab = json_decode($row['f_survey'], true);
		//opn($jawab['topten']);

		$to_pdf = array(
			'f_survey_username' => $row['f_survey_username'],
			'f_survey_password' => $row['f_survey_password'],
			'f_email' => $row['f_email'],
			'f_bahasa' => $row['f_bahasa'],
			'report_type' => $row['f_report_type'],
			'topten' => $jawab['topten'],
			'total_dimensi' => $jawab['total_dimensi'],
			'soal_perdimensi' => $jawab['soal_perdimensi'],
			'tgl_selesai' => $row['f_survey_updated_on'],
		);
		if ($row['f_report_type'] == 5) {
			createPDF5($to_pdf);
		} else if ($row['f_report_type'] == 10) {
			createPDF10($to_pdf);
		} else if ($row['f_report_type'] == 45 || $row['f_report_type'] == 65) {
			createPDF45($to_pdf);
		} else {
			createPDF5($to_pdf, 'F');
			//createPDF($to_pdf);
		}
		die();
	}

}
