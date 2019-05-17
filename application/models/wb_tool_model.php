<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

/**
 * Enthält alle Methoden die spezifisch für die
 * Übung 'Achtsamkeit' ist.
 *
 * @package Model
 *
 * @since 0.8.0
 * @access public
 *
 * @author Ruven Martin
 */
class Wb_tool_model extends CI_Model
{

  /**
   * Constructer
   * Init of the Psychoeq-Database-Connection.
   */
  public function __construct( )
  {
  	parent::__construct();
  }
    
	public function has_completed_teaz_for_semester($username)
	{
		$instance = $this->user_instance($username);

		$this -> db -> from( 'wb_teaz' );
		$this -> db -> where( 'initials', $username );
		$this -> db -> where( 'instance', $instance );

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0 ) {
			return true;
		}

		return false;
	}
	
	/**
   * There was another function called user_has_rights (now removed)
	 * which had the same functionality as this function.
   */
  public function user_instance($username)
	{
		$this -> db -> select( 'rechte_wb_questionnaire' );
		$this -> db -> from( 'user' );
		$this -> db -> where( 'INITIALS', $username );
		$this -> db -> limit( 1 );
		
		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) === 1 ) {
			return $query -> result()[0] -> rechte_wb_questionnaire > 0;
		}
		
		return false;	
  }
    
  public function get_all_semester( )
	{
		$semester = NULL;

		$this -> db -> from( 'wb_semester' );
		$this -> db -> order_by( 'id', 'DESC' );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0  )
			$semester = $query -> result();
		
		return $semester;
  }
}