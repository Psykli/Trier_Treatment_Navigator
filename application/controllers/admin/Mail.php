<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

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

        $username = $this->session->userdata( 'username' );
        $this->data['top_nav']['username'] = $username;

        //Check User-Credentials for page access rights.
        $this->is_logged_in = $this-> session_model ->is_logged_in($this->session->all_userdata());
        $this->is_admin = $this -> membership_model -> get_role($username) === 'admin';
        
        if( $this->is_logged_in AND $this->is_admin )
        {
            $this-> data['content']['userrole'] = 'admin';
        }
        else //if ($is_logged_in)=FALSE OR ($is_admin)=FALSE
        {
            show_error ('Access denied for your Userrole', 403);
        } // else

        $this -> data = array( 'header' => array( 'title' => 'Admin-Mail' ), 'top_nav' => array( ), 'content' => array( ), 'footer' => array( ) );

        $this -> load -> Model( 'Admin_mail_model' );
        $this->load->library('email');
		
    }//__construct()

    public function index( )
    {
        if( !empty( $this->input->get( 'mail_sent' ) ) ) {
            $this->data['content']['mail_sent'] = $this->input->get('mail_sent');
        }
		
        $messages = $this->Admin_mail_model->get_all_messages();
        $this->data['content']['messages'] = $messages;
        
        $users = $this->Admin_mail_model->get_users();
        $this->data['content']['users'] = $users;
        
        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/mail/overview', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }//_index()

    public function message_management()
    {
        if( !empty( $this->input->get( 'msg_saved' ) ) ) {
            $this->data['content']['msg_saved'] = $this->input->get('msg_saved');
        }
		
        $messages = $this->Admin_mail_model->get_all_messages();
        $this->data['content']['messages'] = $messages;

        $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
        $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'admin/mail/message_management', $this -> data['content'] );
        $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );

        $this -> template -> load( 'template' );
    }

    public function save_message()
    {
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        $this->Admin_mail_model->save_new_message($subject,$message);

        redirect('admin/mail/message_management');
    }

    public function update_message($id)
    {
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        $msg_saved = $this->Admin_mail_model->update_message($id,$subject,$message);

        redirect('admin/mail/message_management');
    }

    public function delete_message($id)
    {
        $msg_saved = $this->Admin_mail_model->delete_single_message($id);

        redirect('admin/mail/message_management');
    }

    public function send_mail()
    {
        //$this->input->post() funktioniert nicht für arrays
        $receiver = isset($_POST['receiver']) ? $_POST['receiver'] : '';
        $cc = isset($_POST['cc']) ? $_POST['cc'] : '';
        $bcc = isset($_POST['bcc']) ? $_POST['bcc'] : '';
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $sender = $this->input->post('sender');
        $this->email->from($sender);
        $this->email->to($receiver);
        $this->email->cc($cc);
        $this->email->bcc($bcc);
        $this->email->subject($subject);
        $this->email->message($message);

        $mail_sent = $this->email->send();
        redirect('admin/mail/index?mail_sent='.intval($mail_sent));
    }
}
?>