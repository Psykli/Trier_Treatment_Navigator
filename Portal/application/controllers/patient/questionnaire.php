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
        $this->data = array(HEADER_STRING => array('title' => 'Patienten'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this-> load -> Model('Patient_Model');
        $this-> load -> Model('Questionnaire_tool_model');
        $this-> load -> Model('Gas_Model');

        $this-> load ->helper('cookie');
        
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);

        $this->files = "application/views/patient/questionnaire/bows/*.xml";
        $this->xsl_file = "application/views/patient/questionnaire/questionnaires.xsl";

        if( $this->session_model->is_logged_in( $this->session->all_userdata( ) ) )
        {
            $this->data[TOP_NAV_STRING]['username'] = $this -> session -> userdata( 'username' );
            $this->data[CONTENT_STRING]['userrole'] = $this -> membership_model -> get_role( $this->data[TOP_NAV_STRING]['username'] );
        }
    }//__construct()

    public function show_questionnaire($id, $instance)
    {
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
            $this-> data[CONTENT_STRING]['endTherapy'] = $abschluss;
        }
        
        $entry = $this -> Questionnaire_tool_model -> get_entry($id, "id, qid, instance");
        if( is_null( $entry ) ) {
            show_error('Couldn\'t find the requested data in the database. Are you sure the link (URL) is correct?' , 404);
        }

        $questionnaire = $this-> Questionnaire_tool_model -> get_questionnaire ($entry -> qid );

        if (!empty($patientcode))
        {
            $last_sb_instance = $this-> SB_Model -> getLastInstance($patientcode);
        }
        else
        {
            $last_sb_instance = $this-> SB_Model -> getLastInstance( $this -> data[TOP_NAV_STRING]['username'] );
        }

        $this->data[CONTENT_STRING]['last_sb_instance'] = $last_sb_instance;
        $this->data[CONTENT_STRING]['questionnaire'] = $questionnaire;
        $this->data[CONTENT_STRING]['entry'] = $entry;
        $this->data[CONTENT_STRING]['xsl_file'] = $this->xsl_file;	
        
        $all_questionnaires = array();
        foreach (glob($this->files) as $file)
        {
			$reader = new XMLReader;
            $reader->open($file);
            
			while ($reader->read() && $reader->name !== "Questionnaire");
			$all_questionnaires[$reader->getAttribute('table')] = $file;
        }

        $this-> data[CONTENT_STRING]['all_questionnaires'] = $all_questionnaires;

        $this->template->set(HEADER_STRING, 'all/header_sb', $this->data[HEADER_STRING]);
        
        if(empty($patientcode))
        {
            $this-> template -> set(TOP_NAV_STRING, 'all/top_nav', $this->data[TOP_NAV_STRING]);
        }
        
        $this->template->set(CONTENT_STRING, 'patient/questionnaire/show_questionnaire', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//show_questionnaire()

    public function send_questionnaire($id, $instance)
    {
        $username = $this-> data [TOP_NAV_STRING]['username'];

        $entry = $this-> Questionnaire_tool_model -> get_entry($id, "id, qid, finished, daysInterval");
        $questionnaire = $this-> Questionnaire_tool_model -> get_questionnaire($entry->qid);

        $questionnaires = array($questionnaire[0]);
        $tables = array($questionnaire[0]->tablename);

        $this->data[CONTENT_STRING]['questionnaires'] = $questionnaires;
		$this->data[CONTENT_STRING]['tables'] = $tables;
		$this->data[CONTENT_STRING]['files'] = $this->files;
		$this->data[CONTENT_STRING]['xsl_file'] = $this->xsl_file;
        $patientcode = $this->session->userdata('patientcode');
        $this->data[CONTENT_STRING]['is_sb'] = !empty($patientcode);

        $prefix = site_url();
        
        if($this->input->post('table')) #CASE: Fragebogen wurde ausgefüllt (Standaard)
        {
            $data = $this-> prepare_data($this->input->post());

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
/* 
                if(strcasecmp($batterie[$step]->tablename, $this->input->post['table']) !== 0) #Fehlercase: Falscher Fragebogen
                    $gas = $this->session->userdata('gas');
                    $is_immutable = $this-> Gas_Model ->is_immutable($patientcode, $username);
                    if(!$gas AND $is_immutable AND $batterie[$step]->gas_section == $section AND ($instance-1) % 5 == 0 AND
                       ($instance-1) != 5 AND $this-> Gas_Model ->does_pr_exist($patientcode, $username))
                    {
                        $z_instance = ($instance - ($instance % 5)); 
                        $z_instance = intval($z_instance) < 10 ? 'Z0'.intval($z_instance) : 'Z'.intval($z_instance);
                        redirect($prefix.'/user/Gas_Tool/fill_gas_sb/'.$patientcode.'/'.$z_instance);
                    }
                    if($step < sizeof($batterie)) //Ist aktueller Fragebogen letzter Step in SB-Sektion?
                    {
                        if(intval($batterie[$step-1]->section) != $section){
                            redirect($prefix.'/patient/sb_dynamic/section_finish');
                        }else{
                            redirect($prefix.'/patient/questionnaire/show_questionnaire/'.$batterie[$step]->qid.'/'.$instance);
                        }                       
                    }
                  
                }// if Abgleich Batterie<->$_POST
    */                  
            } // if !empty($patientcode)
           

            $data['INSTANCE'] = $instance;
           
            if(!empty($data)) //Schreibe $data in DB und ggfs. Abschluss-Email versenden
            {
                $questionnaire_list_tablenames = $this -> Questionnaire_tool_model -> get_questionnaire_list_tablenames();
                $temp_search_array = [];
                $temp_search_array['tablename'] = $this->input->post('table');
                if( !in_array($temp_search_array, $questionnaire_list_tablenames) ) {
                    log_message( 'warn', $username.' tried to insert data into the table '.$this->input->post('table').' which is not listed as a questionnaire table in the "questionnaire_list" table' );
                    show_error( 'Invalid questionnaire tablename.', 403 );
                }
                else {
                    $this-> Questionnaire_tool_model ->insert_row_DANGEROUS($data, $this->input->post('table'));
                }

                if($this->input->post('ETS011') == 1 OR $this->input->post('ETN011') == 1)
                {
					$this->email->from('mail@noreply');
					$this->email->to($this->Admin_mail_model->get_mail_of_user($this->session->userdata('therapist')));
					$this->email->cc('mail2@noreply');
					$this->email->subject('Ende der Therapie von: '.$data['CODE']);
					$this->email->message('Das Ende der Therapie wurde in Sitzung '.$data['INSTANCE'].' festgelegt! Falls dies nicht korrekt sein sollte, wenden Sie sich bitte umgehend an mail2@noreply');
                    $mail_sent = $this->email->send();
                    
                    $this->email->clear();
					$this->email->from('mail@psykli.noreply');
					$this->email->to($this->Admin_mail_model->get_mail_of_user($this->session->userdata('THERAPIST')));
					$this->email->cc('mail@uni-trier.de');
					$this->email->subject('Therapieabschluss '.$data['CODE']);
                    
                    $message = "";
                    
                    $email_data = array( 'main_content' => $message );
                    $email_body = $this -> load -> view( 'emails/basic_html.php', $email_data, true );
                    $this->email->message($email_body);
					
                    $mail_sent = $this->email->send();
				}//if Email-Part
            }//if (!empty($data))

            $therapist_of_patient = $this -> Patient_Model -> get_therapist_of_patient( $data['CODE'], $data['CODE'] );
            $nextInstance = '';

            if(empty($patientcode AND $entry->finished != 1)) // Fallbehandlung: kein Patientcode und nicht letzter Step in Sektion
            {
                if( strcasecmp($this->input->post('table'),'ziel-fragebogen-internetinterventionen-new') == 0)
                {
                    $this->construct_module($data['CODE'],$data['INSTANCE'],$data);
                }

                //Nächste Instanz wird nur automatisch für die OT, Z und SB Fragebögen freigeschaltet
                if(preg_match("/Z\d+/",$instance) AND 
                !$this-> Questionnaire_tool_model ->instance_exists($entry->qid,$data['CODE'],$therapist_of_patient,'PO'))
                {
                    $val = intval(substr($instance,1))+5;
                    $nextInstance = $val < 10 ? "Z0".$val : "Z".$val;
                }
                else if(preg_match("/OT\d+/",$instance) AND 
                !$this-> Questionnaire_tool_model ->instance_exists($entry->qid,$data['CODE'],$therapist_of_patient,'PR'))
                {
                    $val = intval(substr($instance,2))+1;
                    $nextInstance = $val < 10 ? "OT0".$val : "OT".$val;
                } else if(is_numeric($instance) AND
                !$this-> Questionnaire_tool_model ->instance_exists($entry->qid,$data['CODE'],$therapist_of_patient,'PO'))
                {
                    $val = intval($instance)+1;
                    $nextInstance = $val < 10 ? "0".$val : "".$val;
                }

                if(!empty($nextInstance))
                { 
                    $activation = date("Y-m-d", strtotime("+".$entry->daysInterval." days"));

                    if(!isset($therapist_of_patient))
                    {
                        $therapist_of_patient = 'admin';
                    }
                    
                    if($entry->daysInterval > 0)
                    {
                        $this-> Questionnaire_tool_model ->insert_questionnaire( $therapist_of_patient, $data['CODE'], $entry->qid, $nextInstance, $activation, $entry->daysInterval );
                    }
                }
            }//Fallbehandlung: kein Patientencode und nicht letzter Step in Sektion
            
            $this-> Questionnaire_tool_model -> update_questionnaire($id);
        }// if $this->input->post('table')

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
            $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
            $this->template->set(TOP_NAV_STRING, 'all/top_nav', $this->data[TOP_NAV_STRING]);
        }
        else 
        {
            $this->template->set(HEADER_STRING, 'all/header_sb', $this->data[HEADER_STRING]);
        }
        
        $this->template->set(CONTENT_STRING, 'patient/questionnaire/send_questionnaire', $this->data[CONTENT_STRING]);  
        $this->template->load('template');
    }//send_questionnaire()

    private function prepare_data($array)
    {
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
            
			if(strtolower($array['table']) === '5 tstb') {
				$data['TSB001'] = $this->session->userdata('sb_c_1_starttime');
				$data['TSB002'] = $this->session->userdata('therlength');
				$this->session->unset_userdata('therlength');
				$this->session->unset_userdata('sb_c_1_starttime');
			}//if
        }//if
        
        $data['CODE'] = $this->data[TOP_NAV_STRING]['username'];

		return $data;
    }//prepare_data()

    private function starts_with($haystack, $needle) 
    { 
        return (substr($haystack, 0, strlen($needle)) === $needle); 
    }//starts_with()
}//class Questionnaire

/* End of file questionnaire.php */
/* Location: ./application/controllers/patient/questionnaire.php */