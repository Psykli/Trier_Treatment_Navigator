<?php
class SB_Model extends CI_Model {

    public function __construct( )
    {
        parent::__construct();
        $this -> db = $this -> load -> database( 'default', TRUE );
    }

    /*
		Deletes all but the first 10 entries of a given patient in the sb_start table
		,when the next entry would be the 21. entry.
	*/
	public function test_patient_purge(){
		$patients = array("9995P99","9999P99");
		foreach ($patients as $patientcode) {
			$purged = false;
			$this->db->db_select();
			$this->db->select('*');
			$this->db->from('sb_start');
			$this->db->where('sb_start_01', $patientcode);
			$this->db->order_by('sb_start_02', 'ASC');
		
			$query = $this->db->get();
			$query_array = $query->result();
			if ($query->num_rows()+1 > 10) {			
				for ($i=$query->num_rows()-1; $i >= 10 ; $i--) { 
					$this->db->db_select();
					$this->db->where('sb_id', $query_array[$i]->sb_id);
					$this->db->where('sb_start_date', $query_array[$i]->sb_start_date);
					$this->db->delete('sb_start');
				}
				$purged = true;
			}
		
			$this->db->db_select();
			$this->db->select('*');
			$this->db->from('sb_start');
			$this->db->where('sb_start_01', $patientcode);
			$this->db->order_by('sb_start_02', 'ASC');
			
			$query = $this->db->get();
			$query_array = $query->result();
			$highestInstance = $query_array[($query->num_rows()-1)]->sb_start_02;
			
			$tables = array("einzelfragen_patient_sitzungsbogen","1 IIP-46","2 HSCL-11","2 Katamnesedoku_neu",
			"3 INK-10","3 PSTB","4 SRS V30","5 BPRP-P","5 TSTB","6 Einzelfragen Therapeut Sitzungsbogen",
			"8 BPRP-T","8 PSSI-K","Antrag","VEV","entscheidungsregeln_hscl");
			
			foreach ($tables as $val) {
				$sql = "SELECT * FROM `" . $val . "` WHERE CODE=? ORDER BY INSTANCE ASC";
				
				$query = $this->db->query($sql, array($patientcode));
				$query_array = $query->result();
				$sql = "DElETE FROM `" . $val . "` WHERE CODE=? AND INSTANCE>=?"; 
				$query = $this->db->query($sql, array($patientcode, 10));
				$sql = "DElETE FROM `cache` WHERE CODE=? AND INSTANCE>=? AND TEST=?"; 
				$query = $this->db->query($sql, array($patientcode, 10, $val));

			}
		}
		return $purged;
	}

	public function get_instance($patientcode, $table) {
		//TODO
		/*finish this conversion from raw sql queries to the query builder
		$therapist = NULL;
        
        $this -> db -> db_select( );
        $this -> db -> select_max( 'CONVERT(INSTANCE, USNIGNED) AS INSTANCE' );
        $this -> db -> from( $table );
        $this -> db -> where( 'code', $patientcode );
		$this -> db -> limit( 1 );
		
        $query = $this -> db -> get();
		var_dump($query->result());
		die();
        if($query->num_rows() === 1) {
			$therapist = $query->result();
		}
		return $therapist;
		*/

		$this-> db ->db_select();
		$sql = "SELECT MAX(CONVERT(INSTANCE, UNSIGNED INTEGER)) AS INSTANCE FROM `" . $table . "` WHERE CODE=? LIMIT 1";
		$query = $this-> db -> query($sql, array($patientcode));
		
		if($query->num_rows() == 1) {
			$therapist = $query->result();
		}
		return $therapist;
	}//get_instance()

	public function get_PR_date($patientcode){

		/**
		$sql = "SELECT `OQ3DAT` as date FROM `1 oq-30` WHERE `CODE` = '".$patientcode."' AND `INSTANCE` = 'PR'";
		$query = $this -> db -> query($sql);

		if( $query -> num_rows( ) > 0  ){
			$result = $query->result();
			return $result[0]->date;
		}
		*/
		return null;
	}//get_PR_date()

	public function has_filled_request($patientcode) {
		if(!empty($patientcode)){
			if(strtotime($this->get_PR_date($patientcode)) < 1490997600 ) { // 1490997600 = strtotime("2017-04-01")
				return true;
			}

			$this -> db -> select( '1' );
			$this -> db -> from('antrag');
			$this -> db -> where('CODE', $patientcode);
			$this -> db -> where('ANT007', 1);
			$this -> db -> limit( 1 );

			$query = $this -> db -> get( );
			
			if( $query -> num_rows() === 1 ) {
				return true;
			}
		}

		return false;
	}//has_filled_request

	public function has_filled_questionnaire($patientcode, $questionnaire){

		if(!empty($patientcode)){
			if(strtotime($this->get_PR_date($patientcode)) < 1490997600 ) { // 1490997600 = strtotime("2017-04-01")
				return true;
			}

			$this -> db -> select( '1' );
			$this -> db -> from( $questionnaire );
			$this -> db -> where('CODE', $patientcode);

			if($questionnaire === 'gas')
			{
				$this -> db -> where('INSTANCE', 'PR');
			}

			$this -> db -> limit( 1 );

			$query = $this -> db -> get( );
			
			if( $query->num_rows() === 1 ) {
				return true;
			}
		}
		
		return false;
	}//has_filled_personality_disorder()

