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

        $this->data = array(HEADER_STRING => array('title' => 'Portal'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );
		$this->load->Model('membership_model');
        $this->load->Model('session_model');
		$this->load->Model( 'Patient_model' );
        $this->load->Model( 'Message_model' );
        $this->load->Model( 'Questionnaire_tool_model' );
        $this->load->Model( 'Piwik_model' );
        $this->load->helper('date');

        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
        
        if( $this->session_model->is_logged_in( $this->session->all_userdata() ) )
        {
            $this->data[TOP_NAV_STRING]['username'] = $this -> session -> userdata( 'username' );
            $this->data[CONTENT_STRING]['userrole'] = $this -> membership_model -> get_role( $this->data[TOP_NAV_STRING]['username'] );
            
            if( $this->data[CONTENT_STRING]['userrole'] !== 'patient' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            redirect( 'login' );
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
        $username = $this->data[TOP_NAV_STRING]['username'];

        $this->data[CONTENT_STRING]['anzahlMsg'] = $this-> Message_model -> get_count_of_unread_received_msgs( $username );	
        $this->data[CONTENT_STRING]['questionnaire_list'] = $this -> Questionnaire_tool_model -> get_released_not_finished_questionnaires( $username );
        
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this->data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'patient/dashboard', $this->data[CONTENT_STRING] );        
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
			$this -> Patient_model -> set_datenschutz( $this->data[TOP_NAV_STRING]['username'] );
		}
        
        $this -> index();
    }//set_datenschutz()

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
/* Location: ./application/controllers/patient/dashboard.php */