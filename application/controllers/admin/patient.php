<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for administrade all patients.
 * 
 * @package Controller
 * @subpackage Admin
 * 
 * @author Martin Kock <code @ deeagle.de>
 */
class Patient extends CI_Controller 
{

    /**
     * Constructor.
     * 
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
        $username = $this->session->userdata( 'username' );
        $this->data[TOP_NAV_STRING]['username'] = $username;
        $this->load->Model('Patient_model');
        $this->load->Model('Questionnaire_model');
        $this->load->Model('Membership_model');
        $this->load->Model('Message_model');
        $this-> load-> library('form_validation'); 
        $this-> load-> helper ('security');

        //Check User-Credentials for page access rights.
        $this->is_logged_in = $this-> session_model ->is_logged_in($this->session->all_userdata());
        $this->is_admin = $this -> membership_model -> get_role($username) === 'admin';

        if($this->is_logged_in)
        {
            if($this->is_admin)
            {
                $this-> data['content']['userrole'] = 'admin';
            }
            else //if ($is_admin)=FALSE
            {
                show_error ('Access denied for your Userrole', 403);
            } //else
        }
        else //if ($is_logged_in)
        {
            $this -> template -> set( HEADER_STRING, 'all/header', $this -> data[HEADER_STRING] );
            $this -> template -> set( TOP_NAV_STRING, 'guest/top_nav', $this -> data[TOP_NAV_STRING] );
            $this -> template -> set( CONTENT_STRING, 'guest/login_form', $this -> data[CONTENT_STRING] );
        } // else
    }

    public function index()
    {
        $this->template->set('header', 'all/header', $this->data['header']);
        $this->template->set('top_nav', 'admin/top_nav', $this->data['top_nav']);
        $this->template->set('content', 'admin/patient/index', $this->data['content']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);

        $this->template->load('template');
    }

    public function new_patientlogin()
    {
        if(empty($_POST)) // Propably first call to Page
        {
            $this-> data['content']['profile']['rechte_zw'] = 0;
        }
        else
        {
            // get input Patient_Data
            $profile_data = array(
                'first_name'    => $this->input->post('first_name'),
                'last_name'     => $this->input->post('last_name'),
                'initials'      => $this->input->post('initials'),
                'role'          => 'patient',
                'email'         => $this->input->post('email'),
                'password'      => $this->input->post('password'),
                'passconf'      => $this->input->post('passconf'),
                'rechte_zw'     => (($this->input->post('rechte_zw') === 'on') ? 1:0)
            );
                    
            $profile_data_valid = $this -> validate_profile_data();
            $password_valid = $this -> validate_password();
            $initials_unique = $this -> membership_model -> get_id($profile_data['initials']) == -1;
            $initials_valid = $this-> membership_model -> validate_initial_string($profile_data['initials'], 'patient');

            if($initials_valid AND $profile_data_valid AND $initials_unique AND ($initials_valid[0] == TRUE)) //Input Data is Valid
            {   
                //Create User and Set Flash_Session_Data
                $patient_id = $this->membership_model->create_new_user($profile_data);
                        
                if (!empty($patient_id))
                {
                    $this-> session -> set_flashdata('creation_success', TRUE);
                    redirect("admin/user/edit_user/{$patient_id}");
                }
                }
                else // Input Data is Valid --> Load profile_data for repopulation and Validation_Errors
                {
                    $this->data ['content']['msg'] = TRUE;
                    $this->data ['content']['initials_errors'] = $initials_valid[1];
                    $this->data ['content']['profile'] = $profile_data;
                } // else  ( -> data is invalid)
            } // else ($_POST is not empty)

            $this -> template -> set( 'header', 'all/header', $this -> data['header'] );
            $this -> template -> set( 'top_nav', 'admin/top_nav', $this -> data['top_nav'] );
            $this -> template -> set( 'content', 'admin/patient/new_patientlogin', $this -> data['content'] );
            $this -> template -> set( 'footer', 'all/footer', $this -> data['footer'] );
        
            $this -> template -> load( 'template' );
    }



    /**
     * Lists all registered Patients
     */
    public function list_all()
    {
        $username = $this->session->userdata( 'username' );
		$patients = $this->Patient_model->get_all_patients( $username );

        $this->data[CONTENT_STRING]['patients'] = $patients;

        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(TOP_NAV_STRING, 'admin/top_nav', $this->data[TOP_NAV_STRING]);
        $this->template->set(CONTENT_STRING, 'admin/patient/list_all', $this->data[CONTENT_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
 	   
	   
        $this->template->load('template');
    }//list_all()

    /**
     * Search for a patients containing a given string
     */
    public function search()
    {
        $username = $this->session->userdata( 'username' );

        $patientcode = $this -> input -> post( 'patientcode' );
        $this->data[CONTENT_STRING]['searched_patientcode'] = $patientcode;
        
		$patient_exists = FALSE;
		$patient_login_exists = array();
		$patient_data = NULL;
		if( isset( $patientcode ) AND $patientcode != FALSE )
		{
			$patient_data = $this -> Patient_model -> get_patient_like( $username, $patientcode );
			if( !is_null( $patient_data ) )
			{
				$patient_exists = TRUE;

                foreach($patient_data as $p){
                    $patient_login_exists[] = $this -> Patient_model -> does_login_exist( $p->code );
                }
			}
		}
	
        $this->data[CONTENT_STRING]['patient_data'] = $patient_data;
        $this->data[CONTENT_STRING]['patient_exists'] = $patient_exists;
        $this->data[CONTENT_STRING]['patient_login_exists'] = $patient_login_exists;
        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;

        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(TOP_NAV_STRING, 'admin/top_nav', $this->data[TOP_NAV_STRING]);
        $this->template->set(CONTENT_STRING, 'admin/patient/search', $this->data[CONTENT_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
 	   
        $this->template->load('template');
    }//search()

    /**
     * List the count of instances of the FEP-2 questionnaire
     */
    public function instance_count(){
        
        $this->data[CONTENT_STRING]['fep2'] = $this->Questionnaire_model->get_fep2_count();
        
        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(TOP_NAV_STRING, 'admin/top_nav', $this->data[TOP_NAV_STRING]);
        $this->template->set(CONTENT_STRING, 'admin/patient/instance_count', $this->data[CONTENT_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
            
        $this->template->load('template');
    }//instance_count()

    public function messages()
    {
        $username = $this->session->userdata( 'username' );
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
        
        $this->template->set('header', 'all/header', $this->data['header']);
        $this->template->set('top_nav', 'admin/top_nav', $this->data['top_nav']);
        $this->template->set('content', 'admin/patient/messages', $this->data['content']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);

        $this->template->load('template');
    }//messages()

    public function showMessage( $msgid )
    {
        $username = $this->session->userdata( 'username' );
        
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
                
        $this -> data['content']['msg'] = $msg;
                
        $this -> template -> set('header', 'all/header', $this->data['header']);
        $this -> template -> set('top_nav', 'admin/top_nav', $this->data['top_nav']);
        $this -> template -> set('content', 'admin/patient/showMessage', $this->data['content']);
        $this -> template -> set('footer', 'all/footer', $this->data['footer']);
        $this -> template -> load('template');
    }//showMessage()

    public function send_msg( )
	{
		$username = $this->data['top_nav']['username'];

        //Holen der Daten
        $request['betreff'] = $this -> input -> post( 'betreff' );
        $request['nachricht'] = $this -> input -> post( 'nachricht' );
        
        //admin can send messages to all users
        $receiver = $this -> input -> post( 'empfaenger' );
        $sender = $username;
        
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
            show_error( 'Error sending message. Your subject was "'.$request['betreff'].'" and your message was: '.$request['nachricht'], 403 );
        }

		$this -> messages( );
    }//send_msg()

    private function validate_profile_data() // Checks if input profile_data is valid.
    {              
        $is_valid = FALSE;

        $this -> form_validation -> set_rules( 'first_name', 'First name', 'trim|min_length[2]|xss_clean');
        $this -> form_validation -> set_rules( 'last_name', 'Last name', 'trim|min_length[2]|xss_clean');
        $this -> form_validation -> set_rules( 'initials', 'Initals', 'trim|xss_clean|required');
        $this -> form_validation -> set_rules( 'email', 'Email', 'trim|valid_email|xss_clean');
        //$this -> form_validation -> set_rules( 'role', 'Role', 'trim|xss_clean|required');

        $is_valid = $this -> form_validation -> run();

        return $is_valid;
    }//validate_profile_input

    private function validate_password()
    {
        $is_valid = FALSE;

        $this -> form_validation -> set_rules( 'password', 'Password', 'trim|required|matches[passconf]|min_length[5]|xss_clean');
        $this -> form_validation -> set_rules( 'passconf', 'Passconf', 'trim|matches[password]|required');

        $is_valid = $this -> form_validation -> run();

        return $is_valid;
    }//validate_password
}
?>