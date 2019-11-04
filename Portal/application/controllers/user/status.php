<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for administrade all patients of a user.
 * 
 * @package Controller
 * @subpackage User
 * 
 * @author Martin Kock <code @ deeagle.de>
 */
class Status extends CI_Controller 
{

    /**
     * Constructor.
     * <ul>
     *  <li>Init of the Patient_model. @See Patient_model</li>
     *  <li>Init of the Questionnaire_model. @see Questionnaire_model</li>
     * </ul> 
     * 
     * @since 0.4.0
     * @access public
     */
    public function __construct( )
    {
        parent::__construct( );
        $this->data = array(HEADER_STRING => array('title' => 'Status'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this->load->Model('Patient_model');
        $this->load->Model('Questionnaire_model');
        $this->load->library('dompdf_gen');
        
        $this->lang->load( 'user_patient' );

        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
        
        if( $this->session_model->is_logged_in( $this->session->all_userdata() ) )
        {
            $this->data[TOP_NAV_STRING]['username'] = $this -> session -> userdata( 'username' );
            $this->data[CONTENT_STRING]['userrole'] = $this -> membership_model -> get_role( $this->data[TOP_NAV_STRING]['username'] );
            
            if( $this->data[CONTENT_STRING]['userrole'] !== 'user' && $this->data[CONTENT_STRING]['userrole'] !== 'admin' && $this->data[CONTENT_STRING]['userrole'] !== 'supervisor' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            redirect( 'login' );
        }
        
		//Profiler
		//$this->output->enable_profiler( $this->config->item('profiler_status') );
    }//__construct()
    
    public function status( $instance, $patientcode )
    {
        $username = $this->data[TOP_NAV_STRING]['username'];
        $user_role = $this->data[CONTENT_STRING]['userrole'];

        if( $user_role !== 'admin' && !$this -> Patient_model -> is_therapist_or_supervisor_of_patient( $username, $patientcode ) ) {
            show_error( 'Access denied. It\'s not a patient of yours!', 403 );
        }

        //Beinhaltet alle Fragebögen, für die nach Statusinformationen gesucht wird
        //ASQ Invertierung oder nicht Invertierung muss nochmal nachgeprüft werden
        $status_bows = ['asq','fep-2','haq-f','haq-s','phq-9','pstb','tstb'];
        $descriptions = array();
        $scaleItems = array();
        $allValues = array();
        $high = array();
        
        //Baut ein Ergebnisarray zusammen, in dem alle Informationen vorhanden sind, die zum Darstellen der jeweiligen Skala benötigt werden
        //TO DO: alle Werte && hohe Werte
        foreach($status_bows as $bow) {
            $allValues[$bow] = $this->Questionnaire_model->get_all_items_for_questionnaire($bow,$patientcode,$instance);
            $descriptions[$bow] = $this->Questionnaire_model->get_questionnaire_desc($bow);
            $scales = $this->Questionnaire_model->get_scales_of_questionnaire($bow);
            $high[$bow] = $this->Questionnaire_model->get_high($bow);
            
            foreach($scales as $scale) {
                $items = $this->Questionnaire_model->get_items_of_scale($scale['id']);
                $values = $this->Questionnaire_model->get_values($bow,$items,$instance,$patientcode,$scale['sd'],$scale['mean'],$scale['id']);
                $scale['result'] = $values;
                $scaleItems[$bow][$scale['skala']] = $scale;
            }//foreach
            
            for( $i=0; $i < sizeof( $allValues[$bow] ); $i++ ) {
                $high[$bow][$i]['value'] = ($allValues[$bow][$high[$bow][$i]['columnName']]);
            }//for
        }//foreach

        $means = $this->Questionnaire_model->get_status_scales_data($patientcode);

        $infos = array();
        foreach(array_keys($means) as $key){
            $infos[$key] = $this->Questionnaire_model->get_status_scales_info($key);
        }

        $items = $this->Questionnaire_model->get_values_with_info_of_all_items('de',$instance,$patientcode);
        
        $high_items = array();
        foreach($items as $table => $item){
            $high_items[$table] = $this->Questionnaire_model->get_high_items($item,$table);
        }

        $this->data['content']['means'] = $means;
        $this->data['content']['infos'] = $infos;
        $this->data['content']['items'] = $items;
        $this->data['content']['high_items'] = $high_items;

        //Suiziditems für Suizidalitätsgraphiken
        $this->data[CONTENT_STRING]['suicideItems'] = $this->Questionnaire_model->get_suicide_data($patientcode,$instance);
        //Alle Items
        $this->data[CONTENT_STRING]['allItems'] = $high;
        //Enthält die Beschreibungen der Bögen
        $this->data[CONTENT_STRING]['descriptions'] = $descriptions;
        //Enthält alle Informationen für die Graphiken
        $this->data[CONTENT_STRING]['questionnaires'] = $scaleItems;
        $this->data[CONTENT_STRING]['instance'] = $instance;
        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        //Die alte Version verwendet hierzu den OQ30, welcher hier nicht mehr vorhanden sein soll. Informationen abwarten !!!
        //$this->data[CONTENT_STRING]['instance_date'] = $this->Patient_model->get_instance_date( $username, $patientcode, $instance );

        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this->template->set(CONTENT_STRING, 'user/patient/status', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//status()

    public function process($patientcode, $filtered_therapy_type = NULL, $graph_counter = NULL) {
        $username = $this->data[TOP_NAV_STRING]['username'];
        //hier werden die möglichen Graphen abgespeichert ... ggf. noch Funktion zur Ermittlung verfügbarer Graphiken einbauen
        $grapharray = ["HSCL"];
        
        $means = $this->Questionnaire_model->get_process_scales_data($patientcode);
        $infos = array();
        foreach(array_keys($means) as $key){
            $info[$key] = $this->Questionnaire_model->get_process_scales_info($key);
        }
        
        $this->data[CONTENT_STRING]['hsclData'] = $this->Questionnaire_model->get_hscl_process_data($patientcode);

        $filtered_therapy_type = intval($filtered_therapy_type);

        if($filtered_therapy_type === 0) {
            //$filtered_therapy_type === "0" means don't filter and show all data; but still expand the collapsed graph (which is done if graph_counter is set)
            $this->data[CONTENT_STRING]['graph_counter'] = intval($graph_counter);
        }
        
        if( $filtered_therapy_type !== 0 AND $graph_counter !== NULL) {
            //$graph_counter contains which graph should only contain the filtered data. Depending on how high that counter is, it's either a graph of $grapharray or $means

            $graph_counter = intval($graph_counter);
            if($graph_counter < sizeof($grapharray)) {
                //Note: Currently only working for for HSCL, however HSCL will apparently be the only type appearing here anyway.
                
                $this->data[CONTENT_STRING]['hsclData']['INSTANCES'] = $this -> filter_graph_data($this->data[CONTENT_STRING]['hsclData']['INSTANCES'], $filtered_therapy_type);
            }
            else {
                $means_name = array_keys($means)[$graph_counter - sizeof($grapharray)]; //get the name of the means graph that was selected by the user
                
                //TODO Currently only working for FEP-2, the other data is null. Clear up what data will be displayed here and which structure it'll have.
                //Update: check how it works with the better test url: http://127.0.0.1/portal/index.php/user/status/process/9995P99
                $means_key = $means_name;
                
                $means[$means_name][$means_key] = $this -> filter_graph_data($means[$means_name][$means_key], $filtered_therapy_type);
            }

            $this->data[CONTENT_STRING]['graph_counter'] = $graph_counter;
            $this->data[CONTENT_STRING]['filtered_therapy_type'] = $filtered_therapy_type;
        }

        $this->data[CONTENT_STRING]['info'] = $info;
        $this->data[CONTENT_STRING]['means'] = $means;
        $this->data[CONTENT_STRING]['graphs'] = $grapharray;
        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this->template->set(CONTENT_STRING, 'user/patient/process', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }//process()

    private function filter_graph_data($instances, $filtered_therapy_type) {
        $to_filter = array();
        $negated = false;

        if($filtered_therapy_type == 1) {
            //SINGLE_THERAPY_EXCLUDE
            $negated = true;
            $to_filter = ['G','OT','S','L'];
        }
        else if($filtered_therapy_type == 2) {
            //GROUP_THERAPY
            $to_filter = ['WZ', 'PR', 'G', 'PO'];
        }
        else if($filtered_therapy_type == 3) {
            //ONLINE_THERAPY
            $to_filter = ['WZ','OT','PR'];
        }
        else if($filtered_therapy_type == 4) {
            //SEMINAR_THERAPY
            $to_filter = ['WZ','PR','Z','L','S','PO', '^\\\\d+'];
        }

        $filtered_data = array();
        
        if($negated) {
            //filter acts like a blacklist (excludes certain instances)
            foreach($instances as $instance_name=>$instance_data) {
                if( !in_array( (string) $instance_name, $to_filter ) ) {
                    //string casting because otherwise in_array would always return true for the first entry of a numeric array (the one at position 0)
                    $filtered_data[$instance_name] = $instance_data;
                }
            }
        }
        else {
            //filter acts like a whitelist
            foreach($instances as $instance_name=>$instance_data) {
                if( in_array( (string) $instance_name, $to_filter ) ) {
                    //string casting because otherwise in_array would always return true for the first entry of a numeric array (the one at position 0)
                    $filtered_data[$instance_name] = $instance_data;
                }
            }
        }
        
        return $filtered_data;

    }//filter_graph_data()
}//class Status

/* End of file status.php */
/* Location: ./application/controllers/user/status.php */