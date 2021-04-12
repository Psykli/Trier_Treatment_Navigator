<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

/**
 * An instance of the class represents a therapy model.
 * It contains all functions for therapy management.
 * 
 * @package Model
 * 
 * @author Martin Kock <code @ deeagle.de>
 */
class Therapy_Model extends CI_Model
{	
	//	Liste der Zust�nde
	// 0: Wartezeit
	// 1: Laufend
	// 2: Regul�rer Abschluss
	// 3: Abbruch mit bewilligten Sitzungen
	// 4: Abbruch in Probatorik
	// 5: Unterbrechung
	// 6: Therapie nicht Zustande gekommen
	// 7: Abbruch in Probatorik durch Therapeut
	// 8: Abbruch in Probatorik durch Patient
	// 9: Abbruch mit bewilligten Sitzungen durch Therapeut
	//10: Abbruch mit bewilligten Sitzungen durch Patient
	//11: Abbruch aus formalen Gr�nden

    const WAITING_PERIOD = 0;   
    const RUNNING = 1;
    const FINISHED = 2;            
	const DROPOUT = 3;
    const DROPOUT_IN_PROBATORIK = 4;  
    const TEMP_BREAK = 5;
	const NOT_ESTABLISHED = 6;
	const DROPOUT_in_PROBATORIK_THERAPEUT = 7;
	const DROPOUT_in_PROBATORIK_PATIENT = 8;
	const DROPOUT_THERAPEUT = 9;
	const DROPOUT_PATIENT = 10;
	const DROPOUT_FORMAL = 11;
	
 
    private $pat_to_thera_table = 'patient_to_therapeut';
    private $pat_table = 'patient';
    
    /**
     * @since 0.4.0
     * @access public
     */
    public function __construct()
    {
        $this -> db = $this -> load -> database( 'default', TRUE );
		
		$this->load->model( 'Patient_model' );
        $this->load->model( 'Remind_model' );
        $this->load->model( 'Questionnaire_tool_model' );
        $this->load->model( 'SB_model' );
    }//__construct()

    public function get_zw_reminds_of_patient( $patientcode ){
        //Initialisiere eine leeres Arrays
		$reminds = array(); 

        //Anzahl der Sitzungen, die nach einer Zwischenmessung vergangen sein m�ssen um eine Meldung zu erzeugen
        $anzahlSitzungen = 2;
    
        //Hole letzte "Instance" aus den Sitzungsb�gen
        $this -> db -> db_select( );

        $sql = "SELECT code, instance
                    FROM `einzelfragen_patient_sitzungsbogen`
                    WHERE code=? 
                    ORDER BY instance DESC
                    LIMIT 1";
                    
        $query = $this -> db -> query( $sql, array( $patientcode ) );

        if( $query -> num_rows( ) == 1 )
        {
            $last_instance = $query -> row(0) -> instance;
            
            if( strcasecmp( $last_instance, 'KI' ) != 0 && is_numeric( $last_instance ) )
            {
                for( $i=1; $i<=( $last_instance-$anzahlSitzungen )/5; $i++ )
                {
                    $instance_number = $i * 5;
                    if( $instance_number == 5 )
                        $instance = 'Z05';
                    else
                        $instance = 'Z'.$instance_number;
                    /**
                    $sql = "SELECT instance
                            FROM `1 oq-30`
                            WHERE code=? && instance=?";

                    Folgende $sql ist platzhalter!
                    */
                    $sql = "SELECT instance
                    FROM `einzelfragen_patient_sitzungsbogen`
                    WHERE code=? && instance=?";
                    $query = $this -> db -> query( $sql, array( $patientcode, $instance ) );
                    
                    if( $query -> num_rows( ) == 0 )
                    {
                        $username = $this->session->userdata( 'username' );
                        $therapeut = $this->Patient_model->get_therapist_of_patient( $username, $patientcode, false );
                        
                        $remind_date = $this -> Remind_Model -> is_zw_remind_deleted( $username, $patientcode, $instance );
                        
                        if( $remind_date === FALSE){
                            //Schreibe die Therapeuteninitialen noch mit in das Array
                            $reminds[] = array( 'therapeut' => $therapeut, 'code'=> $patientcode, 'instance' => $instance );
                        }//if
                    }//if	
                    
                }//for
            }//if
        }//if
        
        return $reminds;
    }//get_zw_reminds_of_patient()

