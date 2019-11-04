<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

/**
 * An instance of the class represents a patient model.
 * It contains all functions for patient management.
 *
 * @package Model
 *
 * @since 0.8.0
 * @access public
 *
 * @author Tobias Ziegelmayer
 */
class User_Model extends CI_Model
{
    
    public function __construct( )
    {
        $this -> db = $this -> load -> database( 'default', TRUE );
			
		$CI =& get_instance();
		if( !property_exists( $CI, 'db' ) )
			$CI->db =& $this -> db;   
		
		$this -> load -> Model('SB_Model');
        $this -> load -> Model( 'Questionnaire_tool_model' );
	}//__construct()

    public function get_status_recommendation( $patient, $user )
	{
		$status = NULL; 
		
		$this -> db -> select( 'status' );
		$this -> db -> from( 'feedback_recommendation' );
		$this -> db -> where( 'patientcode', $patient );
		$this -> db -> where( 'therapeut', $user );
		$this -> db -> limit( 1 );
		
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) === 1 ) {
			$status = $query -> result( )[0]->status;
		}
		
		return $status;	
	}//get_status_recommendation()

    public function insert_recommendation_status ($user, $patient)
	{
		$today = date("Y-m-d H:i:s", time());
		
		$data = array(
			'therapeut' => $user ,
			'patientcode' => $patient ,
			'date' => $today,
			'status' => 1,
			);

		$this->db->insert('feedback_recommendation', $data);
		//$this -> db -> insert_id();
	}//insert_recommendation_status()
}//class User_model

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */
