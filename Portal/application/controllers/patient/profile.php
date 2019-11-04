<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for user profile.
 * 
 * @package Controller
 * @subpackage User
 * 
 * @author Martin Kock <code @ deeagle.de>
 */
class Profile extends CI_Controller 
{

    /**
     * Constructor
     * 
     * @access private
     */
    function __construct( )
    {
        parent::__construct( );
        $this->data = array(HEADER_STRING => array('title' => 'Profil'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );

        if( $this->session_model->is_logged_in( $this->session->all_userdata() ) )
        {
            $this->data[TOP_NAV_STRING]['username'] = $this -> session -> userdata( 'username' );
            $this->data[CONTENT_STRING]['userrole'] = $this -> membership_model -> get_role( $this->data[TOP_NAV_STRING]['username'] );
            
            if( $this->data[CONTENT_STRING]['userrole'] !== 'patient' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            if($this -> uri -> segment(3, '') == 'change_email_confirmation' && !is_null( $this -> uri -> segment(4) ) ) {
                //user clicked on the link to change his email address which he got through email, but isn't logged in yet
                //make him log in first and then redirect him back to the email address change link
                redirect( 'login/index/change_email_confirmation/' . $this -> uri -> segment(4, '') );
            }
            else if($this -> uri -> segment(3, '') == 'change_password_confirmation' && !is_null( $this -> uri -> segment(4) ) ) {
                //user clicked on the link to change his password which he got through email, but isn't logged in yet
                //make him log in first and then redirect him back to the password change link
                redirect( 'login/index/change_password_confirmation/' . $this -> uri -> segment(4, '') );
            }
            else {
                redirect( 'login' );
            }
        }

        $this -> lang -> load( 'profile' );

        $this -> template -> set( HEADER_STRING, 'all/header', $this -> data[HEADER_STRING] );
        $this -> template -> set( FOOTER_STRING, 'all/footer', $this -> data[FOOTER_STRING] );
    }//__construct()
    
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/user/profile
     *  - or -  
     *      http://example.com/index.php/user/profile/index
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
        $this -> data[CONTENT_STRING]['profile'] = $this -> membership_model -> get_profile( $this -> data[TOP_NAV_STRING]['username'], 'first_name, last_name, initials, email, role' );

        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'patient/profile', $this -> data[CONTENT_STRING] );

        $this -> template -> load( 'template' );
    }//index()

    function change_email( )
	{
        $this -> load -> library( 'email' );

        $this -> data[CONTENT_STRING]['profile'] = $this -> membership_model -> get_profile( $this -> data[TOP_NAV_STRING]['username'], 'first_name, last_name, initials, email, role' );

        $current_password = $this -> input -> post( 'current_password' );
        $new_email_address = $this -> input -> post( 'email' );
        
        if( !$this -> membership_model -> validate( $this->data[TOP_NAV_STRING]['username'], $current_password ) ) {
            $this -> data[CONTENT_STRING]['error_message'] = lang( 'current_password_wrong' );
            $this -> data[CONTENT_STRING]['new_email_address'] = $new_email_address;
        }
        else {
            //no errors occurred, send the email address change confirmation email

            if( !empty( $this -> data[CONTENT_STRING]['profile'] -> email ) ) {
                //$reset_link is a temporarily valid link that the user receives at his current email address.
                //By clicking the link he'll confirm the new email address.
                $reset_code = $this -> membership_model -> generate_random_unhashed_password ( $length = 32, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' );
                $entered = $this -> membership_model -> enter_email_confirmation_code( $this->data[TOP_NAV_STRING]['username'], $reset_code, $this -> data[CONTENT_STRING]['profile'] -> email, $new_email_address );

                if( !$entered ) {
                    $this -> data[CONTENT_STRING]['error_message'] = lang( 'email_address_change_insert_code_error' );
                }
                else {
                    $reset_link = site_url() . '/patient/profile/change_email_confirmation/' . $reset_code;
                    $email_content = lang( 'email_address_change_email_content_part1' ) . $new_email_address . lang( 'email_address_change_email_content_part2' ) . $reset_link;

                    $email_subject = $this -> config -> item( 'company_name' ) . ": " . lang( 'email_address_change_email_subject' );

                    $this -> email -> from(  $this -> config -> item( 'email_address_noreply' ) );
                    $this -> email -> to( $this -> data[CONTENT_STRING]['profile'] -> email );
                    $this -> email -> subject( $email_subject );

                    $email_data = array( 'main_content' => $email_content );
                    $email_body = $this -> load -> view( 'emails/basic_html.php', $email_data, true );
                    $this -> email -> message( $email_body );

                    if( $this->email->send( ) ) {
                        log_message( 'info', 'Email address change confirmation email sent for user '.$this->data[TOP_NAV_STRING]['username'] );
                        $this -> data[CONTENT_STRING]['info_message'] = lang( 'email_address_change_sent' );
                    }
                    else {
                        log_message( 'error', 'Email address change confirmation email FAILED to send for user '.$this->data[TOP_NAV_STRING]['username'] );
                        $this -> data[CONTENT_STRING]['error_message'] = lang( 'email_address_change_failed_to_send' );
                    }
                }
            }
            else {
                log_message( 'info', 'Changing email address of user '.$this->data[TOP_NAV_STRING]['username'].' without sending a confirmation email because he doesn\'t have a valid email address' );
                if( $this -> membership_model -> user_set_email( $this->data[TOP_NAV_STRING]['username'], $new_email_address ) ) {
                    $this -> data[CONTENT_STRING]['success_message'] = lang( 'email_address_changed' );
                }
                else {
                    $this -> data[CONTENT_STRING]['error_message'] = lang( 'unknown_email_address_change_error' );
                }

                $this -> data[CONTENT_STRING]['profile'] = $this -> membership_model -> get_profile( $this -> data[TOP_NAV_STRING]['username'], 'first_name, last_name, initials, email, role' );
            }
        }

		$this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'patient/profile', $this -> data[CONTENT_STRING] );

        $this -> template -> load( 'template' );
    }//change_email()
    
