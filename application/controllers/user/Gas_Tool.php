<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Dompdf\Dompdf;

/**
 * Controller for users patients.
 * 
 * @package Controller
 * @subpackage User
 * 
 */
class Gas_tool extends CI_Controller
{
    function __construct()
    {
        parent::__construct( );

        $this->data = array('header' => array('title' => 'Patientendetails'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );

        $this-> load -> Model('Gas_Model');
        $this-> load -> Model('Membership_model');
        $this-> load -> library('dompdf_gen');

        $this->data['top_nav']['username'] = $this -> session -> userdata( 'username' );
        $this->data['content']['userrole'] = $this -> membership_model -> get_role( $this->data['top_nav']['username'] );

        $this -> template -> set('header', 'all/header', $this->data['header']);
        $this -> template -> set( 'footer', 'all/footer', $this->data['footer'] );

        $is_logged_in = $this->session_model->is_logged_in( $this->session->all_userdata() );
        
        if( $is_logged_in )
        {
            $this->data['top_nav']['username'] = $this -> session -> userdata( 'username' );
            $this->data['content']['userrole'] = $this -> membership_model -> get_role( $this->data['top_nav']['username'] );
            
            if( $this->data['content']['userrole'] !== 'user' && $this->data['content']['userrole'] !== 'admin' && $this->data['content']['userrole'] !== 'supervisor' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            $this->template->set('top_nav', 'guest/top_nav', $this->data['top_nav']);
            $this->template->set('content', 'guest/login_form', $this->data['content']);
            $this->template->load('template');
        }

    }//__construct()

    public function index()
    {
        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this -> template -> set('content', 'user/patient/index', $this->data['content']);
        $this -> template -> load('template');
    }//index()

    public function create_gas( $patientcode )
    {
		$this->data['content']['patientcode'] = $patientcode;
        
		$data = $this -> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], 'PR');
        
        if( isset( $data ) AND ( !isset( $this->data['content']['bereiche'] ) ) ) {
            $bereich_counter = 0;
            $bereiche = array();
            $stufen = array();
            $data = (array) $data[0];
            
            for($i = 12; $i <= 91; $i++ ) {
                if($bereich_counter++ % 8 == 0){
                    $bereiche[] = $data['GAS0'.$i];
                } else {
                    $stufen[] = $data['GAS0'.$i];
                }
            }

            $this->data['content']['stufen'] = $stufen;
            $this->data['content']['bereiche'] = $bereiche;
        }

        $this->data['content']['immutable'] = $this -> Gas_Model -> is_immutable($patientcode, $this->data['top_nav']['username']);

        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this -> template -> set('content', 'user/patient/gas/create_gas', $this->data['content']);
        $this -> template -> load('template');
    }//create_gas()

    public function set_gas( $patientcode )
    {
        // Holen der Daten
        $entries = array();
        $missing = array();
        $missing_bereiche = array();
        $bereiche = array();
        $stufen = array();

        for ($i=1; $i <= 10; $i++) { 
            $bereich = $this -> input -> post( 'bereich'.$i  );
            
            if($bereich !== "") {
                $bereiche[] = $bereich;
                $tmp = array();
                $tmp[] = $bereich;
                
                for ($k = 4; $k >= -2; $k--) { 
                    $ziel = $this -> input -> post( 'zielsetzung'.$i.$k );
                    $stufen[] = $ziel;
                    
                    if(($k == 4 OR $k == 0 OR $k == -2) AND $ziel === "") {
                        $missing['ziel_'.$i.'_'.$k] = true;
                        $missing_bereiche[$i] = true;
                    }
                    else {
                        $tmp[] = $ziel;
                    }  
                }

                $entries = array_merge( array_values( $entries ), array_values( $tmp ) );
            }
        }
        
        $immutable = $this -> input -> post( 'submit') === 'immutable';
        
        if( isset( $patientcode ) && isset( $entries ) && empty($missing) && !empty($bereiche))
		{
			$this -> Gas_Model -> insert_update_gas( $patientcode, $entries, $this->data['top_nav']['username'], $immutable );	
        }
        
        if(!$immutable) {
            $this->data['content']['no_entries'] = empty($bereiche);
            $this->data['content']['stufen'] = $stufen;
            $this->data['content']['bereiche'] = $bereiche;
            $this->data['content']['missing'] = $missing;
            $this->data['content']['missing_bereiche'] = $missing_bereiche;

            $this -> create_gas($patientcode);            
        }
        else {
            redirect('user/patient/list/'.$patientcode);
        }
    }//set_gas()

