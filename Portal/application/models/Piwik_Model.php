<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Piwik_model extends CI_Model{

    public function __construct( )
    {
        $this->load->dbutil();
        if($this->dbutil->database_exists('piwik'))
        {
            
            $this -> piwik_db = $this -> load -> database( 'piwik', TRUE );

            $CI =& get_instance();
            if( !property_exists( $CI, 'piwik_db') )
                $CI->piwik_db =& $this -> piwik_db;
        }
    }

    public function get_piwik_data_for_user($initials){
        $data = NULL;

        if(isset($initials) && isset($this->piwik_db))
        {

          $this-> piwik_db -> db_select();

          $this-> piwik_db -> select('v.idvisit as idvisit, v.visit_total_time as total_time, lva.time_spent_ref_action as action_time, lva.server_time as server_time, a.name as url');
          $this-> piwik_db -> from('piwik_log_visit v');
          $this-> piwik_db -> join('piwik_log_link_visit_action lva', 'lva.idvisit = v.idvisit');
          $this-> piwik_db -> join('piwik_log_action a', 'a.idaction = lva.idaction_url_ref');
          $this-> piwik_db -> where('v.custom_var_v1',$initials);
          $this-> piwik_db -> order_by("v.idvisit", "asc");

          $query = $this -> piwik_db -> get( );

          if( $query -> num_rows( ) > 0 )
            $data = $query -> result( );

        }

        return $data;
    }
    
    public function get_last_date_for_user($initials){
        $data = NULL;

        if(isset($initials) && isset($this->piwik_db))
        {
            $this-> piwik_db -> db_select();

            $this-> piwik_db -> select('v.idvisit as idvisit, lva.server_time as server_time');
            $this-> piwik_db -> from('piwik_log_visit v');
            $this-> piwik_db -> join('piwik_log_link_visit_action lva', 'lva.idvisit = v.idvisit');
            $this-> piwik_db -> where('v.custom_var_v1',$initials);
            $this-> piwik_db -> order_by("lva.server_time", "desc");

            $query = $this -> piwik_db -> get( );

            if( $query -> num_rows( ) > 0 ){
                $data = $query -> result( );
                return $data[0]->server_time;
            }
        }

        return NULL;
    }

} //Piwik_Model
