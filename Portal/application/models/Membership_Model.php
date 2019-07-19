<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership_model extends CI_Model
{

    public function __construct( )
    {      

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
    function validate()
    {
        $login_ok = false;
        
        $this->db->select( 'password' );
        $this->db->where('initials', $this->input->post('username'));
        $this->db->limit( 1 );
        $query = $this->db->get('user');
        
        if( $query->num_rows() == 1 )
        {
            $login_ok = password_verify( $this->input->post('password'), $query->row(0)->password );
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
           
        if( isset( $username ) )
        {
            $this->db->select( 'role' );
            $this->db->from( 'user' );
            $this->db->where( 'initials', $username );
            $this->db->limit( 1 );
            $query = $this->db->get();
            
            if( $query->num_rows() == 1 )
            {
                $role = $query->row(0)->role;
            }//if
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
        if( isset( $username ) && isset( $rechte ) )
        {
            $this->db->select( '1' );
            $this->db->from( 'user' );
            $this->db->where( 'initials', $username );
            $this->db->where( $rechte, 1 );
            $this->db->limit( 1 );

            $query = $this->db->get();
            
            if( $query->num_rows() === 1 )
				return TRUE;		
        }//if
        
        return FALSE;
    }
    
    function passwordStatus( $username )
    {
        $profile = NULL;
        
        if( isset( $username ) )
        {
            $this->db->select( 'change_password' );
            $this->db->from( 'user' );
            $this->db->where( 'initials', $username );
            $this->db->limit( 1 );
            $query = $this->db->get();
            
            if( $query->num_rows() == 1 )
            {
                $profile = $query->result_array();
            }//if
        }//if
        
        return $profile;
    }

    function get_id( $username )
    {
        $id = -1;
        
        if( isset( $username ) )
        {
            $this->db->select( 'id' );
            $this->db->from( 'user' );
            $this->db->where( 'initials', $username );
            $this->db->limit( 1 );
            $query = $this->db->get();
            
            if( $query->num_rows() == 1 )
            {
                $id = $query->row(0)->id;
            }//if
        }//if
        
        return $id;
    }

    function get_id_and_role( $username )
    {
        $data = new stdClass;
        $data -> id = NULL;
        $data -> role = NULL;

        if( isset( $username ) )
        {
            $this -> db -> select( 'id, ROLE' );
            $this -> db -> from( 'user' );
            $this -> db -> where( 'initials', $username );
            $this -> db -> limit( 1 );
            $query = $this -> db -> get( );

            if( $query->num_rows() == 1 )
            {
                $data -> id = $query -> row(0) -> id;
                $data -> role = $query -> row(0) -> ROLE;
            }//if
        }//if

        return $data;
    }


    /*
    Vereint die alten is_X Funktionen, durch den zusätzlichen $Role Parameter
    is_admin        -> $role = 'admin'
    is_supervisor   -> $role = 'supervisor'
    is_patient      -> $role = 'patient'
    is_priviledged_user
                    -> $role = 'priviledged_user'
    is_user         -> $role = 'user'
    */
    function is_role ($username, $role) {
        $is_role = false;

        if( isset( $username ) )
        {
            $this->db->select( 'role' );
            $this->db->from( 'user' );
            $this->db->where( 'initials', $username );
            $this->db->limit( 1 );
            $query = $this->db->get();
            
            if( $query->num_rows() == 1 )
            {
                $is_role = $query -> row(0) -> role === $role;
            }//if
        }//if
        
        return $is_role;
    } 

    function get_profile ($username)
    {
        $profile = NULL;

        if( isset ($username) )
        {
            $this->db->select( 'first_name, last_name, initials, email, role, kohorte, rechte_wb, rechte_feedback, rechte_entscheidung, rechte_nn, rechte_uebungen, change_password, rechte_zuweisung, rechte_wb_questionnaire, rechte_verlauf_normal, rechte_verlauf_online, rechte_verlauf_gruppe, rechte_verlauf_seminare, rechte_zw' );

            $this->db->from( 'user' );
            $this->db->where( 'initials', $username );
            $this->db->limit( 1 );
            $query = $this->db->get();
            
            if( $query->num_rows() == 1 )
            {
                $profile = $query->result_array();
            }//if
        } //if
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
        $this-> db -> insert ('portal.user');
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
        $is_admin = $this->get_role($username) === 'admin' ? TRUE : FALSE;
        if( $is_admin )
        {
            $this->db->select( 'id, first_name, last_name, initials, email, role, rechte_feedback, rechte_entscheidung, rechte_zuweisung, rechte_verlauf_normal, rechte_verlauf_online, rechte_verlauf_gruppe, rechte_verlauf_seminare, rechte_zw');
            $this->db->from('user');
			if( !is_null( $order_column ) && !is_null( $ordering ) )
				$this -> db -> order_by ( $order_column, $ordering );
            
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
            log_message( 'error', 'User (not Admins) performs a user-list request.' );
        }//else
        
        return $all_users;
    }

    function get_username( $id )
    {
        $username = NULL;
        
        if( isset( $id ) AND is_numeric( $id ) )
        {
            $this->db->select( 'initials' );
            $this->db->from( 'user' );
            $this->db->where( 'id', $id );
            $this->db->limit( 1 );
            $query = $this->db->get();
            
            if( $query->num_rows() == 1 )
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
        $is_admin = $this->get_role($username) === 'admin' ? TRUE : FALSE;
        
        $user_count = NULL;
        
        if( $is_admin )
        {
            $this->db->select( 'id');
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
            log_message( 'error', 'User (not Admins) performs a user-count request.' );
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

        $is_admin = $this->get_role($username) === 'admin' ? TRUE : FALSE;

        if( $is_admin ) {
            $this->db->select( 'COUNT(id) AS all_count, SUM(ROLE = "admin") AS admin_count, SUM(ROLE = "user") AS user_count, SUM(ROLE = "migrated") AS migrated_count');
            $this->db->from('user');
            
            $query = $this -> db -> get( );

            if( $query->num_rows( ) == 1 )
            {
                $data -> all = $query -> row(0) -> all_count;
                $data -> admins = $query -> row(0) -> admin_count;
                $data -> users = $query -> row(0) -> user_count;
                $data -> migrated = $query -> row(0) -> migrated_count;
            }//if
        }//if
        else
        {
            log_message( 'error', 'User (not Admins) performs a combined user-count request.' );
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
        if( !isset( $username ) OR is_null( $username ) OR $username == '' )
        {
            return FALSE;
        }//if
        
        if( !isset( $password ) OR is_null( $password ) OR $password == '' )
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
    private function get_hashed_password( $password )
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
        if(!isset($username) OR is_null($username) OR $username == '')
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('first_name', $first_name);
        $this-> db -> where('id', $id);
        $this-> db -> update('portal.user');

        return TRUE;
    }

    function user_set_last_name($username, $last_name)
    {
        if(!isset($username) OR is_null($username) OR $username == '')
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('last_name', $last_name);
        $this-> db -> where('id', $id);
        $this-> db -> update('portal.user');

        return TRUE;
    }

    function user_set_kohorte($username, $kohorte)
    {
        if(!isset($username) OR is_null($username) OR $username == '')
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('kohorte', $kohorte);
        $this-> db -> where('id', $id);
        $this-> db -> update('portal.user');

        return TRUE;
    }

    function user_set_role($username, $role)
    {
        if(!isset($username) OR is_null($username) OR $username == '')
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('role', $role);
        $this-> db -> where('id', $id);
        $this-> db -> update('portal.user');

        return TRUE;
    }

    function user_set_email($username, $email)
    {
        if(!isset($username) OR is_null($username) OR $username == '')
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $this-> db -> set('email', $email);
        $this-> db -> where('id', $id);
        $this-> db -> update('portal.user');

        return TRUE;
    }

    function user_set_access_rights($username, $value_name, $value)      //
    {                                                                   
        if(!isset($username) OR is_null($username) OR $username == '')
        {
            return FALSE;
        }
        
        $id = $this->get_id($username);

        $value_names = array('rechte_feedback', 'rechte_entscheidung', 'rechte_zuweisung',
        'rechte_wb_questionnaire', 'rechte_verlauf_normal', 'rechte_verlauf_gruppe', 'rechte_verlauf_online',
        'rechte_verlauf_seminare', 'rechte_zw');

        
        if (in_array($value_name, $value_names))
        {
            $this-> db -> set($value_name, $value );
            $this-> db -> where('id', $id);
            $this-> db -> update('user');
        }        

        return TRUE;
    }

    public function delete_user( $username )
    {
        if( !isset( $username ) OR is_null( $username ) OR $username == '' )
        {
            return NULL;
        }//if
        
        if( $this->get_role($username) === 'admin' )
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
        log_message( 'info', "Delete the user " . $username );
        
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
            case "priviledged_user":
            $initials_valid = (preg_match('/^[[:alpha:]]{2}[[:digit:]]{3}$/', $initials)) == 1 ? TRUE : FALSE;
            $initials_error = ($initials_valid) ? NULL : "Die Initialien für Privilegierte Benutzer düfren nur aus drei Buchstaben und zwei Ziffern bestehen!";
            default:
            $initials_valid = FALSE; // something went wrong
            $initials_error = NULL;
            break;
        }
        
        return array($initials_valid, $initials_error);
    }

    public function validate_profile_data() // Checks if input profile_data is valid.
    {
              
        $is_valid = FALSE;


        $this -> form_validation -> set_rules( 'first_name', 'First name', 'trim|min_length[2]|xss_clean');
        $this -> form_validation -> set_rules( 'last_name', 'Last name', 'trim|min_length[2]|xss_clean');
        $this -> form_validation -> set_rules( 'initials', 'Initals', 'trim|xss_clean|required');
        $this -> form_validation -> set_rules( 'email', 'Email', 'trim|valid_email|xss_clean');
        $this -> form_validation -> set_rules( 'role', 'Role', 'trim|xss_clean|required');
        

        $is_valid = $this -> form_validation -> run();

        return $is_valid;

    } // validate_profile_input

    public function validate_password()
    {
        $is_valid = FALSE;

        $this -> form_validation -> set_rules( 'password', 'Password', 'trim|required|matches[passconf]|min_length[5]|xss_clean');
        $this -> form_validation -> set_rules( 'passconf', 'Passconf', 'trim|matches[password]|required');

        $is_valid = $this -> form_validation -> run();

        return $is_valid;
    }


}//class Membership_model



?>