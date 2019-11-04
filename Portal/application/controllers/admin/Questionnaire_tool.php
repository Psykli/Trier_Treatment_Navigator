<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

class Questionnaire_tool extends CI_Controller
{
    private const QUESTIONNAIRE_BOW_XML_PATH = APPPATH.'views\\patient\\questionnaire\\bows\\';

    function __construct( )
    {
        parent::__construct( );
        $this->data = array(HEADER_STRING => array('title' => 'Fragebogen-Tool'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this -> load -> Model( 'Questionnaire_tool_model' );
        $this -> load -> Model( 'Questionnaire_model' );
        $this -> load -> Model( 'Patient_model' );
        $this-> lang -> load( 'admin_questionnaire_tool_lang' );

        if( $this -> session_model -> is_logged_in( $this -> session -> all_userdata( ) ) )
        {
            $this -> data[TOP_NAV_STRING]['username'] = $this -> session -> userdata( 'username' );
            $this -> data[CONTENT_STRING]['userrole'] = $this -> membership_model -> get_role( $this -> data[TOP_NAV_STRING]['username'] );
            
            if( $this -> data[CONTENT_STRING]['userrole'] !== 'admin' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            redirect( 'login' );
        }

        $this -> template -> set( HEADER_STRING, 'all/header', $this -> data[HEADER_STRING] );
        $this -> template -> set( FOOTER_STRING, 'all/footer', $this -> data[FOOTER_STRING] );
        
        $this->evaluationXSL = "application/views/patient/questionnaire/feedback.xsl";
    }//__construct

    public function index( )
    {  
        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/overview', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//index()

    public function patientenverwaltung( )
    {
        //search for patients
        if( $this -> input -> post( 'patientcode' ) || $this -> input -> post( 'therapist' ) ) {
            $this -> data['content']['patients'] = $this -> Patient_model -> search_patients( $this -> data[TOP_NAV_STRING]['username'], $this -> input -> post( 'patientcode' ), $this -> input -> post( 'therapist' ), 'CODE, THERAPIST', $this -> data[CONTENT_STRING]['userrole'] );
            $this -> data['content']['searched_patientcode'] = $this -> input -> post( 'patientcode' );
            $this -> data['content']['searched_therapist'] = $this -> input -> post( 'therapist' );

            $this -> data['content']['released_questionnaires'] = [];
            foreach($this -> data['content']['patients'] as $patient) {
                $this -> data['content']['released_questionnaires'][$patient -> CODE] = $this -> Questionnaire_tool_model -> get_released_questionnaires( $patient -> CODE, 0 );
            }
        }
        
        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/patientenverwaltung', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//patientenverwaltung()

    public function show_questionnaire_list($patientcode){
        $questionnaires = $this -> Questionnaire_tool_model -> get_released_questionnaires( $patientcode, 1 , true);
        
        $sorted_quests = array();
        foreach($questionnaires as $quest){
            $sorted_quests[$quest->tablename][] = $quest;
        }
        
        $questionnaire_list = $this -> Questionnaire_tool_model -> get_all_questionnaire( );
        $batteries = $this -> Questionnaire_tool_model -> get_all_batteries( );
        
        $this->data['content']['questionnaires'] = $questionnaires;
        $this->data['content']['sorted_quests'] = $sorted_quests;
        $this->data['content']['questionnaire_list'] = $questionnaire_list;
        $this->data['content']['batteries'] = $batteries;
        $this->data['content']['patientcode'] = $patientcode;
       
        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/show_questionnaire_list', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//show_questionnaire_list()

    public function show_questionnaire( $patientcode, $questionnaire, $instance )
    {
        $xml_file = 'application/views/patient/questionnaire/bows/'.$questionnaire;
        $tables = $this -> tables( $xml_file, $this -> evaluationXSL, $patientcode, $instance, 'de' );
        
        $qName = $this -> Questionnaire_tool_model -> get_questionnaire_by_file( $questionnaire );

        $xml_directories = array();
        $xml_directories['xml'] = $xml_file;
        $xml_directories['name'] = $qName -> name;

        $this->data['content']['xml_directories'] = $xml_directories;
        $this->data['content']['evaluationXSL'] = $this->evaluationXSL;
        $this->data['content']['tables'] = $tables;

        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/show_questionnaire', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//show_questionnaire()

    private function tables($xml_directories, $xsl_file, $patientcode, $instance, $language) {
		$content = simplexml_load_file($xml_directories);
        $table = (string) $content['table'];
        $columns = array();
        
        foreach($content->Language as $lang) {
            $attr = (array)$lang->attributes();

            if($attr['@attributes']['lang'] === $language){
                foreach($lang->Set as $set) {
                    $attr = (array)$set->attributes();
                    
                    if ($attr['@attributes']['type'] === "check_t"){
                        foreach($set->Scale->Option as $option) {
                            array_push($columns, $option['column']);
                        }
                    } else {
                        foreach($set->Question as $question) {
                            array_push($columns, $question['column']);
                        }
                    }
                    
                }
            }
        }
        
        $res = $this->SB_Model->get_columns($table, $patientcode, $instance, $columns);
        $col_array = array();

        foreach($res[0] as $key => $item) {
            if(is_numeric($item)){
                intval($item);
            }

            $col_array[$key] = ($item);
        }
        
        $table = $col_array;
			
		return $table;
    }//tables()

    public function quest_release($patientcode) {
        $table = $this->input->post('quest_select');
        $this->data['content']['qid'] = $this -> Questionnaire_tool_model -> get_questionnaire_id_by_table($table);

        $patient = new stdClass();
        $patient -> CODE = $patientcode;
        $patient -> THERAPIST = $this -> Patient_model -> get_therapist_of_patient( $this -> data[TOP_NAV_STRING]['username'], $patientcode, true );
        $this->data['content']['patient'] = $patient;
        
        $this->set_questionnaire_release_information($qid, $this->data['content']['patient']);

        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/quest_release', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//quest_release()

    public function insert_questionnaire($patientcode, $questionnaire, $therapist = "admin")
    {
        $instancePost = $this -> input -> post('instance');

        if($instancePost !== "") {
            $instancePost = intval($instancePost) < 10 ? '0'.intval($instancePost) : $instancePost;
        }
            
        $instance = $this -> input -> post('instance_prefix') . $instancePost; 
        $interval = $this -> input -> post('interval');
        $start = $this -> input -> post('start');

		$this -> Questionnaire_tool_model -> insert_questionnaire ( $therapist, $patientcode, $questionnaire, $instance, $start, $interval );
        
        redirect('admin/questionnaire_tool/show_questionnaire_list/'.$patientcode);
    }//insert_questionnaire()

    public function battery_release( $patientcode ) {
        $bid = $this->input->post('battery_select');
        $questionnaires = $this -> Questionnaire_tool_model ->get_all_questionnaire_by_battery($bid);
        
        $patient = new stdClass();
        $patient -> CODE = $patientcode;
        $patient -> THERAPIST = $this -> Patient_model -> get_therapist_of_patient( $this -> data[TOP_NAV_STRING]['username'], $patientcode, true );
        
        $this->data['content']['battery'] = $bid;
        $this->data['content']['questionnaires'] = $questionnaires;
        $this->data['content']['patient'] = $patient;
        
        $this->set_questionnaire_release_information($questionnaires[0]->qid, $patient);

        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/battery_release', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//battery_release()

    public function insert_questionnaire_batterie_patient( $patientcode, $bid, $therapist )
    {
        if ( empty($therapist) ){
            $therapist = 'admin';
        }

		$all_questionnaires = $this -> Questionnaire_tool_model -> get_all_questionnaire_by_batterie( $bid );
        $instancePost = $this -> input -> post('instance'); 
        
        if($instancePost !== "") {
            $instancePost = intval($instancePost) < 10 ? '0'.intval($instancePost) : $instancePost;
        }
        
        $instance = $this -> input -> post('instance_prefix') . $instancePost;

        foreach ($all_questionnaires as $key => $questionnaire) {
            $interval = $this -> input -> post('interval_'.$key);
            $start = $this -> input -> post('start_'.$key);
            $this -> Questionnaire_tool_model -> insert_questionnaire ($therapist, $patientcode, $questionnaire -> qid,$instance,$start,$interval);
        }

        redirect('admin/questionnaire_tool/show_questionnaire_list/'.$patientcode);
    }//insert_questionnaire_batterie_patient()

    private function set_questionnaire_release_information( $qid, $patient ) {
        $this -> data['content']['instanceOT'] = $this -> Questionnaire_tool_model -> get_next_instance_of_questionnaire($qid, $patient -> CODE, $patient -> THERAPIST, 'OT');
        $this -> data['content']['instanceZ'] = $this -> Questionnaire_tool_model -> get_next_instance_of_questionnaire($qid, $patient -> CODE, $patient -> THERAPIST, 'Z');	  
        $this -> data['content']['instanceSB'] = $this -> Questionnaire_tool_model -> get_next_sb_instance($qid, $patient -> CODE, $patient -> THERAPIST);

        $this -> data['content']['disableWZ'] = $this->Questionnaire_tool_model->instance_exists($qid, $patient->CODE, $patient->THERAPIST, 'WZ');
        $this -> data['content']['disablePR'] = $this->Questionnaire_tool_model->instance_exists($qid, $patient->CODE, $patient->THERAPIST, 'PR');
        $this -> data['content']['disablePO'] = $this->Questionnaire_tool_model->instance_exists($qid, $patient->CODE, $patient->THERAPIST, 'PO');
    }//set_questionnaire_release_information()

    public function add_questionnaire()
    {
        $xml = scandir(self::QUESTIONNAIRE_BOW_XML_PATH);
        unset($xml[0]); // .
        unset($xml[1]); // ..
        foreach($xml as $key => $val){
            if(preg_match('/.*\.xsl$/',$val)){
                unset($xml[$key]);
            }
        }
        sort($xml);

        $all_questionnaire = $this -> Questionnaire_tool_model -> get_all_questionnaire( );

        foreach ($all_questionnaire as $value) {
            if(!in_array($value->filename,$xml)){
                $this-> Questionnaire_tool_model->remove_questionnaire_DB($value->id);
            }
        }
        $process_scales = array();
        $status_scales = array();
        $process_item_invert = array();
        $status_item_invert = array();
        $process_info = array();
        $status_info = array();

        $item_info = array();

        foreach($xml as $file)
        {
            $quest = simplexml_load_file(self::QUESTIONNAIRE_BOW_XML_PATH.$file);
            $attr = $quest->attributes();
            $table = (array)$attr['table'];
            $table = $table[0];
            $lang_array = array();

            foreach($quest->Language as $language)
            {
                $attr = $language->attributes();
                $lang = (array)$attr['lang'];
                $lang = $lang[0];
                $header = strip_tags($language->Headline->asXml());
                $desc = strip_tags($language->Info->asXml());
                
                $lang_array[$lang] = array(
                    'header_name' => $header,
                    'description' => $desc
                );
                $i_names = array();
                $i_texts = array();
                foreach($language->Set as $set){
                    $attr = $set->attributes();
                    $set_type = (array)$attr['type'];
                    $set_type = $set_type[0];

                    $iterator = null;
                    switch($set_type){
                        case 'radio':
                        case 'an_radio':
                        case 'textarea':
                        case 'mmm_slider':
                            $iterator = $set->Question;                           
                            break;
                        case 'check_t':
                            $iterator = $set->Scale->Option;
                            break;             
                    }
                    foreach($iterator as $question){
                        $attr = $question->attributes();
                        $item_name = (array)$attr['column'];
                        $item_name = $item_name[0];
                        if(isset($question->Text)){
                            $text = strip_tags($question->Text->asXml());
                        } else {
                            $text = strip_tags($question->asXml());
                        }
                        
                        $i_names[] = $item_name;
                        $i_texts[] = $text; 
                    }
                }
                if(!isset($item_info[$table])){
                    $item_info[$table] = array();
                }
                $item_info[$table][$lang] = array('item_names' => $i_names, 'item_texts' => $i_texts);
            }

            $this-> Questionnaire_tool_model->add_or_update_item_infos($item_info);
            $this-> Questionnaire_tool_model->add_or_update_questionnaire_DB($table, $file, $lang_array);

            foreach($quest->Data->Scale as $scale){
                $a = $scale->attributes();
                $a = (array)$a['type'];
                $a = $a[0];
                
                $name = strip_tags($scale->Name->asXml());
                $title = strip_tags($scale->Title->asXml());
                $min = strip_tags($scale->Min->asXml());
                $max = strip_tags($scale->Max->asXml());
                $desc = strip_tags($scale->Description->asXml());
                
                if($a === 'process'){
                    if(!isset($process_info[$table])){
                        $process_info[$table] = array();
                    }
                    $process_info[$table][$name]= array('title' => $title, 'min' => $min, 'max' => $max, 'description' => $desc);
                } else {
                    $mean = strip_tags($scale->Mean->asXml());
                    $sd = strip_tags($scale->SD->asXml());
                    $low = strip_tags($scale->Low->asXml());
                    $mid = strip_tags($scale->Mid->asXml());
                    $high = strip_tags($scale->High->asXml());
                    $status_info[$table][$name]= array('title' => $title, 'min' => $min, 'max' => $max, 'mean' => $mean, 'sd' => $sd, 'low' => $low, 'mid' => $mid, 'high' => $high, 'description' => $desc);
                }
                $items = $scale->Items->children();
                $item_names = array();
                
                foreach($items as $i){
                    $attr = $i->attributes();
                    $invert = (array)$attr['invert'];
                    $invert = $invert[0];
                    $i_name = strip_tags($i->asXml());
                    
                    if($invert != null){
                        if($a === 'process'){
                            $process_item_invert[$name][$i_name] = $invert;
                        } else {
                            $status_item_invert[$name][$i_name] = $invert;
                        }
                        
                    }
                    
                    $item_names[] = $i_name;
                }
                if($a === 'process'){
                    $process_scales[$name][$table] = $item_names;
                } else {
                    $status_scales[$name][$table] = $item_names;
                }
                
            }        
        }

        $this->Questionnaire_tool_model->add_or_update_process_scales($process_scales, $process_item_invert, $process_info);
        $this->Questionnaire_tool_model->add_or_update_status_scales($status_scales, $status_item_invert, $status_info);

        $this->data['content']['all_questionnaire'] = $this -> Questionnaire_tool_model -> get_all_questionnaire( );
        
        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/add_questionnaire', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//add_questionnaire()

    public function do_upload()
    {
        $config['upload_path'] = APPPATH.'\\views\\patient\\questionnaire\\bows\\';
		$config['allowed_types'] = 'xml';
        $config['max_size']	= '20';
        
        $this-> load -> library('upload', $config);

        if(!$this-> upload -> do_upload()) //Datei verstößt gegen geltende Upload-Parameter
		{
			$error = array('error' => $this->upload->display_errors());
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
        }
        
        $this-> data['content']['all_questionnaire'] = $this-> Questionnaire_tool_model -> get_all_questionnaire();

        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/add_questionnaire', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//do_upload()

    public function edit_questionnaire($qid)
    {
        if($this -> input -> get('creation_successful')) {
            $this->data['content']['creation_successful'] = true;
        }

        $quest = $this-> Questionnaire_tool_model->get_questionnaire($qid);
        $xml = simplexml_load_file(self::QUESTIONNAIRE_BOW_XML_PATH.$quest[0]->filename);

        if($xml === FALSE) {
            //XML file is either not well-formed or doesn't exist
            show_error(lang('questionnnaire_tool_load_error'), 404);
        }

        $attr = $xml->attributes();
        $table = (array)$attr['table'];
        $table = $table[0];
        $date = (array)$attr['date'];
        $date = $date[0];
        
        $languages = array();
        $langSets = array();
        $langCaptions = array();

        foreach($xml->Language as $language) {
            
            $type_array = array();
            $sets = array();
            $attr = $language->attributes();
            
            $lang = (array)$attr['lang'];
            $languages[] = $lang[0];

            foreach($language->Set as $set) {
                $attr = $set->attributes();
                $attr = (array)$attr['type'];
                $type_array[] = $attr[0];
                $sets[] = $set;
            }
            
            $langSets[] = $sets;
        }

        $this->data['content']['fields'] = $this->constructDefaultFields($langSets,$type_array);
        
        $this->data['content']['languages'] = $languages;
        $this->data['content']['table'] = $table;
        $this->data['content']['dateField'] = $date;
        
        $this->data['content']['qid'] = $quest[0]->qid;
        $this->data['content']['tablename'] = $quest[0]->tablename;
        $this->data['content']['filename'] = $quest[0]->filename;
        $this->data['content']['names'] = $quest[0]->header_name;
        $this->data['content']['descriptions'] = $quest[0]->description;
        $this->data['content']['xml_filename'] = $quest[0]->filename;
        
        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/edit_questionnaire', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//edit_questionnaire()

    public function create_questionnaire($error_message = NULL)
    {
        if($this -> input -> get('creation_error')) {
            $this->data['content']['creation_error'] = true;
        }

        $xml = simplexml_load_file(self::QUESTIONNAIRE_BOW_XML_PATH.'_questionnaire_standard_template.xml');

        if($xml === FALSE) {
            //XML file is either not well-formed or doesn't exist
            show_error(lang('questionnnaire_tool_load_template_error'), 404);
        }

        $attr = $xml->attributes();
        $table = (array)$attr['table'];
        $table = $table[0];
        
        $languages = array();
        $langSets = array();
        $langCaptions = array();

        foreach($xml->Language as $language) {
            
            $type_array = array();
            $sets = array();
            $attr = $language->attributes();
            
            $lang = (array)$attr['lang'];
            $languages[] = $lang[0];

            foreach($language->Set as $set) {
                $attr = $set->attributes();
                $attr = (array)$attr['type'];
                $type_array[] = $attr[0];
                $sets[] = $set;
            }
            
            $langSets[] = $sets;
        }

        $this->data['content']['fields'] = $this->constructDefaultFields($langSets,$type_array);
        
        $this->data['content']['languages'] = $languages;
        $this->data['content']['table'] = $table;
        
        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/create_questionnaire', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//create_questionnaire()

    public function save_created_xml()
    {
        $form_array = $this->input->post('form');
        $languages = !is_null($this-> input -> post('languages')) ? $this-> input -> post('languages') : '';
        $languages = json_decode($languages);
        
        $questionnaire_name = "";
        $questionnaire_description = "";

        //Filter out the form fields for the name and description of the questionnaire,
        //so the user can set the information in the Formbuilder form but it doesn't appear in the final questionnaire shown to the patients.
        foreach ($form_array as $key => $form_element) {
            if($form_element['name'] === "questionnaire_name") {
                $questionnaire_name = $form_element['value'] === NULL ? '' : $form_element['value'];
                unset($form_array[$key]);
            }
            else if($form_element['name'] === "questionnaire_description") {
                $questionnaire_description = $form_element['value'] === NULL ? '' : $form_element['value'];
                unset($form_array[$key]);
            }
        }

        if($questionnaire_name === "" || $questionnaire_description === "") {
            echo site_url().'/admin/Questionnaire_tool/create_questionnaire/?creation_error=true';
        }
        else {
            $normalized_questionnaire_name = str_replace(' ', '-', $questionnaire_name); // Replaces all spaces with hyphens
            $normalized_questionnaire_name = preg_replace('/[^A-Za-z0-9\-]/', '', $normalized_questionnaire_name ); // Removes special chars
            
            $name_is_duplicate = $this -> Questionnaire_tool_model -> does_questionnaire_exist($normalized_questionnaire_name);
            while($name_is_duplicate) {
                //a questionnaire with the chosen name already exists, generate a new one to avoid duplicates because it gets used in the XML-filename
                $normalized_questionnaire_name = $normalized_questionnaire_name.rand(0, 9);
                $name_is_duplicate = $this -> Questionnaire_tool_model -> does_questionnaire_exist($normalized_questionnaire_name);
            }

            $form_array = array_values($form_array);

            //TODO Temporary workaround for the language issue with creating new questionnaires.
            //(Languages don't get sent with the other formbuilder fields, when improving it check how it's done in edit_questionnaire?)
            $form_array_final = array();
            for($i = 0; $i < sizeof($languages); $i++) {
                $form_array_final[$i] = $form_array;
            }

            $new_questionnaire_id = $this -> save_xml($form_array_final, $languages, $questionnaire_name, $questionnaire_description, $normalized_questionnaire_name);
            //JavaScript code redirects the user to the edit page of the created questionnaire once the response to the ajax call has been received
            echo site_url().'/admin/Questionnaire_tool/edit_questionnaire/'.$new_questionnaire_id.'?creation_successful=true';
        }
    }//save_created_xml()

    private function constructDefaultFields($langSets, $types){
        $result = array();

        foreach($langSets as $sets){
            $defaultFields = array();

            foreach($sets as $key => $set){
                $setAttr = $set->attributes();
                
                if(isset($setAttr['parentQuestion'])){
                    $parent = (array)$setAttr['parentQuestion'];
                    $parent = $parent[0];
                    
                }
                
                $triggers = '';
                
                foreach($set->Trigger as $t){
                    $triggers .= $t->__toString();
                }
                
                $fieldArray = array();

                switch($types[$key]){
                    case 'radio':
                    case 'an_radio':
                        foreach($set->Question as $q){
                            $column = $q->attributes();
                            $column = (array)$column['column'];
                            $values = array();

                            foreach($set->Scale->Option as $option){
                                $text = $option->Text->__toString();
                                $value = $option->Value->__toString();
                                $values[] = (object)['label' => $text, 'value' => $value];
                            }

                            $fieldArray = ['type' => 'radio-group', 'label' => $q->__toString(), 'name' => $column[0], 'values' => $values]; 
                            
                            if(isset($parent)){
                                $fieldArray['parentName'] = $parent;
                                $fieldArray['parentTriggers'] = $triggers;
                            }
                            
                            $field = (object) $fieldArray;
                            $defaultFields[] = $field;
                        }
                        break;
                    case 'mm_slider':
                    case 'mmm_slider':
                        $min = $set->Scale->MinValue->__toString();
                        $max = $set->Scale->MaxValue->__toString();

                        foreach($set->Question as $q){
                            $attr = $q->attributes();
                            $column = (array)$attr['column'];
                            $minText = $q->Min->__toString();
                            $maxText = $q->Max->__toString();
                        
                            $fieldArray = ['type' => 'slider', 'label' => strip_tags($q->Text->asXml()), 'name' => $column[0], 'minText' => $minText, 'maxText' => $maxText, 'minVal' => $min, 'maxVal' => $max]; 
                            
                            if(isset($parent)){
                                $fieldArray['parentName'] = $parent;
                                $fieldArray['parentTriggers'] = $triggers;
                            }

                            $field = (object) $fieldArray;
                            $defaultFields[] = $field;
                        }
                        break;
                    case 'input':
                        $attr = $set->attributes();
                        $popover = (array)$attr['popover'];

                        foreach($set->Question as $q){                      
                            $values = array();
                            $attr = $q->attributes();
                            $length = (array)$attr['maxlength'];
                            $column = (array)$attr['column'];
                            $size = (array)$attr['size'];
                            $fieldArray = ['type' => 'text', 'label' => $q->__toString(), 'name' => $column[0], 'maxlength' => $length[0], 'size' => $size[0], 'popover' => $popover[0] == 'true'];

                            if(isset($parent)){
                                $fieldArray['parentName'] = $parent;
                                $fieldArray['parentTriggers'] = $triggers;
                            }

                            $field = (object) $fieldArray;
                            $defaultFields[] = $field;
                        }
                        break;
                    case 'check_t':
                        foreach($set->Question as $q){                      
                            $values = array();

                            foreach($set->Scale->Option as $option){
                                $attr = $option->attributes();
                                $column = (array)$attr['column'];
                                $hasCheckbox = (array)$attr['has_checkbox'];
                                $optional = (array)$attr['optional'];
                                $text = $option->Text->__toString();
                                $selected = $hasCheckbox[0] === "true";
                                $values[] = (object)['label' => $text, 'value' => $column[0], 'selected' => $selected];
                            }

                            $fieldArray = ['type' => 'checkbox-group', 'label' => $q->__toString(), 'values' => $values, 'optional' => $optional[0] === 'true'];

                            if(isset($parent)){
                                $fieldArray['parentName'] = $parent;
                                $fieldArray['parentTriggers'] = $triggers;
                            }

                            $field = (object) $fieldArray;
                            $defaultFields[] = $field;
                        }
                        break;
                    case 'textarea':
                        foreach($set->Question as $q){                      
                            $values = array();
                            $attr = $q->attributes();
                            $column = (array)$attr['column'];
                            $optional = (array)$attr['optional'];
                            $rows = $set->Scale->Rows->__toString();
                            $columns = $set->Scale->Cols->__toString();
                            $fieldArray = ['type' => 'textarea', 'label' => $q->__toString(), 'name' => $column[0], 'rows' => $rows, 'cols' => $columns, 'optional' => $optional[0] === 'true'];

                            if(isset($parent)){
                                $fieldArray['parentName'] = $parent;
                                $fieldArray['parentTriggers'] = $triggers;
                            }

                            $field = (object) $fieldArray;
                            $defaultFields[] = $field;
                        }
                        break;
                    case 'radio_head_foot':

                        $header = ['type' => 'header', 'subtype' => 'h3', 'label' => $set->Caption->__toString()];
                        $defaultFields[] = (object) $header;
                        foreach($set->Question as $q){
                            $column = $q->attributes();
                            $column = (array)$column['column'];
                            $left = $q->Head->__toString();
                            $right = $q->Foot->__toString();
                            $values = array();
                            
                            foreach($set->Scale->Option as $option){
                                $text = $option->Text->__toString();
                                $value = $option->Value->__toString();
                                $values[] = (object)['label' => $text, 'value' => $value];
                            }
                            
                            $fieldArray = ['type' => 'radio-group', 'label' => '<- '.$left.' - '.$right.' ->', 'name' => $column[0], 'values' => $values, 'inline' => true]; 
                            
                            if(isset($parent)){
                                $fieldArray['parentName'] = $parent;
                                $fieldArray['parentTriggers'] = $triggers;
                            }

                            $field = (object) $fieldArray;
                            $defaultFields[] = $field;
                        }
                        break;
                    case 'hidden_instance':
                        break;
                }            
            }

            $result[] = $defaultFields;
        }

        return $result;
    }//constructDefaultFields()

    public function save_xml( $form_array = NULL, $languages = NULL, $questionnaire_name = NULL, $questionnaire_description = NULL, $normalized_questionnaire_name = NULL) {
        $new_questionnaire_id = null;
        
        if($form_array !== NULL && $languages !== NULL && $questionnaire_name !== NULL && $questionnaire_description !== NULL && $normalized_questionnaire_name !== NULL) {
            //new XML-file has to be created, data received via method parameters
            $creating_new_questionnaire = true;

            $xml_filename = $normalized_questionnaire_name.".xml";
            $table = $normalized_questionnaire_name;
            $dateField = "DAT";
        }
        else {
            //editing existing XML-file, data received via POST
            $creating_new_questionnaire = false;

            $form_array = !is_null($this-> input -> post('form')) ? $this-> input -> post('form') : '';
            $languages = !is_null($this-> input -> post('languages')) ? $this-> input -> post('languages') : '';
            $languages = json_decode($languages);
            $table = $this->input->post('table');
            $dateField = $this->input->post('dateField');

            $xml_filename = $this-> input -> post('xml_filename');
        }

        $sorted_form = array();
        $current_type = NULL;
        
        foreach($form_array as $key => $form){ //loops over all languages
            $sorted_form[$languages[$key]] = array();
            $last_elem = NULL;
            
            for ($i=0; $i < count($form); $i++) {
                $new_set = false;
                
                /*
                Temporary workaround for a bug with editing the labels.
                If a user appends a space to the label with the formbuilder (even if he later deletes it again),
                then "<br>" gets added to the end of the label name.
                The user can also easily add HTML code automatically by pressing enter or pasting formatted text.
                Basically the input textfield behaves like a textarea which preserves formatted/styled text. 
                This causes this save_xml method to not save the data to the updated XML file properly and
                a corrupted XML file is the result.
                An update of formbuilder may make this workaround obsolete.
                */
                $form[$i]['label'] = strip_tags($form[$i]['label']);
                
                if(!isset($current_type)){
                    $current_type = $form[$i]['type'];
                } else {
                    if($form[$i]['type'] === 'header'){
                        $current_type = $form[$i+1]['type'];
                        $new_set = true;
                    } else {
                        if($current_type !== $form[$i]['type']){
                            $current_type = $form[$i]['type'];
                            $new_set = true;
                        } else {
                            if(isset($last_elem) AND $last_elem['type'] !== 'header'){
                                if(count($last_elem['values']) !== count($form[$i]['values'])){
                                    $new_set = true;
                                } else {
                                    for($k = 0; $k < count($form[$i]['values']); $k++){
                                        if($form[$i]['values'][$k]['label'] !== $last_elem['values'][$k]['label']){
                                            $new_set = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $index = max(0,count($sorted_form[$languages[$key]])-1);
                
                if($new_set){                   
                    $sorted_form[$languages[$key]][$index+1] = array();
                    $sorted_form[$languages[$key]][$index+1][] = $form[$i];
                } else {
                    
                    if(!isset($sorted_form[$languages[$key]][$index])){
                        $sorted_form[$languages[$key]][$index] = array();
                    }

                    $sorted_form[$languages[$key]][$index][] = $form[$i];
                }

                $last_elem = $form[$i];
            }//for
        }//foreach

        $xml_string = '<?xml version="1.0" encoding="UTF-8"?><Questionnaire table="'.$table.'" date="'.$dateField.'">';

        foreach($sorted_form as $lang => $form){
            $xml_string .= '<Language lang="'.$lang.'">';

            if( $creating_new_questionnaire ) {
                //Add the questionnaire name and description to the XML-file too (not just the DB) so the values in the DB
                //don't get purged every time add_or_update_questionnaire_DB() of Questionnaire_tool_model.php gets called.
                $xml_string .= '<Headline alternate="'.$normalized_questionnaire_name.'">'.$questionnaire_name.'</Headline>';
                $xml_string .= '<Info>'.$questionnaire_description.'</Info>';
            }

            foreach($form as $set){
                $index = $set[0]['type'] === 'header' ? 1 : 0;
                $type = $set[$index]['type'];
                $parent = $set[$index]['parentName'];
                $parentQ = !empty($parent) ? 'parentQuestion="'.$parent.'"' : '';
                $trigger_string = '';
                
                foreach(explode(',',$set[$index]['parentTriggers']) as $trigger){
                    if(!empty($trigger)) {
                        $trigger_string .= '<Trigger>'.$trigger.'</Trigger>';
                    }
                }

                switch($type){
                    case 'text':
                        $popover = isset($set[$index]['popover']) ? 'popover="true"' : '';
                        $xml_string .= '<Set type="input" '.$parentQ.' '.$popover.'>';
                        
                        foreach($set as $part){
                            if($part['type'] === 'header'){
                                $xml_string .= '<Description>'.$part['label'].'</Description>';
                            } else {
                                $xml_string .= '<Question column="'.$part['name'].'" size="'.$part['size'].'" maxLength="'.$part['maxlength'].'">'.$part['label'].'</Question>';
                            }
                        }
                        
                        $xml_string .= $trigger_string;
                        $xml_string .= '<Scale></Scale></Set>';
                        break;
                    
                    case 'radio-group':
                        $xml_string .= '<Set type="an_radio" '.$parentQ.' enumerate="yes">';
                        
                        foreach($set as $part){
                            if($part['type'] === 'header'){
                                $xml_string .= '<Description>'.$part['label'].'</Description>';
                            } else {
                                $xml_string .= '<Question column="'.$part['name'].'">'.$part['label'].'</Question>';
                            }
                        }
                        
                        $xml_string .= $trigger_string;
                        $xml_string .= '<Scale>';
                        
                        foreach($part['values'] as $option){
                            $xml_string .= '<Option><Text>'.$option['label'].'</Text><Value>'.$option['value'].'</Value></Option>';
                        }
                        
                        $xml_string .= '</Scale></Set>';
                        break;
                    
                    case 'checkbox-group':
                        $xml_string .= '<Set type="check_t" '.$parentQ.'>';
                        
                        foreach($set as $part){
                            if($part['type'] === 'header'){
                                $xml_string .= '<Description>'.$part['label'].'</Description>';
                            } else {
                                $xml_string .= '<Question>'.$part['label'].'</Question>';
                            }
                        }
                        
                        $xml_string .= $trigger_string;
                        $xml_string .= '<Scale>';
                        $optional = isset($set[$index]['optional']) ? 'optional="true"' : '';
                        
                        foreach($set[$index]['values'] as $option){
                            $check_type = '';
                            if($option['selected'] === 'true'){
                                $check_type = 'has_checkbox="true" has_text="false" '.$optional;
                            } else {
                                $check_type = 'has_checkbox="false" has_text="true" '.$optional;
                            }
                            $xml_string .= '<Option column="'.$option['value'].'" '.$check_type.'><Text>'.$option['label'].'</Text><Value>Ja</Value></Option>';
                        }

                        $xml_string .= '</Scale></Set>';
                        break;
                    
                    case 'slider':
                        $xml_string .= '<Set type="mmm_slider" '.$parentQ.'>';

                        foreach($set as $part){
                            if($part['type'] === 'header'){
                                $xml_string .= '<Description>'.$part['label'].'</Description>';
                            } else {
                                $xml_string .= '<Question column="'.$part['name'].'"><Text>'.$part['label'].'</Text><Min>'.$part['minText'].'</Min><Max>'.$part['maxText'].'</Max></Question>';
                            }
                        }
                        
                        $xml_string .= $trigger_string;
                        $xml_string .= '<Scale><MinValue>'.$set[$index]['minVal'].'</MinValue><MaxValue>'.$set[$index]['maxVal'].'</MaxValue></Scale></Set>';
                        break;
                    
                    case 'textarea':
                        $xml_string .= '<Set type="textarea" '.$parentQ.'>';

                        foreach($set as $part){
                            $optional = isset($part['optional']) ? 'optional="true"' : '';
                            if($part['type'] === 'header'){
                                $xml_string .= '<Description>'.$part['label'].'</Description>';
                            } else {
                                $xml_string .= '<Question column="'.$part['name'].'" '.$optional.'>'.$part['label'].'</Question>';
                            }
                        }
                        
                        $xml_string .= $trigger_string;
                        $xml_string .= '<Scale><Rows>'.$set[$index]['rows'].'</Rows><Cols>'.$set[$index]['cols'].'</Cols></Scale></Set>';
                        break;
                }//switch
            }//foreach
            $xml_string .= '</Language>';
        }//foreach

        $xml_string .= '</Questionnaire>';

        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xml_string);
        $dom->formatOutput = TRUE;
        $xml_string = $dom->saveXML();
        file_put_contents(self::QUESTIONNAIRE_BOW_XML_PATH.$xml_filename, $xml_string);

        if( $creating_new_questionnaire ) {
            $lang_array = array();
            foreach($languages as $language)
            {
                $lang_array[$language] = array(
                    'header_name' => $questionnaire_name,
                    'description' => $questionnaire_description
                );
            }

            $new_questionnaire_id = $this -> Questionnaire_tool_model -> add_or_update_questionnaire_DB($table, $xml_filename, $lang_array);
        }

        return $new_questionnaire_id;
    }//save_xml()

    public function batterieverwaltung()
    {
        $this-> data['content']['all_batteries'] = $this-> Questionnaire_tool_model -> get_all_batteries();
        $this-> data['content']['patients'] = $this-> Patient_model -> get_all_patients( $this -> data[TOP_NAV_STRING]['username'] );

        $this-> data['content']['questionnaires_batteries'] = [];
        $this-> data['content']['z_batteries'] = [];
        $this-> data['content']['section_names_collection'] = [];
        
        foreach($this -> data['content']['all_batteries'] as $batterie) {
            $this-> data['content']['questionnaires_batteries'][$batterie -> id] = $this -> Questionnaire_tool_model -> get_all_questionnaire_by_battery( $batterie -> id );
            $this-> data['content']['z_batteries'][$batterie -> id] = $this -> Questionnaire_tool_model -> get_all_questionnaire_by_battery( $batterie -> id, true);
            $this-> data['content']['section_names_collection'][$batterie -> id] = $this-> Questionnaire_tool_model ->get_section_names($batterie->id);
            $this-> data['content']['questionnaire_list'] = $this -> Questionnaire_tool_model -> get_all_questionnaire( );
        }

        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/batterieverwaltung', $this -> data['content'] );
        $this -> template -> load( 'template' );    
    }//batterieverwaltung()

    public function insert_new_batterie()
    {
        $name = $this-> input -> post('name'); //Eingetragener Name

        if(isset($name)) {
            $this-> Questionnaire_tool_model -> insert_new_batterie_to_DB($name);
        }

        $this->batterieverwaltung();
    }//insert_new_batterie()

    public function delete_batterie($bid)
    {
        $this-> Questionnaire_tool_model -> delete_batterie_from_DB($bid);
        
        $this-> batterieverwaltung();
    }//delete_batterie()

    public function add_section($bid)
    {
        $this-> Questionnaire_tool_model -> add_section_to_batterie($bid);
        
        $this-> batterieverwaltung();
    }//add_section()

    public function delete_section($bid)
    {
        $this-> Questionnaire_tool_model -> delete_section_from_batterie($bid);
        
        $this-> batterieverwaltung();
    }//delete_section()

    public function save_changes($bid)
    {
        $order = $this-> input -> post('order');
        $orderZ = $this-> input -> post('orderZ');
        $sections = explode('?', $order);
        $batterie = $this-> Questionnaire_tool_model->get_battery($bid);

        //Update section_names
        $section_names = $this-> input -> post('section_names'); 
        $this-> Questionnaire_tool_model -> set_section_names($bid, $section_names);

        //Update SectionOrder
        foreach($sections as $section_key => $section)
        {
            $order = explode('&', $section);
            
            foreach($order as $order_key => $quest)
            {
                preg_match('/\d+/',$quest,$id);
                $id = intval($id[0]);
                $this-> Questionnaire_tool_model->save_order_of_item($id,$section_key,$order_key);
                $this-> Questionnaire_tool_model->set_quest_type($id,0);
            }//foreach
        }//foreach

        //Update ZSectionOrder
        $orderZ = explode('&amp',$orderZ);
        foreach($orderZ as $order_key => $quest){
            preg_match('/\d+/',$quest,$id);
            $id = intval($id[0]);
            $this-> Questionnaire_tool_model->save_order_of_item($id,-1,$order_key);
            $this-> Questionnaire_tool_model->set_quest_type($id,1);
        }//foreach
    }//save_changes()
    
    public function add_questionnaire_to_battery()
	{
		$bid = $this-> input -> post('bid');
		$qid = $this-> input -> post('qid');

		if(isset($bid) AND isset($qid)) {
            $this-> Questionnaire_tool_model -> insert_questionnaire_in_battery($bid, $qid);
        }

		$this-> batterieverwaltung();
	}//add_questionnaire_to_battery()

    public function delete_questionnaire_from_battery($bid, $qid)
    {
        $this-> Questionnaire_tool_model -> delete_questionnaire_in_battery($bid, $qid);
        
        $this-> batterieverwaltung();
    }//delete_questionnaire_from_battery()

    public function set_gas($bid)
    {
        $gas = intval($this-> input -> post('checked'));
        $this-> Questionnaire_tool_model->set_gas($bid, $gas);
    }

    private function set_quest_type($hid)
    {
        $is_Z = intval($this-> input ->post('checked'));
        $this-> Questionnaire_tool_model->set_quest_type($hid, $is_Z);
    }//set_quest_type()
    
    public function batterie_feedback($id)
	{
		$this-> data['content']['bid'] = $id;

        $batterie = $this -> Questionnaire_tool_model -> get_all_questionnaire_by_battery($id);
        $this -> data['content']['batterie'] = $batterie;
        $feedback = $this -> Questionnaire_tool_model -> get_feedback_of_batterie($id);
        $this -> data['content']['feedback'] = $feedback;
        
        $process_info = array();
        foreach ($batterie as $key => $quest) {
            $process_info[$quest->tablename] = $this->Questionnaire_model->get_process_scales_info_by_table($quest->tablename);     
        }
        $process_info['HSCL-11'] = array((object) array('name'=>'HSCL-11'));
        $this -> data['content']['process_info'] = $process_info;

        $this -> template -> set( 'top_nav', 'all/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/batterie_feedback', $this -> data['content'] );
        $this -> template -> load( 'template' );
    }//batterie_feedback()
    
    public function feedback_add_text($bid)
    {
        $this -> Questionnaire_tool_model-> add_feedback_item($bid, 'text', $this-> input -> post('textelement'));
        redirect('admin/questionnaire_tool/batterie_feedback/'.$bid);
    }//feedback_add_text()

    public function feedback_add_process($bid)
    {
        $this->Questionnaire_tool_model->add_feedback_item($bid, 'process', $this->input->post('process'));

        redirect('admin/questionnaire_tool/batterie_feedback/'.$bid);
    }//feedback_add_process()

    public function feedback_add_review($bid)
    {
        $this->Questionnaire_tool_model->add_feedback_item($bid, 'review', $this->input->post('review'));

        redirect('admin/questionnaire_tool/batterie_feedback/'.$bid);
    }//feedback_add_review()

    public function feedback_remove_item($id, $bid)
    {
        $this->Questionnaire_tool_model->delete_feedback_item($id, $bid);

        redirect('admin/questionnaire_tool/batterie_feedback/'.$bid);
    }//feedback_remove_item()

    public function feedback_save_order($id)
    {
        $order = $this-> input -> post('order');
        $order_array = explode('&', $order);

        foreach($order_array as $key => $item)
        {
            preg_match('/\d+/',$item,$id);
            $id = intval($id[0]);
            $this-> Questionnaire_tool_model ->save_order_of_feedback_item($id,$key);
        }
    }//feedback_save_order()

    public function set_as_standard_battery($bid)
    {
        $this-> Questionnaire_tool_model -> set_standard_battery($bid);
    }//set_as_standard_battery()
}//class Questionnaire_tool

/* End of file Questionnaire_tool.php */
/* Location: ./application/controllers/admin/Questionnaire_tool.php */