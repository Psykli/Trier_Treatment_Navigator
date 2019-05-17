<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

class Questionnaire_tool extends CI_Controller
{
    function __construct( )
    {
        parent::__construct( );
        $this -> data = array( 'header' => array( 'title' => 'Fragebogen-Tool' ), 'top_nav' => array( ), 'content' => array( ), 'footer' => array( ) );
        $this->username = $this->session->userdata( 'username' );
        $this->data['top_nav']['username'] = $this->username;
        $this->evaluationXSL = "application/views/patient/questionnaire/feedback.xsl";

        $this -> load -> Model( 'Questionnaire_tool_model' );
        $this -> load -> Model( 'Patient_model' );
        //Check User-Credentials for page access rights.
        $this->is_logged_in = $this-> session_model ->is_logged_in($this->session->all_userdata());
        $this->is_admin = $this -> membership_model -> get_role($this->username) === 'admin';
        
        if( $this->is_logged_in AND $this->is_admin )
        {
            $this-> data['content']['userrole'] = 'admin';
        }
        else //if ($is_logged_in)=FALSE OR ($is_admin)=FALSE
        {
            show_error ('Access denied for your Userrole', 403);
        } // else
    }

    public function index( )
    {  
        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/overview', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }//_index()

    public function patientenverwaltung( )
    {
        $this-> username = $this -> data['top_nav']['username'];

        // Suche nach Patiente
        $patientcode = $this -> input -> post( 'patientcode' );
        $therapist = $this -> input -> post( 'therapist' );         
        $patients = $this -> Questionnaire_tool_model -> search_patients ( $patientcode, $therapist );
        $this->data['content']['patients'] = $patients;
       
        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/patientenverwaltung', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }//_patientenverwaltung()

    public function show_questionnaire_list($patientcode){
        $this->username = $this -> data['top_nav']['username'];

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
       
        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/show_questionnaire_list', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }

    public function show_questionnaire( $patientcode, $questionnaire, $instance )
    {
        $username = $this -> data['top_nav']['username'];

        $xml_file = 'application/views/patient/questionnaire/bows/'.$questionnaire;
        $tables = $this->tables($xml_file,$this->evaluationXSL,$patientcode,$instance,'de');
        $qName = $this -> Questionnaire_tool_model -> get_questionnaire_by_file( $questionnaire );

        $xml_directories = array();
        $xml_directories['xml'] = $xml_file;
        $xml_directories['name'] = $qName -> name;

        $this->data['content']['xml_directories'] = $xml_directories;
        $this->data['content']['evaluationXSL'] = $this->evaluationXSL;
        $this->data['content']['tables'] = $tables;

        $this -> template -> set( 'header', 'all/header_sb', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/show_questionnaire', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }//_show_questionnaire()

    private function tables($xml_directories, $xsl_file, $patientcode, $instance, $language) {

		$dir = $xml_directories;
        $content = simplexml_load_file($dir);
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
    } // _show_questionnaire_list

    public function quest_release($patientcode){

        $table = $this->input->post('quest_select');
        $qid = $this -> Questionnaire_tool_model ->get_questionnaire_id_by_table($table);
        $patient = $this -> Patient_model -> search_patient ( $patientcode );
        $patient = $patient[0];

        $this->set_questionnaire_release_information($qid, $patient);

        $this->data['content']['patient'] = $patient;
        $this->data['content']['qid'] = $qid;

        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/quest_release', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }

    public function insert_questionnaire($patientcode, $questionnaire, $therapist = "")
    {
        
        if( isset( $patientcode ) && isset( $questionnaire ))
		{
            if(!isset($therapist)){
                $therapist = 'admin';
            }
            $username = $this -> data['top_nav']['username'];
            $instancePost = $this -> input -> post('instance'); 
            if($instancePost !== "")
                $instancePost = intval($instancePost) < 10 ? '0'.intval($instancePost) : $instancePost;
            $instance = $this -> input -> post('instance_prefix') . $instancePost; 
            $interval = $this -> input -> post('interval');
            $start = $this -> input -> post('start');
            if(!isset($therapist))
                $therapist = $username;

			$this -> Questionnaire_tool_model -> insert_questionnaire ($therapist, $patientcode, $questionnaire, $instance,$start,$interval);
            		
		}
        redirect('admin/questionnaire_tool/show_questionnaire_list/'.$patientcode);
    }//_insert_questionnaire

    public function battery_release($patientcode){
        $bid = $this->input->post('battery_select');
        $questionnaires = $this -> Questionnaire_tool_model ->get_all_questionnaire_by_battery($bid);
        $patient = $this -> Patient_model -> search_patient ( $patientcode );
        $patient = $patient[0];
        $this->data['content']['battery'] = $bid;
        $this->data['content']['patient'] = $patient;
        $this->data['content']['questionnaires'] = $questionnaires;

        $this->set_questionnaire_release_information($questionnaires[0]->qid,$patient);

        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/battery_release', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }

    public function insert_questionnaire_batterie_patient ( $patientcode, $bid, $therapist)
    {
        if ( empty($therapist) ){
            $therapist = 'admin';
        }
		if( isset(  $therapist ) && isset ($patientcode ) && isset ($bid) ){
            $username = $this -> data['top_nav']['username'];
			$all_questionnaires = $this -> Questionnaire_tool_model -> get_all_questionnaire_by_batterie( $bid );
            $instancePost = $this -> input -> post('instance'); 
            if($instancePost !== "")
                $instancePost = intval($instancePost) < 10 ? '0'.intval($instancePost) : $instancePost;
            $instance = $this -> input -> post('instance_prefix') . $instancePost;    

            if(!isset($therapist))
                $therapist = $username;   

            foreach ($all_questionnaires as $key => $questionnaire) {
                $interval = $this -> input -> post('interval_'.$key);
                $start = $this -> input -> post('start_'.$key);
                $this -> Questionnaire_tool_model -> insert_questionnaire ($therapist, $patientcode, $questionnaire -> qid,$instance,$start,$interval);
            }
        }
        redirect('admin/questionnaire_tool/show_questionnaire_list/'.$patientcode);
    }//_insert_questionnaire_batterie_patient()

    private function set_questionnaire_release_information($qid, $patient){
        $instanceOT = $this -> Questionnaire_tool_model -> get_next_instance_of_questionnaire($qid, $patient -> CODE,$patient -> THERPIST, 'OT');
        $instanceZ = 	$this -> Questionnaire_tool_model -> get_next_instance_of_questionnaire($qid, $patient -> CODE,$patient -> THERPIST, 'Z');	  
        $instanceSB = $this -> Questionnaire_tool_model -> get_next_sb_instance($qid, $patient -> CODE, $patient -> THERPIST);

        $disableWZ = $this->Questionnaire_tool_model->instance_exists($qid,$patient->CODE,$patient->THERPIST,'WZ');
        $disablePR = $this->Questionnaire_tool_model->instance_exists($qid,$patient->CODE,$patient->THERPIST,'PR');
        $disablePO = $this->Questionnaire_tool_model->instance_exists($qid,$patient->CODE,$patient->THERPIST,'PO');

        $this->data['content']['instanceOT'] = $instanceOT;
        $this->data['content']['instanceZ'] = $instanceZ;
        $this->data['content']['instanceSB'] = $instanceSB;

        $this->data['content']['disableWZ'] = $disableWZ;
        $this->data['content']['disablePR'] = $disablePR;
        $this->data['content']['disablePO'] = $disablePO;
    }

    public function add_questionnaire()
    {
        $path = APPPATH.'views\\patient\\questionnaire\\bows\\';
        $xml = scandir($path);
        unset($xml[0]); // .
        unset($xml[1]); // ..
        unset($xml[2]); // feedback.xsl
        sort($xml);

        $all_questionnaire = $this -> Questionnaire_tool_model -> get_all_questionnaire( );

        foreach ($all_questionnaire as $value) {
            if(!in_array($value->filename,$xml)){
                $this-> Questionnaire_tool_model->remove_questionnaire_DB($value->id);
            }
        }
        $scales = array();
        $item_invert = array();
        $info = array();
        foreach($xml as $file)
        {
            $quest = simplexml_load_file($path.$file);
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
            }

            $this-> Questionnaire_tool_model->add_or_update_questionnaire_DB($table, $file, $lang_array);

            foreach($quest->Data->Scale as $scale){
                $name = strip_tags($scale->Name->asXml());
                $title = strip_tags($scale->Title->asXml());
                $min = strip_tags($scale->Min->asXml());
                $max = strip_tags($scale->Max->asXml());
                $info[$name] = array('title' => $title, 'min' => $min, 'max' => $max);
                $items = $scale->Items->children();
                $item_names = array();
                
                foreach($items as $i){
                    $attr = $i->attributes();
                    $invert = (array)$attr['invert'];
                    $invert = $invert[0];
                    $i_name = strip_tags($i->asXml());
                    if($invert != null){
                        $item_invert[$name][$i_name] = $invert;
                    }
                    $item_names[] = $i_name;
                }
                $scales[$name][$table] = $item_names;
            }        
        }

        $this->Questionnaire_tool_model->add_or_update_process_scales($scales, $item_invert, $info);

        $all_questionnaire = $this -> Questionnaire_tool_model -> get_all_questionnaire( );
        
        $this->data['content']['all_questionnaire'] = $all_questionnaire;
        
        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/add_questionnaire', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }//_add_questionnaire()

    public function do_upload()
    {
        $config['upload_path'] = APPPATH.'\\views\\patient\\questionnaire\\bows\\';
		$config['allowed_types'] = 'xml';
        $config['max_size']	= '20';
        
        $this-> load -> library('upload', $config);

        if(!$this-> upload -> do_upload()) //Datei verstößt gegen geltende Upload-Parameter
		{
			$error = array('error' => $this->upload->display_errors());
            //var_dump($error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
            //var_dump($data);
        }
        
        $all_questionnaire = $this-> Questionnaire_tool_model -> get_all_questionnaire();
        $this-> data['content']['all_questionnaire'] = $all_questionnaire;

        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/add_questionnaire', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }

    public function edit_questionnaire($qid)
    {
        $path = APPPATH.'\\views\\patient\\questionnaire\\bows\\';
        
        $quest = $this-> Questionnaire_tool_model->get_questionnaire($qid);
        $xml = simplexml_load_file($path.$quest[0]->filename);
        $attr = $xml->attributes();
        $table = (array)$attr['table'];
        $table = $table[0];
        $date = (array)$attr['date'];
        $date = $date[0];
        
        $languages = array();
        $langSets = array();
        foreach($xml->Language as $language){
            $type_array = array();
            $sets = array();
            $attr = $language->attributes();
            $lang = (array)$attr['lang'];
            $languages[] = $lang[0];
            foreach($language->Set as $set){
                $attr = $set->attributes();
                $attr = (array)$attr['type'];
                $type_array[] = $attr[0];
                $sets[] = $set;
            }
            $langSets[] = $sets;
        }
        $fields = $this->constructDefaultFields($langSets,$type_array);
        $this->data['content']['languages'] = $languages;
        $this->data['content']['table'] = $table;
        $this->data['content']['dateField'] = $date;
        $this->data['content']['fields'] = $fields;
        $this->data['content']['types'] = $type_array;
        $this->data['content']['sets'] = $sets;
        $this->data['content']['name'] = $quest[0]->header_name[0];
        $this->data['content']['xml_string'] = file_get_contents($path.$quest[0]->filename);

        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/edit_questionnaire', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }

    private function constructDefaultFields($langSets,$types){

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
    }

    public function save_xml(){
        $form_array =  isset($_POST['form']) ? $_POST['form'] : '';
        $languages =  isset($_POST['languages']) ? $_POST['languages'] : '';
        $languages = json_decode($languages);
        $table = $this->input->post('table');
        $dateField = $this->input->post('dateField');

        $sorted_form = array();
        $current_type = NULL;
        foreach($form_array as $key => $form){
            $sorted_form[$languages[$key]] = array();
            $last_elem = NULL;
            for ($i=0; $i < count($form); $i++) { 
                $new_set = false;
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
                
            }
        }

        $xml_string = '<?xml version="1.0" encoding="UTF-8"?><Questionnaire table="'.$table.'" date="'.$dateField.'">';
        foreach($sorted_form as $lang => $form){
            $xml_string .= '<Language lang="'.$lang.'">';
            foreach($form as $set){
                $index = $set[0]['type'] === 'header' ? 1 : 0;
                $type = $set[$index]['type'];
                $parent = $set[$index]['parentName'];
                $parentQ = !empty($parent) ? 'parentQuestion="'.$parent.'"' : '';
                $trigger_string = '';
                foreach(explode(',',$set[$index]['parentTriggers']) as $trigger){
                    if(!empty($trigger))
                        $trigger_string .= '<Trigger>'.$trigger.'</Trigger>';
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
                }
            }
            $xml_string .= '</Language>';
        }

        $xml_string .= '</Questionnaire>';

        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xml_string);
        $dom->formatOutput = TRUE;
        $xml_string = $dom->saveXML();
        file_put_contents('test.xml',$xml_string);
    }

    public function batterieverwaltung()
    {
        
        $all_batteries = $this-> Questionnaire_tool_model -> get_all_batteries();
        $this -> data['content']['all_batteries'] = $all_batteries;

        $patients = $this-> Patient_model -> get_all_patients($this-> username);
        $this-> data['content']['patients'] = $patients;

        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/batterieverwaltung', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );    
    } //batterieverwaltung//

    public function insert_new_batterie()
    {
        $name = $this-> input -> post('name'); //Eingetragener Name

        if(isset($name))
            $this-> Questionnaire_tool_model -> insert_new_batterie_to_DB($name);

        $this->batterieverwaltung();
    }//insert_new_batterie()

    public function delete_batterie($bid)
    {
        if(isset($bid))
            $this-> Questionnaire_tool_model -> delete_batterie_from_DB($bid);
        
        $this-> batterieverwaltung();
    }//delete_batterie()

    public function add_section($bid)
    {
        if(isset($bid))
            $this-> Questionnaire_tool_model -> add_section_to_batterie($bid);

        $this-> batterieverwaltung();
    }//add_section()

    public function delete_section($bid)
    {
        if(isset($bid))
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
    }//save_changes
    
    public function add_questionnaire_to_battery()
	{
		$bid = $this-> input -> post('bid');
		$qid = $this-> input -> post('qid');

		if(isset($bid) AND isset($qid))
			$this-> Questionnaire_tool_model -> insert_questionnaire_in_battery($bid, $qid);

		$this-> batterieverwaltung();
	}//add_questionnaire_to_battery()

    public function delete_questionnaire_from_battery($bid, $qid)
    {
        if(isset($bid) && isset($qid))
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

		$batterie = $this-> Questionnaire_tool_model -> get_all_questionnaire_by_battery($id);
		$this-> data['content']['feedback'] = $batterie;

		$feedback = $this-> Questionnaire_tool_model -> get_feedback_of_batterie($id);
        $this->data['content']['feedback'] = $feedback;

        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/questionnaire_tool/batterie_feedback', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }//batterie_feedback()
    
    public function feedback_add_text($bid)
     {
         $text = $this-> input -> post('textelement');

         $this -> Questionnaire_tool_model-> add_feedback_item($bid, 'text', $text);
         redirect('admin/questionnaire_tool/batterie_feedback/'.$bid);
     }//feedback_add_text()

    public function feedback_add_process($bid)
    {
        $process = $this->input->post('process');

        $this->Questionnaire_tool_model->add_feedback_item($bid,'process',$process);

        redirect('admin/questionnaire_tool/batterie_feedback/'.$bid);
    }//feedback_add_process()

    public function feedback_add_review($bid)
    {
        $review = $this->input->post('review');

        $this->Questionnaire_tool_model->add_feedback_item($bid,'review',$review);

        redirect('admin/questionnaire_tool/batterie_feedback/'.$bid);
    }//feedback_add_review()

    public function feedback_remove_item($id,$bid){

        $this->Questionnaire_tool_model->delete_feedback_item($id,$bid);

        redirect('admin/questionnaire_tool/batterie_feedback/'.$bid);
    }//feedback_remove_item()

    public function feedback_save_order($id)
    {
        $order = $this-> input -> post('order');
        $order_array = explode('$amp', $order);

        foreach($order_array as $key => $item)
        {
            preg_match('/\d+/',$item,$id);
            $id = intval($id[0]);
            $this-> Questionnaire_tool_model ->save_order_of_feedback_item($id,$key);
        }
    }//feedback_save_order()

    public function set_as_standard_battery($bid)
    {
        if(isset($bid))
            $this-> Questionnaire_tool_model -> set_standard_battery($bid);
    }


}