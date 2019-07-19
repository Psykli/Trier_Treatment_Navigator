<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * An instance of the class represents a exercise model.
 * It contains all functions for exercise management.
 *
 * @package Model
 *
 * @since 0.8.0
 * @access public
 *
 * @author Ruven Martin
 */
class Message_model extends CI_Model
{
	/** 
	 *
	 * Entspricht dem ISO-8601 Format 	
	 * Beispiel: 2005-08-14T16:13:03+00:00
	 * @since 0.8.0
	*/
	
	private $dateFormat = 'DATE_ISO8601';
	
  public function __construct( )
  {
		$this -> db_ci_patientfeedback = $this -> load -> database( 'default', TRUE );
		$this -> load -> helper('date');
  }//__construct()
    
  public function get_count_of_unread_received_msgs( $receiver )
	{
		if( isset( $receiver ) )
		{
			$anzahl = NULL;
			
			$this -> db -> select( 'count(id) AS anzahlMsgs' );
			$this -> db -> from( 'ex_nachrichten' );
			$this -> db -> where( 'receiver', $receiver );
			$this -> db -> where( 'status', 0 );	

			$query = $this -> db -> get( );
			
			if( $query -> num_rows( ) > 0  )
				$anzahl = $query -> row(0) -> anzahlMsgs;
			
			return $anzahl;
		}
	}
	
	public function get_received_msgs( $receiver )
	{
		if( isset( $receiver ) )
		{
			$msgs = NULL; 

			$this -> db -> from( 'ex_nachrichten' );
			$this -> db -> where( 'receiver', $receiver );
			$this -> db -> order_by( 'datum', 'DESC' );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) > 0  )
				$msgs = $query -> result( );
			
			return $msgs;			
		}
	}

	public function get_sent_msgs( $sender )
	{
		if( isset( $sender ) )
		{
			$msgs = NULL; 

			$this -> db -> from( 'ex_nachrichten' );
			$this -> db -> where( 'sender', $sender );
			$this -> db -> order_by( 'datum', 'DESC' );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) > 0  )
				$msgs = $query -> result( );
			
			return $msgs;			
		}
	}

	public function set_status( $id, $status, $receiver )
	{
		if( isset( $id ) && isset( $status ) && isset( $receiver ) )
		{
			$data = array ('status' => $status );
			
			$this -> db -> where( 'id', $id );
			$this -> db -> where( 'receiver', $receiver );
			
			$this -> db -> update( 'ex_nachrichten', $data );
		}
	}

	public function get_msg( $msgid )
	{
		if( isset( $msgid ) )
		{
			$msg = NULL; 

			$this -> db -> from( 'ex_nachrichten' );
			$this -> db -> where( 'id', $msgid );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) == 1  )
				$msg = $query -> result( );
			
			return $msg;			
		}
	}

	public function insert_msg( $sender, $receiver, $betreff, $nachricht, $cipher, $iv, $tagSubject, $tagMessage, $randomKeyBytes )
  {
		if( isset( $sender ) && isset( $receiver ) )
		{		
			$data = array(
				'sender' => $sender,
				'receiver' => $receiver,
				'betreff' => $betreff,
				'nachricht' => $nachricht,
				'status' => 0,
				'cipher' => $cipher,
				'iv' => $iv,
				'tagSubject' => $tagSubject,
				'tagMessage' => $tagMessage,
				'randomKeyBytes' => $randomKeyBytes);

			$this -> db -> insert( 'ex_nachrichten', $data );
			
			return $this->db->insert_id();
		}
		
		return NULL;
	}

	public function get_sent_msgs_who_to_who( $sender, $receiver )
	{
		if( !empty( $sender ) && !empty($receiver) )
		{
			$msgs = NULL; 

			$this -> db -> from( 'ex_nachrichten' );
			$this -> db -> where( 'sender', $sender );
			$this -> db -> where( 'receiver', $receiver );
			$this -> db -> order_by( 'datum', 'DESC' );

			$query = $this -> db -> get( );

			if( $query -> num_rows( ) > 0  )
				$msgs = $query -> result( );
			
			return $msgs;			
		}
	}

	public function exist_msg_who_to_who( $sender, $receiver )
	{
		if( isset( $sender ) AND isset( $receiver ) )
		{
			$this -> db -> select( 'id' );
			$this -> db -> from( 'ex_nachrichten' );
			$this -> db -> where( 'sender', $sender );
			$this -> db -> where( 'receiver', $receiver );
			$this -> db -> limit(1);

			$query = $this -> db -> get( );
			
			if( $query -> num_rows( ) == 1 ) {
				return true;
			}
		}
		
		return false;
	}

	public function get_msg_encryption_constant()
  	{
		return $this->config->item('msg_encryption_constant');
	}

	public function get_msg_encryption_key_size()
  	{
		return $this->config->item('msg_encryption_key_size');
	}

	public function get_msg_encryption_cipher()
  	{
		return $this->config->item('msg_encryption_cipher');
	}
}