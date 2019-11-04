<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for admin dashboard.
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
     * @since 0.0.1
     * @access public
     */
    function __construct( )
    {
        parent::__construct( );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this->load->Model( 'SB_Model' );
        $this->data = array(HEADER_STRING => array('title' => 'Portal'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );

        //Profiler
		// $this->output->enable_profiler( $this->config->item('profiler_status') );

        $this -> template -> set( HEADER_STRING, 'all/header', $this -> data[HEADER_STRING] );
        $this -> template -> set( FOOTER_STRING, 'all/footer', $this -> data[FOOTER_STRING] );

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
    }//__construct()

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/admin/dashboard
     *  - or -
     *      http://example.com/index.php/admin/dashboard/index
     *  - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this->template->set(CONTENT_STRING, 'admin/dashboard', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//index()

    public function purge_testpatients(){
        $this->SB_Model->test_patient_purge();
    }//purge_testpatients

}//class Dashboard

/* End of file dashboard.php */
/* Location: ./application/controllers/admin/dashboard.php */
?>