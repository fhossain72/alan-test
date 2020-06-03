<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$data = array();
		$data['page_title'] = 'Coming Soon';

		// load the views
		$data['layout'] = $this->load->view('coming_soon', $data, TRUE);
		$this->load->view('template', $data);
	}

	public function pdf_test(){
		//load mPDF library
		$this->load->library('pdf');
		$pdf = $this->pdf->load();;

		$pdfHtml = '<table width="100%" class="table table-striped table-bordered table-hover" id="">
                                            <thead>
                                            <tr>
                                                <th class="text-center">SL#</th>
                                                <th class="text-center">SR</th>
                                                <th class="text-center">Mobile</th>
                                                <th class="text-center">Area</th>
                                                <th class="text-center">Memo</th>
                                                <th class="text-center" width="25%">Product</th>
                                                <th class="text-center">SKU</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-right">Sub Total</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                                                                        <tr>
                                                                                                                                    <td class="text-center" rowspan="1" style="vertical-align: middle;">1</td>
                                                                    <td class="text-center" rowspan="1" style="vertical-align: middle;">Md. Zaman</td>
                                                                    <td class="text-center" rowspan="1" style="vertical-align: middle;">01825315195</td>
                                                                    <td class="text-center" rowspan="1" style="vertical-align: middle;">Banglabazar</td>
                                                                    <td class="text-center" rowspan="1" style="vertical-align: middle;">1</td>
                                                                                                                                    <td class="text-center">Sonic Lascha Shemai</td>
                                                                    <td class="text-center">200 gm</td>
                                                                    <td class="text-center">50</td>
                                                                    <td class="text-right">1,210.00</td>
                                                                                                                                    <td class="text-right" rowspan="1" style="vertical-align: middle;">1,210.00</td>
                                                                                                                            </tr>
                                                                                                </tbody>
                                        </table>';
		$pdfStylesheet = file_get_contents(FCPATH . 'assets/css/bootstrap.min.css');
		$pdfStylesheet .= file_get_contents(FCPATH . 'assets/pdf/pdf.css');

		$pdfName = 'manual_score.pdf';

		$pdf->SetTitle('Test pdf');
		$pdf->SetSubject('Test pdf');
		$pdf->WriteHTML($pdfStylesheet, 1);
		$pdf->WriteHTML($pdfHtml, 2);
		$pdf->Output($pdfName, 'D');
	}


	public function db_backup(){
		// Load the DB utility class
		$this->load->dbutil();

		// Backup your entire database and assign it to a variable
		$backup = $this->dbutil->backup();

		// Load the file helper and write the file to your server
		$this->load->helper('file');
		$file_name = date('Y-m-d-H-i-s').'-backup.gz';
		write_file('assets/backup/'.$file_name, $backup);

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download($file_name, $backup);
	}
}