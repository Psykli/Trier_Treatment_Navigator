<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for administrade all patients.
 * 
 * @package Controller
 * @subpackage Admin
 * 
 * @author Martin Kock <code @ deeagle.de>
 */
class USER extends CI_Controller
{
    /**
     * Constructor.
     * 
     * @access private
     */
    function __construct( )
    {
        parent::__construct( );
        $this->data = array(HEADER_STRING => array('title' => 'Benutzer'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this-> load-> library('email');
        $this-> load-> library('form_validation'); 
        $this-> load-> library('user_agent');
        $this-> load-> helper ('security');

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

        $this -> lang -> load('new_user');

        $this -> template -> set( HEADER_STRING, 'all/header', $this -> data[HEADER_STRING] );
        $this -> template -> set( FOOTER_STRING, 'all/footer', $this -> data[FOOTER_STRING] );
    } //__contruct()

    public function index( )
    {
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'admin/user/index', $this -> data[CONTENT_STRING] );
        $this -> template -> load( 'template' );       
    }//index()    

    public function list_all( $role = 'all', $order_column = NULL, $ordering = NULL )
    {
        $username = $this-> session -> userdata('username');
        
        if( !is_null( $order_column ) && !is_null( $ordering ) )
        {
            if( $ordering == 0 ) {
                $order = 'ASC';
            }
            else {
                $order = 'DESC';
            }
                
            $this -> data[TOP_NAV_STRING]['order_column'] = $order_column;
            $this -> data[TOP_NAV_STRING]['ordering'] = abs( $ordering - 1 );
        }
        else
        {
            $order = NULL;
        }

        // Get all Users from DB and add to data[TOP_NAV_STRING]
        $users = $this-> membership_model -> get_all_users($username, $role, $order_column, $order);

        $this -> data[TOP_NAV_STRING]['users'] = $users;
        $this -> data[TOP_NAV_STRING]['users']['list'] = $role;
        
        $user_counts = $this -> membership_model -> get_count_of_roles_combined( $username );
        $this -> data[TOP_NAV_STRING]['users']['count']['all'] = $user_counts -> all;
        $this -> data[TOP_NAV_STRING]['users']['count']['admins'] = $user_counts -> admins;
        $this -> data[TOP_NAV_STRING]['users']['count']['users'] = $user_counts -> users;
        $this -> data[TOP_NAV_STRING]['users']['count']['migrated'] = $user_counts -> migrated;

        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'admin/user/list_all', $this -> data[CONTENT_STRING] );
        $this -> template -> load( 'template');
    }//list_all()

