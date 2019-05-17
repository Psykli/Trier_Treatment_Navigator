<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

/**
 * An instance of the class represents a patient model.
 * It contains all functions for patient management.
 *
**/
class Patient_model extends CI_Model
{

    public function __construct( )
    {
        parent::__construct();

        $this -> load -> Model('Membership_model');
    }

    public function get_all_patients( $username )
    {
        $is_admin = $this -> membership_model -> get_role( $username ) === 'admin';

        $all_patients = NULL;
        $this -> db -> db_select( );
        
        $this -> db -> select( 'sub.code as code, dok.dok012 as zustand, sub.therpist as therpist'  );
        $this -> db -> select( 'date(sub.erstsich) as erstsich', FALSE );
        $this -> db -> from( 'subjects sub' );
        $this -> db -> join( 'dokumentation dok', 'dok.code = sub.code' );
        $this -> db -> order_by( 'sub.code' );

        //if it's a admin show all.
        if( !$is_admin )
        {
            $this -> db -> where( 'therpist', $username );
        }//if
        else
        {
			log_message( 'info', 'Admin patientlist request.' );
        }//else

        $query = $this -> db -> get( );

        if( $query )
        {
            $all_patients = $query -> result( );
        }//if
        
        return $all_patients;
    }//get_all_patients()