	public function has_gas($patientcode) {
		if(!empty($patientcode)){
			
			if(strtotime($this->get_PR_date($patientcode)) < strtotime("2017-04-01")){
				return true;
			}
			$sql = "SELECT * FROM `gas` WHERE `CODE` = '".$patientcode."' AND `INSTANCE` = 'PR'";

			$query = $this -> db -> query($sql);
			
			if( $query -> num_rows( ) > 0  ){
				return true;
			}
		}

		return false;
	}

	/*letzte Sitzung -> heute*/
	public function firstInstanceInQuartal($patientcode) {
		$instance = $this->getLastInstance($patientcode);
		if ($instance <= 2) { return false; }
		$pq = $this->getQuartal($patientcode, $instance);	
		$year1 = intval($pq[0]); $quartal1 = $pq[1];
		
		$year = intval(date("Y")); $quartal = $this->quartal(intval(date("m")));
		return !($year == $year1 && $quartal == $quartal1);
	}

	function getLastInstance($patientcode) {
		$this->db->db_select();
		$sql = "SELECT MAX(CONVERT(sb_start_02, UNSIGNED INTEGER)) AS instance FROM `sb_start` WHERE sb_start_01=? LIMIT 1";
		$query = $this->db->query($sql, array($patientcode));
		$res = $query->result();
		return intval($res[0]->instance);
	}

	private function quartal($month) {
		if($month <= 3) {
			return 1;
		} else if ($month <= 6) {
			return 2;
		} else if ($month <= 9) {
			return 3;
		} else if ($month <= 12) {
			return 4;
		}
		return 0;
	}

	public function getQuartal($patientcode, $instance) {
		// richtet sich nach der letzten Sitzung in 5 tstb
		$this->db->db_select();
		$sql = "SELECT DATE_FORMAT(sb_start_date, '%Y') AS year, DATE_FORMAT(sb_start_date, '%m') AS month FROM `sb_start` WHERE sb_start_01=? AND sb_start_02=? LIMIT 1";
		$query = $this->db->query($sql, array($patientcode, $instance));
		
		$res = $query->result();
		$month = $res[0]->month;
		$quartal = $this->quartal($month);
		return array($res[0]->year, $quartal);
	}

	public function endTherapy($instance, $patientcode) {
		#Zur Kompatibilität mit altem Fragebogen; Enthält nur Nullwerte
		#Löschen wenn nicht mehr benötigt, bzw. Fragebogen nicht mehr in Portal-DB enthalten.
		$therapist = NULL;
		$this->db->db_select();		
		$sql = "SELECT MAX(ETS011) AS ABSCHLUSS FROM `einzelfragen_therapeut_sitzungsbogen` WHERE CODE=? LIMIT 1";
		$query = $this->db -> query($sql, array($patientcode));
		
		if($query->num_rows() === 1) {
			$therapist = $query->result();
		}

		$this->db->db_select();		
		$sql = "SELECT MAX(ETN011) AS ABSCHLUSS FROM `einzelfragen_therapeut_sitzungsbogen_neu` WHERE CODE=? LIMIT 1";
		$query = $this->db -> query($sql, array($patientcode));
		
		if($query->num_rows() === 1) {
			$therapist = $query->result();
		}

		return $therapist;
	}//endTherapy()

	public function getSuicideColour($instance, $patientcode) {
		$this->db->db_select();
		$sql = "SELECT CONVERT(HSC010, UNSIGNED INTEGER) AS SUICIDENUMBER FROM `HSCL-11` WHERE CODE=? AND INSTANCE=? LIMIT 1";
		$query = $this->db->query($sql, array($patientcode, $instance));
		
		if($query->num_rows() == 1) {
			$suicidenumber = $query->result();
		}
		//suicidecolour = ($suicidenumber[0]->SUICIDENUMBER) <= 2 ? "green" : "red"; // sp�ter aus xml-Datei holen?
        
        $suicidecolour = ($suicidenumber[0]->SUICIDENUMBER);
        
        if (($suicidenumber[0]->SUICIDENUMBER) == 1) {
            $suicidecolour = "green";
        } elseif (($suicidenumber[0]->SUICIDENUMBER) == 2) {
            $suicidecolour = "DarkOrange";
        } elseif (($suicidenumber[0]->SUICIDENUMBER) == 3) {
            $suicidecolour = "red";
        }elseif (($suicidenumber[0]->SUICIDENUMBER) == 4) {
            $suicidecolour = "darkred";
        }
        
		return $suicidecolour;
	}//getSuicideColour()

	public function is_subject($patientcode) {
		$this->db->db_select();
		$this->db->select('1');
		$this->db->from('subjects');
		$this->db->where('CODE', $patientcode);
		$this->db->limit( 1 );
		
		return $this->db->get()->num_rows() === 1;
	}//is_subject()

	public function get_columns($table, $patientcode, $instance, $columns)
	{
		$this-> db -> db_select();
		
		$sql = "SELECT " . implode(', ', array_values($columns)) . " FROM `" . $table . "` WHERE CODE=? AND INSTANCE=? LIMIT 1";
		// da INSTANCE als varchar abgespeichert ist -> CONVERT(INSTANCE, UNSIGNED INTEGER)
		if(is_numeric($instance) AND $instance < 10) {
			$instance = '0'.intval($instance);
		}

		$query = $this-> db ->query($sql, array($patientcode, $instance));
		
		return $query -> result();
	}//get_columns()

	public function get_therapist($patientcode)
	{
		$therapist = null;

		$this->db->db_select();
		$this->db->select('THERAPIST');
		$this->db->from('subjects');
		$this->db->where('CODE', $patientcode);

		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$therapist = $query->result();
		}
		return $threapist;
	}//get_therapist()
}//SB_Model
?>