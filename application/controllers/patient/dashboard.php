<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Controller for patient dashboard.
 * 
 * @package Controller
 * @subpackage Admin
 * 
 * @author Martin Kock <code @ deeagle.de>
 */
class Dashboard extends CI_Controller 
{ 



    /**
     * Constructor
     * 
     * @since 0.7.0
     * @access public
     */
    public function __construct( )
    {
        parent::__construct( );

        $this->data = array('header' => array('title' => 'Portal'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );
		
		$this->load->Model( 'Exercise_model' );
		$this->load->Model( 'Patient_model' );
        $this->load->Model( 'Message_model' );
        $this->load->Model( 'Membership_model' );
        $this->load->Model( 'Questionnaire_tool_model' );
        $this->load->Model( 'Piwik_model' );
        $this->load->helper('date');

        $this->template->set('header', 'all/header', $this->data['header']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        
        $is_logged_in = $this->session_model->is_logged_in( $this->session->all_userdata() );
        
        if( $is_logged_in )
        {
            $this->data['top_nav']['username'] = $this -> session -> userdata( 'username' );
            $this->data['content']['userrole'] = $this -> membership_model -> get_role( $this->data['top_nav']['username'] );
            
            if( $this->data['content']['userrole'] !== 'patient' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            $this->template->set('top_nav', 'guest/top_nav', $this->data['top_nav']);
            $this->template->set('content', 'guest/login_form', $this->data['content']);
            $this->template->load('template');
        }
    }//__construct()

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/patient/dashboard
     *  - or -  
     *      http://example.com/index.php/patient/dashboard/index
     *  - or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index( )
    {
        $username = $this->data['top_nav']['username'];

        $anzahlMsg = $this-> Message_model -> get_count_of_unread_received_msgs( $username );	
        $this->data['content']['anzahlMsg'] = $anzahlMsg; 
        $this->data['content']['questionnaireAvailable'] = $this->Questionnaire_tool_model->is_questionnaire_available($username);

        $this->data['content']['datenschutz_status'] = $this-> Patient_model -> get_datenschutz_status_by_patient( $username );
        
        $loginDate = $this -> Piwik_model -> get_last_date_for_user( $username );
        $login = $this -> Questionnaire_tool_model -> get_last_login( $username );
        
        if( !empty( $loginDate ) AND strtotime( $loginDate ) > strtotime( $login ) ) {
            $login = $loginDate;
        }
        
        $patientData = $this -> Questionnaire_tool_model -> search_patient( $username );
        $therapist = empty( $patientData[0] -> THERPIST ) ? "admin" : $patientData[0] -> THERPIST;
        
        $qid = $this -> Questionnaire_tool_model -> get_questionnaire_id_by_table( "einhaltung_von_internetinterventionen" );
        $fei = $this -> Questionnaire_tool_model -> get_single_released_questionnaire( $username, $qid, null, false );
        
        if( !empty( $login ) AND strtotime( "-1 week" ) > strtotime( $login ) AND empty( $fei ) ) {
            $otNum = $this -> Questionnaire_tool_model -> get_next_instance_of_questionnaire( $qid, $username, $therapist, "OT" );
            $this -> Questionnaire_tool_model -> insert_questionnaire( $therapist, $username, $qid, "OT".$otNum );
        }

        $this -> template -> set( 'top_nav', 'patient/top_nav', $this->data['top_nav'] );
        $this -> template -> set( 'content', 'patient/dashboard', $this->data['content'] );        
        $this -> template -> load( 'template' );
    }//index()

    public function set_datenschutz()
    {
        // Holen der Daten
        $ot_studie_ziel = $this -> input -> post( 'ot_studie_ziel' );
        $ot_studie_info = $this -> input -> post( 'ot_studie_info' );
        $ot_studie_anonym = $this -> input -> post( 'ot_studie_anonym' );
        $ot_studie_notfall = $this -> input -> post( 'ot_studie_notfall' );
        $ot_studie_risiken = $this -> input -> post( 'ot_studie_risiken' );
        
        if( isset( $ot_studie_ziel ) && isset( $ot_studie_info ) && isset( $ot_studie_anonym ) && isset( $ot_studie_notfall ) && isset( $ot_studie_risiken ) )
		{
            //only change the privacy/datenschutz entries for the patient in the DB if all checkboxes have been confirmed
			$this -> Patient_model -> set_datenschutz( $this->data['top_nav']['username'] );
		}
        
        $this -> index();
    }//set_datenschutz

    private function delete_ot_quest( ) {
        $qid = $this -> Questionnaire_tool_model -> get_questionnaire_id_by_table( 'ziel-fragebogen-internetinterventionen' );
        $patientcode = $this -> session -> userdata( 'username' );
        $zfi = $this -> Questionnaire_tool_model -> get_single_released_questionnaire( $patientcode, $qid, null, 0 );
        
        if( isset( $zfi ) ) {
            $this -> Questionnaire_tool_model -> delete_questionnaire_from_patient( $patientcode, $zfi -> id );
            $zfi = $this -> Questionnaire_tool_model -> get_released_not_finished_questionnaires( $patientcode );
            
            if( isset( $zfi ) ) {
                echo 'quests_available';
            } else {
                echo 'no_quests';
            }
        }
    }//delete_ot_quest()
}//class Dashboard

/* End of file dashboard.php */
/* Location: ./application/controllers/admin/dashboard.php */