<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

/**
 * An instance of the class represents a login.
 * It contains all functions for logins.
 * 
 * @package Controller
 *
 * @author Martin Kock <code @ deeagle.de>
 */
class Login extends CI_Controller
{
    /**
     * Constructor
     * 
     * @since 0.0.1
     * @access private
     */
    function __construct( )
    {
        parent::__construct( );
        $this->data = array('header' => array('title' => 'Login'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );
		$this->load->Model('membership_model');
        $this->load->Model('session_model');
		//Laden der Sprachdatei
		$this->lang->load('login');
        //$this->load->Model( 'Questionnaire_tool_model' );
        $username = $this->session->userdata( 'username' );
        $this->data['top_nav']['username'] = $username;
    }//__construct()
    
    /**
     * The index of the page.
     * 
     * @see portal.php
     * If a valide user is logged in it redirects to the portal.
     * @see login.php
     * Else the user will be redirect to the login_form.
     * 
     * @since 0.0.1
     * @access public
     */
    function index( $redirect_page = NULL, $redirect_param_1 = NULL )
    {
        $is_logged_in = $this->session_model->is_logged_in( $this->session->all_userdata() );
        
        //if a user is logged in, we need the top_nav with user_information and logout.
        if( $is_logged_in )
        {   
            redirect(portal);
        }//if
        else
        {
            $this -> data[CONTENT_STRING]['redirect_page'] = $redirect_page;
            $this -> data[CONTENT_STRING]['redirect_param_1'] = $redirect_param_1;

            $this->template->set('header', 'all/header', $this->data['header']);
            $this->template->set('top_nav', 'all/top_nav', $this->data['top_nav']);
            $this->template->set('content', 'guest/login_form', $this->data['content']);
            $this->template->set('footer', 'all/footer', $this->data['footer']);
            $this->template->load('template');
        }//else
    }//index()

    /**
     * Checks the userlogin and redirects to the userrole dashboard or to index (if not logged in, with error code).
     * <ul>
     *  <li>admin to @see admin/dashboard.php</li>
     *  <li>user to @see user/dashboard.php</li>
     *  <li>guest to @see portal/index.php</li>
     * </ul>
     * 
     * @since 0.0.1
     * @access public
     */
    function validate_credentials( )
    {
        $query = $this -> membership_model -> validate( );

        $redirect_page = $this -> input -> post( 'redirect_page' );
        $redirect_param_1 = $this -> input -> post( 'redirect_param_1' );

        if( $query )//if login-data validates
        {
            $username = $this -> input -> post( 'username' );
            $data = array( 'username' => $username, 'is_logged_in' => true );
            $this -> session -> set_userdata( $data );
            
            $user_role = $this->membership_model->get_role( $username );

            //check if user should be redirected to a specific page first, before using the default after-login redirects
            if( !empty( $redirect_page ) && $redirect_page === 'change_email_confirmation' && !empty( $redirect_param_1 ) ) {
                redirect( $user_role . '/profile/' . $redirect_page . '/' . $redirect_param_1 );
            }
            else if( !empty( $redirect_page ) && $redirect_page === 'change_password_confirmation' && !empty( $redirect_param_1 ) ) {
                redirect( $user_role . '/profile/' . $redirect_page . '/' . $redirect_param_1 );
            }
            
            switch( $user_role )
            {
                case 'admin':
                    redirect( 'admin/dashboard' );
                    break;
				case 'supervisor':
					redirect( 'supervisor/dashboard' );
					break;
                case 'user':
                    redirect( 'user/dashboard' );
                    break;
                case 'privileged_user':
                    redirect( 'admin/meeting_tool/index/overview' );
                    break;
				case 'patient':
                    //$this->Questionnaire_tool_model->set_login_date($username);
					redirect( 'patient/dashboard' );
					break;
                default:
                    redirect( 'portal/index' ); //goto startpage       
            }//switch
        }//if
        else//login Incorect
        {
            $this -> data[CONTENT_STRING]['error'] = TRUE;
            $this -> data[CONTENT_STRING]['error_code'] = 403;

            $this -> data[CONTENT_STRING]['redirect_page'] = $redirect_page;
            $this -> data[CONTENT_STRING]['redirect_param_1'] = $redirect_param_1;
            
            $this->template->set('header', 'all/header', $this->data['header']);
            $this->template->set('top_nav', 'all/top_nav', $this->data['top_nav']);
            $this->template->set('content', 'guest/login_form', $this->data['content']);
            $this->template->set('footer', 'all/footer', $this->data['footer']);
            $this->template->load('template');
        }//else
    }//validate_crendentials()

    /**
     * Destroys a running session and redirects to portal (default) or 
     * a registered page (to prevent URL-Cloacking).
     * 
     * @since 0.0.1
     * @access public
     * @param string url destination.
     */
    function logout( $destination = NULL )
    {
        $this -> session -> sess_destroy( );
        
        switch( $destination )
        {
            case 'questionnaire':
                redirect( '#' );
                break;
            case 'room_book':
                redirect( '#' );
                break;
            default: 
                redirect( 'portal/index' );
        }//switch
    }//logout()
}//class Login

/* End of file login.php */
/* Location: ./application/controllers/login.php */