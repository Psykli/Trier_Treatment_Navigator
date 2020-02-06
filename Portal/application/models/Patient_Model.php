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

    /**
     * Constructer
     * Init of the Psychoeq-Database-Connection.
     */
    public function __construct( )
    {
        $this -> db = $this -> load -> database( 'default', TRUE );
			
		$CI =& get_instance();
		if( !property_exists( $CI, 'db_default' ) ) {
            $CI->db_default =& $this -> db;
        }

        $this->load->Model('Questionnaire_tool_model');
    }

    public function get_all_patients( $username )
    {
        $is_admin = $this -> membership_model -> get_role( $username ) === 'admin';

        $all_patients = NULL;
        $this -> db -> db_select( );
        
        $this -> db -> select( 'code, zustand, therapist'  );
        $this -> db -> select( 'date(erstsich) as erstsich', FALSE );
        $this -> db -> from( 'subjects' );
        $this -> db -> order_by( 'code' );

        //if it's an admin show all.
        if( !$is_admin )
        {
            $this -> db -> where( 'therapist', $username );
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
        $this -> db -> limit( 1 );
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) === 1 )
		{
			$exists = TRUE;
		}//if

        return $exists;
    }//does_login_exist()

    public function get_patient_codes_of_therapist( $username )
    {
        $result = [];
        if ( isset( $username ))
		{
            $this -> db -> db_select( );
            $this -> db -> select( 'CODE' );
			$this -> db -> from ( 'subjects' );
			$this -> db -> where( 'THERAPIST', $username );	
			
			$query = $this -> db -> get( );
			
			if( $query -> num_rows( ) > 0 ){
                $result = $query -> result();
            }
		}
		
        return $result;
    }//get_patient_codes_of_therapist()

    public function search_patients( $username, $patientcode, $therapist = NULL, $columns = NULL, $userrole = NULL )
	{
		$patients = NULL;
        
        if( !isset($userrole) ) {
            $userrole = $this -> membership_model -> get_role( $username );
        }

        if($userrole === 'therapist' || $userrole === 'supervisor' || $userrole === 'admin') {
            if( isset( $columns ) && $columns != "" ) {
                $this -> db -> select( $columns );
            }

            $this -> db -> from( 'subjects' );
            $this -> db -> like( 'CODE', $patientcode );
            
            if( isset( $therapist ) AND !empty($therapist)) {
                $this -> db -> like( 'THERAPIST', $therapist );
            }
            
            $this -> db -> order_by( 'CODE', 'ASC' );
            
            //if it's an admin show all hits
            if( $userrole === 'admin' )
            {
                log_message( 'info', 'Admin patient request.' );
            }//if
            else
            {
                $this -> db -> where( 'THERAPIST', $username );
            }//else
            
            $query = $this -> db -> get( );
    
            if( $query -> num_rows( ) > 0  ) {
                $patients = $query -> result( );
            }
        }

	    return $patients;
    }//search_patient()

    /* get_patient_exact_match($username, $patientcode, $columns = NULL) replaces the former get_patient($username, $patientcode) */
    public function get_patient_exact_match( $username, $patientcode, $columns = NULL )
	{
        $patient = NULL;
        
        $is_patient_of_user = $this -> is_patient_of_user( $username, $username, $patientcode );
        
        $is_admin = NULL;
        if( !$is_patient_of_user ) {
            $is_admin = $this -> membership_model -> is_role( $username, 'admin' );
        }

        if( $is_patient_of_user OR $is_admin )
        {
            if( isset( $columns ) ) {
                $this -> db -> select( $columns );
            }
            
            $this -> db -> from( 'subjects' );
            $this -> db -> where( 'CODE', $patientcode );
            $this -> db -> limit( 1 );
            
            if( $is_admin ) {
                log_message( 'info', 'Admin patient request.' );
            }

            $query = $this -> db -> get( );

            if( $query -> num_rows( ) === 1  ) {
                $patient = $query -> result( )[0];
            }
        }//if
        else
        {
            log_message( 'warn', "Request without rights for $username -> $patientcode" );
        }//else

        return $patient;
    }//get_patient_exact_match()
    
    public function is_therapist_of_patient( $therapist, $patient ){
        $result = false;

        $this -> db -> select( '1' );
        $this -> db -> from( 'subjects' );
        $this -> db -> where( 'THERAPIST', $therapist );
        $this -> db -> where( 'CODE', $patient );
        $this -> db -> limit( 1 );
        
        $query = $this -> db -> get( );
        
        if( $query->num_rows() === 1 ) {
            $result = true;
        }

        return $result;
    }//is_therapist_of_patient()

    public function is_supervisor_of_patient( $supervisor, $patient ) {
        $result = false;

        $this -> db -> select( '1' );
        $this -> db -> from( 'subjects' );
        $this -> db -> where( 'SUPERVIS', $supervisor );
        $this -> db -> where( 'CODE', $patient );
        $this -> db -> limit( 1 );

        $query = $this -> db -> get( );
        
        if( $query->num_rows() === 1 ) {
            $result = true;
        }

        return $result;
    }//is_supervisor_of_patient()

    public function is_therapist_or_supervisor_of_patient( $therapist_or_supervisor, $patient ){
        $result = false;

        $this -> db -> select( '1' );
        $this -> db -> from( 'subjects' );
        $this -> db -> where( 'CODE', $patient );
        
        $this -> db -> group_start();
        $this -> db -> where( 'THERAPIST', $therapist_or_supervisor );
        $this -> db -> or_where( 'SUPERVIS', $therapist_or_supervisor );
        $this -> db -> group_end();

        $this -> db -> limit( 1 );
            
        $query = $this -> db -> get( );
            
        if( $query->num_rows() === 1 ) {
            $result = true;
        }

        return $result;
    }//is_therapist_or_supervisor_of_patient()

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

    public function get_therapist_of_patient( $username, $patientcode, $return_username_if_patientcode_empty = false ) {
        $therapist = null;

        if($patientcode === '' && $return_username_if_patientcode_empty) {
            $therapist = $username;
        }
        elseif (!is_null( $username ) && $username !== '') {
            $this -> db -> select( 'THERAPIST' );
            $this -> db -> from( 'subjects' );
            $this -> db -> where( 'CODE', $patientcode );
            $this -> db -> limit( 1 );
            $query = $this -> db -> get( );
            
            if( $query -> num_rows() === 1 )
            {
                //Only return the result if the user is either the patient, therapist, supervisor (of the patient) or an admin.
                //Others aren't allow to get that information.
                if( $patientcode === $username || $query -> row( 0 ) -> THERAPIST === $username ) {
                    $therapist = $query -> row( 0 ) -> THERAPIST;
                }
                else
                {
                    //The other permission checks failed, but the requesting user could still be a supervisor of the patient or an admin,
                    //which are allowed to get that information.
                    //These two checks are done after the first two in order to prevent executing 2 SQL queries even if for example $patientcode === $username is already true.
                    $is_supervisor = $this -> is_supervisor_of_patient($username, $patientcode);
                    $is_admin = null;
                    
                    if( !$is_supervisor ) {
                        $is_admin = $this -> membership_model -> is_role( $username, 'admin' );
                    }

                    if( $is_supervisor || $is_admin ) {
                        $therapist = $query -> row( 0 ) -> THERAPIST;
                    }
                }
            }
        }

		return $therapist;
    }//get_therapist_of_patient()

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

    //Zusammenführung und Vereinfachung von get_status und _get_status
    //phq nur vorläufig; kann auch geändert werden
    public function get_status($patientcode)
    {
        $status = NULL;
        
        $battery = $this->Questionnaire_tool_model->get_sb_batterie($patientcode, true);

        if($battery == NULL){
            return NULL;
        }
        $table = $battery[0]->tablename;
        $this -> db -> db_select( );
        $this -> db -> select( "'$table' as status_name, code, instance" );
        $this -> db -> from( $table );
        $this -> db -> where( 'code', $patientcode );
        $this -> db -> order_by('instance', 'desc');

        $query = $this -> db -> get();

        if ($query -> num_rows() > 0) {
            $status = $query -> result();
        } else {
            return NULL;
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
    }//sort_status_by_instance()

    public function get_last_hscl($patientcode)
    {

        $result = null;

        $sql = "SELECT code, instance, hscdat as date
                FROM `hscl-11`
                WHERE code=?
                AND instance REGEXP '^\\\\d+'
                ORDER BY date DESC";
        
        //TODO
        /* finish the conversion of this function to the query builder (note: don't use limit 1 because of the order by)
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
    }//get_last_hscl()

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

    public function get_boundary($patientcode, $instance, $columns = NULL){
        $result = NULL;

        $this -> db -> db_select( );
        
        if( isset( $columns ) ) {
            $this -> db -> select( $columns );
        }

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
            log_message( 'warn', "Some arguments for the fetch_bow_data method are missing" );
        }//if
        else
        {
            $is_patient_of_user = $this -> is_patient_of_user( $username, $username, $patientcode );
            
            $is_admin = NULL;
            if( !$is_patient_of_user ) {
                $is_admin = $this -> membership_model -> is_role( $username, 'admin' );
            }
			
            if( $is_patient_of_user || $is_admin || strtoupper($patientcode) === "9995P99" )
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

    public function is_patient_of_user($username, $therapist, $patientcode)
    {
        $is_patient = false;
        $authorized = false;

        if($username !== $therapist && $patientcode !== $username) {
            if( $this -> membership_model -> is_role( $username, 'admin' ) ) {
                $authorized = true;
            }
        }
        else {
            $authorized = true;
        }

        if( $authorized ) {
            $this -> db -> select( '1' );
            $this -> db -> from('subjects');
            $this -> db -> where('CODE', $patientcode);
            $this -> db -> where('THERAPIST', $therapist);
            $this -> db -> limit( 1 );

            $query = $this-> db -> get(); 
            
            if ($query -> num_rows() === 1)
            {
                $is_patient = true;
            }
        }
        else {
            log_message( 'warn', "is_patient_of_user request without rights for $username -> $patientcode" );
        }

        return $is_patient;
    }//is_patient_of_user()

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
            return "black";
        }
    }//get_feedback_of_patient()

}