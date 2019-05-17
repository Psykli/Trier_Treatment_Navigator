<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this-> data = array('header' => array('title' => 'Portal'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );
        //Laden der Model und Helper
        $this->load->model( 'Therapy_model' );
        $this->load->model( 'Questionnaire_tool_model' );
        $this->load->model( 'Message_model' );   
        $this->load->model( 'Remind_model' );
		$this->load->model( 'Wb_tool_model' );
        $this->load->helper( 'url' );
		
		//Laden der Sprachdateien
		$this->lang->load('user_dashboard');
        $this->lang->load('login');
        
        //Profiler
        //Falls aktiv werden soll, muss die Datei in System/Lang/.. erstellt werden
        //$this->output->enable_profiler( $this->config->item('profiler_status') );
        
        $this->template->set('header', 'all/header', $this->data['header']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        
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
    }

    public function index()
    {
		//BUG: Wenn das hier aktiviert, dann funktioniert die deutsche Seite nicht. (die englische hingegen schon)
		//$this->output->enable_profiler(TRUE);
        
        $username = $this->data['top_nav']['username'];
        
        //get profile data
        $this->data['content']['passwordStatus'] =  $this->membership_model->passwordStatus( $username );
        
        $this->data['content']['status'] = $this->Therapy_model->get_status_data( $username, array( "open" => true, "temp_break" => true, "close" => true ) );
        /* MUSS ABEGKLÄRT WERDEN
        $this->data['content']['reminds'] = $this->Therapy_model->get_reminds_of_user( $username ); 
        $this->data['content']['gasReminds'] = $this->Therapy_model->get_gas_reminds_of_user( $username );
        $this->data['content']['zwReminds'] = $this->Therapy_model->get_zw_reminds_of_user( $username );
        */
        
        $releasedQuestionnaires = $this -> Questionnaire_tool_model -> get_released_questionnaires($username);
        
        if(!isset($releasedQuestionnaires)){
            $this->data['content']['noReleasedQuestionnaires'] = true;
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

        $this->data['content']['inactiveMessages'] = $inactiveMessages;
        
        $this->data['content']['anzahlUnreadMsg'] = $this-> Message_model -> get_count_of_unread_received_msgs( $username );	
        
        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this->template->set('content', 'user/dashboard', $this->data['content']);
        $this->template->load('template');
    }
    
    public function delete_therapy_remind( $code )
	{
		$username = $this->data['top_nav']['username'];
		
		if( isset( $code ) )
		{
			$data = array(	'therapist' => $username,
							'code' => $code,
							'type' => 'therapy_remind',
                            'date' => date("Y-m-d H:i:s")
						);

			$this -> Remind_model -> insert( 'reminds_deleted', $data );
		}
		
		$this -> index();
	}

    public function delete_gas_remind( $code )
	{
        $username = $this->data['top_nav']['username'];
        
		if( isset( $code ) )
		{
			$data = array(	'therapist' => $username,
							'code' => $code,
                            'type' => 'gas_remind',
                            'date' => date("Y-m-d H:i:s")
						);

			$this -> Remind_model -> insert( 'reminds_deleted', $data );
        }
        
		$this -> index();		
	}

    public function delete_zw_remind( $code, $instance )
	{
        $username = $this->data['top_nav']['username'];
        
		if( isset( $code ) && isset( $instance ) )
		{
			$data = array(	'therapist' => $username,
							'code' => $code,
							'instance' => $instance,
                            'type' => 'zw_remind',
                            'date' => date("Y-m-d H:i:s")
						);
			$this -> Remind_model -> insert( 'reminds_deleted', $data );
        }
        
		$this -> index();		
	}

    public function delete_quest_remind( $code, $instance, $quest_name )
	{
        $username = $this->data['top_nav']['username'];
        
		if( isset( $code ) && isset( $instance ) && isset( $quest_name ) )
		{
			$data = array(	'therapist' => $username,
							'code' => $code,
							'instance' => $instance,
                            'type' => 'questionnaire_remind',
                            'date' => date("Y-m-d H:i:s"),
                            'inactive_questionnaire' => $quest_name
						);

			$this -> Remind_model -> insert( 'reminds_deleted', $data );
        }
        
		$this -> index();		
	}
}