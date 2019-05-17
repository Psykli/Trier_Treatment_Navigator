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
		parent::__construct();
        //$this -> db = $this -> load -> database( 'default', TRUE );
		$this -> load -> Model( 'SB_Model' );
	}//__construct()

	public function get_sb_batterie($patientcode, $get_Z = false){
		if(isset($patientcode)){
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
    }
    
    public function inactive_patients($therapist){
		if(isset($therapist)){
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
		}
	}

    //Zusammengefügt aus den alten "get_released_questionnaires_of_therapist" und "get_released_questionnaires" Funktionen
    public function get_released_questionnaires( $user )
	{
		$questionnaire = NULL; 
		
		if( isset( $user ) )
		{	
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
		}
		
		return $questionnaire;	
	}

	public function is_questionnaire_available($patientcode)
	{
		
		if( isset( $patientcode ) )
		{	
			$this -> db -> from( 'questionnaire_released' );
			$this -> db -> where( 'patientcode', $patientcode );
			$this -> db -> where( 'finished', 0 );
			$this -> db -> where( 'activation <=', date('Y-m-d',time()));

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) > 0  )
				return true;
				
		}
		
		return $false;	
	}

	public function get_last_login($patientcode)
	{
		$activity = $this->get_ex_activity($patientcode);
		if(!is_null($activity)){
			return $activity->last_login;
		}

		return NULL;
	}

	public function get_ex_activity($patientcode)
	{
		if(isset($patientcode)){
			$this->db->from('ex_activity');
			$this->db->where('patientcode',$patientcode);
			$query = $this -> db -> get( );
			if($query->num_rows > 0){
				$result = $query->result();
				return $result[0];
			} 
		}

		return NULL;
	}

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

	public function get_questionnaire_id_by_table( $table ) {
		if( isset( $table ) ){

			$this -> db -> from('questionnaire_list ql');
			$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
			$this -> db -> where('ql.tablename',$table);
			$query = $this -> db -> get( );
			
			if( $query -> num_rows() > 0) {
				$result = $query->result( );
				$result = $this->group_questionnaires( $result );
				return $result[0] -> qid;
			}
		}
		return null;
	}

	public function get_single_released_questionnaire($patientcode, $id, $instance = null, $finished = null)
	{

		$result = NULL;
		if(isset($patientcode) AND isset($id)){
			$this -> db -> from( 'questionnaire_released' );
			$this -> db -> where( 'patientcode', $patientcode );
			$this -> db -> where( 'qid', $id );
			if(isset($instance)){
				$this -> db -> where( 'instance', $instance );
			}
			if(isset($finished)){
				$this -> db -> where( 'finished', $finished );
			}

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) > 0  ){
				$result = $query->result();
				$result = $result[0];
			}
		}
		return $result;
	}

	public function get_released_not_finished_questionnaires( $username )
	{
		$questionnaires = NULL; 
		
		if( isset( $username ) )
		{	
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
		}
		
		return $questionnaires;	
	}
	public function search_patients ( $patientcode = NULL, $therapist = NULL )
	{
        $patients = NULL; 
        if(isset($patientcode) || isset($therapist)){
            $this -> db -> from( 'subjects' );
            if(isset($patientcode) && !empty($patientcode))
                $this -> db -> like( 'CODE', $patientcode );
            if(isset($therapist) && !empty($therapist))
                $this -> db -> like( 'THERPIST', $therapist );
            $this -> db -> order_by( 'CODE', 'ASC' );

            $query = $this -> db -> get( );

            if( $query -> num_rows( ) > 0  )
                $patients = $query -> result( );
        }

		return $patients;	

	}//search_patient
	
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
	}
	
	public function get_all_questionnaire_by_battery($id, $is_Z = FALSE)
	{
		$questionnaires = NULL; 
		
		if( isset( $id ) )
		{	
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
		}
		
		return $questionnaires;	
	}

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
	}
	
	public function get_next_instance_of_questionnaire ($questionnaireID, $patientcode, $therapist, $instance){

		$result = $instance != 'Z' ? '01' : '05';

		if( isset( $questionnaireID ) AND isset( $patientcode ) AND isset( $instance ) ) {
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
		}

		return $result;
	}
	
	public function get_next_sb_instance($questionnaireID, $patientcode, $therapist){
		if(isset($patientcode)){
			$this -> db -> from( 'questionnaire_released' );
			$this -> db -> where( 'patientcode', $patientcode );
			$this -> db -> where( 'therapist', $therapist );
			$this -> db -> where( 'qid', $questionnaireID );
			$where = "instance REGEXP '^[0-9]*$'";
			$this -> db -> where($where);
			$query = $this -> db -> get( );
			
			$highestNumber = 1;
			if($query->num_rows() == 1) {
				$tmpres = $query->result();
				$highestNumber = intval($tmpres[0]->instance) + 1;
			}
			return $highestNumber;
		}
	}
	
	public function instance_exists($questionnaireID, $patientcode, $therapist, $instance){
		if(isset($patientcode)){
			$this -> db -> from( 'questionnaire_released' );
			$this -> db -> where( 'patientcode', $patientcode );
			$this -> db -> where( 'therapist', $therapist );
			$this -> db -> where( 'qid', $questionnaireID );
			$this -> db -> like('instance',$instance);
			$query = $this -> db -> get( );

			if($query->num_rows() > 0) {
				$result = $query -> result();
				return $result[0]->id;
			}
		}
	}

	public function get_all_questionnaire_by_batterie( $id, $is_Z = FALSE )
	{
		$questionnaires = NULL; 
		
		if( isset( $id ) )
		{	
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
		}
		
		return $questionnaires;	
	}

	public function insert_questionnaire( $therapist, $patientcode, $questionnaire, $instance, $activation = null, $days_interval = null )
	{
		if( !isset( $instance ) ) {
			$instance = 'OT';
		}

		if( !isset( $activation ) ) {
			$activation = date("Y-m-d H:i:s", time());
		}
		
		if( !isset( $days_interval ) ) {
			$days_interval = 0;
		}
		
		if( isset( $patientcode ) AND isset( $questionnaire ))
        {
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
		}
	}//insert_questionnaire

	public function get_questionnaire_by_file( $file )
	{
		$questionnaire = NULL; 
		
		if( isset( $file ) )
		{	
			$this -> db -> from( 'questionnaire_list ql' );
			$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
			$this -> db -> where( 'ql.filename', $file );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) >= 1 )
				$questionnaire = $query -> result( );	
				$questionnaire = $this->group_questionnaires($questionnaire);	
		}
		
		return $questionnaire[0];	
	}

	public function update_questionnaire ($questionnaire)
	{
		if( isset( $questionnaire ) )
        {
			$data = array(
				'finished' => 1
				);
				
			$this->db->where('id', $questionnaire);
			$this->db->update('questionnaire_released', $data); 
		}
	}//update_questionnaire

	public function get_questionnaire( $id )
	{
		$questionnaire = NULL; 
		
		if( isset( $id ) )
		{	
			$this -> db -> from( 'questionnaire_list ql' );
			$this -> db -> join( 'questionnaire_list_names qln', 'ql.id = qln.qid'  );
			$this -> db -> where( 'ql.id', $id );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) >= 1 ){
				$questionnaire = $query -> result( );
				$questionnaire = $this->group_questionnaires($questionnaire);
			}		
		}
		
		return $questionnaire;	
	}//get_questionnaire()

	public function get_entry( $id )
	{
		$entry = NULL; 
		
		if( isset( $id ) )
		{	
			$this -> db -> from( 'questionnaire_released' );
			$this -> db -> where( 'id', $id );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) == 1 )
				$entry = $query -> result( );		
		}
		
		return $entry;	
	}

	public function insert_row($data, $table) {
		if(isset($data['CODE'])) {
			$data['CODE'] = strtoupper($data['CODE']);
			$this->db->db_select();
			//Sicherheit? Test, ob alles in Ordnung ist?
			$sql = 'INSERT INTO `' . $table . '` (' .
			implode(", ", array_keys($data)) . ") VALUES ('" .
			implode("', '", array_values($data)) . "')";
			$this->db->query($sql);


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
	}//insert_row()


	public function get_remaining_questionnaires($patientcode){
		$questionnaires = NULL; 
		
		if( isset( $patientcode ) )
		{	
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
		}
		
		return $questionnaires;	
	}//get_remaining_questionnaires


	public function has_zwischen($patientcode, $z_instance)
	{

		if( isset( $patientcode ) && isset( $z_instance ) )
		{	
			$this -> db -> from( 'questionnaire_released' );
			$this -> db -> where( 'patientcode', $patientcode );
			$this -> db -> where( 'instance', $z_instance );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) >= 1 )
				return TRUE;	
		}
		
		return FALSE;	
	}

	public function get_feedback_of_batterie($bid){
		
		//$sql = "SELECT * FROM `questionnaire_batterie_feedback` WHERE bid = 2 ORDER BY feedback_order ASC";
		
		
		$this-> db -> from('questionnaire_batterie_feedback');
		$this-> db -> where('bid', $bid);
		$this -> db -> order_by( 'feedback_order', 'ASC' );
		
		
		$query = $this ->  db  -> get();
		if($query->num_rows() > 0){
			$result = $query->result();
			return $result;
		}

		return null;
	} //get_feedback of _batterie()

	public function get_review_by_data($bid,$data){
		$this-> db ->from('questionnaire_batterie_feedback');
		$this-> db ->where('bid',$bid);
		$this-> db ->where('type','review');
		$this-> db ->where('data',$data);
		$this -> db -> order_by( 'feedback_order', 'ASC' );

		$query = $this ->  db  -> get( );
		if($query->num_rows() > 0){
			$result = $query->result();
			return $result[0];
		}

		return null;
	}

	/**
	 * Ersetzt: publ. func. get_questionnaire_DB($filename)
	 
	 *  @return StdObject mit entsprechenden Daten aus portal.questionnaire_list (int qid, String tablename, String filename)
	 * 
	 */
	private function get_questionnaire_list_data($filename)
	{
		$this-> db -> from ('questionnaire_list');
		$this-> db -> where ('filename', $filename);
		$query = $this-> db -> get();
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			return $result[0];
		}
		return null;
	}

	public function add_or_update_questionnaire_DB($tablename, $filename, $language)
	{
		if(isset($tablename) AND isset($filename)) //Ist Questionnaire in DB vorhanden?
		{
			$get = $this-> get_questionnaire_list_data($filename);
			$id = $get->id;
			if (!isset($id)) //Bei Suche nach nicht XML-Dateien
			{
				$get = $this-> get_questionnaire_list_data(str_replace('.xml','',$filename));
				$id = $get->id;
			}
			
			//Daten für questionnaire_list zusammenstellen
			$data = array(
				'tablename' => $tablename,
				'filename' => $filename
			);

			if(isset($id)) //Questionnaire bereits in DB vorhanden --> UPDATE
			{
				$this-> db -> where('id', $id);
				$this-> db -> update('questionnaire_list', $data);
				$qid = $id;
			}
			else // Questionnaire noch nicht in DB vorhanden --> INSERT
			{
				$this -> db -> insert('questionnaire_list', $data);
				$qid = $this-> db -> insert_id();
			}

			// Daten für questionnaire_list_names zusammenstellen und jew. Eintrag updaten/einfügen
			foreach($language as $key => $lang)
			{
				$this -> db -> from ('questionnaire_list_names');
				$this-> db -> where ('qid', $qid);
				$this-> db -> where ('language', $key);
				$query = $this -> db -> get();

				$data2 = array(
					'qid' => $qid,
					'language' => $key,
					'header_name' => $lang['header_name'],
					'description' => $lang['description']
				);

				if($query->num_rows() > 0)
				{
					$this-> db -> where('qid', $qid);
					$this-> db -> where('language', $key);
					$this-> db -> update('questionnaire_list_names', $data2);
				}
				else
				{
					$this-> db -> insert('questionnaire_list_names', $data2);
				}
			}//foreach
		}//if
	}//add_or_update_questionnaire_DB

	public function add_or_update_process_scales($scales, $item_invert, $info){

		foreach($scales as $name => $tables){
			foreach($tables as $table_name => $items){
				$this->db->from('questionnaire_process_scales');
				$this->db->where('name',$name);
				$this->db->where('table_name',$table_name);
				$query = $this->db->get();

				$data = array(
					'name' => $name,
					'table_name' => $table_name,
					'items' => json_encode($items),
					'item_invert' => json_encode($item_invert[$name]),
					'title' => $info[$name]['title'],
					'min' => $info[$name]['min'],
					'max' => $info[$name]['max']
				);
				if($query->num_rows() > 0){
					$this->db->where('name',$name);
					$this->db->where('table_name',$table_name);
					$this->db->update('questionnaire_process_scales', $data);
				} else {
					$this->db->insert('questionnaire_process_scales', $data);
				}
			}
		}
	}

	public function remove_questionnaire_DB($id){
		$this->db->where('id',$id);
		$this->db->delete('questionnaire_list');

		$this->db->where('qid',$id);
		$this->db->delete('questionnaire_list_names');
	}

	public function get_section_names($id)
	{
		$this-> db -> from('questionnaire_batterie');
		$this-> db -> where('id', $id);

		$query = $this-> db -> get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			return $result[0]->section_names;
		}
	}

	public function insert_new_batterie_to_DB($name)
	{
		if (isset($name))
		{
			$data = array('name' => $name);
			$this-> db -> insert('questionnaire_batterie', $data);
			
		}
	}//insert_new_batterie

	public function delete_batterie_from_DB($bid)
	{
		if(isset($bid))
		{
			$this-> db -> where('id', $bid);
			$this-> db -> delete('questionnaire_batterie');

			$this-> db -> where('bid', $bid);
			$this-> db -> delete('questionnaire_batterie_hat');
		}
	}//delete_batterie_from_DB

	public function add_section_to_batterie($bid)
	{
		$this-> db -> from('questionnaire_batterie');
		$this-> db -> where('id', $bid);

		$query = $this-> db -> get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$data = array('sections' => $result[0]->sections+1);

			$this-> db -> where('id', $bid);
			$this-> db -> update('questionnaire_batterie', $data);

			return TRUE;
		}
		return FALSE;
	} // add_section_to_batterie()

	public function delete_section_from_batterie($bid)
	{
		$this-> db -> from('questionnaire_batterie');
		$this-> db ->where('id', $bid);

		$query = $this-> db -> get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$current_sections = $result[0]->sections;

			if($current_sections > 1)
			{
				//Update entries of batterie in qbh if any exist
				$this-> db -> from('questionnaire_batterie_hat');
				$this-> db -> where('bid', $bid);
				$this-> db -> where('section', $current_sections-1);

				$query = $this-> db -> get();
				if($query->num_rows() > 0)
				{
					$result = $query->result();
					foreach($result as $questionnaire)
					{
						$data = array('section' => $questionnaire->section-1);

						$this-> db ->where('id', $questionnaire->id);
						$this-> db ->update('questionnaire_batterie_hat', $data);
					}//foreach
				}//if

				//Update entry for battery in qb
				$data = array('sections' => $current_sections-1);
				$this-> db -> where ('id', $bid);
				$this-> db -> update('questionnaire_batterie', $data);

				return TRUE;
			}//if($current_sections > 1)
		}
		
		return FALSE;
	}//delete_section_from_batterie()

	public function get_battery($bid)
	{
		$this-> db -> from('questionnaire_batterie');
		$this-> db -> where('id', $bid);

		$query = $this-> db -> get();
		$result = ($query) ? $query->result() : NULL;

		return $result[0];
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
		if(isset($is_Z)) 
		{
			$data = array('is_Z' => $is_Z);
			$this-> db -> where('id', $id);
			$this-> db -> update('questionnaire_batterie_hat', $data);
		}
	}//set_quest_type

	public function insert_questionnaire_in_battery($bid, $qid)
	{
		if (isset($bid) && (isset($qid)))
		{
			$data = array(
				'bid' => $bid,
				'qid' => $qid
			);
			$this-> db -> select_max('section_order');
			$this-> db -> from('questionnaire_batterie_hat');
			$this-> db -> where('bid', $bid);
			$this-> db -> where('section', 0);
			$query = $this-> db -> get();
			if($query -> num_rows() > 0)
			{
				$result = $query->result();
				if(isset($result[0]->section_order))
					$data['section_order'] = $result[0]->section_order+1;
			}
			$this-> db -> insert('questionnaire_batterie_hat', $data);
		}//insert_questionnaire_in_batterie()
	}

	private function reorder_battery($hid)
	{
		$this-> db -> from('questionnaire_batterie_hat');
		$this-> db -> where('id', $hid);
		$query = $this-> db -> get();

		if($query-> num_rows() > 0)
		{
			$result = $query->result();
			$section = $result[0] ->section;
			$order = $result[0]->section_order;

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
	}//reorder_battery

	public function delete_questionnaire_in_battery($bid, $hid)
	{
		$this-> reorder_battery($hid);
		$this-> db -> where('bid', $bid);
		$this-> db -> where('id', $hid);
		$this-> db -> delete('questionnaire_batterie_hat');
	}//delete_questionnaire_in_batterie()

	public function set_gas($bid, $gas_section)
	{
		if(isset($bid) && isset($gas_section))
		{
			$data = array('gas_section' => $gas_section);
			
			$this-> db -> where('id', $bid);
			$this-> db -> update('questionnaire_batterie', $data);
		}
	}//set_gas()

	public function add_feedback_item($bid, $type, $data)
	{
		if(isset($bid) && isset($type) && isset($data))
		{
			$order = 0;
			$this-> db -> select_max('feedback_order');
			$this-> db -> from('questionnaire_batterie_feedback');
			$this-> db -> where('bid', $bid);

			
			$query = $this -> db -> get( );
			if($query->num_rows() > 0){
				$result = $query->result();
				if(isset($result[0]->feedback_order))
					$order = $result[0]->feedback_order+1;
			}

			$insert = array(
				'bid' => $bid,
				'type' => $type,
				'data' => $data,
				'feedback_order' => $order
			);

			$this-> db ->where('bid', $bid);
			$this-> db ->insert('questionnaire_batterie_feedback', $insert);
		}//if
	}//add_feedback_item()

	public function delete_feedback_item($id, $bid)
	{
		$this-> reorder_feedback($id, $bid);
		$this-> db -> where('id', $id);
		$this-> db -> delete('questionnaire_batterie_feedback');
	}//delete_feedback_item()

	private function reorder_feedback($id, $bid)
	{	
		$this-> db -> from('questionnaire_batterie_feedback');
		$this-> db -> where('id', $id);

		$query = $this-> db -> get();
		if($query-> num_rows() > 0) // True: Item  mit $id in Tabelle existiert 
		{
			$result = $query->result();
			$this-> db -> from('questionnaire_batterie_feedback');
			$this-> db -> where('bid', $bid);
			$this-> db -> where('feedback_order <', $result[0]->feedback_order);

			$query = $this-> db -> get();
			if($query-> num_rows() > 0) // True: in Batterie $bid existieren Items mit höherer Feedback_Order als Item mit $id
			{
				$result = $query->result();
				foreach($result2 as $r ) //Setze alle größeren Feedback_orders um eins herunter
				{
					$data = array('feedback_order' => $r->feedback_order-1);

					$this-> db -> where ('id', $id);
					$this-> db -> update('questionnaire_batterie_feedback', $data);
				}
			}

		}
	}//reorder_feedback()

	public function set_standard_battery($bid)
	{
		if(isset($bid))
		{
			//bisherige Standardbatterie zurücksetzen
			$this-> db -> from('questionnaire_batterie');
			$this-> db -> where('is_standard', 1);
			$query = $this-> db -> get();
			if($query->num_rows() > 0)
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
		}//if
	}//set_standard_batterie()
}

?>