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
        $this->data = array('header' => array('title' => 'Patientendetails'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );
        $this->load->library('user_agent');
        $this->load->Model( 'User_Model' );
        $this->load->Model( 'Patient_Model' );
		$this->load->Model( 'Therapy_model' );
		$this->load->Model( 'Membership_Model' );
        $this->load->Model( 'Questionnaire_tool_model' );
        $this->load->Model( 'Message_model');
        $this->load->Model( 'Questionnaire_model');
		//Laden der Sprachdateien
        $this->lang->load( 'user_patient' );
        
        $this -> template -> set('header', 'all/header', $this->data['header']);
        $this -> template -> set( 'footer', 'all/footer', $this->data['footer'] );

        $is_logged_in = $this->session_model->is_logged_in( $this->session->all_userdata() );
        
        if( $is_logged_in )
        {
            $this->data['top_nav']['username'] = $this -> session -> userdata( 'username' );
            $this->data['content']['userrole'] = $this -> membership_model -> get_role( $this->data['top_nav']['username'] );
            
            if( $this->data['content']['userrole'] !== 'user' && $this->data['content']['userrole'] !== 'admin' && $this->data['content']['userrole'] !== 'supervisor' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            $this->template->set('top_nav', 'guest/top_nav', $this->data['top_nav']);
            $this->template->set('content', 'guest/login_form', $this->data['content']);
            $this->template->load('template');
        }
    }//__construct()
    
    public function index()
    {
        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'user/patient/index', $this->data['content'] );
        $this -> template -> load( 'template' );
    }//index()

    public function list_all( $show_all = false )
    {
        $username = $this -> data['top_nav']['username'];
        
        if( $this -> data['content']['userrole'] === 'priviledged_user' AND $show_all ){
            $patients = $this -> Patient_model -> get_all_patients( 'admin' );
            $this -> data['content']['show_all'] = true;
        } else {
            $patients = $this -> Patient_model -> get_all_patients( $username );
            $this -> data['content']['show_all'] = false;
        }

        $status = $this -> Therapy_model -> extract_status_data_from_patients( $patients );
	
		$this -> data['content']['patients'] = $patients;
		$this -> data['content']['status'] = $status;
        
        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this -> template -> set( 'content', 'user/patient/list_all', $this->data['content'] );
        $this -> template -> load( 'template' );
    }//list_all()

    public function list( $patientcode )
    {
        $username = $this->data['top_nav']['username'];
		
        //if no $patientcode was set
        if( !isset( $patientcode ) )
        {
            log_message( 'warn', 'List patient request without patientdata.');
            $this -> index();
        }//if
        else //Patientcode is set
        {
            $user_role = $this -> data['content']['userrole'];
            
            if( $user_role === 'admin' OR $user_role === 'priviledged_user' OR ($user_role === 'supervisor' AND $this -> Patient_model -> is_supervisor_of_patient( $username, $patientcode ) ) ) {
                $patient = $this -> Patient_Model -> get_patient( 'admin', $patientcode );
            } else {
                $patient = $this -> Patient_Model -> get_patient( $username, $patientcode );
            }
            
            if( !isset( $patient ) AND $user_role !== 'admin' AND $user_role !== 'priviledged_user' ) //NOT SET --> No patient of user
            {
                show_error( 'Access denied. It\'s not a patient of yours!', 403 );
            }//if
            else // patient to therapeut is correct
            {
                $status = $this->Patient_model->get_status( $patientcode );
                $lastHscl = $this->Patient_model->get_last_hscl( $patientcode);
									
                $color = $this->Patient_model->get_feedback_of_patient($patientcode);	
                
                $this->data['content']['color'] = $color;
                
                $this->data['content']['hasOT'] = false;
                
                foreach ( $status as $s ) {
                   if( strpos( $s -> instance, 'OT' ) !== false ) {
                      $this -> data['content']['hasOT'] = true;
                   }	
                }

                $this -> data['content']['patient'] = $patient;
                $this -> data['content']['patientcode'] = $patientcode;
                $this -> data['content']['status'] = $status; 	
                $this -> data['content']['lastHscl'] = $lastHscl;    
                
                //CI beschwert sich wenn Argument nicht explizit gegeben; in alter Version geht es jedoch auch ohne
                $sb_allowed = $this -> Patient_model -> get_sb_allowed( $patientcode );
                if( isset( $sb_allowed ) ) {
                    $this -> data['content']['sb_allowed'] = $sb_allowed;
                }
                
                $view_status = $this -> Patient_model -> get_view_status( $patientcode );
                
                if( $view_status == 2 OR $user_role === 'admin' ) { // neues Feedback
                    $this -> template -> set( 'content', 'user/patient/feedback/details_feedback_2', $this -> data['content'] );
                }
                elseif( $view_status == 1 ) { // Kontrollgrp
                    // Patienten, die die Therapie abgebrochen oder beendet haben, soll das "neue" Feedback angezeigt werden
                    $dok_status = $this -> Patient_model -> get_status_of_patient( $patientcode ); 
                    
                    if ( ($dok_status >= 2 && $dok_status <= 4 ) || ($dok_status >= 7 && $dok_status <= 11 ) ) {
                        $this -> template -> set( 'content', 'user/patient/feedback/details_feedback_2', $this->data['content'] );
                    }
                    else{
                        //TODO details_feedback_monitoring view isn't created yet. copy it from the admin views?
                        $this -> template -> set( 'content', 'user/patient/feedback/details_feedback_monitoring', $this->data['content'] );
                    }   
                }
                else {
                    /*
                    Currently some patients still have another view_status (e.g. 0) which would result in a broken page being displayed.
                    The view_status was used for internal testing only anyway and isn't really needed anymore.
                    Therefore view_status = 2 gets assumed by default. 
                    */
                    $this -> template -> set( 'content', 'user/patient/feedback/details_feedback_2', $this -> data['content'] );
                }
                
                $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
                $this -> template -> load( 'template' );
            }//else
        }//else
    }//list()    

    public function messages()
    {
        $username = $this->data['top_nav']['username'];
		$user_id = $this->membership_model->get_id( $username );

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
        
        $this->data['content']['receivedMsgs'] = $receivedMsgs;
        $this->data['content']['anzahlUnreadMsg'] = $anzahlUnreadMsg;
        $this->data['content']['anzahlReadMsg'] = $anzahlReadMsg;

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

		$this->data['content']['sentMsgs'] = $sentMsgs;
		$this->data['content']['anzahlSentMsg'] = sizeof($sentMsgs);

        //therapists are allowed to send messages to their patients, themselves and all admins and supervisors
        $allowed_receivers = $this -> Patient_model -> get_patient_codes_of_therapist( $username );
        
        $own_username_receiver = new stdClass;
        $own_username_receiver->CODE = $username;
        array_push( $allowed_receivers, $own_username_receiver );
        
        $allowed_receivers = array_merge( $allowed_receivers, $this -> Membership_Model -> get_all_admin_and_supervisor_codes( ) ) ;
        $this->data['content']['allowed_receivers'] = $allowed_receivers;


        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this->template->set('content', 'user/patient/messages', $this->data['content']);
        $this->template->load('template');
    }//messages()
    
    public function showMessage( $msgid )
    {
        $username = $this->data['top_nav']['username'];

		//Nachrichtenstatus wird auf "Gelesen" gesetzt
		$this -> Message_model -> set_status( $msgid, 1, $username );

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
        
        $this->data['content']['msg'] = $msg;

        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this->template->set('content', 'user/patient/showMessage', $this->data['content']);
        $this->template->load('template');
    }//showMessage()

    public function send_msg( )
	{
		$username = $this->data['top_nav']['username'];

        //Holen der Daten
        $request['betreff'] = $this -> input -> post( 'betreff' );
		$request['nachricht'] = $this -> input -> post( 'nachricht' );
        $sender = $username;
        
        $receiver = $this -> input -> post( 'empfaenger' );
        
        //therapists are allowed to send messages to their patients and all admins and supervisors
        $receiver_role = $this -> Membership_Model -> get_role( $receiver );
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

		$this -> messages( );
    }//send_msg()

    public function diagnostiktool($patientcode){
        $username = $this -> data['top_nav']['username'];
        $user_role = $this -> data['content']['userrole'];
        $instance = "PR";
        
        //if no $patientcode was set
        if( !isset( $patientcode ) )
        {
            log_message( 'warn', 'diagnostiktool request without patientdata.');
            $this->index();
        }//if
        else //Patientcode is set
        {
            if( $user_role === 'admin' OR $user_role === 'priviledged_user' OR ($user_role === 'supervisor' AND $this->Patient_model->is_supervisor_of_patient( $username, $patientcode ))){    
                $diagnose_user = 'admin';
            } else{
                $diagnose_user = $username;
            }

            $patient = $this->Patient_model->get_patient( $diagnose_user, $patientcode );
            
            if( !isset( $patient ) ) //NOT SET --> No patient of user
            {
                  show_error( 'Access denied. It\'s not a patient of yours!', 403 );
            }//if
            else // patient to therapeut is correct
            {
                //TODO $recommendation_status is missing and $is_patient_of_user is commented out for some reason
                //$is_patient_of_user = $this->Patient_model->_is_patient_of_user($username, $patientcode);
                if ( empty($recommendation_status[0]) AND $user_role === 'user' AND $is_patient_of_user){
                    $this->User_model->insert_recommendation_status( $username, $patientcode );
                }
                
                //$this->Patient_model->set_r2_test( $patientcode );  
				$this->data['content']['suicideItems'] = $this->Questionnaire_model->get_suicide_data($patientcode,$instance);
                //$this->Patient_model->set_nn_therapists_test( $patientcode );  

                $this->data['content']['patient'] = $patient;

                $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
                $this->template->set('content', 'user/patient/diagnostiktool', $this->data['content']);
                $this->template->load('template');    
            }//else
        }//else
    }
    //diagnostiktool()
}
