<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

/**
 * An instance of the class represents a patient model.
 * It contains all functions for patient management.
 *
 * @package Model
 *
 * @since 0.2.0
 * @access public
 *
 * @author Martin Kock <code @ deeagle.de>
 */
class Gas_Model extends CI_Model
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
		
        $this -> load -> Model( 'Membership_Model' );
		$this -> load -> Model('Patient_Model');
    }//__construct()

    private function _check_permissions( $username, $patientcode ) {
        $is_patient_of_user = $this -> Patient_Model -> is_therapist_of_patient( $username, $patientcode );
        
        $is_admin = NULL;
        if( !$is_patient_of_user ) {
            $is_admin = $this -> Membership_Model -> is_role( $username, 'admin' );
        }

        if( $is_patient_of_user OR $is_admin ) {
            return true;
        }
        else {
            return false;
        }
    }//_check_permissions()

    public function insert_update_gas( $patientcode, $entries, $username, $immutable = false)
    {
        if( !$this -> _check_permissions( $username, $patientcode ) ) {
            log_message( 'warn', "insert_update_gas without rights for $username -> $patientcode" );
            return;
        }

        //Anführungszeichen führen zu einem Fehler in der Datenbank, wenn sie nicht escaped wurden
        foreach($entries as $key=>$e) {
            $entries[$key] = addslashes($e);
        }
        
        $instance = 'PR';

        $this -> db -> from( 'gas' );
        $this -> db -> where( 'CODE', $patientcode );
        $this -> db -> where( 'INSTANCE', $instance );
        $query = $this -> db -> get(  ); 

        //Data wird schon vor der Abfrage vorbereitet, um möglicherweise eine Z-Instanz einzufügen, 
        //falls diese bei der PR noch nicht angelegt wurde.
        $data = array();
        $index = 0;
        
        for ($i=12; $i <= 91 ; $i++) { 
            if($index <= sizeof($entries)) {
                $data['GAS0'.$i] = $entries[$index++];
            }
            else {
                $data['GAS0'.$i] = NULL;
            }
        }

        $data['CODE'] = $patientcode;
        $data['INSTANCE'] = $instance;
        $data['GASDAT'] = date('Y-m-d H:i:s',time());
        $data['GASMED'] = 1;
        $data['GAS001'] = -9999;

        for($i= 2; $i <= 11; $i++){
            $str = $i<10 ? '0'.$i : $i;
            $data['GAS0'.$str] = -9999;
        
            if($i < 11) {
                $data['GASM'.$str] = -1;
            }
            
        }

        $data['IMMUTABLE'] = $immutable;

        if($query->num_rows() < 1) {
            //TODO check; use query builder?
            $sql = 'INSERT INTO `gas` (' .
                    implode(", ", array_keys($data)) . ") VALUES ('" .
                    implode("', '", array_values($data)) . "')"; 
            $this -> db -> query($sql); 
            
            if($immutable) {
                $data['INSTANCE'] = 'Z05';

                for($i= 2; $i <= 11; $i++) {
                    if(isset($entries[($i-2)*8])){
                        $str = $i<10 ? '0'.$i : $i;
                        $data['GAS0'.$str] = 0;
                    }
                }

                $data['GASDAT'] = date('Y-m-d H:i:s',time()+1);
                
                $sql = 'INSERT INTO `gas` (' .
                        implode(", ", array_keys($data)) . ") VALUES ('" .
                        implode("', '", array_values($data)) . "')"; 
                $this -> db -> query($sql); 
            }
        } else {
            $result = $query->result();
            //Kommt im Normalfall nicht mehr vor. Sollte aber sicherheitshalber da sein, damit auch bei Patienten, 
            //bei denen bereits eine PR vorliegt nun auch automatisch eine Z-Instanz angelegt wird
            if( !$this->does_instance_exist( $patientcode, 'Z05', $username ) AND $immutable ){
                $data['INSTANCE'] = 'Z05';
                
                for($i= 2; $i <= 11; $i++){
                    if(isset($entries[($i-2)*8])){
                        $str = $i<10 ? '0'.$i : $i;
                        $data['GAS0'.$str] = 0;
                    }
                }
                
                $data['GASDAT'] = date('Y-m-d H:i:s',strtotime($result[0]->GASDAT)+1);
                
                $sql = 'INSERT INTO `gas` (' .
                        implode(", ", array_keys($data)) . ") VALUES ('" .
                        implode("', '", array_values($data)) . "')"; 
                $this -> db -> query($sql); 
            }

            $data = array();
            $date = date('Y-m-d H:i:s',time());
            //$data[] = "GASDAT = '".$date."'"; 
            
            $index = 0;
            for ($i=12; $i <= 91 ; $i++) { 
                if($index <= sizeof($entries))
                    $data[] = "GAS0".$i." = '".$entries[$index++]."'";
                else
                    $data[] = "GAS0".$i." = NULL";
            } 

            $data[] = "IMMUTABLE = ".intval($immutable); 
            //$data[] = "GASDAT = NULL";

            $sql = "UPDATE `gas`                   
                    SET ".implode(',',$data)."
                    WHERE CODE = '".$patientcode."'"; 
            $this -> db -> query($sql);
        }
    }//insert_update_gas()

    public function insert_new_z( $patientcode, $instance, $entries, $username ) {
        if( !$this -> _check_permissions( $username, $patientcode ) ) {
            log_message( 'warn', "insert_new_z without rights for $username -> $patientcode" );
            return;
        }

        $data = array();

        for ($i=12; $i < 92 ; $i++) { 
            $data['GAS0'.$i] = $entries['GAS0'.$i];
        }

        $data['CODE'] = $patientcode;
        $data['INSTANCE'] = $instance;
        //$data['GASDAT'] = date('Y-m-d H:i:s',time());
        $data['GASMED'] = 1;

        for($i= 1; $i <= 11; $i++) {
            $str = $i<10 ? '0'.$i : $i;
            $data['GAS0'.$str] = -9999;
            
            if($i < 11){
                $data['GASM'.$str] = -1;
            }
        }

        foreach($data as $key => $val) {
            $data[$key] = addslashes($val);
        }

        //TODO use query builder? same as the other query with implode
        $sql = 'INSERT INTO `gas` (' .
                    implode(", ", array_keys($data)) . ") VALUES ('" .
                    implode("', '", array_values($data)) . "')"; 
        
        $this -> db -> query($sql); 
    }//insert_new_z()

    public function get_gas_data($patientcode, $username, $instance = NULL)
    {
        $results = NULL;
        
        if( $this -> _check_permissions( $username, $patientcode ) ) {
            $this -> db -> from( 'gas' );
            $this -> db -> where( 'CODE', $patientcode );

            if( isset($instance) ) {
                $this -> db -> where( 'INSTANCE', $instance );
            }
            
            $this -> db -> order_by('GASDAT', 'asc');        
            $query = $this -> db -> get( );
            
            if( $query -> num_rows() > 0)
            {
                $results = $query->result();
            }
        }
        else {
            log_message( 'warn', "get_gas_data without rights for $username -> $patientcode" );
        }

        return $results;
    }//get_gas_data()

    public function save_gas_values($patientcode, $username, $instance, $entries, $sb_instance = null){
        if( $this -> _check_permissions( $username, $patientcode ) ) {
            $data = array();

            if(isset($sb_instance)){
                $data[] = "GAS001 = '".$sb_instance."'";
            }
            
            foreach ($entries as $key => $value) {
                $data[] = $key . " = '".$value."'";
            }
            
            $data[] = "GASDAT = '" .date('Y-m-d H:i:s',time())."'";
            //TODO use query builder
            $sql = "UPDATE `gas`                   
                        SET ".implode(',',$data)."
                        WHERE CODE = '".$patientcode."' AND INSTANCE = '".$instance."'"; 
            
            $this -> db -> query($sql);
        }
        else {
            log_message( 'warn', "save_gas_values without rights for $username -> $patientcode" );
        }
    }//save_gas_values

    public function delete_gas($patientcode, $instance, $username) {
        if( !$this -> _check_permissions( $username, $patientcode ) ) {
            log_message( 'warn', "delete_gas without rights for $username -> $patientcode" );
            return false;
        }

        $this -> db -> where( 'CODE', $patientcode );
        $this -> db -> where( 'INSTANCE', $instance );
        $this -> db -> delete( 'gas' );
        return true;
    }//delete_gas

    public function is_predecessor_filled($patientcode, $instance, $username) {
        if( !$this -> _check_permissions( $username, $patientcode ) ) {
            log_message( 'warn', "is_predecessor_filled without rights for $username -> $patientcode" );
            return false;
        }

        if($instance === 'PR') {
            return true;
        }

        $pred = "PR";
        
        if(preg_match('/Z\d+/',$instance)) {
            $num = intval(substr($instance, 1));
            $num -= 5;

            while($num > 0) {
                $z = $num >= 10 ? 'Z'.$num : 'Z0'.$num;
                
                $this -> db -> select( '1' );
                $this -> db -> from( 'gas' );
                $this -> db -> where( 'CODE', $patientcode );
                $this -> db -> where( 'INSTANCE', $z );
                $this -> db -> limit( 1 );
                $query = $this -> db -> get( );
                
                if($query->num_rows() === 1) {
                    $pred = $z;
                    break;
                }

                $num-= 5;            
            }
        } elseif($instance === 'PO') {
            //TODO use query builder
            $where = "INSTANCE REGEXP '^Z[0-9]+$'";
            
            $sql = "SELECT INSTANCE
                FROM `gas` 
                WHERE CODE = '".$patientcode."' AND INSTANCE REGEXP '^Z[0-9]+$'
                ORDER BY INSTANCE DESC";     
            $query = $this -> db -> query($sql);
            
            if($query->num_rows() >= 1) {
                $results = $query->result();
                $pred = $results[0]->INSTANCE;
            } else {
                $pred = 'PR';
            }
        } elseif(preg_match('/K\d+/',$instance)){
            $num = intval(substr($instance, 1));
            $num -= 1;
            
            while($num > 0) {
                $k = $num >= 10 ? 'K'.$num : 'K0'.$num;
                
                $this -> db -> select( '1' );
                $this -> db -> from( 'gas' );
                $this -> db -> where( 'CODE', $patientcode );
                $this -> db -> where( 'INSTANCE', $k );
                $this -> db -> limit( 1 );
                $query = $this -> db -> get( );
                
                if($query->num_rows() === 1) {
                    $pred = $k;
                    break;
                }
            }

            /*
            This comparison does nothing but the code seems to work anyway (it was this way in the old version too).
            Maybe it should once have been a single "="? Nobody knows why it was there, it's commented out for now though.
            if($pred === 'PR') {
                $pred === 'PO';
            }
            */
        }

        $this -> db -> select( 'GASDAT' );
        $this -> db -> from( 'gas' );
        $this -> db -> where( 'CODE', $patientcode );
        $this -> db -> where( 'INSTANCE', $pred );
        $this -> db -> limit( 1 );
        $query = $this -> db -> get( );
        
        if($query -> num_rows() === 1) {
            if($query -> result()[0] -> GASDAT != NULL) {
                return true;
            }
        }

        return false;
    }

    public function does_pr_exist($patientcode, $username)
    {
        $pr_exists = false;
        
        if( $this -> _check_permissions( $username, $patientcode ) ) {
            $instance = 'PR';

            $this -> db -> select( '1' );
            $this -> db -> from( 'gas' );
            $this -> db -> where( 'CODE', $patientcode );
            $this -> db -> where( 'INSTANCE', $instance );
            $this -> db -> limit( 1 );
            
            $query = $this -> db -> get( );
            
            if($query->num_rows() === 1) {
                $pr_exists = true;
            }
        }
        else {
            log_message( 'warn', "does_pr_exist without rights for $username -> $patientcode" );
        }

        return $pr_exists;
    }//does_pr_exist

    public function does_instance_exist( $patientcode, $instance, $username ) {
        $instance_exists = false;
        
        if( $this -> _check_permissions( $username, $patientcode ) ) {
            $this -> db -> select( '1' );
            $this -> db -> from( 'gas' );
            $this -> db -> where( 'CODE', $patientcode );
            $this -> db -> where( 'INSTANCE', $instance );
            $this -> db -> limit( 1 );
            
            $query = $this -> db -> get( );
            
            if($query->num_rows() === 1) {
                $instance_exists = true;
            }
        }
        else {
            log_message( 'warn', "does_instance_exist without rights for $username -> $patientcode" );
        }

        return $instance_exists;
    }//does_instance_exists()

    public function is_immutable($patientcode, $username)
    {
        $is_immutable = false;

        if( $this -> _check_permissions( $username, $patientcode ) ) {
            $instance = 'PR';

            $this -> db -> select( 'IMMUTABLE' );
            $this -> db -> from( 'gas' );
            $this -> db -> where( 'CODE', $patientcode );
            $this -> db -> where( 'INSTANCE', $instance );
            $this -> db -> limit( 1 );
            
            $query = $this -> db -> get( );
            
            if($query->num_rows() === 1) {
                if( $query -> row( 0 ) -> IMMUTABLE ) {
                    $is_immutable = true;
                }
            }
        }
        else {
            log_message( 'warn', "is_immutable without rights for $username -> $patientcode" );
        }

        return $is_immutable;
    }//is_immutable()
}//Gas_Model