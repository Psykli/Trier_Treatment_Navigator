<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this-> data = array(HEADER_STRING => array('title' => 'Portal'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );

        //Laden der Model und Helper
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this->load->model( 'Therapy_model' );
        $this->load->model( 'Questionnaire_tool_model' );
        $this->load->model( 'Message_model' );
        $this->load->model( 'Remind_model' );
        $this->load->helper( 'url' );
		
		//Laden der Sprachdateien
		$this->lang->load('user_dashboard');
        $this->lang->load('login');
        
        //Profiler
        //Falls aktiv werden soll, muss die Datei in System/Lang/.. erstellt werden
        //$this->output->enable_profiler( $this->config->item('profiler_status') );
        
        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
        
        if( $this->session_model->is_logged_in( $this->session->all_userdata() ) )
        {
            $this->data[TOP_NAV_STRING]['username'] = $this -> session -> userdata( 'username' );
            $this->data[CONTENT_STRING]['userrole'] = $this -> membership_model -> get_role( $this->data[TOP_NAV_STRING]['username'] );
            
            if( $this->data[CONTENT_STRING]['userrole'] !== 'user' && $this->data[CONTENT_STRING]['userrole'] !== 'admin' && $this->data[CONTENT_STRING]['userrole'] !== 'supervisor' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            redirect( 'login' );
        }
    }//__construct()

    public function index()
    {
		//BUG: Wenn das hier aktiviert, dann funktioniert die deutsche Seite nicht. (die englische hingegen schon)
		//$this->output->enable_profiler(TRUE);
        
        $username = $this->data[TOP_NAV_STRING]['username'];
        
        //get profile data
        $this->data[CONTENT_STRING]['passwordStatus'] =  $this->membership_model->passwordStatus( $username );
        
        $this->data[CONTENT_STRING]['status'] = $this->Therapy_model->get_status_data( $username, array( "open" => true, "temp_break" => true, "close" => true ) );
        /* MUSS ABEGKLÄRT WERDEN
        $this->data[CONTENT_STRING]['reminds'] = $this->Therapy_model->get_reminds_of_user( $username ); 
        $this->data[CONTENT_STRING]['gasReminds'] = $this->Therapy_model->get_gas_reminds_of_user( $username );
        $this->data[CONTENT_STRING]['zwReminds'] = $this->Therapy_model->get_zw_reminds_of_user( $username );
        */
        
        $releasedQuestionnaires = $this -> Questionnaire_tool_model -> get_released_questionnaires($username);
        
        if(!isset($releasedQuestionnaires)){
            $this->data[CONTENT_STRING]['noReleasedQuestionnaires'] = true;
        } else {
            $inactiveMessages = array();
            
            foreach ($this ->Questionnaire_tool_model->inactive_patients($username) as $quest) {
                $time = strtotime($quest->activation);
                
                if($time === false OR strtotime('now') > strtotime('+1 week', $time) ){
                    $remind_date = $this->Remind_model->is_quest_remind_deleted($username, $quest->patientcode, $quest->instance, $quest->filename);
                    
                    if($remind_date === FALSE OR strtotime($remind_date) <= strtotime('-8 weeks')){
                        $msg = $arrayName = array('code' => $quest->patientcode, 'instance' => $quest->instance , 'name' => $quest->filename);
                        $inactiveMessages[] = $msg;
                    }
                }
            }
        }

        $this->data[CONTENT_STRING]['inactiveMessages'] = $inactiveMessages;
        
        $this->data[CONTENT_STRING]['anzahlUnreadMsg'] = $this-> Message_model -> get_count_of_unread_received_msgs( $username );	
        
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this->template->set(CONTENT_STRING, 'user/dashboard', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//index()
    
    public function delete_therapy_remind( $code )
	{
		$data = array(	'therapist' => $this->data[TOP_NAV_STRING]['username'],
						'code' => $code,
						'type' => 'therapy_remind',
                        'date' => date("Y-m-d H:i:s")
					);

		$this -> Remind_model -> insert( 'reminds_deleted', $data );
        
        $this -> index();
	}//delete_therapy_remind()

    public function delete_gas_remind( $code )
	{
        $data = array(	'therapist' => $this->data[TOP_NAV_STRING]['username'],
						'code' => $code,
                        'type' => 'gas_remind',
                        'date' => date("Y-m-d H:i:s")
					);

        $this -> Remind_model -> insert( 'reminds_deleted', $data );
        
		$this -> index();		
	}//delete_gas_remind()

    public function delete_zw_remind( $code, $instance )
	{
        $data = array(	'therapist' => $this->data[TOP_NAV_STRING]['username'],
						'code' => $code,
						'instance' => $instance,
                        'type' => 'zw_remind',
                        'date' => date("Y-m-d H:i:s")
                    );
            
		$this -> Remind_model -> insert( 'reminds_deleted', $data );
    
		$this -> index();		
	}//delete_zw_remind()

    public function delete_quest_remind( $code, $instance, $quest_name )
	{
        $data = array(	'therapist' => $this->data[TOP_NAV_STRING]['username'],
						'code' => $code,
				    	'instance' => $instance,
                        'type' => 'questionnaire_remind',
                        'date' => date("Y-m-d H:i:s"),
                        'inactive_questionnaire' => $quest_name
					);

		$this -> Remind_model -> insert( 'reminds_deleted', $data );
        
		$this -> index();		
	}//delete_quest_remind()
}