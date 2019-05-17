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
        $this->data = array('header' => array('title' => 'Status'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );
        $this->load->Model('Patient_model');
        $this->load->Model('Questionnaire_model');
        $this->load->Model('Membership_Model');
        $this->load->library('dompdf_gen');
        
        $this->template->set('header', 'all/header', $this->data['header']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        
        $is_logged_in = $this->session_model->is_logged_in( $this->session->all_userdata() );
        
        if( $is_logged_in )
        {
            $username = $this -> session -> userdata( 'username' );
            $this->data['top_nav']['username'] = $username;
            
            $user_role = $this -> membership_model -> get_role( $this->data['top_nav']['username'] );
            $this->data['content']['userrole'] = $user_role;

            if( $user_role !== 'user' AND $user_role !== 'priviledged_user' AND $user_role !== 'admin' AND $user_role !== 'supervisor' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            $this->template->set('top_nav', 'guest/top_nav', $this->data['top_nav']);
            $this->template->set('content', 'guest/login_form', $this->data['content']);
            $this->template->load('template');
        }
        
		//Profiler
		//$this->output->enable_profiler( $this->config->item('profiler_status') );
    }//__construct()
    
    public function status( $instance, $patientcode )
    {
        $username = $this->data['top_nav']['username'];
        $user_role = $this->data['content']['userrole'];

        if( ( $user_role === 'user' AND !$this->Patient_model->is_therapist_of_patient( $username, $patientcode ) ) OR ( $user_role === 'supervisor' AND !$this->Patient_model->is_supervisor_of_patient( $username, $patientcode ) ) ) {
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

        //Suiziditems für Suizidalitätsgraphiken
        $this->data['content']['suicideItems'] = $this->Questionnaire_model->get_suicide_data($patientcode,$instance);
        //Alle Items
        $this->data['content']['allItems'] = $high;
        //Enthält die Beschreibungen der Bögen
        $this->data['content']['descriptions'] = $descriptions;
        //Enthält alle Informationen für die Graphiken
        $this->data['content']['questionnaires'] = $scaleItems;
        $this->data['content']['instance'] = $instance;
        $this->data['content']['patientcode'] = $patientcode;
        //Die alte Version verwendet hierzu den OQ30, welcher hier nicht mehr vorhanden sein soll. Informationen abwarten !!!
        //$this->data['content']['instance_date'] = $this->Patient_model->get_instance_date( $username, $patientcode, $instance );

        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this->template->set('content', 'user/patient/status', $this->data['content']);
        $this->template->load('template');
    }

    public function process ($patientcode) {
        $username = $this->data['top_nav']['username'];
        //hier werden die möglichen Graphen abgespeichert ... ggf. noch Funktion zur Ermittlung verfügbarer Graphiken einbauen
        $grapharray = ["HSCL"];
        
        $means = $this->Questionnaire_model->get_process_scales_data($patientcode);
        $infos = array();
        foreach(array_keys($means) as $key){
            $info[$key] = $this->Questionnaire_model->get_process_scales_info($key);
        }
        $this->data['content']['info'] = $info;
        $this->data['content']['means'] = $means;
        $this->data['content']['graphs'] = $grapharray;
        $this->data['content']['hsclData'] = $this->Questionnaire_model->get_hscl_process_data($patientcode);
        $this->data['content']['patientcode'] = $patientcode;
        $this->template->set('header', 'all/header', $this->data['header']);

        $is_priviledged_user = $this->membership_model->is_role($username,"priviledged_user");	
		if($is_priviledged_user){
			$this -> template -> set( 'top_nav', 'priviledged_user/top_nav', $this -> data['top_nav'] );
		}else{
			$this -> template -> set( 'top_nav', 'user/top_nav', $this -> data['top_nav'] );
        }
        
        $this->template->set('content', 'user/patient/process', $this->data['content']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        $this->template->load('template');
    }
}