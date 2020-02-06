<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );


class Supervisor_model extends CI_Model
{	
    
    public function __construct()
    {
        $this -> db = $this -> load -> database( 'default', TRUE );
    }//__construct()
                     
	/*
	* Liefert alle Patienten eines Supervsiors zurück.
	*
	*
	*/
    public function get_all_patients( $username )
    {
        $patients_of_supervisor = NULL;
        
        $this->db->select( 'code' );
		$this->db->from( 'antrag' );
		$this->db->where( 'ant006', $username );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0 )
		{
			$patients_of_supervisor = $query -> result( );	
		}//if
		
        return $patients_of_supervisor;
    }//get_all_patients()
	
	/*
	* Liefert alle Patienten und dessen Therapeuten eines Supervsiors zurück.
	* Die Rückgabe wird nach Therapeutenkürzel absteigend alphabetisch sortiert.
	*
	*
	*/
    public function get_all_patients_with_therapeut( $username )
    {
        $patients_of_supervisor = NULL;
        
        $this->db->select( 'ant.code as patientcode, sub.therapist as therapist' );
		$this->db->from( 'antrag ant' );
		$this->db->join( 'subjects sub', 'ant.code = sub.code' );
		$this->db->where( 'ant.ant006', $username );
		$this->db->distinct();
		$this->db->order_by( 'therapist', 'ASC' );
		$this->db->order_by( 'patientcode', 'ASC' );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0 )
		{
			$patients_of_supervisor = $query -> result( );	
		}//if
		
		return $patients_of_supervisor;
    }//get_all_patients_with_therapeut()
	
	public function is_patient_of_supervisor( $supervisor, $patientcode )
    {
        $is_patient_of_supervisor = FALSE;
		
		$this->db->select('1');
        $this->db->from( 'antrag' );
		$this->db->where( 'code', $patientcode );
		$this->db->where( 'ant006', $supervisor );
		$this->db->limit(1);

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) === 1 )
		{
			$is_patient_of_supervisor = TRUE;	
		}//if
		
        return $is_patient_of_supervisor;
    }//is_patient_of_supervisor()
    
    public function get_all_patients_with_therapeut_with_status_open( $username )
    {
        $patients_of_supervisor = NULL;
        
        $this->db->select( 'sub.code as patientcode, sub.therapist as therapist' );
		$this->db->from( 'subjects sub' );
		$this->db->where( 'sub.therapist', $username );
		$this->db->where( 'sub.zustand', 1 );
		$this->db->distinct();
		$this->db->order_by( 'therapist', 'ASC' );
		$this->db->order_by( 'patientcode', 'ASC' );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0 )
		{
			$patients_of_supervisor = $query -> result( );	
		}//if
		
        return $patients_of_supervisor;
    }//get_all_patients_with_therapeut_with_status_open()
    
    public function get_all_patients_with_therapeut_with_status_temp_break( $username )
    {
        $patients_of_supervisor = NULL;
        
        $this->db->select( 'sub.code as patientcode, sub.therapist as therapist' );
		$this->db->from( 'subjects sub' );
		$this->db->where( 'sub.therapist', $username );
		$this->db->where( 'sub.zustand', 5 );
		$this->db->order_by( 'therapist', 'ASC' );
		$this->db->order_by( 'patientcode', 'ASC' );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0 )
		{
			$patients_of_supervisor = $query -> result( );	
		}//if
		
        return $patients_of_supervisor;
    }//get_all_patients_with_therapeut_with_status_temp_break()
    
    public function get_all_patients_with_therapeut_with_status_closed( $username )
    {
        $patients_of_supervisor = NULL;
        
        $this->db->select( 'sub.code as patientcode, sub.therapist as therapist' );
		$this->db->from( 'subjects sub' );
		$this->db->where( 'sub.therapist', $username );
		$this->db->where( 'sub.zustand', 2 );
		$this->db->order_by( 'therapist', 'ASC' );
		$this->db->order_by( 'patientcode', 'ASC' );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0 )
		{
			$patients_of_supervisor = $query -> result( );	
		}//if
		
        return $patients_of_supervisor;
    }//get_all_patients_with_therapeut_with_status_closed()
    
    public function get_all_patients_with_therapeut_with_status_waiting( $username )
    {
        $patients_of_supervisor = NULL;
        
        $this->db->select( 'sub.code as patientcode, sub.therapist as therapist' );
		$this->db->from( 'subjects sub' );
		$this->db->where( 'sub.therapist', $username );
		$this->db->where( 'sub.zustand', 0 );
		$this->db->order_by( 'therapist', 'ASC' );
		$this->db->order_by( 'patientcode', 'ASC' );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0 )
		{
			$patients_of_supervisor = $query -> result( );	
		}//if
		
        return $patients_of_supervisor;
    }//get_all_patients_with_therapeut_with_status_waiting()
    
    public function get_all_patients_with_therapeut_with_status_abort( $username )
    {
        $patients_of_supervisor = NULL;
        
        $this->db->select( 'ant.code as patientcode, sub.therapist as therapist' );
		$this->db->from( 'subjects sub' );
		$this->db->where( 'sub.therapist', $username );
		$this->db->where( 'sub.zustand', 4 );
		//$this->db->or_where( 'd.dok012', 3 );
		// $this->db->or_where( 'd.dok012 >=', 7 );
		// $this->db->where( 'd.dok012 <=', 11 );
		$this->db->order_by( 'therapist', 'ASC' );
		$this->db->order_by( 'patientcode', 'ASC' );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0 )
		{
			$patients_of_supervisor = $query -> result( );	
		}//if
		
        return $patients_of_supervisor;
    }//get_all_patients_with_therapeut_with_status_abort()	
}//class Supervisor_model