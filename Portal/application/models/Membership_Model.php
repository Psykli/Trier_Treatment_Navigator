<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership_model extends CI_Model
{

    /**
     * Constructer
     * Init of the Psychoeq-Database-Connection.
     */
    public function __construct( )
    {
        try{
            $this -> db = $this -> load -> database( 'default', TRUE );
                
            $CI =& get_instance();
            if( !property_exists( $CI, 'db_default' ) ) {
                $CI->db_default =& $this -> db;
            }
        } catch (Exception $e){
            redirect('setup/step2');
        }
        $this->load->library('form_validation');
    }


     /**
     * To check if the userlogin is correct.
     * 
     * @see bugtracker URL
     * 
     * @since  0.0.1
     * @access public
     * @return boolean True if the login initials and password validates correct to database.  
     */
    function validate( $username = NULL, $password = NULL )
    {
        $login_ok = false;
        
        if( is_null( $username ) ) {
            $username = $this->input->post('username');
        }

        $this->db->select( 'password' );
        $this->db->where('initials', $username);
        $this->db->limit( 1 );
        $query = $this->db->get('user');
        
        if( $query->num_rows() === 1 )
        {
            if( is_null( $password ) ) {
                $password = $this->input->post('password');
            }

            $login_ok = password_verify( $password, $query->row(0)->password );
        }//if
        
        return $login_ok;
    }//validate()

     /**
     * Returns the userrole to the argument username.
     * 
     * @since 0.0.1
     * @access public
     * @param string $username (initials) of a account.
     * @return string Default userrole is status guest.
     */
    function get_role( $username )
    {
        $role = 'guest';
           
        $this->db->select( 'role' );
        $this->db->from( 'user' );
        $this->db->where( 'initials', $username );
        $this->db->limit( 1 );
        $query = $this->db->get();
            
        if( $query->num_rows() === 1 )
        {
            $role = $query->row(0)->role;
        }//if
        
        return $role;
    }//get_role()

      /**
     * Checks the userrole of a given username.
     * 
     * @since 0.0.1
     * @access public
     * @param string $username (initials) of a account.
     * @return boolean True if userrole is user, otherwise false.
     */
    
    function is_rechte_set( $username, $rechte )
    {
        $this->db->select( '1' );
        $this->db->from( 'user' );
        $this->db->where( 'initials', $username );
        $this->db->where( $rechte, 1 );
        $this->db->limit( 1 );

        $query = $this->db->get();
            
        if( $query->num_rows() === 1 ) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    function passwordStatus( $username )
    {
        $profile = NULL;
        
        $this->db->select( 'change_password' );
        $this->db->from( 'user' );
        $this->db->where( 'initials', $username );
        $this->db->limit( 1 );
        $query = $this->db->get();
        
        if( $query->num_rows() === 1 )
        {
            $profile = $query->result_array();
        }//if
        
        return $profile;
    }

    function get_id( $username )
    {
        $id = -1;
        
        $this->db->select( 'id' );
        $this->db->from( 'user' );
        $this->db->where( 'initials', $username );
        $this->db->limit( 1 );
        $query = $this->db->get();
            
        if( $query->num_rows() === 1 )
        {
            $id = $query->row(0)->id;
        }//if
        
        return $id;
    }

    function get_id_and_role( $username )
    {
        $data = new stdClass;
        $data -> id = NULL;
        $data -> role = NULL;

        $this -> db -> select( 'id, ROLE' );
        $this -> db -> from( 'user' );
        $this -> db -> where( 'initials', $username );
        $this -> db -> limit( 1 );
        $query = $this -> db -> get( );

        if( $query->num_rows() === 1 )
        {
            $data -> id = $query -> row(0) -> id;
            $data -> role = $query -> row(0) -> ROLE;
        }//if

        return $data;
    }


    /*
    Vereint die alten is_X Funktionen, durch den zusätzlichen $Role Parameter
    is_admin        -> $role = 'admin'
    is_supervisor   -> $role = 'supervisor'
    is_patient      -> $role = 'patient'
    is_privileged_user
                    -> $role = 'privileged_user'
    is_user         -> $role = 'user'
    */
    function is_role ($username, $role) {
        $is_role = false;

        $this->db->select( 'role' );
        $this->db->from( 'user' );
        $this->db->where( 'initials', $username );
        $this->db->limit( 1 );
        $query = $this->db->get();
            
        if( $query->num_rows() === 1 )
        {
            $is_role = $query -> row(0) -> role === $role;
        }//if
        
        return $is_role;
    }

    function get_profile ($username, $columns = NULL)
    {
        $profile = NULL;
        
        if( is_null( $columns ) ) {
            $this->db->select( 'first_name, last_name, initials, email, role, kohorte, rechte_wb, rechte_feedback, rechte_entscheidung, rechte_nn, rechte_uebungen, change_password, rechte_zuweisung, rechte_wb_questionnaire, rechte_verlauf_normal, rechte_verlauf_online, rechte_verlauf_gruppe, rechte_verlauf_seminare, rechte_zw' );
        }
        else {
            $this->db->select( $columns );
        }

        $this->db->from( 'user' );
        $this->db->where( 'initials', $username );
        $this->db->limit( 1 );
        $query = $this->db->get();
        
        if( $query->num_rows() === 1 )
        {
            $profile = $query->result()[0];
        }//if
        
        return $profile;
    }

    function get_all_admin_and_supervisor_codes( ) {
        $initials = NULL;

        $this->db-> db_select( );
        $this->db->select( 'INITIALS AS CODE' );
        $this->db->from( 'user' );
        $this->db->where( 'ROLE', 'admin' );
        $this->db->or_where( 'ROLE', 'supervisor' );
        
        $query = $this->db->get();
            
        if( $query->num_rows() > 0 )
        {
            $initials = $query->result();
        }//if

        return $initials;
    }

    function get_all_admin_codes( ) {
        $initials = NULL;

        $this->db-> db_select( );
        $this->db->select( 'INITIALS AS CODE' );
        $this->db->from( 'user' );
        $this->db->where( 'ROLE', 'admin' );
        
        $query = $this->db->get();
            
        if( $query->num_rows() > 0 )
        {
            $initials = $query->result();
        }//if

        return $initials;
    }


    /* 
    Section: User-Management
    */

    function new_user_initial_check($initials) //Checks wether a User with given $initials already exists --> returns true if no user with $initials exists
    {
        $new_user_initials_valid = FALSE;

        $this -> db -> select( '1' );
        $this -> db -> from ('user');
        $this -> db -> where ('initials', $initials);
        $this -> db -> limit( 1 );
        $query = $this -> db -> get();

        if ($query->num_rows() === 0)
        {
            $new_user_initials_valid = TRUE;
        }
       
        return $new_user_initials_valid;
    }

    function create_new_user($new_user_data)
    {
        $user_ID = -1;
        $this-> db -> set('initials', $new_user_data['initials']);
        $this-> db -> insert ('user');
        $user_ID = $this->get_id($new_user_data['initials']);

        // Set-New-User-Profile-Data
        $username = $new_user_data['initials'];
        $this-> user_set_first_name($username, $new_user_data['first_name']);
        $this-> user_set_last_name($username, $new_user_data['last_name']);
        (isset($new_user_data['kohorte'])) ? $this-> user_set_kohorte($username, $new_user_data['kohorte']): $this-> user_set_kohorte($username, 0);
        $this-> user_set_email($username, $new_user_data['email']);
        $this-> user_set_role($username, $new_user_data['role']);
        $this-> set_user_password($username, $new_user_data['password']);

        foreach($new_user_data as $key => $value)
        {
            $this-> user_set_access_rights($username, $key, $value);
        }
            
        
        return $user_ID;
    }


    /*######################################################
     -----  Setter/Update Functions for User-DB-Entrys ----
    ######################################################*/

    public function get_all_users( $username, $role = 'all', $order_column = NULL , $ordering = NULL )
    {      
        $all_users = NULL;
        
        if( $this->is_role($username, 'admin') )
        {
            $this->db->select( 'id, first_name, last_name, initials, email, role, rechte_feedback, rechte_entscheidung, rechte_zuweisung, rechte_verlauf_normal, rechte_verlauf_online, rechte_verlauf_gruppe, rechte_verlauf_seminare, rechte_zw');
            $this->db->from('user');
			if( !is_null( $order_column ) && !is_null( $ordering ) ) {
                $this -> db -> order_by ( $order_column, $ordering );
            }
            
            switch( $role )
            {
                case 'admins':
                    $this->db->where( 'role', 'admin' );
                    break;
                case 'users':
                    $this->db->where( 'role', 'user' );
                    break;
                case 'migrated':
                    $this->db->where( 'role', 'migrated' );
                    break;
                case 'no_admins':
                    $this->db->where_not_in( 'role', 'admin' );
                    break;
                case 'all':
                default:
                    //no where needed
            }//switch
            
            $query = $this->db->get();
            
            if( $query )
            {
                $all_users = $query->result();
            }//if
        }//if
        else
        {
            log_message( 'error', 'A user (not admin) tried to perform a user-list request.' );
        }//else
        
        return $all_users;
    }

    function get_username( $id )
    {
        $username = NULL;
        
        if( is_numeric( $id ) )
        {
            $this->db->select( 'initials' );
            $this->db->from( 'user' );
            $this->db->where( 'id', $id );
            $this->db->limit( 1 );
            $query = $this->db->get();
            
            if( $query->num_rows() === 1 )
            {
                $username = $query->row(0)->initials;
            }//if
        }//if
        
        return $username;
    }//get_username()

    public function get_userdata($id = NULL)
    {
        if(!isset($id))
        {
            return FALSE;
        }

        $this-> db -> select('*');
        $this-> db -> from('user');
        $this-> db -> where('id', $id);
        $this-> db -> limit(1);

        $query = $this-> db -> get();

        if ($query)
        {
            $temp =  $query->result();
        }
        
        $userdata = json_decode(json_encode($temp[0]), true);
        
        return $userdata;
    }


     /**
     * Returns the count of userroles in the database.
     * 
     * @access public
     * @since 0.7.0
     * 
     * @param string $username The username who runs the method.
     * @param string $role The role you want the count (DEFAULT = 'all' );
     * @return integer The count of the specific userrole.
     */
    public function get_count_of_role( $username, $role = 'all' )
    {
        $user_count = NULL;
        
        if( $this->is_role($username, 'admin') )
        {
            $this->db->select('1');
            $this->db->from('user');
            
            switch( $role )
            {
                case 'admins':
                    $this->db->where( 'role', 'admin' );
                    break;
                case 'users':
                    $this->db->where( 'role', 'user' );
                    break;
                case 'migrated':
                    $this->db->where( 'role', 'migrated' );
                    break;
                case 'all':
                default:
                    //no where needed    
            }//switch
            
            $result = $this->db->count_all_results();
            
            if( $result )
            {
                $user_count = $result;
                log_message( 'debug', 'Get usercount for role ' . $role . ': ' . $user_count );
            }//if
        }//if
        else
        {
            log_message( 'error', 'A user (not admin) tried to perform a user-count request.' );
        }//else
        
        return $user_count;
    }//get_count_of_role()

     /**
     * Returns the count of all userroles in the database in a combined format.
     * 
     * @access public
     * @since 0.7.0
     * 
     * @param string $username The username who runs the method.
     * @return object object containing the counts of the userroles
     */
    public function get_count_of_roles_combined( $username )
    {
        $data = new stdClass;
        $data -> all = NULL;
        $data -> admins = NULL;
        $data -> users = NULL;
        $data -> migrated = NULL;

        if( $this->is_role($username, 'admin') ) {
            $this->db->select( 'COUNT(id) AS all_count, SUM(ROLE = "admin") AS admin_count, SUM(ROLE = "user") AS user_count, SUM(ROLE = "migrated") AS migrated_count');
            $this->db->from('user');
            
            $query = $this -> db -> get( );

            if( $query->num_rows( ) === 1 )
            {
                $data -> all = $query -> row(0) -> all_count;
                $data -> admins = $query -> row(0) -> admin_count;
                $data -> users = $query -> row(0) -> user_count;
                $data -> migrated = $query -> row(0) -> migrated_count;
            }//if
        }//if
        else
        {
            log_message( 'error', 'A user (not admin) tried to perform a combined user-count request.' );
        }//else
        
        return $data;
    }//get_count_of_roles_combined()

    /**
     * Set a new password for a user.
     * 
     * @access public
     * @since 0.7.0
     * 
     * @param string $username The username of the edit user.
     * @param string $password The new password of the edit user.
     * @return boolean TRUE if the query runs and updates at least one password, otherwise FALSE.
     */
    public function set_user_password( $username, $password )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return FALSE;
        }//if
        
        if( $this -> username_empty_checks( $password ) )
        {
            $password = $this->config->item('default_password');
        }//if
     
        $hashed_password = $this->get_hashed_password($password);
     
        $id = $this->get_id($username);

        $data = array( 'password' => $hashed_password );
        $this->db->where( 'id', $id );
        $this->db->update( 'user', $data );

        return ( $this->db->affected_rows() > 0 );
    }//set_user_password()

    /**
     * Set a new password for a user.
     * The password MUST already be hashed, do NOT use a plaintext password as the password parameter.
     * This function is only used for the change password functionality.
     * Otherwise use set_user_password().
     */
    public function set_user_password_dangerous( $username, $hashed_password )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return FALSE;
        }//if
        
        if( $this -> username_empty_checks( $password ) )
        {
            $password = $this->config->item('default_password');
        }//if
     
        $id = $this->get_id($username);

        $data = array( 'password' => $hashed_password );
        $this->db->where( 'id', $id );
        $this->db->update( 'user', $data );

        return ( $this->db->affected_rows() > 0 );
    }//set_user_password_dangerous()


    public function generate_random_unhashed_password ( $length = 8, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"#$%&\'()*+,-./' )
    {
        $max = mb_strlen($keyspace, '8bit') - 1;
        
        if ($max < 1) {
            throw new Exception('$keyspace must be at least two characters long');
        }

        $random_pw = '';

        for ($i = 0; $i < $length; ++$i) {
            $random_pw .= $keyspace[random_int(0, $max)];
        }
        
        return $random_pw;
    }


     /**
     * Returns a hashed string for the given input password.
     * 
     * @access private
     * @since 0.7.0
     * 
     * @param string $password The plain input password.
     * @return string The hash string of the password.
     */
    public function get_hashed_password( $password )
    {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        if($password_hash !== FALSE) {
            return $password_hash;
        }
        else {
            throw new Exception('Failed to calculate password hash');
        }
    }//get_hashed_password()


    function user_set_first_name($username, $first_name)
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('first_name', $first_name);
        $this-> db -> where('id', $id);
        $this-> db -> update('user');

        return TRUE;
    }

    function user_set_last_name($username, $last_name)
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('last_name', $last_name);
        $this-> db -> where('id', $id);
        $this-> db -> update('user');

        return TRUE;
    }

    function user_set_kohorte($username, $kohorte)
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('kohorte', $kohorte);
        $this-> db -> where('id', $id);
        $this-> db -> update('user');

        return TRUE;
    }

    function user_set_role($username, $role)
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('role', $role);
        $this-> db -> where('id', $id);
        $this-> db -> update('user');

        return TRUE;
    }

    function user_set_access_rights($username, $value_name, $value)      //
    {                                                                   
        if( $this -> username_empty_checks( $username ) )
        {
            return FALSE;
        }

        $value_names = array('rechte_feedback', 'rechte_entscheidung', 'rechte_zuweisung',
        'rechte_wb_questionnaire', 'rechte_verlauf_normal', 'rechte_verlauf_gruppe', 'rechte_verlauf_online',
        'rechte_verlauf_seminare', 'rechte_zw');
        
        if (in_array($value_name, $value_names))
        {
            $id = $this->get_id($username);

            $this-> db -> set($value_name, $value );
            $this-> db -> where('id', $id);
            $this-> db -> update('user');
        }        

        return TRUE;
    }

    function user_set_email($username, $email)
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('email', $email);
        $this-> db -> where('id', $id);
        $this-> db -> update('user');

        return TRUE;
    }

    function enter_email_confirmation_code( $username, $reset_code, $old_email_address, $new_email_address )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return false;
        }

        $user_id = $this->get_id( $username );

        //remove expired email confirmation codes
        $expiry_value = $this -> config -> item( 'email_confirmation_codes_expiry' );
        $this->db->where("created_on < (NOW() - INTERVAL ".$expiry_value.")");
        $this->db->delete('email_confirmation_codes');

        //remove previous confirmation codes for this user
        $this->db->where('user_id', $user_id);
        $this->db->delete('email_confirmation_codes');

        //enter the new confirmation code
        $reset_data = array(
            'user_id' => $user_id,
            'confirmation_code' => $reset_code,
            'old_email' => $old_email_address,
            'new_email' => $new_email_address
        );

        $this -> db -> insert('email_confirmation_codes', $reset_data);
        return true;        
    }

    function get_email_confirmation_data( $username )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return null;
        }

        //remove expired email confirmation codes
        $expiry_value = $this -> config -> item( 'email_confirmation_codes_expiry' );
        $this->db->where("created_on < (NOW() - INTERVAL ".$expiry_value.")");
        $this->db->delete('email_confirmation_codes');

        //get the confirmation data of the user
        $user_id = $this->get_id( $username );

        $this->db->select( 'confirmation_code, old_email, new_email' );
        $this->db->where('user_id', $user_id);
        
        $query = $this->db->get('email_confirmation_codes');
        
        if( $query->num_rows() === 1 )
        {
            return $query->row(0);
        }//if     
    }

    function delete_email_confirmation_codes( $username )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return false;
        }

        $user_id = $this->get_id( $username );

        //remove previous confirmation codes for this user
        $this->db->where('user_id', $user_id);
        $this->db->delete('email_confirmation_codes');
    }

    function enter_password_confirmation_code( $username, $reset_code, $new_hashed_pw )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return false;
        }

        $user_id = $this->get_id( $username );

        //remove expired password confirmation codes
        $expiry_value = $this -> config -> item( 'password_confirmation_codes_expiry' );
        $this->db->where("created_on < (NOW() - INTERVAL ".$expiry_value.")");
        $this->db->delete('password_confirmation_codes');

        //remove previous confirmation codes for this user
        $this->db->where('user_id', $user_id);
        $this->db->delete('password_confirmation_codes');

        //enter the new confirmation code
        $reset_data = array(
            'user_id' => $user_id,
            'confirmation_code' => $reset_code,
            'new_password' => $new_hashed_pw
        );

        $this -> db -> insert('password_confirmation_codes', $reset_data);
        return true;        
    }

    function get_password_confirmation_data( $username )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return null;
        }

        //remove expired password confirmation codes
        $expiry_value = $this -> config -> item( 'password_confirmation_codes_expiry' );
        $this->db->where("created_on < (NOW() - INTERVAL ".$expiry_value.")");
        $this->db->delete('password_confirmation_codes');

        //get the confirmation data of the user
        $user_id = $this->get_id( $username );

        $this->db->select( 'confirmation_code, new_password' );
        $this->db->where('user_id', $user_id);
        
        $query = $this->db->get('password_confirmation_codes');
        
        if( $query->num_rows() === 1 )
        {
            return $query->row(0);
        }//if     
    }

    function delete_password_confirmation_codes( $username )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return false;
        }

        $user_id = $this->get_id( $username );

        //remove previous confirmation codes for this user
        $this->db->where('user_id', $user_id);
        $this->db->delete('password_confirmation_codes');
    }

    public function delete_user( $username )
    {
        if( $this -> username_empty_checks( $username ) )
        {
            return NULL;
        }//if
        
        if( $this->is_role($username, 'admin') )
        {
            // check if it is the last admin in database
            if( 1 == $this->get_count_of_role($username, 'admins') )
            {
                // if it is -> say no!
                log_message( 'error', "You can't delete the last administrator!");
                return FALSE;
            }//if
        }//if

        $this->db->where( 'initials', $username );
        $this->db->delete( 'user' );
        log_message( 'info', "Deleted the user " . $username );
        
        return TRUE;
    }//delete_user()
    

    /*
    * Validation Method for ensuring User-Initial-Consistency
    */

    public function validate_initial_string($initials, $role) 
    {
        
        switch($role)
        {
            case "user": // Therapeut
            $initials_valid = (preg_match('/^[[:alpha:]]{2}[[:digit:]]{2}$/', $initials)) == 1 ? TRUE : FALSE;
            $initials_error = ($initials_valid) ? NULL : "Die Initialien für Therapeuten dürfen nur aus zwei Buchstaben und zwei Ziffern bestehen!";
            break;
            case "supervisor": //Supervisor
            $initials_valid = (preg_match('/^[[:alpha:]]{2}[[:digit:]]{3}$/', $initials)) == 1 ? TRUE : FALSE;
            $initials_error = ($initials_valid) ? NULL : "Die Initialien für Supervisoren düfren nur aus drei Buchstaben und zwei Ziffern bestehen!";
            break;
            case "patient": // Patient
            $initials_valid = (preg_match('/^[[:digit:]]{4}P+[[:digit:]]{2}$/', $initials)) == 1 ? TRUE : FALSE;
            $initials_error = ($initials_valid) ? NULL : "Die Initialien für einen Patienten müssen dem Muster 'vier Ziffern, P, zwei Ziffern' folgen!";
            break;
            case "admin":
            $initials_valid = TRUE; // no inital restrictions
            $initials_error = NULL;
            break;
            case "privileged_user":
            $initials_valid = (preg_match('/^[[:alpha:]]{2}[[:digit:]]{3}$/', $initials)) == 1 ? TRUE : FALSE;
            $initials_error = ($initials_valid) ? NULL : "Die Initialien für Privilegierte Benutzer düfren nur aus drei Buchstaben und zwei Ziffern bestehen!";
            default:
            $initials_valid = FALSE; // something went wrong
            $initials_error = NULL;
            break;
        }
        
        return array($initials_valid, $initials_error);
    }//validate_initial_string()

    public function validate_profile_data() // Checks if input profile_data is valid.
    {
              
        $is_valid = FALSE;


        $this -> form_validation -> set_rules( 'first_name', 'First name', 'trim|min_length[2]');
        $this -> form_validation -> set_rules( 'last_name', 'Last name', 'trim|min_length[2]');
        $this -> form_validation -> set_rules( 'initials', 'Initals', 'trim|required');
        $this -> form_validation -> set_rules( 'email', 'Email', 'trim|valid_email');
        $this -> form_validation -> set_rules( 'role', 'Role', 'trim|required');
        

        $is_valid = $this -> form_validation -> run();

        return $is_valid;

    }//validate_profile_input()

    public function validate_password()
    {
        $is_valid = FALSE;

        $this -> form_validation -> set_rules( 'password', 'Password', 'trim|required|matches[passconf]|min_length[5]');
        $this -> form_validation -> set_rules( 'passconf', 'Passconf', 'trim|matches[password]|required');

        $is_valid = $this -> form_validation -> run();

        return $is_valid;
    }

    public function get_navbar_content($role = 'guest')
    {
        ob_start();

        switch($role)
        {
            case 'guest':
                include('application/views/guest/top_nav.php');
            break;
            case 'patient':
                include('application/views/patient/top_nav.php');
            break;
            case 'admin':
                include('application/views/admin/top_nav.php');
            break;
            case 'user':
                include('application/views/user/top_nav.php');
            break;
        }
        
        return ob_get_clean();
    }

    public function create_admin($name, $email, $password){

        if(isset($name) && isset($email) && isset($password)){
            $data = array(
                'INITIALS' => $name,
                'email' => $email,
                'PASSWORD' => $this->get_hashed_password($password),
                'ROLE' => 'admin'
            );

            return $this->db->insert('user',$data);          
        }
        return FALSE;
    }

    private function username_empty_checks( $username ) {
        return is_null($username) || $username == '';
    }//username_empty_check()
}//class Membership_model



?>