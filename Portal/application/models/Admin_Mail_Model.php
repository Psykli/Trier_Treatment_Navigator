<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );


class Admin_mail_model extends CI_Model
{
    public function get_all_messages(){

        $this -> db -> from( 'admin_mail_messages' );

        $query = $this -> db -> get( );

        if( $query -> num_rows( ) > 0  ){
            return $query->result();
        } 

        return NULL;
    }

    public function get_users(){

        
        $this -> db -> from( 'user' );
        $this-> db-> where('EMAIL IS NOT NULL');
        $this-> db-> where('EMAIL !=','');
        $query = $this -> db -> get( );

        if( $query -> num_rows( ) > 0  ){
            return $query->result();
        }

        return NULL;
    }

    public function save_new_message($subject,$message){
        if( isset( $subject ) && isset( $message ) )
		{		
			$data = array(
				'subject' => $subject,
				'message' => $message
                );

			$this -> db -> insert( 'admin_mail_messages', $data );
			
			return true;
		}

        return false;
    }

    public function update_message($id,$subject,$message){
        if(isset($id)){
            $data = array(
                'subject' => $subject,
                'message' => $message
            );
            $this->db->where('id',$id);
            $this->db->update('admin_mail_messages',$data);
        }
    }

    public function delete_single_message($id){
        $this->db->where('id', $id);
        $this->db->delete('admin_mail_messages');
    }
}

?>