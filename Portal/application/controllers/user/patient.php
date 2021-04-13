<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for users patients.
 * 
 * @package Controller
 * @subpackage User
 * 
 * @author Martin Kock <code @ deeagle.de>
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
        $this->data = array(HEADER_STRING => array('title' => 'Patientendetails'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this->load->library('user_agent');
        $this->load->Model( 'User_model' );
        $this->load->Model( 'Patient_model' );
		$this->load->Model( 'Therapy_model' );
		$this->load->Model( 'Questionnaire_tool_model' );
        $this->load->Model( 'Message_model');
        $this->load->Model( 'Questionnaire_model');
        $this->load->Model( 'SB_model');

		//Laden der Sprachdateien
        $this->lang->load( 'user_patient' );
        
        $this -> template -> set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this -> template -> set( FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING] );

        if( $this->session_model->is_logged_in( $this->session->all_userdata() ) )
        {
            $this->data[TOP_NAV_STRING]['username'] = $this -> session -> userdata( 'username' );
            $this->data[CONTENT_STRING]['userrole'] = $this -> membership_model -> get_role( $this->data[TOP_NAV_STRING]['username'] );
            
            if( $this->data[CONTENT_STRING]['userrole'] !== 'user' && $this->data[CONTENT_STRING]['userrole'] !== 'admin' && $this->data[CONTENT_STRING]['userrole'] !== 'supervisor' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            redirect( 'login' );
        }
    }//__construct()
    
    public function index()
    {
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'user/patient/index', $this->data[CONTENT_STRING] );
        $this -> template -> load( 'template' );
    }//index()

    public function list_all( $show_all = false )
    {
        if( ($this -> data[CONTENT_STRING]['userrole'] === 'privileged_user' || $this -> data[CONTENT_STRING]['userrole'] === 'admin') && $show_all ){
            $this -> data[CONTENT_STRING]['patients'] = $this -> Patient_model -> get_all_patients( 'admin' );
            $this -> data[CONTENT_STRING]['show_all'] = true;
        } else {
            $this -> data[CONTENT_STRING]['patients'] = $this -> Patient_model -> get_all_patients( $this -> data[TOP_NAV_STRING]['username'] );
            $this -> data[CONTENT_STRING]['show_all'] = false;
        }

        $this -> data[CONTENT_STRING]['status'] = $this -> Therapy_model -> extract_status_data_from_patients( $this -> data[CONTENT_STRING]['patients'] );
    

        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'user/patient/list_all', $this->data[CONTENT_STRING] );
        $this -> template -> load( 'template' );
    }//list_all()

    public function list( $patientcode )
    {
        $username = $this -> data[TOP_NAV_STRING]['username'];
        $user_role = $this -> data[CONTENT_STRING]['userrole'];
        
        if( $user_role !== 'admin' && $user_role !== 'privileged_user' && !$this -> Patient_model -> is_therapist_or_supervisor_of_patient( $username, $patientcode ) )
        {    
            show_error( 'Access denied. It\'s not a patient of yours!', 403 );
        }
        else
        {
            $status = $this->Patient_model->get_status( $patientcode );
            $lastHscl = $this->Patient_model->get_last_hscl( $patientcode);
            $this -> data[CONTENT_STRING]['lastHscl'] = $lastHscl;
            
           // $color = $this->Patient_model->get_feedback_of_patient($patientcode);
            $boundary = $this->Patient_model->get_boundary($patientcode, $lastHscl->instance, "BOUNDARY_UEBERSCHRITTEN");
            $color = $boundary == NULL ? 'green' : 'red';
            $this->data[CONTENT_STRING]['color'] = $color;

            $this -> data[CONTENT_STRING]['patientcode'] = $patientcode;
            $this -> data[CONTENT_STRING]['status'] = $status; 	
            
            $view_status = $this -> Patient_model -> get_view_status( $patientcode );
            
            $this -> data[CONTENT_STRING]['rechte_feedback'] = $this -> membership_model -> is_rechte_set( $username, 'rechte_feedback' );

            $this -> data[CONTENT_STRING]['recommendation_status'] = $this -> User_model -> get_status_recommendation( $patientcode, $username );
            $this -> data[CONTENT_STRING]['sb_allowed'] = $this -> Patient_model -> get_sb_allowed( $patientcode );

            $this -> data[CONTENT_STRING]['has_gas'] = $this -> SB_model -> has_gas( $patientcode );
            $this -> data[CONTENT_STRING]['has_request'] = $this -> SB_model -> has_filled_request( $patientcode );
            $this -> data[CONTENT_STRING]['last_instance'] = $this -> SB_model -> getLastInstance( $patientcode );

            $therapists = $this -> membership_model -> get_all_users('admin','users');
            $this -> data[CONTENT_STRING]['therapists'] = $therapists;

            $assigned_therapist = $this -> Patient_model -> get_therapist_of_patient($username, $patientcode);
            $this -> data[CONTENT_STRING]['assigned_therapist'] = $assigned_therapist;

            $state = $this-> Patient_model -> get_state($patientcode);
            $this -> data[CONTENT_STRING]['patient_state'] = $state;

            $this -> template -> set( CONTENT_STRING, 'user/patient/feedback/details_feedback_2', $this -> data[CONTENT_STRING] );
            
            $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
            $this -> template -> load( 'template' );
        }//else
    }//list()

    public function assign_therapist($patientcode){
        $therapist = $this -> input -> post('assignment');

        $this->Patient_model->assign_therapist_to_patient($therapist,$patientcode);

        $user_role = $this -> data[CONTENT_STRING]['userrole'];

        if($user_role == 'admin'){
            redirect('user/patient/list/'.$patientcode);
        } else {
            redirect($user_role.'/dashboard');
        }
    }

    public function therapy_state_change(){
        $patientcode = $this -> input -> post('patientcode');
        $state = $this -> input -> post('state');

        $this->Patient_model->set_state($patientcode,$state);
    }

    public function messages()
    {
        $username = $this->data[TOP_NAV_STRING]['username'];
		
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

        //therapists are allowed to send messages to their patients, themselves and all admins and supervisors
        $allowed_receivers = $this -> Patient_model -> get_patient_codes_of_therapist( $username );
        
        $own_username_receiver = new stdClass;
        $own_username_receiver->CODE = $username;
        array_push( $allowed_receivers, $own_username_receiver );
        
        $allowed_receivers = array_merge( $allowed_receivers, $this -> membership_model -> get_all_admin_and_supervisor_codes( ) ) ;
        $this->data[CONTENT_STRING]['allowed_receivers'] = $allowed_receivers;


        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this->template->set(CONTENT_STRING, 'user/patient/messages', $this->data[CONTENT_STRING]);
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

        //mark message as read
		$this -> Message_model -> set_status( $msgid, 1, $this->data[TOP_NAV_STRING]['username'] );

        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this->template->set(CONTENT_STRING, 'user/patient/showMessage', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//showMessage()

    public function send_msg( )
	{
		$username = $this->data[TOP_NAV_STRING]['username'];

        //Holen der Daten
        $request['betreff'] = $this -> input -> post( 'betreff' );
		$request['nachricht'] = $this -> input -> post( 'nachricht' );
        $sender = $username;
        
        $receiver = $this -> input -> post( 'empfaenger' );
        
        //therapists are allowed to send messages to their patients and all admins and supervisors
        $receiver_role = $this -> membership_model -> get_role( $receiver );
        if( !$this -> Patient_model -> is_therapist_of_patient( $username, $receiver ) AND !( $receiver_role === 'admin' OR  $receiver_role === 'supervisor') ) {
            show_error( 'Error sending message. You can only send messages to your patients and all admins and supervisors. Your subject was "'.$request['betreff'].'" and your message was: '.$request['nachricht'], 403 );
        }

        //encrypt the message content and subject before storing it in the DB
        $messageEncryptionConstant = $this -> Message_model -> get_msg_encryption_constant( );
        $keySize = $this -> Message_model -> get_msg_encryption_key_size( );
        $randomKeyBytes = openssl_random_pseudo_bytes($keySize);
        $key = $messageEncryptionConstant.$randomKeyBytes;

        $cipher = $this -> Message_model -> get_msg_encryption_cipher( );

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $messageEncrypted = openssl_encrypt(nl2br( $request['nachricht'] ), $cipher, $key, $options=0, $iv, $tagMessage);
        $subjectEncrypted = openssl_encrypt($request['betreff'], $cipher, $key, $options=0, $iv, $tagSubject);

        //Daten in die DB schreiben
        $id = $this -> Message_model -> insert_msg( $sender, $receiver, $subjectEncrypted, $messageEncrypted, $cipher, $iv, $tagSubject, $tagMessage, $randomKeyBytes );
        
        if( is_null( $id ) ) {
            show_error( 'Error sending message. You can only send messages to your patients and all admins and supervisors. Your subject was "'.$request['betreff'].'" and your message was: '.$request['nachricht'], 403 );
        }

		redirect( 'user/patient/messages' );
    }//send_msg()

    public function diagnostiktool($patientcode)
    {
        $username = $this -> data[TOP_NAV_STRING]['username'];
        $user_role = $this -> data[CONTENT_STRING]['userrole'];
        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        $instance = "PR";

        if( $user_role === 'admin' OR $user_role === 'privileged_user' OR ($user_role === 'supervisor' AND $this->Patient_model->is_supervisor_of_patient( $username, $patientcode ))){    
            $diagnose_user = 'admin';
        } else{
            $diagnose_user = $username;
        }

        if( $diagnose_user !== 'admin' && !$this -> Patient_model -> is_therapist_of_patient( $diagnose_user, $patientcode ) )
        {
            show_error( 'Access denied. It\'s not a patient of yours!', 403 );
        }
        else
        {
            $is_patient_of_user = $this -> Patient_model -> is_patient_of_user( $username, $username, $patientcode );
            //TODO $recommendation_status is missing:
            if ( empty($recommendation_status[0]) AND $user_role === 'user' AND $is_patient_of_user){
                $this->User_model->insert_recommendation_status( $username, $patientcode );
            }
                
			$this->data[CONTENT_STRING]['suicideItems'] = $this->Questionnaire_model->get_suicide_data($patientcode, $instance);

            $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
            $this->template->set(CONTENT_STRING, 'user/patient/diagnostiktool', $this->data[CONTENT_STRING]);
            $this->template->load('template');    
        }//else
    }//diagnostiktool()
}//class Patient

/* End of file patient.php */
/* Location: ./application/controllers/user/patient.php */