    public function edit_user($id)
    {
        //Get Userdata from DB
        $userdata = $this-> membership_model -> get_userdata($id);

        if(empty($_POST))
        {
            $this -> data[TOP_NAV_STRING]['userdata'] = $userdata;
        }
        else
        {
            $old_userdata = $userdata;

            $userdata = array(
                'FIRST_NAME'    => $this->input->post('first_name'),
                'LAST_NAME'     => $this->input->post('last_name'),
                'INITIALS'      => $this->input->post('initials'),
                'kohorte'       => $this->input->post('kohorte'),
                'email'         => $this->input->post('email'),
                'ROLE'          => $this->input->post('role'),                
                'rechte_feedback'       => array_key_exists('rechte_feedback', $_POST) ? 1 : 0,
                'rechte_entscheidung'   => array_key_exists('rechte_entscheidung', $_POST) ? 1 : 0,
                'rechte_verlauf_normal' => array_key_exists('rechte_verlauf_normal', $_POST) ? 1 : 0,
                'rechte_verlauf_online' => array_key_exists('rechte_verlauf_online', $_POST) ? 1 : 0,
                'rechte_verlauf_gruppe' => array_key_exists('rechte_verlauf_gruppe', $_POST) ? 1 : 0,
                'rechte_verlauf_seminare'   => array_key_exists('rechte_verlauf_seminare', $_POST) ? 1 : 0,
                'rechte_zw'          => array_key_exists('rechte_zw', $_POST) ? 1 : 0,
            );

            //Evaluate Changes between Database and Forminput
            $changed_input = $this-> check_inputChange($old_userdata, $userdata);

            //Change Password if Password_Change Checkbox is set
            if ($this-> input -> post ('change_password') == 'on')
            {
                $pw_changed = FALSE;
                $pw_valid = $this-> validate_password();

                if ($pw_valid)
                {
                    $password = $this-> input -> post('password');
                    $this-> membership_model -> set_user_password($userdata['INITIALS'], $password);
                    $pw_changed = TRUE;
                }//if
            }//if

            //Change User_Data and Access Rights if something changed
            if (!empty($changed_input))
            {
                $userdata_changed = FALSE;
                $input_valid = $this-> validate_profile_data();

                if($input_valid)
                {
                    foreach($changed_input as $key)
                    {
                        switch($key)
                        {
                            case 'FIRST_NAME':
                                $this-> membership_model -> user_set_first_name($userdata['INITIALS'], $userdata['FIRST_NAME']);
                                break;
                            case 'LAST_NAME':
                                $this-> membership_model -> user_set_last_name($userdata['INITIALS'], $userdata['LAST_NAME']);
                                break;
                            case 'kohorte':
                                $this-> membership_model -> user_set_kohorte($userdata['INITIALS'], $userdata['kohorte']);
                                break;
                            case 'ROLE':
                                $this-> membership_model -> user_set_role($userdata['INITIALS'], $userdata['ROLE']);
                                break;
                            case 'email':
                                $this-> membership_model -> user_set_email($userdata['INITIALS'], $userdata['email']);
                                break;
                            default: 
                                $this-> membership_model -> user_set_access_rights($userdata['INITIALS'], $key, $userdata[$key]);
                                break;
                        } //switch
                    }//foreach
                    $userdata_changed = TRUE; 
                }//if
            }//if


            //Evaluate if changes were successful and load either the new userdata or show validation errors before calling view
            if((isset($pw_changed) AND $pw_changed) OR (isset($userdata_changed) AND $userdata_changed))
            {
                $this-> data[TOP_NAV_STRING]['changes_success'] = TRUE;
                $this-> data[TOP_NAV_STRING]['userdata'] = $this-> membership_model -> get_userdata($id);
            }//if
            else
            {
                if((isset($pw_valid) AND $pw_valid == FALSE) OR (isset($input_valid) AND $input_valid == FALSE))
                {
                    $this-> data[TOP_NAV_STRING]['data_valid_error'] = TRUE;
                }

                $this-> data[TOP_NAV_STRING]['userdata'] = $userdata;
            }//else

        }//else

        //Assemble Data for View
        $this-> data[TOP_NAV_STRING]['userdata']['id'] = $id;
        $this->load->database();
        $this-> load-> Model( 'piwik_model');
        $this-> load-> dbutil();
        $this-> data[TOP_NAV_STRING]['piwik_exists'] = $this-> dbutil -> database_exists('piwik');

        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'admin/user/edit_user', $this -> data[CONTENT_STRING] );
        $this -> template -> load( 'template');
    }//edit_user()

    public function reset_password ($id)
    {
        $show_username = $this -> membership_model -> get_username( $id );
        
        $password = $this -> membership_model -> generate_random_unhashed_password( );
        $this -> membership_model -> set_user_password( $show_username, $password );

        $profile = $this -> membership_model -> get_profile( $show_username, 'first_name, last_name, email' );
        $this -> reset_password_email ($profile, $password);

        $this -> edit_user( $id );
    }//reset_password()

    private function reset_password_email($profile, $password)
    {    
        $message = "<p>Liebe/r Therapeut/in ".$profile["first_name"]." ".$profile["last_name"].", <br><br>Ihr Passwort wurde zurückgesetzt. <br> Das neue Passwort lautet: ".$password."<br> Bitte ändern Sie Ihr Passwort umgehend.<br><br> Mit freundlichen Grüßen<br> Team des Feedbackportals</p>";

        $this->email->from( $this -> config -> item( 'email_address_noreply' ) );
        $this->email->to($profile["email"]);
        $this->email->bcc( $this -> config -> item( 'email_address_main' ) );
        $this->email->subject("Feedbackportal Login");

        $email_data = array( 'main_content' => $message );
        $email_body = $this->load->view( 'emails/basic_html.php', $email_data, true );
        $this->email->message( $email_body );

        $this->email->send();
    }//reset_password_email()

    public function new_user()
    {   
        $profile_data = array(
            'first_name'    => $this->input->post('first_name'),
            'last_name'     => $this->input->post('last_name'),
            'initials'      => $this->input->post('initials'),
            'kohorte'       => $this->input->post('kohorte'),
            'email'         => $this->input->post('email'),
            'role'          => $this->input->post('role'),
            'password'      => $this->input->post('password'),
            'passconf'      => $this->input->post('passconf')
        );

        $access_data = array(
            'rechte_feedback'       => array_key_exists('rechte_feedback', $_POST) ? 1 : 0,
            'rechte_entscheidung'   => array_key_exists('rechte_entscheidung', $_POST) ? 1 : 0,
            'rechte_verlauf_normal' => array_key_exists('rechte_verlauf_normal', $_POST) ? 1 : 0,
            'rechte_verlauf_online' => array_key_exists('rechte_verlauf_online', $_POST) ? 1 : 0,
            'rechte_verlauf_gruppe' => array_key_exists('rechte_verlauf_gruppe', $_POST) ? 1 : 0,
            'rechte_verlauf_seminare'   => array_key_exists('rechte_verlauf_seminare', $_POST) ? 1 : 0,
            'rechte_zw'          => array_key_exists('rechte_zw', $_POST) ? 1 : 0,
        );
        
        //Validate Input Data from View 
        $profile_data_valid = $this -> validate_profile_data(); //Check if Profile/data is Valid
        $password_data_valid = $this -> validate_password(); // Check if Password is valid
        $initials_valid = $this-> membership_model -> validate_initial_string($profile_data['initials'], $profile_data['role']);
        $this-> data[TOP_NAV_STRING]['initial_unique'] = $this->membership_model->new_user_initial_check($this->input->post('initials'));//Check if User with given Initials exists

        //If input data is valid create new user and reroute to user_profile
        if ($profile_data_valid AND $password_data_valid AND $this-> data[TOP_NAV_STRING]['initial_unique'] AND ($initials_valid[0] == TRUE)) 
        {
            $new_user_data = array_merge($profile_data, $access_data);
            $user_id = $this->membership_model->create_new_user($new_user_data);
            
            $this-> session -> set_flashdata('creation_success', TRUE);

            //TODO-> Redirect to edit_user($id)
            redirect("admin/user/edit_user/{$user_id}");
        }
        else //Load view and show validation errors
        {
            $this -> data[TOP_NAV_STRING]['profile_data'] = $profile_data;
            $this -> data[TOP_NAV_STRING]['access_data'] = $access_data;
            $this -> data[TOP_NAV_STRING]['initials_errors'] = $initials_valid[1];

            $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
            $this -> template -> set( CONTENT_STRING, 'admin/user/new_user', $this -> data[CONTENT_STRING] );
            $this -> template -> load( 'template');       
        }
    }//new_user()

    private function check_inputChange($old_userdata, $userdata) // Checks if $userdata has changed compared to $old_userdata
    {
        $changed_keys = array();
        
        foreach($userdata as $key => $new_value)
        {
            $old_value = $old_userdata[$key];
            if ($old_value != $new_value )
            {
                $changed_keys [] = $key;
            }
        }
        return $changed_keys;
    }//check_inputChange()

    public function user_statistics($id, $patientcode = NULL)
    {
        //Catch request without $id
        if (!isset($id))
        {
            log_message('warn', 'user statistics request without id');
            $this->index();
        } //if 
        else
        {
            //Fetch Data from DB
            $show_username = $this-> membership_model -> get_username($id);
            $this-> load-> Model( 'piwik_model');
            $piwik = $this-> piwik_model -> get_piwik_data_for_user(strtolower($show_username));

        // Add Data to CONTENT_STRING
        $this -> data[TOP_NAV_STRING]['piwik'] = $piwik;      
        $this -> data[TOP_NAV_STRING]['id'] = $id;  
        $this -> data[TOP_NAV_STRING]['user'] = $show_username;   
        $this -> data[TOP_NAV_STRING]['patientcode'] = $patientcode;

            // Assemble and Load View
            $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
            $this -> template -> set( CONTENT_STRING, 'admin/user/user_statistics', $this -> data[CONTENT_STRING] );
            $this -> template -> load( 'template');   
        }//else
    }//user_statistics()

    public function list_all_delete( )
    {
        $username = $this -> data[TOP_NAV_STRING]['username'];
        $users = $this -> membership_model -> get_all_users( $username, $role );
        
        $this -> data[TOP_NAV_STRING]['users'] = $users;
        
        $user_counts = $this -> membership_model -> get_count_of_roles_combined( $username );
        $this -> data[TOP_NAV_STRING]['users']['count']['all'] = $user_counts -> all;
        $this -> data[TOP_NAV_STRING]['users']['count']['admins'] = $user_counts -> admins;
        $this -> data[TOP_NAV_STRING]['users']['count']['users'] = $user_counts -> users;
        $this -> data[TOP_NAV_STRING]['users']['count']['migrated'] = $user_counts -> migrated;
        
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( CONTENT_STRING, 'admin/user/list_all_delete', $this -> data[CONTENT_STRING] );
        $this -> template -> load( 'template' );
    }//list_all_delete()


    public function delete_user_validation( $del_user_id = NULL, $del_username = NULL)
    {
        if( is_null( $del_user_id ) OR is_null( $del_username ) )
        {
            log_message( 'error', 'Del-User-Cmd but not all data was given!' );
            $this -> load_template_delete_error( );
        }//if
        else
        {
            // if it is a false delete input (maybe a hack)
            $db_user_name = $this -> membership_model -> get_username( $del_user_id );
            if( $del_username !== $db_user_name )
            {
                log_message( 'error', 'Del-User-Cmd: given username !== database->username(id)!' );
                $this->data[TOP_NAV_STRING]['user_input_to_db_error'] = TRUE;
                $this -> load_template_delete_error( );
            }//if
            else
            {
                $last_admin = TRUE;

                if( $this-> membership_model->delete_user( $del_username ) )
                {
                    $last_admin = FALSE;
                }//if
                
                $this -> data[TOP_NAV_STRING]['del_user_id'] = $del_user_id;
                $this -> data[TOP_NAV_STRING]['del_username'] = $del_username;
                
                if( $last_admin )
                {
                    $this->data[TOP_NAV_STRING]['last_admin_error'] = TRUE;
                    $this->load_template_delete_error();
                }//if
                else 
                {
                    $this->load_template_delete_success();    
                }//else
            }//else
        }//else
    }//delete_user_validation()

    private function load_template_delete_error( )
    {
        $this -> data[TOP_NAV_STRING]['data_valid_error'] = TRUE;
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( TOP_NAV_STRING, 'admin/user/delete_user_failed', $this -> data[TOP_NAV_STRING] );
        $this -> template -> load( 'template' );
    }//load_template_delete_error()

    /**
     * Loads the success template @see views/admin/user/delete_user_success.
     */
    private function load_template_delete_success( )
    {
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set( TOP_NAV_STRING, 'admin/user/delete_user_success', $this -> data[TOP_NAV_STRING] );
        $this -> template -> load( 'template' );
    }//load_template_delete_success()

    private function validate_profile_data() // Checks if input profile_data is valid.
    {  
        $is_valid = FALSE;

        $this -> form_validation -> set_rules( 'first_name', 'First name', 'trim|min_length[2]|xss_clean' );
        $this -> form_validation -> set_rules( 'last_name', 'Last name', 'trim|min_length[2]|xss_clean' );
        $this -> form_validation -> set_rules( 'initials', 'Initals', 'trim|xss_clean|required' );
        $this -> form_validation -> set_rules( 'email', 'Email', 'trim|valid_email|xss_clean' );
        $this -> form_validation -> set_rules( 'role', 'Role', 'trim|xss_clean|required' );
        
        $is_valid = $this -> form_validation -> run( );

        return $is_valid;
    }//validate_profile_input()

    private function validate_password()
    {
        $is_valid = FALSE;

        $this -> form_validation -> set_rules( 'password', 'Password', 'trim|required|matches[passconf]|min_length[5]|xss_clean');
        $this -> form_validation -> set_rules( 'passconf', 'Passconf', 'trim|matches[password]|required');

        $is_valid = $this -> form_validation -> run();

        return $is_valid;
    }//validate_password()
}//class USER

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */
?>