<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

/**
 * An instance of the class represents a session model.
 * It contains all functions for session management.
 * 
 * @package Model
 * 
 * @author Martin Kock <code @ deeagle.de>
 */
class Session_model extends CI_Model
{
    public function __construct()
    {
        try{
            $this -> db = $this -> load -> database( 'default', TRUE );
            $CI =& get_instance();
            if( !property_exists( $CI, 'db_default' ) ) {
                $CI->db_default =& $this -> db;
            }
        } catch(Exception $e){
            redirect('setup/step2');
        }
    }//__construct()
    /**
     * Checks the cookie if the user is logged in.
     * 
     * @since 0.0.1
     * @access public
     * @param array $all_session_data contains the complete cookie-data.
     * @return boolean True if user is logged in, otherwise false.
     */
    function is_logged_in( $all_session_data )
    {
        return isset( $all_session_data['is_logged_in'] ) AND $all_session_data['is_logged_in'];
    }//is_logged_in()
}//class Session_model