    public function get_haq_reminds_of_patient( $patientcode ){
        //Initialisiere eine leeres Arrays
		$reminds = array(); 

        //Anzahl der Sitzungen, die nach einer Zwischenmessung vergangen sein m�ssen um eine Meldung zu erzeugen
        $anzahlSitzungen = 2;
    
        //Hole letzte "Instance" aus den Sitzungsb�gen
        $this -> db -> db_select( );

        $sql = "SELECT code, instance
                    FROM `einzelfragen_patient_sitzungsbogen`
                    WHERE code=? 
                    ORDER BY instance DESC
                    LIMIT 1";
                    
        $query = $this -> db -> query( $sql, array( $patientcode ) );

        if( $query -> num_rows( ) == 1 )
        {
            $last_instance = $query -> row(0) -> instance;
            
            if( strcasecmp( $last_instance, 'KI' ) != 0 && is_numeric( $last_instance ) )
            {
                for( $i=1; $i<=( $last_instance-$anzahlSitzungen )/5; $i++ )
                {
                    $instance_number = $i * 5;
                    if( $instance_number == 5 )
                        $instance = 'Z05';
                    else
                        $instance = 'Z'.$instance_number;
                    
                    $sql = "SELECT instance
                            FROM `haq-f`
                            WHERE code=? && instance=?";
                            
                    $query = $this -> db -> query( $sql, array( $patientcode, $instance ) );

                    if( $query -> num_rows( ) == 0 )
                    {
                        $username = $this->session->userdata( 'username' );
                        $therapeut = $this->Patient_model->get_therapist_of_patient( $username, $patientcode, false );
                        $remind_date = $this -> Remind_Model -> is_remind_type_deleted( $therapeut, $patientcode, $instance,'haq_remind' ); 
                        if( $remind_date === FALSE){
                            //Schreibe die Therapeuteninitialen noch mit in das Array
                            $reminds[] = array( 'therapeut' => $therapeut, 'code'=> $patientcode, 'instance' => $instance );
                        }//if
                    }//if	
                    
                }//for
            }//if
        }//if

        return $reminds;
    }//get_haq_reminds_of_patient()

  

    /*
    get_status_data existierte vorher sowohl im patient controller, als auch im dashboard view 
    und ist nun hier zusammengefasst. Im Controller war "dropout" noch als "abort" bezeichnet,
    die Funktionalität ist aber die gleiche und demnach in der get_count_of_therapy Methode enthalten,
    dort nun über den Parameter $zustand = 'dropout' verfügbar
    */

    public function get_status_data( $username, $limit_to = array() )
    {
        //If $limit_to is an empty array, all states (Zustände) get counted
        $limit_to_length = count($limit_to);

        $status = array('open' => 0,
                        'temp_break' => 0,
                        'closed' => 0,
                        'waiting' => 0,
                        'dropout' => 0);

        if( $limit_to_length === 0 OR isset( $limit_to['open'] ) ) {
            $status['open'] = $this->get_count_of_therapy( $username, 'open' );
        }
        
        if( $limit_to_length === 0 OR isset( $limit_to['temp_break'] ) ) {
            $status['temp_break'] = $this->get_count_of_therapy( $username, 'temp_break' );
        }

        if( $limit_to_length === 0 OR isset( $limit_to['close'] ) ) {
            $status['closed'] = $this->get_count_of_therapy( $username, 'close' );
        }

        if( $limit_to_length === 0 OR isset( $limit_to['waiting'] ) ) {
            $status['waiting'] = $this->get_count_of_therapy( $username, 'waiting' );
        }

        if( $limit_to_length === 0 OR isset( $limit_to['dropout'] ) ) {
            $status['dropout'] = $this->get_count_of_therapy( $username, 'dropout');
        }
        
		return $status;
    }//get_status_data()