    public function enter_gas( $patientcode )
    {
        $this->data['content']['therapist'] = $this->data['top_nav']['username'];
        $this->data['content']['patientcode'] = $patientcode;
        
        $data = $this -> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username']);
        
        if( isset( $data ) ) {
            $this->data['content']['gas'] = $data;
        }
        else {
            show_error( 'Access denied. It\'s not a patient of yours!', 403 );
        }

        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this -> template -> set('content', 'user/patient/gas/enter_gas', $this->data['content']);
        $this -> template -> load('template');
    }//enter_gas()

    public function activate_gas($patientcode)
    {
        $data = $this -> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], 'PR');

        if( isset( $data ) ) {
            $data_array = (array) $data[0];
             
            $instance_number = $this -> input -> post('instance');
            $instance_prefix = $this -> input -> post('prefix');

            $instance_number = intval($instance_number) < 10 ? '0'.$instance_number : $instance_number;
            $instance = $instance_prefix !== 'PO' ? $instance_prefix.$instance_number : $instance_prefix;
            
            if( isset( $patientcode ) && isset( $data_array ))
            {
                $this -> Gas_Model -> insert_new_z( $patientcode, $instance, $data_array, $this->data['top_nav']['username'] );		
            }
        }
 
        $this -> enter_gas( $patientcode );
    }//activate_gas()

    public function fill_gas($patientcode, $instance)
    {
        $data = $this-> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], $instance);
        $data_pr = $this-> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], 'PR');

        $this->data['content']['patientcode'] = $patientcode;
        $this->data['content']['instance'] = $instance;
        
        if( isset( $data ) ) {
            $bereich_counter = 0;
            $bereiche = array();
            $stufen = array();
            $werte = array();
            $data = (array) $data[0];
            $data_pr = (array) $data_pr[0];

            for ($i=2; $i <=11 ; $i++) { 
                $str = $i < 10 ? '0'.$i : $i;
                $werte[] = $data['GAS0'.$str];
            }
            
            for($i = 12; $i <= 91; $i++ ) {
                if($bereich_counter++ % 8 == 0){
                    if((isset($data['GAS0'.$i]) AND $data['GAS0'.$i] !== "") OR
                        isset($data_pr['GAS0'.$i]) AND $data_pr['GAS0'.$i] !== "") {
                        
                        if(empty($data['GAS0'.$i])){
                            $tmp = $data_pr['GAS0'.$i];
                        } else {
                            $tmp = $data['GAS0'.$i];
                        }

                        $bereiche[] = $tmp;
                    }
                } else {
                    if(empty($data['GAS0'.$i])){
                        $tmp = $data_pr['GAS0'.$i];
                    } else {
                        $tmp = $data['GAS0'.$i];
                    }

                    $stufen[] = $tmp;
                }
                
            }//for
            $this->data['content']['stufen'] = $stufen;
            $this->data['content']['bereiche'] = $bereiche;
            $this->data['content']['werte'] = $werte;

            //TODO is this correct? should it be immutable instead of gasdat
            $this->data['content']['immutable'] = isset($data['GASDAT']);
        }//if

        //set top_nav depending on user_role
        if(!$this->data['content']['sb'])
        {
            $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        }//if

        $this-> template -> set('content', 'user/patient/gas/fill_gas', $this->data['content']);
        $this-> template -> load('template');   
    }//fill_gas()

    public function fill_gas_sb($patientcode, $instance)
    {
        $data = $this -> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], $instance);
        
        if(!isset($data))
        {
            $data = $this-> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], 'PR');
            $data_array = (array) $data[0];

            $this-> Gas_Model -> insert_new_z($patientcode, $instance, $data_array, $this->data['top_nav']['username']);
        }//if
        
        $this->data['content']['sb'] = true; 
        $this->fill_gas($patientcode, $instance);
    }//fill_gas_sb()

    public function save_gas( $patientcode, $instance ) {
        $columns = array();
        $filled_values = array();
        $not_filled = false;
        
        $bereiche = $this -> input -> get( 'bereiche' );
        $sb = $this -> input -> get('sb');
        
        for ($i = 1; $i <= $bereiche; $i++) { 
            $value = $this -> input -> post( 'col'.$i  );
            
            if($value === ""){
                $not_filled = true;
            } else {
                $filled_values['col'.$i] = $value;
            }
            
            $str = ($i+1) < 10 ? '0'.($i+1) : ($i+1);           
            $columns['GAS0'.$str] = $value !== "" ? $value : '-9999';
        }

        if( !$not_filled ) {
            $sb_instance = $this->session->userdata('instance');
            
            if(empty($sb_instance) OR $sb_instance == 0) {
                $sb_instance = $this->session->userdata('INSTANCE');
            }
            
            if(empty($sb_instance) OR $sb_instance == 0) {
                $sb_instance = NULL;
            }
            
            $this -> Gas_Model -> save_gas_values( $patientcode, $this->data['top_nav']['username'], $instance, $columns, $sb_instance);
            
            if(!$sb){
                $this->enter_gas($patientcode);
            } else {
                $this->session->set_userdata( array('gas' => true) );
                
                if($this->session->userdata('sb_dynamic')){
                    $section = $this->session->userdata('section');
                    $step = $this->session->userdata('step');
                    $batterie = $this->session->userdata('batterie');
                    
                    $instance = substr($instance,1);
                    
                    if($batterie[$step]->section != $section){
                        $this->session->set_userdata('section',++$section);
                        redirect('patient/sb_dynamic/section_finish');
                    } else {
                        redirect('patient/sb_dynamic/overview/');
                    }
                }

                if($this->session->userdata('patient_vb')){
                    redirect('patient/sb/finish_vb');
                } else {
                    redirect('patient/sb/index/');
                }
            }
        } else {           
            $this->data['content']['not_filled'] = true;
            $this->data['content']['filled_values'] = $filled_values;
            if($sb){
                $this->_fill_gas_sb($patientcode,$instance);
            } else {
                $this->_fill_gas($patientcode,$instance);
            }
        }
    }//save_gas()

    public function delete_gas($patientcode,$instance)
    {
        if( !$this -> Gas_Model -> delete_gas($patientcode, $instance, $this->data['top_nav']['username'])) {
            show_error( 'Access denied. It\'s not a patient of yours!', 403 );
        }

        $this -> enter_gas($patientcode);
    }//delete_gas()

    public function download_gas( $patientcode )
    {
        $data = $this -> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], 'PR');
        
        if(isset($data)) {
            $bereich_counter = 0;
            $bereiche = array();
            $stufen = array();
            $data = (array) $data[0];

            for($i = 12; $i <= 91; $i++ ) {
                if($bereich_counter++ % 8 == 0){
                    $bereiche[] = $data['GAS0'.$i];
                } else {
                    $clearhtml = $data['GAS0'.$i];
                    $stufen[] = $clearhtml;
                }
            }
        }
        else {
            show_error( 'Access denied. It\'s not a patient of yours!', 403 );
        }

        $dompdf = new DOMPDF();
        
		$html =
			"<html>
				<head>
					<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
				
				$html.= '  
					<script type="text/php">
						if (isset($pdf)) 
						{
							// open the PDF object - all drawing commands will
							// now go to the object instead of the current page
							$footer = $pdf->open_object();
						
							// get height and width of page
							$w = $pdf->get_width();
							$h = $pdf->get_height();
						
							// get font
							$font = Font_Metrics::get_font("sans-serif", "normal");
							$txtHeight = Font_Metrics::get_font_height($font, 10);
						
							// draw a line along the bottom
							$y = $h - 2 * $txtHeight - 24;
							$x = $w/2 - 15;
							$color = array(0, 0, 0);
							
							// set page number on the left side
							//$pdf->page_text($x, $y, "Seite: {PAGE_NUM} von {PAGE_COUNT}", $font, 10, $color);
							// set additional text
							// $text = "Dompdf is awesome";
							// $width = Font_Metrics::get_text_width($text, $font, 8);
							// $pdf->text($w - $width - 16, $y, $text, $font, 8);
						
							// close the object (stop capture)
							$pdf->close_object();
						
							// add the object to every page (can also specify
							// "odd" or "even")
							$pdf->add_object($footer, "all");
						}
					</script>
					<style>
                        @page {
                            margin-top: 0.0cm;
                        }
						.header,
						.footer {
							width: 100%;
							text-align: center;
							position: fixed;
						}
						.header {
							top: 0px;
						}
						.footer {
							bottom: 50px;
						}
						table, th, td { 
                            border-collapse: collapse;
							border: 1px solid; 
						}
						#coll { 
							border-collapse: collapse; 
						}
						caption { 
							text-align: left; 
						}
					</style>
				  </head>
                     <body>';
                $html.='<pre><h3>PatientIn Code: '.$patientcode.'   Zwischenmessung nach Sitzung: ______     Datum: ____.____.________</h3></pre>';
                $html.='<h2>Zielerreichungsskala: Einschätzung der Patientin/des Patienten (GAS)</h2>';

                $html.='
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="28%">Bereich 1<br/>'.$bereiche[0].'</th>
                            <th width="5%" align="center" valign="middle"></th>
                            <th width="28%">Bereich 2<br/>'.$bereiche[1].'</th>
                            <th width="5%"></th>
                            <th width="28%">Bereich 3<br/>'.$bereiche[2].'</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>';
                        $stufebezeichnung = 4;
                        for ($i=0; $i<7; $i++){
                        $html.='<tr>
                            <td align="center" valign="middle">'.$stufen[$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            <td align="center" valign="middle">'.$stufen[7+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            <td align="center" valign="middle">'.$stufen[14+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                        </tr>';
                        $stufebezeichnung = $stufebezeichnung - 0.5;
                        if ($stufebezeichnung > -2){
                            $html.='<tr>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            </tr>';
                            $stufebezeichnung = $stufebezeichnung - 0.5;
                        }
                        }
                    $html.='</tbody>
                </table>
                ';
                

                if (!empty($bereiche[3])){
                    $html.= '<div style="page-break-after:always;"></div>';	
                    $html.='<pre><h3>PatientIn Code: '.$patientcode.'</h3></pre>';
                    $html.='<h2>Zielerreichungsskala: Einschätzung der Patientin/des Patienten (GAS)</h2>';
                    $html.='
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="28%">Bereich 4<br/>'.$bereiche[3].'</th>
                            <th width="5%" align="center" valign="middle"></th>
                            <th width="28%">Bereich 5<br/>'.$bereiche[4].'</th>
                            <th width="5%"></th>
                            <th width="28%">Bereich 6<br/>'.$bereiche[5].'</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>';
                        $stufebezeichnung = 4;
                        for ($i=0; $i<7; $i++){
                            
                        $html.='<tr>
                            <td align="center" valign="middle">'.$stufen[21+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            <td align="center" valign="middle">'.$stufen[28+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            <td align="center" valign="middle">'.$stufen[35+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                        </tr>';
                        $stufebezeichnung = $stufebezeichnung - 0.5;
                        if ($stufebezeichnung > -2){
                            $html.='<tr>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            </tr>';
                            $stufebezeichnung = $stufebezeichnung - 0.5;
                        }
                        }
                    $html.='</tbody>
                </table>
                ';
                
                }


                if (!empty($bereiche[6])){
                    $html.= '<div style="page-break-after:always;"></div>';	
                    $html.= '<div style="page-break-after:always;"></div>';	
                    $html.='<pre><h3>PatientIn Code: '.$patientcode.'</h3></pre>';
                    $html.='
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="28%">Bereich 7<br/>'.$bereiche[6].'</th>
                            <th width="5%" align="center" valign="middle"></th>
                            <th width="28%">Bereich 8<br/>'.$bereiche[7].'</th>
                            <th width="5%"></th>
                            <th width="28%">Bereich 9<br/>'.$bereiche[8].'</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>';
                        $stufebezeichnung = 4;
                        for ($i=0; $i<7; $i++){
                            
                        $html.='<tr>
                            <td align="center" valign="middle">'.$stufen[42+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            <td align="center" valign="middle">'.$stufen[49+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            <td align="center" valign="middle">'.$stufen[56+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                        </tr>';
                        $stufebezeichnung = $stufebezeichnung - 0.5;
                        if ($stufebezeichnung > -2){
                            $html.='<tr>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            </tr>';
                            $stufebezeichnung = $stufebezeichnung - 0.5;
                        }
                        }
                    $html.='</tbody>
                </table>
                ';
                
                }


                if (!empty($bereiche[9])){
                    $html.= '<div style="page-break-after:always;"></div>';	
                    $html.= '<div style="page-break-after:always;"></div>';	
                    $html.='<pre><h3>PatientIn Code: '.$patientcode.'</h3></pre>';
                    $html.='
                <table width="33%">
                    <thead>
                        <tr>
                            <th width="28%">Bereich 10<br/>'.$bereiche[9].'</th>
                            <th width="5%" align="center" valign="middle"></th>
                        </tr>
                    </thead>
                    <tbody>';
                        $stufebezeichnung = 4;
                        for ($i=0; $i<7; $i++){
                            
                        $html.='<tr>
                            <td align="center" valign="middle">'.$stufen[63+$i].'</td>
                            <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                        </tr>';
                        $stufebezeichnung = $stufebezeichnung - 0.5;
                        if ($stufebezeichnung > -2){
                            $html.='<tr>
                                <td align="center" valign="middle"></td>
                                <td align="center" valign="middle">'.$stufebezeichnung.'</td>
                            </tr>';
                            $stufebezeichnung = $stufebezeichnung - 0.5;
                        }
                        }
                    $html.='</tbody>
                </table>
                ';
                }
                
                
						
        $html .= '			
					</font>	
                </body>
            </html>';


		// Convert to PDF
		$dompdf->load_html($html);
        $dompdf->set_paper('a4', 'landscape');
		$dompdf->render();
		$dompdf->stream('GAS_'.$patientcode.'.pdf',array('Attachment'=>1));
    }//download_gas()
}//class Gas_tool