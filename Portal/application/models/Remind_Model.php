<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

class Remind_Model extends CI_Model
{

    /**
     * Constructer
     * Init of the Psychoeq-Database-Connection.
     */
    public function __construct( )
    {
        $this -> db = $this -> load -> database( 'default', TRUE );
	}//__construct()

	public function insert( $tablename, $data )
	{
		$this -> db -> insert( $tablename, $data );

		return $this -> db -> insert_id();
  }//insert()

  public function is_therapy_remind_deleted( $therapeut, $code )
	{
		$this -> db -> select( 'date' );
		$this -> db -> from( 'reminds_deleted' );
		$this -> db -> where( 'therapist', $therapeut );
		$this -> db -> where( 'code', $code );
		$this -> db -> where( 'type', 'therapy_remind' );
		$this -> db -> order_by( 'date', 'DESC' );
		
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0  ) {
			return $query -> result()[0] -> date;
		}//if
		
		return FALSE;
	}//is_therapy_remind_deleted()
    
  	public function is_zw_remind_deleted( $therapeut, $code, $instance )
	{
		$this -> db -> select( 'date' );
		$this -> db -> from( 'reminds_deleted' );
		$this -> db -> where( 'therapist', $therapeut );
		$this -> db -> where( 'code', $code );
		$this -> db -> where( 'type', 'zw_remind' );
		$this -> db -> where( 'instance', $instance );
		$this -> db -> order_by( 'date', 'DESC' );
		
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0 ) {
			return $query->result()[0] -> date;
		}
		
		return FALSE;
	}//is_zw_remind_deleted()
    
  	public function is_quest_remind_deleted( $therapist, $code, $instance, $quest )
	{
		$this -> db -> select( 'date' );
		$this -> db -> from( 'reminds_deleted' );
		$this -> db -> where( 'therapist', $therapist );
		$this -> db -> where( 'code', $code );
		$this -> db -> where( 'type', 'questionnaire_remind' );
		$this -> db -> where( 'instance', $instance );
		$this -> db -> where( 'inactive_questionnaire', $quest );
		$this -> db -> where( 'type', $type );
		$this -> db -> where( 'instance', $instance );
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0 ) {
			return $query->result()[0] -> date;
		}

		return FALSE;
	}//is_quest_remind_deleted()
	
	public function is_remind_type_deleted( $therapist, $code, $instance, $type, $quest = null )
	{
		$this -> db -> select( 'date' );
		$this -> db -> from( 'reminds_deleted' );
		$this -> db -> where( 'therapist', $therapist );
		$this -> db -> where( 'code', $code );
		$this -> db -> where( 'type', $type );
		$this -> db -> where( 'instance', $instance );

		if(isset($quest)){
			$this -> db -> where( 'inactive_questionnaire', $quest );
		}

		$this -> db -> order_by( 'date', 'DESC' );
		
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0 ) {
			return $query->result()[0] -> date;
		}
		
		return FALSE;
	}//is_remind_type_deleted()
}