     /*
    Hieß vorher _get_status_data2 bzw. get_status_data2 und 
    befand sich vorher im Patient controller und wurde der Konsistenz wegen auch in Therapy_model
    transferiert, da sich auch die andere Funktion dort befindet
    */
    function extract_status_data_from_patients( $patients ) {
        $status = array( 'open' => 0,
	                        'closed' => 0,
							'abort' => 0,
							'temp_break' => 0,
                            'waiting' => 0);

        foreach($patients as $patient){
            switch($patient->zustand){               
                case 1:
                    $status['open']++;
                    break;
                case 3:
                case 4:
                case 7:
                case 8:
                case 9:
                case 10:
                case 11:
                    $status['abort']++;
                    break;
                case 2:
                    $status['closed']++;
                    break;
                case 5:
                    $status['temp_break']++;
                    break;
                case 0:
                default:
                    $status['waiting']++;
            }
        }

        return $status;
    }//extract_status_data_from_patients()

    /*Vereinigung der alten "get_count_of ..." Funktionen
    Der Parameter $zustand determiniert, welcher Count zurückgegeben wird
    die verschiedenen Zustände und die Funktionen, die sie ersetzen sind hier aufgeführt:
    'open' = get_count_of_open_therapy
    'temp_break' = get_count_of_temp_break_therapy
    'close' = get_count_of_closed_therapy
    'waiting' = get_count_of_waiting_therapy
    'dropout' = get_count_of_abort_therapy
    */
    public function get_count_of_therapy($username, $zustand, $user_role = 'therapeut')
    {
        $count = 0;
        
        $this -> db -> db_select( );
        switch( $user_role )
        {
            case 'therapeut':
            case 'supervisor':
				$this->db->select( 'zustand' );
				$this->db->from( 'subjects' );
                $this->db->where( 'therapist', $username );
                //Dies ist die einzige Zeile gewesen, in der sich die alten Funktionen unterschieden haben
                switch($zustand)
                {
                    case 'open':
                        $this->db->where( 'zustand', self::RUNNING );
                        break;
                    case 'temp_break':
                        $this->db->where( 'zustand', self::TEMP_BREAK );
                        break;
                    case 'close':
                        $this->db->where( 'zustand', self::FINISHED );
                        break;
                    case 'waiting':
                        $this->db->where( 'zustand', self::WAITING_PERIOD );
                        break;
                    case 'dropout':
                        $this->db->where( 'zustand', self::DROPOUT );
                        $this->db->or_where( 'therapist', $username );
                        $this->db->where( 'zustand', self::DROPOUT_IN_PROBATORIK );	
                        $this->db->or_where( 'therapist', $username );
                        $this->db->where( 'zustand', self::DROPOUT_in_PROBATORIK_THERAPEUT  );	
                        $this->db->or_where( 'therapist', $username );
                        $this->db->where( 'zustand', self::DROPOUT_in_PROBATORIK_PATIENT );	
                        $this->db->or_where( 'therapist', $username );
                        $this->db->where( 'zustand', self::DROPOUT_THERAPEUT );	
                        $this->db->or_where( 'therapist', $username );
                        $this->db->where( 'zustand', self::DROPOUT_PATIENT );						
                        $this->db->or_where( 'therapist', $username );
                        $this->db->where( 'zustand', self::DROPOUT_FORMAL );
                        break;
                }
    
                $count = $this->db->count_all_results();
                log_message( 'debug', 'count for username ' . $username . ' is ' . $count);

                break;
            default:
                log_message( 'error', 'Userrole is not valid.');
                log_message( 'error', '> Userrole:.' . $user_role );
        }//switch

        return $count;
    }//get_count_of_therapy()
}
?>