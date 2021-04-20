<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

class Questionnaire_tool_model extends CI_Model
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
		$this -> load -> Model( 'SB_model' );
    }

	public function get_sb_batterie($patientcode, $get_Z = false){
		$this->db->db_select();
		$this -> db -> from( 'questionnaire_batterie_patient bp' );
		$this -> db -> join( 'questionnaire_batterie b', 'bp.bid = b.id'  );
		$this -> db -> join( 'questionnaire_batterie_hat bh', 'bp.bid = bh.bid'  );
		$this -> db -> join( 'questionnaire_list ql', 'bh.qid = ql.id'  );
		$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
		
		if($get_Z){
			$this -> db -> where( 'bh.is_Z', 1 );
		} else {
			$this -> db -> where( 'bh.is_Z', 0 );
		}
		
		$this -> db -> where( 'bp.patientcode', $patientcode );
		$this -> db -> order_by( 'bh.section', 'ASC' );
		$this -> db -> order_by( 'bh.section_order', 'ASC' );

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0  ){
			$result = $query->result();
			$result = $this->group_questionnaires($result);
			return $result;
		} else {								
			$this -> db -> from( 'questionnaire_batterie b' );
			$this -> db -> join( 'questionnaire_batterie_hat bh', 'b.id = bh.bid'  );
			$this -> db -> join( 'questionnaire_list ql', 'bh.qid = ql.id'  );
			$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
			
			if($get_Z){
				$this -> db -> where( 'bh.is_Z', 1 );
			} else {
				$this -> db -> where( 'bh.is_Z', 0 );
			}
			
			$this -> db -> where( 'b.is_standard', 1 );
			$this -> db -> order_by( 'bh.section', 'ASC' );
			$this -> db -> order_by( 'bh.section_order', 'ASC' );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) > 0  ){
				$result = $query->result();
				$result = $this->group_questionnaires($result);
				return $result;
			}		
		}

		return NULL;
    }//get_sb_batterie()
    
    private function group_questionnaires($quests){
		$final = array();
		$last = null;
		foreach($quests as $r){
			if(!empty($final) AND $final[count($final)-1]->tablename == $r->tablename AND $final[count($final)-1]->instance == $r->instance){
				$lang = array();
				$headers = array();
				$descriptions = array();
				foreach((array)$final[count($final)-1]->language as $l){
					$lang[] = $l;
				}
				$lang[] = $r->language;

				foreach((array)$final[count($final)-1]->header_name as $h){
					$headers[] = $h;
				}
				$headers[] = $r->header_name;

				foreach((array)$final[count($final)-1]->description as $d){
					$descriptions[] = $d;
				}
				$descriptions[] = $r->description;

				$final[count($final)-1]->language = $lang;
				$final[count($final)-1]->header_name = $headers;
				$final[count($final)-1]->description = $descriptions;
			} else {
				$final[] = $r;
			}
		}
		return $final;
    }//group_questionnaires()
    
    public function inactive_patients($therapist){
		$this -> db -> from( 'questionnaire_released qr' );
		$this -> db -> join( 'questionnaire_list ql', 'qr.qid = ql.id'  );
		$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
		$this -> db -> where( 'therapist', $therapist );
		$this -> db -> where( 'finished', 0 );

		$query = $this -> db -> get( );
		if( $query -> num_rows( ) > 0  ){
			$result = $query->result();
			$result = $this->group_questionnaires($result);
			return $result;
		}
	}//inactive_patients()

    //Zusammengefügt aus den alten "get_released_questionnaires_of_therapist" und "get_released_questionnaires" Funktionen
    public function get_released_questionnaires( $user )
	{
		$questionnaire = NULL; 
		
		$this -> db -> select( 'qr.id, qr.therapist, qr.patientcode, qr.qid, qr.datum, qr.finished, qr.instance, qr.activation, ql.tablename, ql.filename, qln.language, qln.header_name, qln.description' );
		$this -> db -> from( 'questionnaire_released qr' );
		$this -> db -> join( 'questionnaire_list ql', 'qr.qid = ql.id'  );
		$this -> db -> join( 'questionnaire_list_names qln', 'qr.qid = qln.qid'  );
		if(preg_match('/\d{4}\D\d{2}/',$user))
		{
			$this -> db -> where( 'patientcode', $user );
		}
		elseif(preg_match('/\D{2}\d{2}/',$user)) 
		{
			$this -> db -> where( 'qr.therapist', $user );
		}
		$this -> db -> order_by( 'qln.header_name', 'ASC' );
		$this -> db -> order_by( 'datum', 'DESC' );
		$this -> db -> order_by( 'finished', 'ASC' );
		$this -> db -> group_by( 'tablename, instance');
		

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0  )
		{
			$questionnaire = $query -> result( );
			$questionnaire = $this->group_questionnaires($questionnaire);
		}
		
		return $questionnaire;	
	}//get_released_questionnaires()

	public function is_questionnaire_available($patientcode)
	{
		$this -> db -> select( '1' );
		$this -> db -> from( 'questionnaire_released' );
		$this -> db -> where( 'patientcode', $patientcode );
		$this -> db -> where( 'finished', 0 );
		$this -> db -> where( 'activation <=', date('Y-m-d',time()));
		$this -> db -> limit( 1 );

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) === 1 ) {
			return true;
		}
		
		return false;
	}//is_questionnaire_available()

	public function get_last_login($patientcode)
	{
		$activity = $this->get_ex_activity($patientcode);
		if(!is_null($activity)){
			return $activity->last_login;
		}

		return NULL;
	}//get_last_login()

	public function get_ex_activity($patientcode)
	{
		$this->db->from('ex_activity');
		$this->db->where('patientcode',$patientcode);
		$this -> db -> limit( 1 );

		$query = $this -> db -> get( );
		
		if($query->num_rows === 1) {
			return $query->result()[0];
		} 
		
		return NULL;
	}//get_ex_activity()

	public function get_questionnaire_id_by_table( $table ) {
		$this -> db -> from('questionnaire_list ql');
		$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
		$this -> db -> where('ql.tablename',$table);
		
		$query = $this -> db -> get( );
		
		if( $query -> num_rows() > 0) {
			$result = $query->result( );
			$result = $this->group_questionnaires( $result );
			return $result[0] -> qid;
		}
		
		return null;
	}//get_questionnaire_id_by_table()

	public function get_single_released_questionnaire($patientcode, $id, $instance = null, $finished = null)
	{
		$result = NULL;
		
		$this -> db -> from( 'questionnaire_released' );
		$this -> db -> where( 'patientcode', $patientcode );
		$this -> db -> where( 'qid', $id );
		
		if(isset($instance)){
			$this -> db -> where( 'instance', $instance );
		}
		
		if(isset($finished)){
			$this -> db -> where( 'finished', $finished );
		}

		$this -> db -> limit( 1 );

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) === 1 ){
			$result = $query->result()[0];
		}
		
		return $result;
	}//get_single_released_questionnaire()

	public function get_released_not_finished_questionnaires( $username )
	{
		$questionnaires = NULL; 
		
		$this -> db -> select( 'qr.id, qr.therapist, qr.patientcode, qr.qid, qr.datum, qr.finished, qr.instance, qr.activation, ql.tablename, ql.filename, qln.language, qln.header_name, qln.description' );
		$this -> db -> from( 'questionnaire_released qr' );
		$this -> db -> join( 'questionnaire_list ql', 'qr.qid = ql.id'  );
		$this -> db -> join( 'questionnaire_list_names qln', 'qr.qid = qln.qid'  );
		$this -> db -> where( 'patientcode', $username );
		$this -> db -> where( 'finished', 0 );
		$this -> db -> order_by( 'qln.header_name', 'ASC' );
		$this -> db -> order_by( 'datum', 'DESC' );
		
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0  ){
			$questionnaires = $query -> result( );
			$questionnaires = $this->group_questionnaires($questionnaires);
		}
		
		return $questionnaires;	
	}//get_released_not_finished_questionnaires()

	public function get_all_questionnaire( )
	{
		$all_questionnaire = NULL;

        $this -> db -> select( 'ql.id, ql.tablename, ql.filename, qln.language ,qln.header_name, qln.description' );
		$this -> db -> from( 'questionnaire_list ql' );
        $this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
        $this -> db ->distinct();
        $this -> db -> order_by( 'ql.id' );

        $query = $this -> db -> get( );

        if( $query )
        {
			$all_questionnaire = $query -> result( );
			$all_questionnaire = $this->group_questionnaires($all_questionnaire);
        }//if
		
		return $all_questionnaire;
	}//get_all_questionnaire()
	
	public function get_all_questionnaire_by_battery($id, $is_Z = FALSE)
	{
		$questionnaires = NULL; 
		
		$this -> db -> select('qbh.id as id, qbh.bid as bid, qbh.section as section, qbh.section_order as section_order, qbh.is_Z as is_Z, ql.id as qid, ql.tablename as tablename, ql.filename as filename, qln.language, qln.header_name, qln.description');
		$this -> db -> from( 'questionnaire_batterie_hat qbh' );
		$this -> db -> join( 'questionnaire_list ql', 'ql.id = qbh.qid'  );
		$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );			
		$this -> db -> where( 'qbh.bid', $id );
		if($is_Z){
			$this -> db -> where( 'qbh.is_Z', 1 );
		}
		$this -> db -> order_by( 'qbh.section', 'ASC' );
		$this -> db -> order_by( 'qbh.section_order', 'ASC' );
		
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0  ){
			$questionnaires = $query -> result( );
			$questionnaires = $this->group_questionnaires($questionnaires);
		}
		
		return $questionnaires;	
	}//get_all_questionnaire_by_battery()

	public function get_all_batteries( )
	{
		$all_batteries = NULL;

		$this -> db -> from( 'questionnaire_batterie' );
		$this -> db -> order_by( 'id' );

        $query = $this -> db -> get( );

        if( $query )
        {
            $all_batteries = $query -> result( );
        }//if
		
		return $all_batteries;
	}//get_all_batteries()
	
	public function get_next_instance_of_questionnaire ($questionnaireID, $patientcode, $therapist, $instance){

		$result = $instance != 'Z' ? '01' : '05';

		$highestNumber = 0;
		$where = "instance REGEXP '^".$instance."[0-9]+$'";

		$this -> db -> from( 'questionnaire_released' );
		$this -> db -> where( 'patientcode', $patientcode );
		$this -> db -> where( 'qid', $questionnaireID );
		$this -> db -> where($where);
		$this -> db -> group_start()
							->where( 'therapist', $therapist)
							->or_where( 'therapist', 'admin' )
						->group_end();
		$this -> db -> order_by( 'instance', 'ASC' );

		$query = $this -> db -> get( );
		
		if( $query -> num_rows( ) > 0  ){
			$data = $query -> result( );

			$next = substr($data[sizeof($data)-1] -> instance,0,-2);
			$nextNum = intval(substr($data[sizeof($data)-1] -> instance,-2,2));
			$highestNumber = $nextNum;

			if(strcasecmp($instance,'Z') === 0){				
				$nextNum += 5;
			} else {
				$nextNum++;
			}
			
			if($nextNum < 10){
				$result = '0' . $nextNum;
			} else {
				$result = $nextNum;
			}
		} 

		return $result;
	}//get_next_instance_of_questionnaire()

	public function get_next_sb_instance($questionnaireID, $patientcode, $therapist){
		$this -> db -> from( 'questionnaire_released' );
		$this -> db -> where( 'patientcode', $patientcode );
		$this -> db -> where( 'therapist', $therapist );
		$this -> db -> where( 'qid', $questionnaireID );
		$where = "instance REGEXP '^[0-9]*$'";
		$this -> db -> where($where);
		$this -> db -> limit( 1 );
		$query = $this -> db -> get( );
		
		$highestNumber = 1;
		
		if($query->num_rows() === 1) {
			$tmpres = $query->result()[0];
			$highestNumber = intval($tmpres->instance) + 1;
		}

		return $highestNumber;
	}//get_next_sb_instance()
	
	public function instance_exists($questionnaireID, $patientcode, $therapist, $instance){
		$this -> db -> from( 'questionnaire_released' );
		$this -> db -> where( 'patientcode', $patientcode );
		$this -> db -> where( 'therapist', $therapist );
		$this -> db -> where( 'qid', $questionnaireID );
		$this -> db -> like('instance',$instance);
		$this -> db -> limit( 1 );

		$query = $this -> db -> get( );

		if($query->num_rows() === 1) {
			return $query -> result()[0]->id;
		}
	}//instance_exists()

	public function get_all_questionnaire_by_batterie( $id, $is_Z = FALSE )
	{
		$questionnaires = NULL; 
		
		$this -> db -> select('qbh.id as id, qbh.bid as bid, qbh.section as section, qbh.section_order as section_order, qbh.is_Z as is_Z, ql.id as qid, ql.tablename as tablename, ql.filename as filename, qln.language, qln.header_name, qln.description');
		$this -> db -> from( 'questionnaire_batterie_hat qbh' );
		$this -> db -> join( 'questionnaire_list ql', 'ql.id = qbh.qid'  );
		$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );			
		$this -> db -> where( 'qbh.bid', $id );
		
		if($is_Z){
			$this -> db -> where( 'qbh.is_Z', 1 );
		}
		
		$this -> db -> order_by( 'qbh.section', 'ASC' );
		$this -> db -> order_by( 'qbh.section_order', 'ASC' );
		
		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0  ) {
			$questionnaires = $query -> result( );
			$questionnaires = $this->group_questionnaires($questionnaires);
		}
		
		return $questionnaires;	
	}//get_all_questionnaire_by_batterie()

	public function insert_questionnaire( $therapist, $patientcode, $questionnaire, $instance = 'OT', $activation = null, $days_interval = null )
	{
		if( !isset( $activation ) ) {
			$activation = date("Y-m-d H:i:s", time());
		}
		
		if( !isset( $days_interval ) ) {
			$days_interval = 0;
		}
		
		$today = date("Y-m-d", time());
		$data = array(
			'therapist' => $therapist ,
			'patientcode' => $patientcode ,
			'qid' => $questionnaire,
			'datum' => $today,
			'finished' => 0,
			'instance' => $instance,
			'activation' => $activation,
			'daysInterval' => $days_interval
			);
		
		$query = $this-> db ->insert('questionnaire_released', $data);

		return $this-> db ->insert_id();
	}//insert_questionnaire()

	public function get_questionnaire_by_file( $file )
	{
		$questionnaire = NULL; 
		
		$this -> db -> from( 'questionnaire_list ql' );
		$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
		$this -> db -> where( 'ql.filename', $file );

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0 ) {
			$questionnaire = $query -> result( );	
			$questionnaire = $this->group_questionnaires($questionnaire)[0];
		}
		
		return $questionnaire;	
	}//get_questionnaire_by_file()

	public function update_questionnaire ($questionnaire)
	{
		$data = array(
			'finished' => 1
		);
			
		$this->db->where('id', $questionnaire);
		$this->db->update('questionnaire_released', $data); 
	}//update_questionnaire()

	public function get_questionnaire( $id )
	{
		$questionnaire = NULL; 
		
		$this -> db -> from( 'questionnaire_list ql' );
		$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
		$this -> db -> where( 'ql.id', $id );

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) > 0 ){
			$questionnaire = $query -> result( );
			$questionnaire = $this->group_questionnaires($questionnaire);
		}

		return $questionnaire;
	}//get_questionnaire()

	public function get_entry( $id, $columns = NULL )
	{
		$entry = NULL; 
		
		if( !is_null( $columns ) ) {
			$this -> db -> select( $columns );
		}

		$this -> db -> from( 'questionnaire_released' );
		$this -> db -> where( 'id', $id );
		$this -> db -> limit( 1 );

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) === 1 ) {
			$entry = $query -> result( )[0];
		}
		
		return $entry;	
	}//get_entry()

	public function insert_row_DANGEROUS($data, $table) {
		/*
			This method can be dangerous because it allows to insert any data into any table of the database.
			It's important to sanitize $data and to check if $table is an expected tablename BEFORE this method gets called from e.g. a controller.
			Otherwise an attacker is able to choose the "user"-table and insert a new admin user (the table structure for the insert is public too when this software got open-sourced).
			At the time of writing, the only use of this method is from controllers/patient/questionnaire.php/send_questionnaire() and it should probably stay that way.
			Follow the usual procedure of inserting data for all other insert queries whenever possible.
		*/

		if( isset( $data['CODE'] ) ) {
			$data['CODE'] = strtoupper($data['CODE']);
			
			$this->db->db_select();
			
			/*
			//Sicherheit? Test, ob alles in Ordnung ist?
			$sql = 'INSERT INTO `' . $table . '` (' .
			implode(", ", array_keys($data)) . ") VALUES ('" .
			implode("', '", array_values($data)) . "')";
			
			$this->db->query($sql);
			*/
			//There are issues with the security and maintainability of the above custom SQL query,
			//so it got replaced with the following using the CI query builder:
			$this-> db -> insert($table, $data);
			
			if($table == 'HSCL-11')
			{
				$aktPosition = getcwd();
				$path = APPPATH.'third_party\\r_skripte';
				var_dump($path);
				chdir($path);

				//SOLL DIENST ZUR AKTUALISIERUNG DES HSCL_GRAPHEN ersetzten, (wurde bisher per Eintrag in feedback_log getriggert)
				exec("Rscript entscheidungsregeln_mit200Faellen.R ".$data['CODE']." ".$data['INSTANCE']." &> /dev/null &");
			}
		}
	}//insert_row_DANGEROUS()

	public function get_remaining_questionnaires($patientcode){
		$questionnaires = NULL; 
		
		$this -> db -> select( 'qr.id, qr.therapist, qr.patientcode, qr.qid, qr.datum, qr.finished, qr.instance, qr.activation, ql.tablename, ql.filename, qln.language, qln.header_name, qln.description' );
		$this -> db -> from( 'questionnaire_released qr' );
		$this -> db -> join( 'questionnaire_list ql', 'qr.qid = ql.id'  );
		$this -> db -> join( 'questionnaire_list_names qln', 'qr.qid = qln.qid'  );
		$this -> db -> where( 'patientcode', $patientcode );
		$this -> db -> where( 'finished', 0 );
		$this -> db -> where( "activation <= '".date('Y-m-d')."'" );
		$this -> db -> order_by( 'qln.header_name', 'ASC' );
		$this -> db -> order_by( 'datum', 'DESC' );
		
		$query = $this -> db -> get( );
		$sql = $this->db->last_query();
		if( $query -> num_rows( ) > 0  ){
			$questionnaires = $query -> result( );
			$questionnaires = $this->group_questionnaires($questionnaires);
		}
		
		return $questionnaires;	
	}//get_remaining_questionnaires

	public function has_zwischen($patientcode, $z_instance)
	{
		$this -> db -> from( 'questionnaire_released' );
		$this -> db -> where( 'patientcode', $patientcode );
		$this -> db -> where( 'instance', $z_instance );
		$this -> db -> limit( 1 );

		$query = $this -> db -> get( );

		if( $query -> num_rows( ) === 1 ) {
			return TRUE;
		}

		return FALSE;
	}//has_zwischen()

	public function get_feedback_of_batterie($bid) {
		//$sql = "SELECT * FROM `questionnaire_batterie_feedback` WHERE bid = 2 ORDER BY feedback_order ASC";
		
		$this -> db -> from('questionnaire_batterie_feedback');
		$this -> db -> where('bid', $bid);
		$this -> db -> order_by( 'feedback_order', 'ASC' );
		
		$query = $this -> db -> get();
		
		if($query->num_rows() > 0) {
			return $query->result();
		}

		return null;
	}//get_feedback_of_batterie()

	public function get_review_by_data($bid,$data) {
		$this -> db -> from('questionnaire_batterie_feedback');
		$this -> db -> where('bid',$bid);
		$this -> db -> where('type','review');
		$this -> db -> where('data',$data);
		$this -> db -> order_by( 'feedback_order', 'ASC' );

		$query = $this -> db -> get( );
		
		if($query->num_rows() > 0) {
			return $query->result()[0];
		}

		return null;
	}//get_review_by_data()

	public function get_questionnaire_list_tablenames( )
	{
		$this -> db -> distinct();
		$this -> db -> select('tablename');
		$this -> db -> from('questionnaire_list');
		
		$query = $this-> db -> get();
		
		return $query->result_array();
	}//get_questionnaire_list_tablenames()

	/**
	 * Ersetzt: publ. func. get_questionnaire_DB($filename) 
	 */
	private function get_questionnaire_list_data($filename, $columns = NULL)
	{
		if( isset($columns) ) {
			$this -> db -> select( $columns );
		}

		$this -> db -> from ('questionnaire_list');
		$this -> db -> where ('filename', $filename);
		$this -> db -> limit( 1 );

		$query = $this-> db -> get();
		
		if ($query->num_rows() === 1)
		{
			return $query->result()[0];
		}

		return null;
	}//get_questionnaire_list_data()

	public function add_or_update_questionnaire_DB($tablename, $filename, $language)
	{
		$qid = null;

		$get = $this-> get_questionnaire_list_data($filename, 'id');
		$id = $get->id;

		if (!isset($id)) //Bei Suche nach nicht XML-Dateien
		{
			$get = $this-> get_questionnaire_list_data(str_replace('.xml','',$filename), 'id');
			$id = $get->id;
		}
		
		//Daten für questionnaire_list zusammenstellen
		$questionnaire_list_data = array(
			'tablename' => $tablename,
			'filename' => $filename
		);

		if(isset($id)) //Questionnaire bereits in DB vorhanden --> UPDATE
		{
			$this-> db -> where('id', $id);
			$this-> db -> update('questionnaire_list', $questionnaire_list_data);
			$qid = $id;
		}
		else // Questionnaire noch nicht in DB vorhanden --> INSERT
		{
			$this -> db -> insert('questionnaire_list', $questionnaire_list_data);
			$qid = $this-> db -> insert_id();
		}

		// Daten für questionnaire_list_names zusammenstellen und jew. Eintrag updaten/einfügen
		foreach($language as $key => $lang)
		{
			$this -> db -> select('1');
			$this -> db -> from ('questionnaire_list_names');
			$this -> db -> where ('qid', $qid);
			$this -> db -> where ('language', $key);
			$this -> db -> limit(1);
			$query = $this -> db -> get();

			$questionnaire_list_names_data = array(
				'qid' => $qid,
				'language' => $key,
				'header_name' => $lang['header_name'],
				'description' => $lang['description']
			);

			if($query->num_rows() === 1)
			{
				$this-> db -> where('qid', $qid);
				$this-> db -> where('language', $key);
				$this-> db -> update('questionnaire_list_names', $questionnaire_list_names_data);
			}
			else
			{
				$this-> db -> insert('questionnaire_list_names', $questionnaire_list_names_data);
			}
		}//foreach

		return $qid;
	}//add_or_update_questionnaire_DB()

	public function add_or_update_process_scales($scales, $item_invert, $info){

		foreach($scales as $name => $tables){
			foreach($tables as $table_name => $items){
				$this->db->select('1');
				$this->db->from('questionnaire_process_scales');
				$this->db->where('name',$name);
				$this->db->where('table_name',$table_name);
				$this->db->limit(1);
				$query = $this->db->get();

				$data = array(
					'name' => $name,
					'table_name' => $table_name,
					'items' => json_encode($items),
					'item_invert' => json_encode($item_invert[$name]),
					'title' => $info[$table_name][$name]['title'],
					'min' => $info[$table_name][$name]['min'],
					'max' => $info[$table_name][$name]['max'],
				);

				if($query->num_rows() === 1){
					$this->db->where('name',$name);
					$this->db->where('table_name',$table_name);
					$this->db->update('questionnaire_process_scales', $data);
				} else {
					$this->db->insert('questionnaire_process_scales', $data);
				}
			}
		}
	}//add_or_update_process_scales()

	public function add_or_update_status_scales($scales, $item_invert, $info){

		foreach($scales as $name => $tables){
			foreach($tables as $table_name => $items){
				$this->db->select('1');
				$this->db->from('questionnaire_status_scales');
				$this->db->where('name',$name);
				$this->db->where('table_name',$table_name);
				$this->db->limit(1);
				$query = $this->db->get();

				$data = array(
					'name' => $name,
					'table_name' => $table_name,
					'items' => json_encode($items),
					'item_invert' => json_encode($item_invert[$name]),
					'title' => $info[$table_name][$name]['title'],
					'min' => $info[$table_name][$name]['min'],
					'max' => $info[$table_name][$name]['max'],
					'mean' => $info[$table_name][$name]['mean'],
					'sd' => $info[$table_name][$name]['sd'],
					'low' => $info[$table_name][$name]['low'],
					'mid' => $info[$table_name][$name]['mid'],
					'high' => $info[$table_name][$name]['high'],
					'description' => $info[$table_name][$name]['description']
				);

				if($query->num_rows() === 1){
					$this->db->where('name',$name);
					$this->db->where('table_name',$table_name);
					$this->db->update('questionnaire_status_scales', $data);
				} else {
					$this->db->insert('questionnaire_status_scales', $data);
				}
			}
		}
	}//add_or_update_status_scales()

	public function add_or_update_item_infos($item_infos){
		foreach($item_infos as $table => $entry){
			foreach($entry as $lang => $infos){
				$this->db->select('1');
				$this->db->from('questionnaire_item_infos');
				$this->db->where('table_name', $table);
				$this->db->where('language', $lang);
				$this->db->limit(1);
				$query = $this->db->get();

				$data = array(
					'table_name' => $table,
					'item_names' => json_encode($infos['item_names']),
					'item_texts' => json_encode($infos['item_texts']),
					'language' => $lang
				);

				if($query->num_rows() === 1){
					$this->db->where('table_name', $table);
					$this->db->where('language', $lang);
					$this->db->update('questionnaire_item_infos', $data);
				} else {
					$this->db->insert('questionnaire_item_infos', $data);
				}
			}
		}
	}//add_or_update_item_infos()

	public function remove_questionnaire_DB($id){
		$this->db->where('id',$id);
		$this->db->delete('questionnaire_list');
	}//remove_questionnaire_DB()

	public function does_questionnaire_exist( $name ){
		$exists = false;

		$this -> db -> select('1');
		$this -> db -> from('questionnaire_list');
		$this -> db -> where('tablename', $name);
		$this -> db -> limit( 1 );

		$query = $this -> db -> get();

		if ($query -> num_rows() === 1)
		{
			$exists = true;
		}

		return $exists;
	}//does_questionnaire_exist()

	public function get_section_names($id)
	{
		$this-> db -> select('section_names');
		$this-> db -> from('questionnaire_batterie');
		$this-> db -> where('id', $id);
		$this-> db -> limit(1);

		$query = $this-> db -> get();

		if($query->num_rows() === 1)
		{
			return $query->result()[0]->section_names;
		}
	}//get_section_names()

	public function insert_new_batterie_to_DB($name)
	{
		$data = array('name' => $name);
		$this-> db -> insert('questionnaire_batterie', $data);
	}//insert_new_batterie()

	public function delete_batterie_from_DB($bid)
	{
		$this-> db -> where('id', $bid);
		$this-> db -> delete('questionnaire_batterie');

		$this-> db -> where('bid', $bid);
		$this-> db -> delete('questionnaire_batterie_hat');
	}//delete_batterie_from_DB()

	public function add_section_to_batterie($bid)
	{
		$this-> db -> select('sections');
		$this-> db -> from('questionnaire_batterie');
		$this-> db -> where('id', $bid);
		$this-> db -> limit(1);

		$query = $this-> db -> get();
		if($query->num_rows() === 1)
		{
			$data = array('sections' => $query->result()[0]->sections + 1);

			$this-> db -> where('id', $bid);
			$this-> db -> update('questionnaire_batterie', $data);

			return TRUE;
		}
		
		return FALSE;
	}//add_section_to_batterie()

	public function delete_section_from_batterie($bid)
	{
		$this-> db -> select('sections');
		$this-> db -> from('questionnaire_batterie');
		$this-> db ->where('id', $bid);
		$this-> db -> limit(1);

		$query = $this-> db -> get();
		if($query->num_rows() === 1)
		{
			$current_sections = $query->result()[0]->sections;

			if($current_sections > 1)
			{
				//Update entries of batterie in qbh if any exist
				$this-> db -> from('questionnaire_batterie_hat');
				$this-> db -> where('bid', $bid);
				$this-> db -> where('section', $current_sections - 1);

				$query = $this-> db -> get();
				if($query->num_rows() > 0)
				{
					$result = $query->result();
					foreach($result as $questionnaire)
					{
						$data = array('section' => $questionnaire->section - 1);

						$this-> db ->where('id', $questionnaire->id);
						$this-> db ->update('questionnaire_batterie_hat', $data);
					}//foreach
				}//if

				//Update entry for battery in qb
				$data = array('sections' => $current_sections - 1);
				$this-> db -> where ('id', $bid);
				$this-> db -> update('questionnaire_batterie', $data);

				return TRUE;
			}//if($current_sections > 1)
		}
		
		return FALSE;
	}//delete_section_from_batterie()

	public function get_battery($bid)
	{
		$result = NULL;

		$this-> db -> from('questionnaire_batterie');
		$this-> db -> where('id', $bid);
		$this-> db -> limit(1);

		$query = $this-> db -> get();

		if($query->num_rows() === 1)
		{
			$result = $query->result()[0];
		}

		return $result;
	}//get_battery()

	public function set_section_names($bid, $section_names)
	{
		$data = array('section_names' => $section_names);
		$this-> db -> where('id', $bid);
		$this-> db -> update('questionnaire_batterie', $data);
	}//set_section_names

	public function save_order_of_item($id, $section, $order)
	{
		$data = array(
			'section' => $section,
			'section_order' => $order
		);

		$this-> db -> where('id', $id);
		$this-> db -> update('questionnaire_batterie_hat', $data);
	}//save_order_of_item()

	public function save_order_of_feedback_item($id, $order)
	{
		$data = array('feedback_order' => $order);

		$this-> db -> where('id', $id);
		$this-> db -> update('questionnaire_batterie_feedback', $data);
	}//save_order_of_feedback_item()

	public function set_quest_type($id, $is_Z)
	{
		$data = array('is_Z' => $is_Z);
		$this-> db -> where('id', $id);
		$this-> db -> update('questionnaire_batterie_hat', $data);
	}//set_quest_type()

	public function insert_questionnaire_in_battery($bid, $qid)
	{
		$data = array(
			'bid' => $bid,
			'qid' => $qid
		);

		$this-> db -> select_max('section_order');
		$this-> db -> from('questionnaire_batterie_hat');
		$this-> db -> where('bid', $bid);
		$this-> db -> where('section', 0);
		$this-> db -> limit(1);

		$query = $this-> db -> get();
		
		if($query -> num_rows() === 1)
		{
			$result = $query->result();
			
			if(isset($result[0]->section_order)) {
				$data['section_order'] = $result[0]->section_order+1;
			}
		}
		
		$this-> db -> insert('questionnaire_batterie_hat', $data);
	}//insert_questionnaire_in_batterie()

	private function reorder_battery($hid)
	{
		$this-> db -> select('section, section_order');
		$this-> db -> from('questionnaire_batterie_hat');
		$this-> db -> where('id', $hid);
		$this-> db -> limit(1);

		$query = $this-> db -> get();

		if($query-> num_rows() === 1)
		{
			$section = $query->result()[0]->section;
			$order = $query->result()[0]->section_order;

			$this-> db -> from('questionnaire_batterie_hat');
			$this-> db -> where('section_order >', $order);
			$query = $this-> db -> get();
			
			if($query -> num_rows() > 0)
			{
				$result = $query->result();

				foreach($result as $key => $item)
				{
					$data = array('section_order' => $item->section_order-1);
					$this-> db -> where('id', $item->id);
					$this-> db -> update('questionnaire_batterie_hat', $data);
				}//foreach
			}//if
		}//if
	}//reorder_battery()

	public function delete_questionnaire_in_battery($bid, $hid)
	{
		$this-> reorder_battery($hid);
		$this-> db -> where('bid', $bid);
		$this-> db -> where('id', $hid);
		$this-> db -> delete('questionnaire_batterie_hat');
	}//delete_questionnaire_in_batterie()

	public function set_gas($bid, $gas_section)
	{
		$data = array('gas_section' => $gas_section);
		
		$this-> db -> where('id', $bid);
		$this-> db -> update('questionnaire_batterie', $data);
	}//set_gas()

	public function add_feedback_item($bid, $type, $data)
	{
		$order = 0;
		$this-> db -> select_max('feedback_order');
		$this-> db -> from('questionnaire_batterie_feedback');
		$this-> db -> where('bid', $bid);

		$query = $this -> db -> get( );
		
		if($query->num_rows() > 0){
			$result = $query->result();
			if(isset($result[0]->feedback_order)) {
				$order = $result[0]->feedback_order+1;
			}
		}

		$insert = array(
			'bid' => $bid,
			'type' => $type,
			'data' => $data,
			'feedback_order' => $order
		);

		$this-> db ->where('bid', $bid);
		$this-> db ->insert('questionnaire_batterie_feedback', $insert);
	}//add_feedback_item()

	public function delete_feedback_item($id, $bid)
	{
		$this-> reorder_feedback($id, $bid);
		$this-> db -> where('id', $id);
		$this-> db -> delete('questionnaire_batterie_feedback');
	}//delete_feedback_item()

	private function reorder_feedback($id, $bid)
	{
		$this-> db -> select('feedback_order');
		$this-> db -> from('questionnaire_batterie_feedback');
		$this-> db -> where('id', $id);
		$this-> db -> limit(1);

		$query = $this-> db -> get();
		if($query-> num_rows() === 1) // True: Item mit $id in Tabelle existiert 
		{
			$result = $query->result();

			$this-> db -> select('feedback_order');
			$this-> db -> from('questionnaire_batterie_feedback');
			$this-> db -> where('bid', $bid);
			$this-> db -> where('feedback_order <', $result[0]->feedback_order);

			$query = $this-> db -> get();
			if($query-> num_rows() > 0) // True: in Batterie $bid existieren Items mit höherer Feedback_Order als Item mit $id
			{
				$result2 = $query->result();
				foreach($result2 as $r) //Setze alle größeren Feedback_orders um eins herunter
				{
					$data = array('feedback_order' => $r->feedback_order-1);

					$this-> db -> where ('id', $id);
					$this-> db -> update('questionnaire_batterie_feedback', $data);
				}//foreach
			}//if
		}//if
	}//reorder_feedback()

	public function set_standard_battery($bid)
	{
		//bisherige Standardbatterie zurücksetzen
		$this-> db -> select('id');
		$this-> db -> from('questionnaire_batterie');
		$this-> db -> where('is_standard', 1);
		$this-> db -> limit(1);

		$query = $this-> db -> get();
		
		if($query->num_rows() === 1)
		{
			$result = $query->result();
			$data = array('is_standard' => 0);

			$this-> db -> where('id', $result[0]->id);
			$this-> db -> update('questionnaire_batterie', $data);
		}//if

		//neue Standardbatterie setzen
		$data = array('is_standard' => 1);
		$this-> db -> where('id', $bid);
		$this-> db -> update('questionnaire_batterie', $data);
	}//set_standard_batterie()
}//class Questionnaire_tool_model

?>