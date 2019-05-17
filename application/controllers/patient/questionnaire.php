<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for Questionnaires
 */

class Questionnaire extends CI_Controller
{
    /**
     * Constructor
     *
     * @since 0.1.0
     * @access private
     */
	private $questionnaires;
	private $files;
	private $xsl_file;
	
    function __construct( )
    {
        parent::__construct( );
        $this->data = array('header' => array('title' => 'Patienten'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );

        $this-> load -> Model('Patient_Model');
        $this-> load -> Model('Questionnaire_tool_model');
        $this-> load -> Model('Gas_Model');

        $this-> load ->helper('cookie');

        $this->files = "application/views/patient/questionnaire/bows/*.xml";
        $this->xsl_file = "application/views/patient/questionnaire/questionnaires.xsl";
        $username = $this-> session -> userdata('username');
        $this-> data ['top_nav']['username'] = $username;
        

        

        
    }

    public function show_questionnaire($id, $instance)
    {   
        $username = $this-> session -> userdata('username');
        $patientcode = $this-> session -> userdata('patientcode');
        
        if (!empty($patientcode))
        {
            $therapist = $this->session->userdata('therapist');
            $questionnaire_id = $this-> Questionnaire_tool_model -> instance_exists($id, $patientcode, $therapist, $instance); //id for last unfinished instance of questionnaire
        
            if (!is_numeric($questionnaire_id)) // if true: Questionnaire with id already released for patient but no instance for given id found
            
            {
                $id = $this-> Questionnaire_tool_model -> insert_questionnaire($therapist, $patientcode, $id, $instance);
                $this-> Questionnaire_tool_model -> update_questionnaire($id);
            }
            else
            {
                $id = $questionnaire_id;
            }

            //Look up if last instance of Therapy
            $abschluss = $this-> SB_Model -> endTherapy($this-> session -> userdata('INSTANCE'), $patientcode);
            $abschluss = $abschluss[0] -> ABSCHLUSS;
            $this-> data['content']['endTherapy'] = $abschluss;
        }

        $entry = $this-> Questionnaire_tool_model -> get_entry($id);

        $questionnaire = $this-> Questionnaire_tool_model -> get_questionnaire ($entry[0]-> qid );

        if (!empty($patientcode))
        {
            $last_sb_instance = $this-> SB_Model -> getLastInstance($patientcode);
        }
        else
        {
            $last_sb_instance = $this-> SB_Model -> getLastInstance($username);
        }
        $this->data['content']['last_sb_instance'] = $last_sb_instance;
        $this->data['content']['questionnaire'] = $questionnaire;
        $this->data['content']['entry'] = $entry;
        $this->data['content']['xsl_file'] = $this->xsl_file;	
        
        $all_questionnaires = array();
        foreach (glob($this->files) as $file)
        {
			$reader = new XMLReader;
			$reader->open($file);
			while ($reader->read() && strcmp($reader->name, "Questionnaire") != 0);
			$all_questionnaires[$reader->getAttribute('table')] = $file;            
        }
        $this-> data['content']['all_questionnaires'] = $all_questionnaires;

        $this->template->set('header', 'all/header_sb', $this->data['header']);
        
        if(empty($patientcode))
        {
            $this-> template -> set('top_nav', 'patient/top_nav', $this->data['top_nav']);
        }
        else
        {
            $this-> template -> set('top_nav', 'patient/top_nav_sb', $this-> data['top_nav']);
        }
        
        $this->template->set('content', 'patient/questionnaire/show_questionnaire', $this->data['content']);  
        $this->template->set('footer', 'all/footer', $this->data['footer']);

        $this->template->load('template');
    }//show_questionnaire()

    public function send_questionnaire($id, $instance)
    {
        
        $username = $this-> data ['top_nav']['username'];

        $entry = $this-> Questionnaire_tool_model -> get_entry($id);
        $questionnaire = $this-> Questionnaire_tool_model -> get_questionnaire($entry[0]->qid);

        $questionnaires = array($questionnaire[0]);
        $tables = array($questionnaire[0]->tablename);

        $this->data['content']['questionnaires'] = $questionnaires;
		$this->data['content']['tables'] = $tables;
		$this->data['content']['files'] = $this->files;
		$this->data['content']['xsl_file'] = $this->xsl_file;
        $patientcode = $this->session->userdata('patientcode');
        $this->data['content']['is_sb'] = !empty($patientcode);

        if(isset($_POST['table'])) #CASE: Fragebogen wurde ausgefüllt (Standaard)
        {
            
            $data = $this-> prepare_data($_POST);
            if (!empty($patientcode))
            {
                
                $data['CODE'] = $patientcode;
                $step = $this-> session -> userdata('step');
                $instance = $this-> session -> userdata('instance');
                $section = intval($this-> session ->userdata('section'));
                $batterie = $this-> session -> userdata('batterie'); // Array mit StdClass-Objekten der jew. Fragebögen    

                #FehlerTest #DEBUG#
                $test1 = $batterie[$step]->tablename;
                $test2 = $this-> input ->post['table'];
                $test3 = strcasecmp($batterie[$step]->tablename, $this->input->post['table']);            
            } // if !empty($patientcode)
           

            $data['INSTANCE'] = $instance;
            if(!empty($data)) //Schreibe $data in DB und ggfs. Abschluss-Email versenden
            {
                $this-> Questionnaire_tool_model ->insert_row($data, $this->input->post('table'));
                if((isset($_POST['ETS011']) AND  $_POST['ETS011'] == 1) OR (isset($_POST['ETN011']) AND $_POST['ETN011'] == 1))
                {
				}//if Email-Part
            }//if (!empty($data))

            $patientData = $this-> Questionnaire_tool_model-> search_patient($data['CODE']);
            $nextInstance = '';

            if(empty($patientcode AND $entry[0]->finished != 1)) // Fallbehandlung: kein Patientcode und nicht letzter Step in Sektion
            {
                if( strcasecmp($_POST['table'],'ziel-fragebogen-internetinterventionen-new') == 0)
                {
                    $this->construct_module($data['CODE'],$data['INSTANCE'],$data);
                }
                //Nächste Instanz wird nur automatisch für die OT, Z und SB Fragebögen freigeschaltet
                if(preg_match("/Z\d+/",$instance) AND 
                !$this-> Questionnaire_tool_model ->instance_exists($entry[0]->qid,$data['CODE'],$patientData[0]->THERPIST,'PO'))
                {
                    $val = intval(substr($instance,1))+5;
                    $nextInstance = $val < 10 ? "Z0".$val : "Z".$val;
                }
                else if(preg_match("/OT\d+/",$instance) AND 
                !$this-> Questionnaire_tool_model ->instance_exists($entry[0]->qid,$data['CODE'],$patientData[0]->THERPIST,'PR'))
                {
                    $val = intval(substr($instance,2))+1;
                    $nextInstance = $val < 10 ? "OT0".$val : "OT".$val;
                } else if(is_numeric($instance) AND
                !$this-> Questionnaire_tool_model ->instance_exists($entry[0]->qid,$data['CODE'],$patientData[0]->THERPIST,'PO'))
                {
                    $val = intval($instance)+1;
                    $nextInstance = $val < 10 ? "0".$val : "".$val;
                } 
                if(!empty($nextInstance))
                { 
                    $activation = date("Y-m-d", strtotime("+".$entry[0]->daysInterval." days"));    
                    if(!isset($patientData[0]->THERPIST))
                    {$patientData[0]->THERPIST = 'admin';}
                    if($entry[0]->daysInterval > 0)
                    {$this-> Questionnaire_tool_model ->insert_questionnaire($patientData[0]->THERPIST,$data['CODE'],$entry[0]->qid,$nextInstance,$activation,$entry[0]->daysInterval);}
                }
            }//Fallbehandlung: kein Patientencode und nicht letzter Step in Sektion
            $this-> Questionnaire_tool_model -> update_questionnaire($id);
        }// if isset($_POST['table'])        
        if(!empty($patientcode))  
        {
            if(strtolower($questionnaire[0]->tablename) == 'hscl-11'){
                $this->session->set_userdata('suicide_set',true);
            }     
            
            if($step < sizeof($batterie) AND $batterie[$step+1]->section == $section)
            {
                $this->session->set_userdata('step',++$step);
                redirect($prefix.'/patient/questionnaire/show_questionnaire/'.$batterie[$step]->qid.'/'.$instance);
            } 
            else 
            {               
                $gas = $this->session->userdata('gas');
                $is_immutable = $this-> Gas_Model->is_immutable($patientcode, $username);
                if(!$gas AND $is_immutable AND $batterie[$step]->gas_section == $section AND ($instance-1) % 5 == 0 AND 
                   ($instance-1) != 5 AND $this-> Gas_Model->does_pr_exist($patientcode, $username))
                {
                    $this->session->set_userdata('step',++$step);
                    $z_instance = ($instance - ($instance % 5)); 
                    $z_instance = intval($z_instance) < 10 ? 'Z0'.intval($z_instance) : 'Z'.intval($z_instance);
                    redirect($prefix.'/user/Gas_Tool/fill_gas_sb/'.$patientcode.'/'.$z_instance);
                }
                $this->session->set_userdata('section',++$section); 
                $this->session->set_userdata('step',++$step);
                if(sizeof($batterie) <= $step)
                {
                    redirect($prefix.'/patient/sb_dynamic/process');
                }
                else
                {
                    redirect($prefix.'/patient/sb_dynamic/section_finish');
                }//else
            }//else
        }//if
        else 
        {
            $open_quests = $this-> Questionnaire_tool_model ->get_remaining_questionnaires( $data['CODE']);
            if(isset($open_quests))
            {
                redirect($prefix.'/patient/questionnaire/show_questionnaire/'.$open_quests[0]->id);
            }
        }//else
        if(empty($patientcode))
        {
            $this->template->set('header', 'all/header', $this->data['header']);
            $this->template->set('top_nav', 'patient/top_nav', $this->data['top_nav']);
        }
        else 
        {
            $this->template->set('header', 'all/header_sb', $this->data['header']);
        }
        
        $this->template->set('content', 'patient/questionnaire/send_questionnaire', $this->data['content']);  
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        $this->template->load('template');

    }//send_questionnaire()

    private function prepare_data($array)
    {
        $username = $this->data['top_nav']['username'];
        if(!empty($array['table']) && !empty($array['date_column']))
        {
            $data = array();
            foreach($array as $key => $item)
            {
                if(!$this->starts_with($key, "submit_") && !$this->starts_with($key, "date_column") &&
                   !$this->starts_with($key, "med_column") && !$this->starts_with($key, "ver_column") && 
                   !$this->starts_with($key, "table")) {$data[$key] = $item;}
            }
            $data[$array['date_column']] = date("Y-m-d H:i:s", time());
			if(!empty($array['med_column'])) {
				$data[$array['med_column']] = 4;
			}
			if(!empty($array['ver_column'])) {
				$data[$array['ver_column']] = 999900;
			}
			if(strcmp(strtolower($array['table']), '5 tstb') == 0) {
				$data['TSB001'] = $this->session->userdata('sb_c_1_starttime');
				$data['TSB002'] = $this->session->userdata('therlength');
				$this->session->unset_userdata('therlength');
				$this->session->unset_userdata('sb_c_1_starttime');
			}//if
        }//if
        $data['CODE'] = $username;
		return $data;
    }//prepare_data()

    private function starts_with($haystack, $needle) 
    { 
        return (substr($haystack, 0, strlen($needle)) === $needle); 
    }


}