    function change_email_confirmation( $confirmation_code, $confirm = null )
	{
        $this -> data[CONTENT_STRING]['profile'] = $this -> membership_model -> get_profile( $this -> data[TOP_NAV_STRING]['username'], 'first_name, last_name, initials, email, role' );

        $confirmation_data = $this -> membership_model -> get_email_confirmation_data( $this -> data[TOP_NAV_STRING]['username'] );

        if( is_null( $confirmation_data ) ) {
            $this -> data[CONTENT_STRING]['error_message'] = lang( 'email_address_change_code_expired' );
        }
        else if( $confirmation_code !== $confirmation_data -> confirmation_code ) {
            $this -> data[CONTENT_STRING]['error_message'] = lang( 'email_address_change_code_wrong' );
        }
        else if( $confirmation_code === $confirmation_data -> confirmation_code && !is_null( $confirm ) ) {
            //confirmation code correct and user confirmed the email address change
            
            if( $this -> membership_model -> user_set_email( $this->data[TOP_NAV_STRING]['username'], $confirmation_data -> new_email ) ) {
                $this -> membership_model -> delete_email_confirmation_codes( $this->data[TOP_NAV_STRING]['username'] );
                $this -> data[CONTENT_STRING]['success_message'] = lang( 'email_address_changed' );
            }
            else {
                $this -> data[CONTENT_STRING]['error_message'] = lang( 'unknown_email_address_change_error' );
            }

            $this -> data[CONTENT_STRING]['profile'] = $this -> membership_model -> get_profile( $this -> data[TOP_NAV_STRING]['username'], 'first_name, last_name, initials, email, role' );
        }
        else {
            //confirmation code correct, ask the user one last time if he wants to change his email address

            $this -> data[CONTENT_STRING]['old_email_address'] = $confirmation_data -> old_email;
            $this -> data[CONTENT_STRING]['new_email_address_unconfirmed'] = $confirmation_data -> new_email;
            $this -> data[CONTENT_STRING]['accept_confirmation_link'] = site_url() . '/patient/profile/change_email_confirmation/' . $confirmation_data -> confirmation_code . "/confirm";
        }

		$this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'patient/profile', $this -> data[CONTENT_STRING] );

