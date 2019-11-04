<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @package Controller
 * @subpackage User
 *
 * @author Christopher Baumann
 */
class Mail extends CI_Controller
{
    /**
     * Constructor.
     *
     * @since 0.9.0
     * @access private
     */
    function __construct( )
    {
        parent::__construct( );
        $this->data = array(HEADER_STRING => array('title' => 'Admin-Mail'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this -> load -> Model( 'Admin_mail_model' );
        $this -> load -> library( 'email' );

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

        $this -> template -> set( HEADER_STRING, 'all/header', $this -> data[HEADER_STRING] );
        $this -> template -> set( FOOTER_STRING, 'all/footer', $this -> data[FOOTER_STRING] );
    }//__construct()

    public function index( )
    {
        $this->data[CONTENT_STRING]['mail_sent'] = $this->input->get('mail_sent');
        
        $this->data[CONTENT_STRING]['messages'] = $this->Admin_mail_model->get_all_messages( );        
        $this->data[CONTENT_STRING]['users'] = $this->Admin_mail_model->get_users();
        
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'admin/mail/overview', $this -> data[CONTENT_STRING] );
        $this -> template -> load( 'template' );
    }//index()

    public function message_management()
    {
        $this->data[CONTENT_STRING]['msg_saved'] = $this->input->get('msg_saved');
		
        $this->data[CONTENT_STRING]['messages'] = $this->Admin_mail_model->get_all_messages( );

        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'admin/mail/message_management', $this -> data[CONTENT_STRING] );
        $this -> template -> load( 'template' );
    }//message_management()

    public function save_message()
    {
        $this->Admin_mail_model->save_new_message($this->input->post('subject'), $this->input->post('message'));

        redirect('admin/mail/message_management');
    }//save_message()

    public function update_message($id)
    {
        $msg_saved = $this->Admin_mail_model->update_message($id, $this->input->post('subject'), $this->input->post('message'));

        redirect('admin/mail/message_management');
    }//update_message()

    public function delete_message($id)
    {
        $msg_saved = $this->Admin_mail_model->delete_single_message($id);

        redirect('admin/mail/message_management');
    }//delete_message()

    public function send_mail()
    {
        $receiver = !is_null( $this->input->post('receiver') ) ? $this->input->post('receiver') : '';
        $cc = !is_null( $this->input->post('cc') ) ? $this->input->post('cc') : '';
        $bcc = !is_null( $this->input->post('bcc') ) ? $this->input->post('bcc') : '';
        
        $this -> email -> from( $this -> input -> post( 'sender' ) );
        $this -> email -> to( $receiver );
        $this -> email -> cc( $cc );
        $this -> email -> bcc( $bcc );
        $this -> email -> subject( $this -> input -> post( 'subject' ) );
        $this -> email -> message( $this -> input -> post( 'message' ) );

        $mail_sent = $this->email->send( );
        redirect( 'admin/mail/index?mail_sent='.intval( $mail_sent ) );
    }//send_mail()
}//class Mail

/* End of file Mail.php */
/* Location: ./application/controllers/admin/Mail.php */
?>