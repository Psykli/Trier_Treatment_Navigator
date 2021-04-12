<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SB_dynamic extends CI_Controller { 

    public function __construct()
    {
        parent::__construct();
        $this->data = array(HEADER_STRING => array('title' => 'Stundenbögen'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this->load->Model('SB_model');
        $this->load->Model('Patient_model');
        $this->load->Model('Gas_model');
        $this->load->Model('Questionnaire_model');
        $this->load->Model('Questionnaire_tool_model');
        $this->load->Model("Therapy_model");
        $this->load->Helper("cookie");
        $this-> lang -> load('sb_lang');
        
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);

        $this -> evaluationXSL = "application/views/patient/questionnaire/bows/feedback.xsl";

        if( $this->session_model->is_logged_in( $this->session->all_userdata( ) ) )
        {
            $this->data[TOP_NAV_STRING]['username'] = $this -> session -> userdata( 'username' );
            $this->data[CONTENT_STRING]['userrole'] = $this -> membership_model -> get_role( $this->data[TOP_NAV_STRING]['username'] );
        }
    }//__construct()

    public function index()
    {
        //set_cookie('language', 'de', 0, '/portal/index.php/patient/sb_dynamic/');
        $this->template->set(HEADER_STRING, 'all/header_sb', $this->data[HEADER_STRING]);
        $this->template->set(CONTENT_STRING, 'patient/sb_dyn/start', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//index()

    public function overview()
    {
        // Get Input from view start()
        $patientcode = $this->input->post('patientcode');
        $instance = intval($this->input->post('instance'));
        $therapist = $this->input->post('therapist');
        if($instance == 1) {
            $instance = 2;
        }
        
        //CASE: This instance is not next instance (that would be needed to be verified by ajax_verify_credentials (e.g.: instance has been skipped))
        if(!empty($patientcode) AND $this-> session ->userdata('instance') !== $instance) 
        {   
            //PreSessionChecks
            $has_gas = $this-> SB_Model ->has_gas($patientcode);
            $this->data[CONTENT_STRING]['is_immutable'] = $this-> Gas_Model ->is_immutable($patientcode, $this->data[TOP_NAV_STRING]['username']);
            $view_status = $this-> Patient_model ->get_view_status( $patientcode );
            
            //Skipped an Instance? --> Send Skipped-Mail
            $check_for_skip = $this->input->post('skipped');
            if(!empty($check_for_skip) && $instance > 2)
                $this->skipped_instance_mail($therapist,$patientcode,$instance);

            //Has not filled GAS yet? --> Send GAS-Mail
            if($has_gas AND $instance > 10 AND ($instance-1) % 5 == 0 AND !$this->data[CONTENT_STRING]['is_immutable'] AND $view_status > 0)
                $this->immutable_gas_mail($therapist,$patientcode,$instance);
            

            //Get Batterie-Data for this User from database
            $batterie = $this->Questionnaire_tool_model->get_sb_batterie($patientcode);
            $patientcode = strtoupper($patientcode);
            $PR_date = $this-> SB_Model ->get_PR_date($patientcode);  
            
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

        $new_quartal = $this->SB_Model->firstInstanceInQuartal($patientcode,$instance);
        $this->data[CONTENT_STRING]['new_quartal'] = $new_quartal;
        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        $this->data[CONTENT_STRING]['instance'] = $instance;
        $this->data[CONTENT_STRING]['therapist'] = $therapist;
        $this->data[CONTENT_STRING]['step'] = $step;
        $this->data[CONTENT_STRING]['section'] = $section;

        $this->data[CONTENT_STRING]['batterie'] = $batterie;
        
        $lastHscl = $this->Patient_model->get_last_hscl( $patientcode );
        $this->data[CONTENT_STRING]['over_boundary'] = -1;

        // Wir wollen hier herausfinden wann die Boundary das letzte mal überschritten war. 
        // In BOUNDARY_UEBERSCHRITTEN steht nur wann sie das erste mal überschritten wurde
        for($i = $lastHscl->instance; $i >= $lastHscl->instance-3; $i--){
            $boundary = $this->Patient_Model->get_boundary($patientcode, $i, "BOUNDARY_UEBERSCHRITTEN");

            if($boundary->BOUNDARY_UEBERSCHRITTEN > 0){
                $this->data[CONTENT_STRING]['over_boundary'] = $i;
                break;
            }
        }


        $z_instance = ($instance - ($instance % 5)); 
        $z_instance = intval($z_instance) < 10 ? 'Z0'.intval($z_instance) : 'Z'.intval($z_instance);
        $this->data[CONTENT_STRING]['z_instance'] = $z_instance;

        if($instance % 5 == 0 && ENVIRONMENT === 'development' ) {  
            $this->data[CONTENT_STRING]['has_zwischen'] = $this -> Questionnaire_tool_model -> has_zwischen($patientcode, $z_instance);
        }

        $this->data[CONTENT_STRING]['pr_exists'] = $this-> Gas_Model ->does_pr_exist($patientcode, $this->data[TOP_NAV_STRING]['username']);

        $this->template->set(HEADER_STRING, 'all/header_sb', $this->data[HEADER_STRING]);
        $this->template->set(CONTENT_STRING, 'patient/sb_dyn/overview', $this->data[CONTENT_STRING]);
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

        if(strtolower($patientcode) === 'tsb' OR strtolower($therapist) === 'tsb'){
            echo 'testpatient';
            return;
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
            $errors[] = array('no_battery');
            $danger = true;
        }
        $current_instance = $current_instance[0]->INSTANCE+1;
        if($current_instance > $instance){
            $errors[] = array('low_instance',$instance, $current_instance);
            $danger = true;
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
            $is_immutable = $this -> Gas_Model -> is_immutable($patientcode, $this->data[TOP_NAV_STRING]['username']);
            if($has_gas AND $instance > 10 AND ($instance-1) % 5 == 0 AND !$is_immutable AND $view_status > 0 ) {
                $this->immutable_gas_mail($therapist,$patientcode,$instance);
            }
            
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
        }//else
    }//ajax_validateCredentials()

    public function section_finish()
    {
        $patientcode = $this-> session -> userdata('patientcode');
        $instance = $this-> session -> userdata('instance');
        $suicide_set = $this-> session -> userdata('suicide_set');

        if ($suicide_set)
        {
            $suicidecolour = $this-> SB_Model -> getSuicideColour($instance, $patientcode);
            $this-> data[CONTENT_STRING]['suicidecolour'] = $suicidecolour;
            $this-> session -> unset_userdata('suicide_set');
        }

        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        $this->data[CONTENT_STRING]['view_status'] = $this -> Patient_model -> get_view_status( $patientcode );

        $this->template->set(HEADER_STRING, 'all/header_sb', $this->data[HEADER_STRING]);
        $this->template->set(CONTENT_STRING, 'patient/sb_dyn/section_finish', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//section_finish()

    public function process()
    {
        $patientcode = $this-> session -> userdata('patientcode');
        $instance = $this-> session -> userdata('instance');
        $therapist = $this-> session -> userdata('therapist');

        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        $this->data[CONTENT_STRING]['instance'] = $instance;
        $this->data[CONTENT_STRING]['therapist'] = $therapist;

        $data = $this-> Questionnaire_Model -> get_process_data($therapist, $patientcode);
        $batterie = $this-> session -> userdata('batterie');
        $feedback = $this-> Questionnaire_tool_model -> get_feedback_of_batterie($batterie[0]->bid);
        
       // $graph_data = $this->Questionnaire_Model -> fetch_hscl_chart_data($patientcode);
        $graphs = array(); //FÄLLT RAUS! --> Erstetzen durch Datenstruktur die Daten enthält
        $means = $this->Questionnaire_Model->get_process_scales_data($patientcode);
        $infos = array();
        foreach(array_keys($means) as $key){
            $info[$key] = $this->Questionnaire_Model->get_process_scales_info($key);
        }

        $this-> data[CONTENT_STRING]['hsclData'] = $this->Questionnaire_Model->get_hscl_process_data($patientcode);
        $this-> data[CONTENT_STRING]['means'] = $means;
        $this-> data[CONTENT_STRING]['infos'] = $info;
        $this-> data[CONTENT_STRING]['graphs'] = $graphs; 
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

        $this->data[CONTENT_STRING]['xml_directories'] = $xml_directories;
        $this->data[CONTENT_STRING]['evaluationXSL'] = $this->evaluationXSL;
        $this->data[CONTENT_STRING]['tables'] = $tables;
        $this->data[CONTENT_STRING]['feedback'] = $feedback;

        $this->data[CONTENT_STRING]['view_status'] = $this -> Patient_model -> get_view_status( $patientcode );

        $this->template->set(HEADER_STRING, 'all/header_sb', $this->data[HEADER_STRING]);
        $this->template->set(CONTENT_STRING, 'patient/sb_dyn/process', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//process()

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
	}//tables()

    public function test(){
        $patientcode = '9995P99';
        $batterie = $this->Questionnaire_tool_model->get_sb_batterie($patientcode);
        $instance = $this->SB_Model->get_instance($patientcode, $batterie[0]->tablename);
        $instance = $instance[0]->INSTANCE + 1;

        $this->session->unset_userdata(array('CODE' => '','INSTANCE' => '','THERAPIST' => '','THERAPIST_FROM_SUBJECTS' => '','patient_vb' => '','patient_nb' => '','therapist_tb' => '','seen_feedback' => '','gas' => ''));
        $this->session->set_userdata('patientcode', $patientcode );
        $this->session->set_userdata('instance', ($instance < 10  ? '0'.$instance : $instance));
        $this->session->set_userdata('therapist', 'TT01');
        $this->session->set_userdata('step', 0);
        $this->session->set_userdata('batterie', $batterie);
        $this->session->set_userdata('gas', false);
        $step = 0;
        $this->session->set_userdata('section', 0);
        $section = 0;
        $this->session->set_userdata('sb_dynamic', true);

        redirect('patient/sb_dynamic/overview');
    }//test()

    public function gas_feedback($patientcode, $instance)
    {
        $z_instance = ($instace - ($instance%5));
        $z_instance = intval($z_instance) < 10 ? 'Z0'.intval($z_instance) : 'Z'.intval($z_instance);

        $data = $this -> Gas_Model -> get_gas_data($patientcode, $this->data[TOP_NAV_STRING]['username'], $z_instance);
        $data_pr = $this -> Gas_Model -> get_gas_data($patientcode, $this->data[TOP_NAV_STRING]['username'], 'PR');

        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        $this->data[CONTENT_STRING]['instance'] = $instance;
        
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
            $this->data[CONTENT_STRING]['stufen'] = $stufen;
            $this->data[CONTENT_STRING]['bereiche'] = $bereiche;
            $this->data[CONTENT_STRING]['werte'] = $werte;

            $this->data[CONTENT_STRING]['immutable'] = $data['IMMUTABLE'];
        }

		$username = strtoupper($patientcode) === "9999P99" ? "tsb" : $this->session->userdata('therapist');
		$p_data = $this-> Questionnaire_model ->get_process_data( $username, $patientcode);
        
        foreach ($p_data as $entry) {
			if($entry['name'] === 'gas'){
				$gas = $entry;
				break;
			}
		}
        
        $this->data[CONTENT_STRING]['gas_process'] = $gas;
		
        $this->template->set(CONTENT_STRING, 'patient/sb_dyn/gas_feedback', $this->data[CONTENT_STRING]);
		$this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->load('template');	
    }//gas_feedback()
}//class SB_dynamic

/* End of file sb_dynamic.php */
/* Location: ./application/controllers/patient/sb_dynamic.php */
?>