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
        $this->load->Model( 'SB_Model' );
        $this->data = array('header' => array('title' => 'Portal'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );


		//Profiler
		// $this->output->enable_profiler( $this->config->item('profiler_status') );
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
        $is_logged_in = $this->session_model->is_logged_in( $this->session->all_userdata() );

        //if a user is logged in, we need the top_nav with user_information and admindashboard.
        if( $is_logged_in )
        {
            $username = $this->session->userdata( 'username' );
            $this->data['top_nav']['username'] = $username;

            $is_admin = $this->membership_model->get_role( $username ) === 'admin';

            //if it's a admin -> dashboard login ok
            if( $is_admin )
            {
                $this->template->set('top_nav', 'admin/top_nav', $this->data['top_nav']);
                $this->template->set('content', 'admin/dashboard', $this->data['content']);
            }//if
            else //you are not admin!
            {
            	show_error( 'Access denied for your Userrole' , 403 );
            }//else
        }//if
        else
        {
            $this->template->set('top_nav', 'guest/top_nav', $this->data['top_nav']);
            $this->template->set('content', 'guest/login_form', $this->data['content']);
        }//else

        $this->template->set('header', 'all/header', $this->data['header']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);

        $this->template->load('template');
    }//index()

    public function purge_testpatients(){
        $this->SB_Model->test_patient_purge();
    }
}//class Dashboardus

/* End of file dashboard.php */
/* Location: ./application/controllers/admin/dashboard.php */