        $this -> template -> load( 'template' );
    }//change_email_confirmation()

    function change_password( )
	{
        $this -> load -> library( 'email' );

        $this -> data[CONTENT_STRING]['profile'] = $this -> membership_model -> get_profile( $this -> data[TOP_NAV_STRING]['username'], 'first_name, last_name, initials, email, role' );

        $current_password = $this -> input -> post( 'current_password' );
        $new_password = $this -> input -> post( 'new_password' );
        $new_password_confirm = $this -> input -> post( 'new_password_confirm' );

        if( $new_password != $new_password_confirm ) {
            $this -> data[CONTENT_STRING]['error_message'] = lang( 'passwords_not_matching' );
        }
        else if( !$this -> membership_model -> validate( $this->data[TOP_NAV_STRING]['username'], $current_password ) ) {
            $this -> data[CONTENT_STRING]['error_message'] = lang( 'current_password_wrong' );
        }
        else {
            //no errors occurred, send the password change confirmation email

            if( !empty( $this -> data[CONTENT_STRING]['profile'] -> email ) ) {
                //$reset_link is a temporarily valid link that the user receives at his email address.
                //By clicking the link he'll confirm the new password.
                $new_password_hashed = $this -> membership_model -> get_hashed_password( $new_password );
                $reset_code = $this -> membership_model -> generate_random_unhashed_password ( $length = 32, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' );
                $entered = $this -> membership_model -> enter_password_confirmation_code( $this->data[TOP_NAV_STRING]['username'], $reset_code, $new_password_hashed );

                if( !$entered ) {
                    $this -> data[CONTENT_STRING]['error_message'] = lang( 'password_change_insert_code_error' );
                }
                else {
                    $reset_link = site_url() . '/admin/profile/change_password_confirmation/' . $reset_code;
                    $email_content = lang( 'password_change_email_content' ) . $reset_link;

                    $email_subject = $this -> config -> item( 'company_name' ) . ": " . lang( 'password_change_email_subject' );

                    $this -> email -> from(  $this -> config -> item( 'email_address_noreply' ) );
                    $this -> email -> to( $this -> data[CONTENT_STRING]['profile'] -> email );
                    $this -> email -> subject( $email_subject );
                    
                    $email_data = array( 'main_content' => $email_content );
                    $email_body = $this -> load -> view( 'emails/basic_html.php', $email_data, true );
                    $this -> email -> message( $email_body );

                    if( $this->email->send( ) ) {
                        log_message( 'info', 'Password change confirmation email sent for user '.$this->data[TOP_NAV_STRING]['username'] );
                        $this -> data[CONTENT_STRING]['info_message'] = lang( 'password_change_sent' );
                    }
                    else {
                        log_message( 'error', 'Password change confirmation email FAILED to send for user '.$this->data[TOP_NAV_STRING]['username'] );
                        $this -> data[CONTENT_STRING]['error_message'] = lang( 'password_change_failed_to_send' );
                    }
                }
            }
            else {
                log_message( 'info', 'Changing password of user '.$this->data[TOP_NAV_STRING]['username'].' without sending a confirmation email because he doesn\'t have a valid email address' );
                if( $this -> membership_model -> set_user_password( $this->data[TOP_NAV_STRING]['username'], $new_password ) ) {
                    $this -> data[CONTENT_STRING]['success_message'] = lang( 'password_changed' );
                }
                else {
                    $this -> data[CONTENT_STRING]['error_message'] = lang( 'unknown_password_change_error' );
                }
            }
        }

        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'patient/profile', $this -> data[CONTENT_STRING] );

        $this -> template -> load( 'template' );
    }//change_password()
    
    function change_password_confirmation( $confirmation_code )
	{
        $this -> data[CONTENT_STRING]['profile'] = $this -> membership_model -> get_profile( $this -> data[TOP_NAV_STRING]['username'], 'first_name, last_name, initials, email, role' );

        $confirmation_data = $this -> membership_model -> get_password_confirmation_data( $this -> data[TOP_NAV_STRING]['username'] );

        if( is_null( $confirmation_data ) ) {
            $this -> data[CONTENT_STRING]['error_message'] = lang( 'password_change_code_expired' );
        }
        else if( $confirmation_code !== $confirmation_data -> confirmation_code ) {
            $this -> data[CONTENT_STRING]['error_message'] = lang( 'password_change_code_wrong' );
        }
        else if( $confirmation_code === $confirmation_data -> confirmation_code ) {
            //confirmation code correct
            
            if( $this -> membership_model -> set_user_password_dangerous( $this->data[TOP_NAV_STRING]['username'], $confirmation_data -> new_password ) ) {
                $this -> membership_model -> delete_password_confirmation_codes( $this->data[TOP_NAV_STRING]['username'] );
                $this -> data[CONTENT_STRING]['success_message'] = lang( 'password_changed' );
            }
            else {
                $this -> data[CONTENT_STRING]['error_message'] = lang( 'unknown_password_change_error' );
            }
        }

		$this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'patient/profile', $this -> data[CONTENT_STRING] );

        $this -> template -> load( 'template' );
    }//change_password_confirmation()
}//class Profile

/* End of file profile.php */
/* Location: ./application/controllers/patient/profile.php */