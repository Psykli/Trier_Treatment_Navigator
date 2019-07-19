<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for patients.
 *
 * @package Controller
 * @subpackage User
 *
 * @author Martin Kock <code @ deeagle.de>
 * @TODO Build class, look admin
 */
class Patient extends CI_Controller
{

    /**
     * Constructor
     *
     * @since 0.1.0
     * @access private
     */
    function __construct( )
    {
        parent::__construct( );
        $this->data = array(HEADER_STRING => array('title' => 'Patienten'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );

        $this->load->Model( 'Patient_model' );
        $this->load->Model( 'Message_model' );
        $this->load->Model( 'Therapy_model' );
		$this->load->Model( 'Questionnaire_tool_model' );

        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
        
        if( $this->session_model->is_logged_in( $this->session->all_userdata( ) ) )
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
     *      http://example.com/index.php/patient/patient
     *  - or -
     *      http://example.com/index.php/patient/patient/index
     *  - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     *
     * @since 0.2.0
     * @access public
     */
    public function index( )
    {
        //there's no function overview page with e.g. a patient list (only for admins and users) -> just throw an error 
        show_error( 'Access denied', 403 );
    }//index()
    
	/**
     * Loads page patient/patient via the index and <b>list</b> page cmd.
     * Run the page via patient/patient/index/questionnaire/
     *
     * @since 0.7.0
     * @access private
     */
    function _questionnaire( $username )
    {
		$questionnaires = $this -> Questionnaire_tool_model -> get_released_not_finished_questionnaires( $this->data[TOP_NAV_STRING]['username'] );
		
		$this->data[CONTENT_STRING]['questionnaires'] = $questionnaires;

        $this->template->set(TOP_NAV_STRING, 'patient/top_nav', $this->data[TOP_NAV_STRING]);
        $this->template->set(CONTENT_STRING, 'patient/questionnaire/overview', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//_questionnaire()

    public function messages()
    {
        $username = $this->data[TOP_NAV_STRING]['username'];
		
        //$this->data[CONTENT_STRING]['therapeut'] is already set when this method gets called at the end of send_msg()
        //skip get_therapist_of_patient() in these cases and save an SQL query
        if( !isset( $this->data[CONTENT_STRING]['therapeut'] ) ) {
            $this->data[CONTENT_STRING]['therapeut'] = $this -> Patient_model -> get_therapist_of_patient( $username, $username, true );
        }

        $receivedMsgs = $this -> Message_model -> get_received_msgs( $username );
        
        $anzahlUnreadMsg = 0;
        $anzahlReadMsg = 0;

        //decrypt received messages
        $messageEncryptionConstant = $this -> Message_model -> get_msg_encryption_constant();
        
        for($i = 0; $i < sizeof($receivedMsgs); $i++) {
            if($receivedMsgs[$i]->randomKeyBytes != null) {
                //message was encrypted
                //this check can be removed once all messages get encrypted and the DB only contains encrypted messages
                $key = $messageEncryptionConstant.$receivedMsgs[$i]->randomKeyBytes;
                $receivedMsgs[$i]->betreff = openssl_decrypt($receivedMsgs[$i]->betreff, $receivedMsgs[$i]->cipher, $key, $options=0, $receivedMsgs[$i]->iv, $receivedMsgs[$i]->tagSubject);
                $receivedMsgs[$i]->nachricht = openssl_decrypt($receivedMsgs[$i]->nachricht, $receivedMsgs[$i]->cipher, $key, $options=0, $receivedMsgs[$i]->iv, $receivedMsgs[$i]->tagMessage);
            }

            //also count the number of read and unread messages here instead of making new queries for that
            if($receivedMsgs[$i] -> status == 1) {
                $anzahlReadMsg++;
            }
            else {
                $anzahlUnreadMsg++;
            }
        }
        
        $this->data[CONTENT_STRING]['receivedMsgs'] = $receivedMsgs;
        $this->data[CONTENT_STRING]['anzahlUnreadMsg'] = $anzahlUnreadMsg;
        $this->data[CONTENT_STRING]['anzahlReadMsg'] = $anzahlReadMsg;
        
        $sentMsgs = $this -> Message_model -> get_sent_msgs( $username );
        // decrypt sent messages
        for($i = 0; $i < sizeof($sentMsgs); $i++) {
            if($sentMsgs[$i]->randomKeyBytes != null) {
                //message was encrypted
                //this check can be removed once all messages get encrypted and the DB only contains encrypted messages
                $key = $messageEncryptionConstant.$sentMsgs[$i]->randomKeyBytes;
                $sentMsgs[$i]->betreff = openssl_decrypt($sentMsgs[$i]->betreff, $sentMsgs[$i]->cipher, $key, $options=0, $sentMsgs[$i]->iv, $sentMsgs[$i]->tagSubject);
                $sentMsgs[$i]->nachricht = openssl_decrypt($sentMsgs[$i]->nachricht, $sentMsgs[$i]->cipher, $key, $options=0, $sentMsgs[$i]->iv, $sentMsgs[$i]->tagMessage);
            }
        }

		$this->data[CONTENT_STRING]['sentMsgs'] = $sentMsgs;
        $this->data[CONTENT_STRING]['anzahlSentMsg'] = sizeof($sentMsgs);

        //patients are allowed to send messages to their therapist, themselves and all admins
        $therapist_receiver = new stdClass;
        $therapist_receiver->CODE = $this->data[CONTENT_STRING]['therapeut'];
        $allowed_receivers = array( $therapist_receiver );
        
        $own_username_receiver = new stdClass;
        $own_username_receiver->CODE = $username;
        array_push( $allowed_receivers, $own_username_receiver );
        
        $allowed_receivers = array_merge( $allowed_receivers, $this->membership_model->get_all_admin_codes( ) ) ;
        $this->data[CONTENT_STRING]['allowed_receivers'] = $allowed_receivers;

        $this->template->set(TOP_NAV_STRING, 'patient/top_nav', $this->data[TOP_NAV_STRING]);
        $this->template->set(CONTENT_STRING, 'patient/patient/messages', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//messages()

    public function showMessage( $msgid )
    {
        $msg = $this -> Message_model -> get_msg( $msgid );
        
        //decrypt the message
        $messageEncryptionConstant = $this -> Message_model -> get_msg_encryption_constant( );
        
        for($i = 0; $i < sizeof($msg); $i++) {
            if($msg[$i]->randomKeyBytes != null) {
                //message was encrypted
                //this check can be removed once all messages get encrypted and the DB only contains encrypted messages
                $key = $messageEncryptionConstant.$msg[$i]->randomKeyBytes;
                $msg[$i]->betreff = openssl_decrypt($msg[$i]->betreff, $msg[$i]->cipher, $key, $options=0, $msg[$i]->iv, $msg[$i]->tagSubject);
                $msg[$i]->nachricht = openssl_decrypt($msg[$i]->nachricht, $msg[$i]->cipher, $key, $options=0, $msg[$i]->iv, $msg[$i]->tagMessage);
            }
        }
        
        $this->data[CONTENT_STRING]['msg'] = $msg;
        $this->data[CONTENT_STRING]['therapeut'] = $this -> Patient_model -> get_therapist_of_patient( $this -> data[TOP_NAV_STRING]['username'], $this -> data[TOP_NAV_STRING]['username'], true );

        //mark message as read
		$this -> Message_model -> set_status( $msgid, 1, $this->data[TOP_NAV_STRING]['username'] );

        $this->template->set(TOP_NAV_STRING, 'patient/top_nav', $this->data[TOP_NAV_STRING]);
        $this->template->set(CONTENT_STRING, 'patient/patient/showMessage', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//showMessage()

    public function send_msg( )
	{
		$username = $this->data[TOP_NAV_STRING]['username'];
        
        //Holen der Daten
        $request['betreff'] = $this -> input -> post( 'betreff' );
		$request['nachricht'] = $this -> input -> post( 'nachricht' );
        $receiver =  $this -> input -> post( 'empfaenger' );
        $sender = $username;

        //patients are allowed to send messages to their therapist, themselves and all admins
        $this->data[CONTENT_STRING]['therapeut'] = $this -> Patient_model -> get_therapist_of_patient( $username, $username, true );

        $admin_codes = $this->membership_model->get_all_admin_codes( );
        
        $receiver_object = new stdClass;
        $receiver_object->CODE = $receiver;

        if( $receiver !== $this->data[CONTENT_STRING]['therapeut'] && $receiver !== $username && !in_array( $receiver_object, $admin_codes ) ) {
            //patients can send messages to a supervisor only if the supervisor sent them a message first
            if( $this -> membership_model -> get_role( $receiver ) !== 'supervisor' OR !$this -> Message_model -> exist_msg_who_to_who( $receiver, $username ) ) {
                show_error( 'Error sending message. You can only send messages to your therapist, yourself and all admins, as well as supervisors who sent you a message first. Your subject was "'.$request['betreff'].'" and your message was: '.$request['nachricht'], 403 );
            }
        }
        
        //encrypt the message content and subject before storing it in the DB
        $messageEncryptionConstant = $this -> Message_model -> get_msg_encryption_constant( );
        $keySize = $this -> Message_model -> get_msg_encryption_key_size( );
        $randomKeyBytes = openssl_random_pseudo_bytes($keySize);
        $key = $messageEncryptionConstant.$randomKeyBytes;

        $cipher = $this -> Message_model -> get_msg_encryption_cipher( );

        $ivlen = openssl_cipher_iv_length( $cipher );
        $iv = openssl_random_pseudo_bytes( $ivlen );
        $messageEncrypted = openssl_encrypt( nl2br( $request['nachricht'] ), $cipher, $key, $options=0, $iv, $tagMessage );
        $subjectEncrypted = openssl_encrypt( $request['betreff'], $cipher, $key, $options=0, $iv, $tagSubject );

		//Daten in die DB schreiben
		$id = $this -> Message_model -> insert_msg( $sender, $receiver, $subjectEncrypted, $messageEncrypted, $cipher, $iv, $tagSubject, $tagMessage, $randomKeyBytes );
        
        if( is_null( $id ) ) {
            show_error( 'Error sending message. You can only send messages to your therapist, yourself and all admins, as well as supervisors who sent you a message first. Your subject was "'.$request['betreff'].'" and your message was: '.$request['nachricht'], 403 );
        }

		$this -> messages( );
    }//send_msg()
}//class Patient

/* End of file patient.php */
/* Location: ./application/controllers/patient/patient.php */