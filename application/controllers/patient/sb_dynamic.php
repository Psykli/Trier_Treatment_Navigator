<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SB_dynamic extends CI_Controller { 

    public function __construct()
    {
        parent::__construct();
        {
        $this->data = array('header' => array('title' => 'Stundenbögen'),
        'top_nav' => array(),
        'content' => array(),
        'footer' => array());            
        $this->load->Model('SB_Model');
        $this->load->Model('Patient_Model');
        $this->load->Model('Gas_Model');
        $this->load->Model('Questionnaire_Model');
        $this->load->Model('Questionnaire_tool_model');
        $this->load->Model("Therapy_Model");
        $this->load->Helper("cookie");
        $this-> lang -> load('sb_lang');
        
        $this-> evaluationXSL = "application/views/patient/questionnaire/bows/feedback.xsl";


        }
    }

    public function index()
    {
        //set_cookie('language', 'de', 0, '/portal/index.php/patient/sb_dynamic/');
        $this->template->set('header', 'all/header_sb', $this->data['header']);
        $this->template->set('top_nav', 'patient/top_nav_sb', $this->data['top_nav']);
        $this->template->set('content', 'patient/sb_dyn/start', $this->data['content']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        $this->template->load('template');
    }

    public function overview()
    {
        // Get Input from view start()
        $patientcode = $this->input->post('patientcode');
        $instance = intval($this->input->post('instance'));
        $therapist = $this->input->post('therapist');
        if($instance == 1) {
            $instance = 2;
        }
        
        $this->data['top_nav']['username'] = $this -> session -> userdata( 'username' );

        //CASE: This instance is not next instance (that would be needed to be verified by ajax_verify_credentials (e.g.: instance has been skipped))
        if(!empty($patientcode) AND $this-> session ->userdata('instance') !== $instance) 
        {   
            //PreSessionChecks
            $has_gas = $this-> SB_Model ->has_gas($patientcode);
            $is_immutable = $this-> Gas_Model ->is_immutable($patientcode, $this->data['top_nav']['username']);
            $view_status = $this-> Patient_model ->get_view_status( $patientcode );
            
            //Skipped an Instance? --> Send Skipped-Mail
            $check_for_skip = $this->input->post('skipped');
            if(!empty($check_for_skip) && $instance > 2)
                $this->skipped_instance_mail($therapist,$patientcode,$instance);

            //Has not filled GAS yet? --> Send GAS-Mail
            if($has_gas AND $instance > 10 AND ($instance-1) % 5 == 0 AND !$is_immutable AND $view_status > 0)
                $this->immutable_gas_mail($therapist,$patientcode,$instance);
            

           //Get Batterie-Data for this User from database
            $batterie = $this->Questionnaire_tool_model->get_sb_batterie($patientcode);
            $patientcode = strtoupper($patientcode);
            $this->SB_Model->insert_into_sb_start($instance,$patientcode,$therapist);
            $PR_date = $this-> SB_Model ->get_PR_date($patientcode);  
            
            //CASE: User is not allowed to fill this sb --> redirect to index()
            if($view_status == 0 OR !isset($batterie)){     
                $therapist_from_subjects = $this->SB_Model->get_therapist($patientcode);
			    $therapist_from_subjects = (is_null($therapist_from_subjects) || strcmp($therapist_from_subjects[0]->THERPIST, "") == 0)  ? "niemand" : $therapist_from_subjects[0]->THERPIST ;
                $this->session->unset_userdata(array('patientcode' => '','instance' => '','therapist' => '','step' => '','batterie' => '','gas' => '','section' => '','sb_dynamic' => ''));
                $this->session->set_userdata( array('CODE' => $patientcode, 'INSTANCE' => ($instance < 10  ? '0'.$instance : $instance), 'THERAPIST' => $therapist,
										'THERAPIST_FROM_SUBJECTS' => $therapist_from_subjects, 'patient_vb' => false, 'patient_nb' => false, 'therapist_tb' => false, 'seen_feedback' => false, 'gas' => false ));
                redirect('patient/sb/index');
            }
            
            //set Userdata for whole Session
            $this->session->unset_userdata(array('CODE' => '','INSTANCE' => '','THERAPIST' => '','THERAPIST_FROM_SUBJECTS' => '','patient_vb' => '','patient_nb' => '','therapist_tb' => '','seen_feedback' => '','gas' => ''));
            $this->session->set_userdata('patientcode', $patientcode);
            $this->session->set_userdata('instance', ($instance < 10  ? '0'.$instance : $instance));
            $this->session->set_userdata('therapist', $therapist);
            $this->session->set_userdata('step', 0);
            $this->session->set_userdata('batterie', $batterie);
            $this->session->set_userdata('gas', false);
            $step = 0;
            $this->session->set_userdata('section', 0);
            $section = 0;
            $this->session->set_userdata('sb_dynamic', true);
            
        } //if CASE:This instance is not next instance
        else 
        {
            //set session->userdata()
            $patientcode = $this->session->userdata('patientcode');
            $instance = intval($this->session->userdata('instance'));
            $therapist = $this->session->userdata('therapist');
            $step = $this->session->userdata('step');
            $section = $this->session->userdata('section');
            $batterie = $this->session->userdata('batterie');
        }

        //check for filled questionnaires
        $has_request = $this->SB_Model->has_filled_request($patientcode);
        $has_pers_dis = $this->SB_Model->has_filled_questionnaire($patientcode, 'abschluss_probatorik_persoenlichkeitsstoerung');
        $has_psypharm = $this->SB_Model->has_filled_questionnaire($patientcode, 'psychopharm_beh');
        $has_gas = $this->SB_Model->has_filled_questionnaire($patientcode, 'gas');

        $zwReminds = $this->Therapy_Model->get_zw_reminds_of_patient($patientcode);
        $haqReminds = $this->Therapy_Model->get_haq_reminds_of_patient($patientcode);

        // assemble data[] and template
        $this->data['content']['has_request'] = $has_request;
        $this->data['content']['has_pers_dis'] = $has_pers_dis;
        $this->data['content']['has_psypharm'] = $has_psypharm;
        $this->data['content']['has_gas'] = $has_gas;
        $this->data['content']['zwReminds'] = $zwReminds;
        $this->data['content']['haqReminds'] = $haqReminds;

        $new_quartal = $this->SB_Model->firstInstanceInQuartal($patientcode,$instance);
        $this->data['content']['new_quartal'] = $new_quartal;
        $this->data['content']['patientcode'] = $patientcode;
        $this->data['content']['instance'] = $instance;
        $this->data['content']['therapist'] = $therapist;
        $this->data['content']['step'] = $step;
        $this->data['content']['section'] = $section;

        $this->data['content']['batterie'] = $batterie;

        $this->template->set('header', 'all/header_sb', $this->data['header']);
        $this->template->set('top_nav', 'patient/top_nav_sb', $this->data['top_nav']);
        $this->template->set('content', 'patient/sb_dyn/overview', $this->data['content']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        $this->template->load('template');

    }//overview()

    public function ajax_validate_credentials()
    {
        $patientcode = $this->input->post('patientcode_post');
        $instance = intval($this->input->post('instance_post'));
        $therapist = $this->input->post('therapist_post');
        $patientcode = strtoupper($patientcode);
        $errors = array();
        $danger = false;

        $this->data['top_nav']['username'] = $this -> session -> userdata( 'username' );

        $view_status = $this->Patient_model->get_view_status( $patientcode );
        $subject = $this-> SB_Model ->is_subject($patientcode);
        if(!$subject){
            $errors[] = array('not_subject',$patientcode);
            $danger = true;
        }

        $is_user = $this->membership_model->get_role( $therapist );
        if($is_user == 'guest'){
            $errors[] = array('not_valid_therapist',$therapist);
            $danger = true;
        }

        $batterie = $this->Questionnaire_tool_model->get_sb_batterie($patientcode);
        if(isset($batterie)){
            $current_instance = $this->SB_Model->get_instance($patientcode, $batterie[0]->tablename);
        } else {
            $current_instance = $this->SB_Model->get_instance($patientcode, 'einzelfragen_patient_sitzungsbogen');
        }
        $current_instance = $current_instance[0]->INSTANCE+1;
        if($current_instance > $instance){
            $errors[] = array('low_instance',$instance, $current_instance);
            $danger = true;
        }

        $allowed_instance = $this->Patient_model->get_sb_allowed($patientcode);
        if(!isset($allowed_instance) OR $allowed_instance->allowed_until_instance < $instance){
            $has_request = $this->SB_Model->has_filled_request($patientcode);
            $has_gas = $this->SB_Model->has_gas($patientcode);
            if(((!$has_request AND $instance >= 10) OR (!$has_gas AND $instance >= 15)) AND $view_status != 0) {
                if(!$has_request){
                    $errors[] = array('request_not_filled', $patientcode);
                }
                if(!$has_gas){
                    $errors[] = array('gas_not_filled', $patientcode);
                }
                // sendet eine E-Mail an den Therapeuten sowie Viola und psyfeedback@uni-trier.de
                $this -> _stopping_sb_tool_mail ($therapist, $patientcode, $instance); 
                $danger = true;
            }
        }

        $correct_therapist = $this-> SB_Model ->get_therapist($patientcode);
        if(strtolower($correct_therapist[0]->THERPIST) !== strtolower($therapist) AND !$danger){
            $errors[] = array('not_therapist',$therapist,$correct_therapist[0]->THERPIST);
            
        }

        if($current_instance < $instance AND !$danger){
            $errors[] = array('not_instance',$instance, $current_instance);
        }
        
       
        if(!empty($errors)){
            $errors[] = array('post',$patientcode,$instance,$therapist);
            echo(json_encode($errors));
        }
        else {
            if($instance == 1){
                $instance = 2;
            }

            $has_gas = $this->SB_Model->has_gas($patientcode);
            $is_immutable = $this -> Gas_Model -> is_immutable($patientcode, $this->data['top_nav']['username']);
            if($has_gas AND $instance > 10 AND ($instance-1) % 5 == 0 AND !$is_immutable AND $view_status > 0 ) {
                $this->immutable_gas_mail($therapist,$patientcode,$instance);
            }

            $this->SB_Model->insert_into_sb_start($instance,$patientcode,$therapist);
            //$PR_date = $this->SB_Model->get_PR_date($patientcode); 
            
            //if(strtotime($PR_date) < strtotime($this->cut_off_date) OR !isset($batterie)){
            if( $view_status == 0 OR !isset($batterie)){    
                $therapist_from_subjects = $this->SB_Model->get_therapist($patientcode);
			    $therapist_from_subjects = (is_null($therapist_from_subjects) || strcmp($therapist_from_subjects[0]->THERPIST, "") == 0)  ? "niemand" : $therapist_from_subjects[0]->THERPIST ;
			    $this->session->unset_userdata(array('patientcode' => '','instance' => '','therapist' => '','step' => '','batterie' => '','gas' => '','section' => '','sb_dynamic' => '', 'acknowledged_missing_data' => ''));
                $this->session->set_userdata( array('CODE' => $patientcode, 'INSTANCE' => ($instance < 10  ? '0'.$instance : $instance), 'THERAPIST' => $therapist,
										'THERAPIST_FROM_SUBJECTS' => $therapist_from_subjects, 'patient_vb' => false, 'patient_nb' => false, 'therapist_tb' => false, 'seen_feedback' => false, 'gas' => false ));
                echo "sb_standard";
            } else {
                $this->session->unset_userdata(array('CODE' => '','INSTANCE' => '','THERAPIST' => '','THERAPIST_FROM_SUBJECTS' => '','patient_vb' => '','patient_nb' => '','therapist_tb' => '','seen_feedback' => '','gas' => '', 'acknowledged_missing_data' => ''));
                $this->session->set_userdata('patientcode', $patientcode);
                $this->session->set_userdata('instance', ($instance < 10  ? '0'.$instance : $instance));
                $this->session->set_userdata('therapist', $therapist);
                $this->session->set_userdata('step', 0);
                $this->session->set_userdata('section', 0);
                $this->session->set_userdata('batterie', $batterie);
                $this->session->set_userdata('sb_dynamic', true);
                $this->session->set_userdata('gas', false);
                if($instance % 5 == 0){
                    $z_batterie = $this->Questionnaire_tool_model->get_sb_batterie($patientcode,true);
                    $z_instance = $instance < 10 ? 'Z0'.$instance : 'Z'.$instance;
                    foreach($z_batterie as $b){
                        $this -> Questionnaire_tool_model -> insert_questionnaire($therapist,$patientcode,$b->qid,$z_instance);
                    }
                }
                echo "sb_dynamic";
            }
        }
    }//ajax_validateCredentials()

    public function section_finish()
    {
        $patientcode = $this-> session -> userdata('patientcode');
        $instance = $this-> session -> userdata('instance');
        $suicide_set = $this-> session -> userdata('suicide_set');

        if ($suicide_set)
        {
            $suicidecolour = $this-> SB_Model -> getSuicideColour($instance, $patientcode);
            $this-> data['content']['suicidecolour'] = $suicidecolour;
            $this-> session -> unset_userdata('suicide_set');
        }

        $this->data['content']['patientcode'] = $patientcode;
        $this->template->set('header', 'all/header_sb', $this->data['header']);
        $this->template->set('top_nav', 'patient/top_nav_sb', $this->data['top_nav']);
        $this->template->set('content', 'patient/sb_dyn/section_finish', $this->data['content']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        $this->template->load('template');
    }//section_finish()

    public function process()
    {
        $patientcode = $this-> session -> userdata('patientcode');
        $instance = $this-> session -> userdata('instance');
        $therapist = $this-> session -> userdata('therapist');

        $this->data['content']['patientcode'] = $patientcode;
        $this->data['content']['instance'] = $instance;
        $this->data['content']['therapist'] = $therapist;

        $data = $this-> Questionnaire_Model -> get_process_data($therapist, $patientcode);
        $batterie = $this-> session -> userdata('batterie');
        $feedback = $this-> Questionnaire_tool_model -> get_feedback_of_batterie($batterie[0]->bid);
        
        $graph_data = $this->Questionnaire_Model -> fetch_hscl_chart_data($patientcode);
        $graphs = array(); //FÄLLT RAUS! --> Erstetzen durch Datenstruktur die Daten enthält
        foreach ($data as $d)
        {
            foreach($feedback as $key => $f)
            {
                if ($f -> type !== 'process')
                {continue;}

                if (strtolower($d['name']) === strtolower($f -> data ))
                {$graphs[$f->feedback_order][] = $d;}
            }
        }
        $this-> data['content']['graphs'] = $graphs; 
        $xml_directories = array();
        foreach ($batterie as $b)
        {
            $f = $this-> Questionnaire_tool_model -> get_review_by_data($b->bid, $b->tablename);
            if(isset($f))
            {
                $xml_directories[$f->feedback_order]['xml'] = 'application/views/patient/questionnaire/bows/'.$b-> filename;
                $xml_directories[$f->feedback_order]['name'] = $b-> header_name[0];
            }
        }
        $tables = $this-> tables ($xml_directories, $this-> evaluationXSL, $patientcode, $instance, 'de');

        $this->data['content']['xml_directories'] = $xml_directories;
        $this->data['content']['evaluationXSL'] = $this->evaluationXSL;
        $this->data['content']['tables'] = $tables;
        $this->data['content']['feedback'] = $feedback;

        $this->template->set('header', 'all/header_sb', $this->data['header']);
        $this->template->set('top_nav', 'patient/top_nav_sb', $this->data['top_nav']);
        $this->template->set('content', 'patient/sb_dyn/process', $this->data['content']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        $this->template->load('template');
    }

    private function tables($xml_directories, $xsl_file, $patientcode, $instance, $language) {
		$tables = array();
		foreach($xml_directories as $dir) {
            $content = simplexml_load_file($dir['xml']);
            $table = (string) $content['table'];
            $columns = array();
            foreach($content->Language as $lang) {
                $attr = (array)$lang->attributes();
                if($attr['@attributes']['lang'] === $language){
                    foreach($lang->Set as $set) {
                        foreach($set->Question as $question) {
                            array_push($columns, $question['column']);
                        }
                    }
                }
            }
            $res = $this->SB_Model->get_columns($table, $patientcode, $instance, $columns);
            $col_array = array();
            foreach($res[0] as $key => $item) {
                $col_array[$key] = intval($item);
            }
            $tables[$table] = $col_array;
			
		}
		return $tables;
	}

    public function gas_feedback($patientcode, $instance)
    {
        $z_instance = ($instace - ($instance%5));
        $z_instance = intval($z_instance) < 10 ? 'Z0'.intval($z_instance) : 'Z'.intval($z_instance);

        $this->data['top_nav']['username'] = $this -> session -> userdata( 'username' );
		$data = $this -> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], $z_instance);
        $data_pr = $this -> Gas_Model -> get_gas_data($patientcode, $this->data['top_nav']['username'], 'PR');

        $this->data['content']['patientcode'] = $patientcode;
        $this->data['content']['instance'] = $instance;
        if(isset($data)){
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
            for($i = 12; $i <= 91; $i++ ){
                
                if($bereich_counter++ % 8 == 0){
                    if((isset($data['GAS0'.$i]) AND $data['GAS0'.$i] !== "") OR
                        isset($data_pr['GAS0'.$i]) AND $data_pr['GAS0'.$i] !== ""){
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
                
            }
            $this->data['content']['stufen'] = $stufen;
            $this->data['content']['bereiche'] = $bereiche;
            $this->data['content']['werte'] = $werte;

            $this->data['content']['immutable'] = isset($data['GASDAT']);
        }

		$p_data = $this-> Questionnaire_model ->get_process_data( $username, $patientcode);
		foreach ($p_data as $entry) {
			if($entry['name'] === 'gas'){
				$gas = $entry;
				break;
			}
		}
		$this->data['content']['gas_process'] = $gas;
		
		$this->template->set('top_nav', 'patient/top_nav_sb', $this->data['top_nav']);
        $this->template->set('content', 'patient/sb_dyn/gas_feedback', $this->data['content']);
		$this->template->set('header', 'all/header', $this->data['header']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);        
        $this->template->load('template');	
    }//gas_feedback()




}//SB_dynamic
?>