    public function does_login_exist( $initials )
    {
        $exists = FALSE;
        
        $this -> db -> select( '1' );
        $this -> db -> from( 'user' );
		$this -> db -> where( 'initials', $initials );
        $this->db->limit( 1 );
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) === 1 )
		{
			$exists = TRUE;
		}//if

        return $exists;
    }//does_login_exist()

    public function get_patient_like( $username, $patientcode ){
        $patient = NULL;

        //is it a patient of the user
        $patient_of_threapist = $this -> get_patient_of_therapist( $username );
        $is_patient = isset($patient_of_threapist) && $patient_of_threapist->CODE === $patientcode;
        //or is the user a admin
        $is_admin = $this -> membership_model -> get_role( $username ) === 'admin';


        //if yes, collect the information
        if( $is_patient OR $is_admin )
        {
            $this -> db -> db_select( );

            $this -> db -> select( 'code, gender, birthday, language, school, supervis, view_status' );
            $this -> db -> from( 'subjects' );
            $this -> db -> like( 'code', $patientcode );
            $this -> db -> order_by('code', 'ASC');
            
            //if it's a admin show all.
            if( !$is_admin )
            {
                $this -> db -> where( 'therpist', $username );
            }//if
            else
            {
                log_message( 'info', 'Admin patient request.' );
            }//else

            $query = $this -> db -> get( );

            if( $query -> num_rows( ) > 0 )
            {
                $patient = $query -> result( );
            }//if
        }//if
        else
        {
            log_message( 'warn', "Request without rights for $username -> $patientcode" );
        }//else

        return $patient;
    }//get_patient_like

    public function get_patient_of_therapist( $username )
    {
        $result = NULL;
        if ( isset( $username ))
		{
			$this -> db -> db_select( );	
			$this -> db -> from ( 'subjects' );
			$this -> db -> where( 'THERPIST', $username );	
			
			$query = $this -> db -> get( );
			
			if( $query -> num_rows( ) == 1 ){
                $result = $query->result();
                $result = $result[0];
            }
		}
		
        return $result;
    }//get_patient_of_therapist

    public function get_patient_codes_of_therapist( $username )
    {
        $result = [];
        if ( isset( $username ))
		{
            $this -> db -> db_select( );
            $this -> db -> select( 'CODE' );
			$this -> db -> from ( 'subjects' );
			$this -> db -> where( 'THERPIST', $username );	
			
			$query = $this -> db -> get( );
			
			if( $query -> num_rows( ) > 0 ){
                $result = $query -> result();
            }
		}
		
        return $result;
    }//get_patient_codes_of_therapist

    public function search_patient ( $patientcode )
	{
		$patients = NULL; 
		if( isset( $patientcode ) )
		{	
			$this -> db -> from( 'subjects' );
			$this -> db -> like( 'CODE', $patientcode );
			$this -> db -> order_by( 'CODE', 'ASC' );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) > 0  )
				$patients = $query -> result( );
				
		}
		return $patients;	

    }//search_patient
    
    public function is_therapist_of_patient( $therapist, $patient ){
        if( isset( $therapist ) ) {
            if( !isset( $patient ) ) {
                return true;
            }
            
            $this -> db -> select( '1' );
            $this -> db -> from( 'subjects' );
            $this -> db -> where( 'THERPIST', $therapist );
            $this -> db -> where( 'CODE', $patient );
            $this->db->limit( 1 );
            $query = $this -> db -> get( );
            
            if( $query->num_rows() > 0 ) {
                return true;
            }

            return false;
        }

        return NULL;
    }//is_therapist_of_patient

    public function is_supervisor_of_patient( $supervisor, $patient ){
        if( isset( $supervisor ) ) {
            if( !isset( $patient ) ) {
                return true;
            }
            
            $this -> db -> select( '1' );
            $this -> db -> from( 'subjects' );
            $this -> db -> where( 'SUPERVIS', $supervisor );
            $this -> db -> where( 'CODE', $patient );
            $this -> db -> limit( 1 );

            $query = $this -> db -> get( );
            
            if( $query->num_rows() === 1 ) {
                return true;
            }

            return false;
        }

        return NULL;
    }//is_supervisor_of_patient

    public function get_view_status( $patientcode )
    {
        $view_status = NULL;
        $this -> db -> db_select( );

        $this -> db -> select( 'view_status' );
        $this -> db -> from( 'subjects' );
        $this -> db -> where( 'code', $patientcode );
        $this -> db -> limit( 1 );

        $query = $this -> db -> get( );

        if( $query -> num_rows( ) === 1 )
        {
            $view_status = intval( $query -> result( )[0] -> view_status );
        }//if
        
        return $view_status;
    } //get_view_status()

    public function get_therapist_name( $username, $patientcode )
    {
        if( empty( $username ))
        {
            return FALSE;
        }//if

        $therapist_initials = NULL;

        // is a code set?
        if( $patientcode !== '' )
        {
            $this -> db -> db_select( );	
            $this -> db -> select( 'therpist' );
            $this -> db -> from ( 'subjects' );
            $this -> db -> where( 'code', $patientcode );	
            $this -> db -> limit( 1 );
            $query = $this -> db -> get( );
            
            if( $query -> num_rows( ) === 1 )
            {
                $therapist_initials = $query -> row( 0 ) -> therpist;
            }//if
        }//if
        else
        {
            // it's the user himself -> the user is the owner.
            $therapist_initials = $username;
        }//else

        return $therapist_initials;
    }//get_therapist_name()

    public function get_datenschutz_status_by_patient( $patientcode )
    {
        $datenschutz = NULL;
        
        $this -> db -> select( 'ot_studie_ziel, ot_studie_info, ot_studie_anonym, ot_studie_notfall, ot_studie_risiken' );
        $this -> db -> from( 'ot_datenschutz' );
        $this -> db -> where( 'patientcode', $patientcode );
        $this -> db -> limit( 1 );

        $query = $this -> db -> get( );
        
        if( $query -> num_rows( ) == 1 )
        {
            $datenschutz = $query -> row( 0 );
        }//if

        return $datenschutz;
    }//get_datenschutz_status_by_patient()

    public function set_datenschutz( $patientcode, $ot_studie_ziel = 1 , $ot_studie_info = 1, $ot_studie_anonym = 1, $ot_studie_notfall = 1, $ot_studie_risiken = 1 )
    {
        $datum = date('Y-m-d');

        $data = array(
                'patientcode' => $patientcode,
                'ot_studie_ziel' => $ot_studie_ziel,
                'ot_studie_info' => $ot_studie_info,
                'ot_studie_anonym' => $ot_studie_anonym, 
                'ot_studie_notfall' => $ot_studie_notfall,
                'ot_studie_risiken' => $ot_studie_risiken,
                'datum' => $datum
        );
        
        $this -> db -> insert('ot_datenschutz', $data); 

    }//set_datenschutz()

    public function get_patient( $username, $patientcode )
    {
        $patient = NULL;

        //is it a patient of the user
        $patient_to_therapeut = $this -> _is_patient_of_user( $username, $patientcode );

        $is_admin = NULL;
        if( !$patient_to_therapeut ) {
            $is_admin = $this -> membership_model -> is_role( $username, 'admin' );
        }

        //if yes, collect the information
        if( $patient_to_therapeut OR $is_admin )
        {
            $this -> db -> db_select( );

            $this -> db -> select( 'code, gender, birthday, language, school, supervis, view_status' );
            $this -> db -> from( 'subjects' );
            $this -> db -> where( 'code', $patientcode );

            //if it's a admin show all.
            if( !$is_admin )
            {
                $this -> db -> where( 'therpist', $username );
            }//if
            else
            {
                log_message( 'info', 'Admin patient request.' );
            }//else

            $this -> db -> limit( 1 );
            $query = $this -> db -> get( );

            if( $query -> num_rows( ) > 0 )
            {
                $patient = $query -> result( );
            }//if
        }//if
        else
        {
            log_message( 'warn', "Request without rights for $username -> $patientcode" );
        }//else

        return $patient;
    }//get_patient()

    //Zusammenführung und Vereinfachung von get_status und _get_status
    //phq nur vorläufig; kann auch geändert werden
    public function get_status($patientcode)
    {
        $status = NULL;
        
        $this -> db -> db_select( );
        $this -> db -> select( "'phq-9' as status_name, code, instance, date(phqdat) as date" );
        $this -> db -> from( 'phq-9' );
        $this -> db -> where( 'code', $patientcode );
        $this -> db -> order_by('date', 'desc');

        $query = $this -> db -> get();

        if ($query -> num_rows() > 0) {
            $status = $query -> result();
        }

        return $this->sort_status_by_instance($status);
    }//get_status()

    private function sort_status_by_instance($status)
    {
        $result = array();
        $post = array();
        $z = array();
        $pr = array();
        $ot = array();
        $wz = array();

        for ($i=0; $i < sizeof($status); $i++) { 
            if(strpos($status[$i]->instance,"Z") !== FALSE && strpos($status[$i]->instance,"WZ") === FALSE){
                $z[] = $status[$i];
                continue;
            }
            if(strpos($status[$i]->instance,"PR") !== FALSE){ 
                $pr[] = $status[$i];
                continue;
            }
            if(strpos($status[$i]->instance,"OT") !== FALSE){
                $ot[] = $status[$i];
                continue;
            }
            if(strpos($status[$i]->instance,"WZ") !== FALSE){ 
                $wz[] = $status[$i];
                continue;
            }

            $post[] = $status[$i];

        }

        $result = array_merge($post,$z,$pr,$ot,$wz);
        return $result;
    }

    public function get_last_hscl($patientcode)
    {
        $result = null;

        $sql = "SELECT code, instance, hscdat as date
                FROM `hscl-11`
                WHERE code=?
                AND instance REGEXP '^\\\\d+'
                ORDER BY date DESC";
        
        /* TODO finish the conversion of this function to the query builder (note: don't use limit 1 because of the order by)
        $this -> db -> select( 'code, instance, hscdat as date' );
        $this -> db -> from( 'hscl-11' );
        $this -> db -> where( 'code', $patientcode );
        $this -> db -> where( 'instance REGEXP',  '/^\\\\\\\\d+/m', FALSE);
        $this -> db -> order_by('date', 'desc');*/
        
        $query = $this -> db -> query( $sql, array( $patientcode ) );
        //$query = $this -> db -> get();

        /*
        $result = $query -> result( );
        var_dump($result);
        die();

        if( $query -> num_rows( ) > 0 )
        {
            $result = $query -> result( )[0];
        }
        */

        if( $query -> num_rows( ) > 0 )
        {
            $result = $query -> result( );
            $result = $result[0];
        }

        return $result;
    }

    public function get_sb_allowed($patientcode){
        $result = null;

        $this -> db -> db_select( );
        $this -> db -> from( 'allow_sb' );
        $this -> db -> where( 'patientcode', $patientcode );
        $this -> db -> limit( 1 );

        $query = $this -> db -> get( );
        
        if( $query -> num_rows( ) === 1 ) {
            $result = $query->result()[0];
        }

        return $result;
    }//get_sb_allowed()

    public function get_boundary($patientcode, $instance){
        $result = NULL;

        $this -> db -> db_select( );
        $this -> db -> from( 'entscheidungsregeln_hscl' );
        $this -> db -> where( 'code', $patientcode );
        $this -> db -> where( 'instance', $instance );
        $this -> db -> limit( 1 );

        $query = $this -> db -> get( );

        if( $query -> num_rows( ) === 1 ) {
            $result = $query->result()[0];
        }

        return $result;
    }//get_boundary()

    public function get_therapist_of_patient( $patientcode ) {
        $therapist = NULL;
        
        $this -> db -> select( 'THERPIST' );
		$this -> db -> from( 'subjects' );
        $this -> db -> where( 'CODE', $patientcode );
        $this -> db -> limit( 1 );
		$query = $this -> db -> get( );
		
        if( $query -> num_rows() === 1 ) {
            $therapist = $query -> row( 0 ) -> THERPIST;
        }

		return $therapist;	
    }

    public function get_therapeut_name( $username, $patientcode )
    {
        if( !isset( $username ) OR is_null( $username ) OR $username === '' )
        {
            return FALSE;
        }//if

        $therapeut_initials = NULL;

        $is_admin = $this -> membership_model -> get_role( $username ) === 'admin';
        
        if( $is_admin )
        {
            // is a code set?
            if( $patientcode !== '' )
            {
				$this -> db -> select( 'therpist' );
				$this -> db -> from ( 'subjects' );
				$this -> db -> where( 'code', $patientcode );	
                $this -> db -> limit( 1 );
                
				$query = $this -> db -> get( );
                
				if( $query -> num_rows( ) === 1 ) {
                    $therapeut_initials = $query -> row( 0 ) -> therpist;
                }
            }//if
        }//if
        else
        {
            // it's the user himself -> the user is the owner.
            $therapeut_initials = $username;
        }//else

        return $therapeut_initials;
    }//get_therapeut()

    /*
    * Umbenannt zur besseren Abgrenzung
    * Ersetzt get_process_data aus altem Release 
    * @return bow_data = alle vorhandenen Instanzen des Fragebogens für den genannten Nutzer als Array
    */
    public function fetch_bow_data( $username, $patientcode, $db_table, $allowed_instances = null , $negated = false)
    {
        $bow_data = NULL;

        if( !$username OR !$patientcode OR !$db_table )
        {
            log_message( 'warn', "Some arguements for the fetch_bow_data method are missing" );
        }//if
        else
        {
            //is it a patient of the user
            $patient_to_therapeut = $this -> _is_patient_of_user( $username, $patientcode );
            //or is the user a admin
            $is_admin = $this-> Membership_model -> get_role( $username ) === 'admin';
			
            if( $patient_to_therapeut OR $is_admin  )
            {
                $this -> db -> db_select( );

                //FIX no fix for CI 2.1.3
                //it's a crappy mixture of doing safe prepared_statements.
                $sql = "SELECT *
                         FROM `$db_table`
                         WHERE CODE=? ";
                if(isset($allowed_instances)){
                    $sql.= "AND (";
                    $comparator = $negated ? 'NOT RLIKE' : 'RLIKE';
                    $count = 0;
                    foreach($allowed_instances as $ins){
                        $sql.= "INSTANCE ". $comparator. " '".$ins."\\d*'";
                        if($count++ < sizeof($allowed_instances)-1){
                            if($negated){
                                $sql.= " AND ";
                            } else {
                                $sql.= " OR ";
                            }
                        }
                    }
                    $sql.= ") ";
                }         
                $sql .= "ORDER BY instance ASC";
                $query = $this -> db -> query( $sql, array( $patientcode ) );

                if( $query -> num_rows( ) > 0 )
                {
                    $bow_data = $query -> result_array( );
                }//if
            }//if
            else
            {
                log_message( 'warn', "Request without rights for $username -> $patientcode" );
            }//else
        }//else
		
        return $bow_data;
    }//fetch_bow_data()

    private function _is_patient_of_user($username, $patientcode)
    {
        $is_patient = false;

        $this -> db -> select( '1' );
        $this -> db -> from('subjects');
        $this -> db -> where('CODE', $patientcode);
        $this -> db -> where('THERPIST', $username);
        $this -> db -> limit( 1 );

        $query = $this-> db -> get(); 
        
        if ($query -> num_rows() === 1)
        {
            $is_patient = true;
        }

        return $is_patient;
    }

    //Ruft im Questionnaire_Model die Funktionen auf
    //Sobald eine Rot ist, ist Behandlungsanpassung klickbar
    //Die Funktionen werden mit $someInstance als Parameter aufgerufen
    public function get_feedback_of_patient ($patientcode) {
        $colAlliance = $this -> Questionnaire_model -> get_bez ($patientcode);
        $colMotivation = $this -> Questionnaire_model -> get_motivation ($patientcode);
        $colRisk = $this -> Questionnaire_model -> get_risk ($patientcode);
        $colSocSupLife = $this -> Questionnaire_model -> get_soc_sup_life ($patientcode);
        $colEmotion = $this -> Questionnaire_model -> get_emo ($patientcode);

        if ($colEmotion === "red" || $colSocSupLife === "red" || $colRisk === "red" || $colMotivation === "red" || $colAlliance === "red" ) {
            return "red";
        } elseif ($colEmotion === "missing" && $colSocSupLife === "missing" && $colRisk === "missing" && $colMotivation === "missing" && $colAlliance === "missing") {
            return "missing";
        } elseif ($colEmotion === "green" && $colSocSupLife === "green" && $colRisk === "green" && $colMotivation === "green" && $colAlliance === "green") {
            return "green";
        }
        else {
            return NULL;
        }
    }

}