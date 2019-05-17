<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for users patients.
 * 
 * @package Controller
 * @subpackage User
 * 
 */
class Feedback extends CI_Controller
{

    function __construct( )
    {
        parent::__construct( );
        $this->data = array('header' => array('title' => 'Feedback'),
                            'top_nav' => array(),
                            'content' => array(),
                            'footer' => array()
        );

        $this -> load -> Helper( 'text' );
        $this->load->Model( 'Membership_Model');
		$this->load->Model( 'Patient_model' );
        $this->load->Model( 'Questionnaire_model' );
        $this->load->Model( 'Supervisor_model' );
        $this->lang->load( 'user_patient' );
        $this->lang->load('user_feedback_motivation');
		$this->lang->load('user_feedback_emotion');
		$this->lang->load('user_feedback_relation');
		$this->lang->load('user_feedback_social');
		$this->lang->load('user_feedback_life');
        $this->lang->load('user_feedback_risk');

        $this->template->set('header', 'all/header', $this->data['header']);
        $this->template->set('footer', 'all/footer', $this->data['footer']);
        
        $is_logged_in = $this->session_model->is_logged_in( $this->session->all_userdata() );
        
        if( $is_logged_in )
        {
            $this->data['top_nav']['username'] = $this -> session -> userdata( 'username' );
            $this->data['content']['userrole'] = $this -> membership_model -> get_role( $this->data['top_nav']['username'] );
            
            if( $this->data['content']['userrole'] !== 'user' && $this->data['content']['userrole'] !== 'admin' && $this->data['content']['userrole'] !== 'supervisor' ) {
                show_error( 'Access denied for your Userrole', 403 );
            }
        }
        else {
            $this->template->set('top_nav', 'guest/top_nav', $this->data['top_nav']);
            $this->template->set('content', 'guest/login_form', $this->data['content']);
            $this->template->load('template');
        }
    }//__construct()

    public function overview( $patientcode ){
        $lastHscl = $this->Patient_model->get_last_hscl( $patientcode);
        $this->data['content']['lastHscl'] = $lastHscl;

        //HSCL PROCESS DATA
        $hsclData = $this->Questionnaire_model->get_hscl_process_data($patientcode);
        $this->data['content']['hsclData'] = $hsclData;
        //Verlauf
        $therapist = $this->Patient_model->get_therapist_of_patient($patientcode);
        if( empty( $therapist ) AND $this->data['content']['userrole'] === 'admin' ){
            $therapist = 'admin';
        }
        //Boundary check
        $lastInstance;
        for ($i=sizeof($hsclData['MEANS']); $i >= 0; $i--) {
            if (isset($hsclData['MEANS'][$i])) {
                $lastInstance = $i+1;
                break;
            }
        }

        $lastNormal;
        for ($i=sizeof($hsclData['MEANS']); $i >= 0; $i--) {
            if ((isset($hsclData['MEANS'][$i]) && isset($hsclData['BOUNDARIES'][$i])) && ($hsclData['MEANS'][$i] < $hsclData['BOUNDARIES'][$i]) ) {
                $lastNormal = $i+1;
                break;
            }
        }

        $this->data['content']['last'] = $lastNormal;
        $this->data['content']['normal'] = $lastInstance;


        if ($lastInstance > $lastNormal) {
            $diff = $lastInstance - $lastNormal;
            if ($diff == 1) {
                $sitzung = "Sitzung";
            } else {
                $sitzung = "Sitzungen";
            }
            $signcolor = "red";
            $symptomtext = "Patient wich seit $diff $sitzung vom erwarteten Symptomverlauf ab";
        } elseif ($lastInstance == $lastNormal) {
            $signcolor = "green";
            $symptomtext = "Allgemeiner Symptomverlauf ist erwartungsgemäß";
        } elseif ($last < 5) {
            $signcolor = "green";
            $symptomtext = "Vor der fünften Sitzung findet keine Berechnung des Feedbacks statt";
        } elseif ($last > 30) {
            $signcolor = "green";
            $symptomtext = "Nach der 30. Sitzung findet keine Berechnung des Feedbacks statt";
        } elseif (!isset($lastInstance) || !isset($lastNormal)) {
            $signcolor = "black";
            $symptomtext = "Unbekannter Fehler";
        }
        
        $this->data['content']['symptomtext'] = $symptomtext;
        $this->data['content']['signcolor'] = $signcolor;

        $this->data['content']['patientcode'] = $patientcode;
        
        $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
        $this -> template -> set('content', 'user/patient/feedback/overview', $this->data['content']);
        $this -> template -> load('template');
    }//overview()

    public function download ( $name, $path, $file, $type ){
        header('Content-type: application/'.$type);
        header("Content-Disposition:attachment;filename=".$name);

        if ($type==="pdf") {
            readfile(FCPATH."/pdf/clinical_support_tools/".$path."/".$file);
        } elseif ($type==="octet-stream") {
            readfile(FCPATH."/audio/feedback/".$path."/".$file);
        }
        else {
            log_message( 'warn', 'feedback download request with unknown filetype');
            show_error( 'Access denied', 403 );
        }
    }//download()

    /*Wird künftig in den Views aufgerufen, mit $patientcode, $page und $path
    Beispiel:
    load/$path/$page/$patientcode
    ->
    echo anchor('user/feedback/load/feedbackAlliance/step_4/'.$patient[0]->code, 'Weiter <span class="glyphicon glyphicon-arrow-right"></span> ', array('class' => 'btn btn-default'))
    */
    public function load ( $path, $page, $patientcode ) {
        if ($path === "feedbackRisk") {
            $this->data['content']['suicideItems'] = $this->Questionnaire_model->get_suicide_data($patientcode);
            $this->data['content']['riskColor'] = $this->Questionnaire_model->get_risk($patientcode);
        }

		//if no $patientcode was set
        if( !isset( $patientcode ) )
        {
            log_message( 'warn', 'feedback load request without patientdata.');
            show_error( 'Access denied', 403 );
        }//if
        else //Patientcode is set
        {
            $username = $this->data['top_nav']['username'];
            $this->data['content']['process'] = $verlauf;
            $patient = $this->Patient_model->get_patient( $username, $patientcode );
            
            if( !isset( $patient ) AND !$this->Patient_model->is_supervisor_of_patient( $username, $patientcode ) )
            {
                show_error( 'Access denied. It\'s not a patient of yours!', 403 );
            }//if
            else // patient to therapeut is correct
            {        
                $therapist = $this->Patient_model->get_therapeut_name('admin',$patientcode);	
                $lastHscl = $this->Patient_model->get_last_hscl( $patientcode);
                
                $this->data['content']['patient'] = $patient;
                
                $this -> template -> set( 'top_nav', $this->data['content']['userrole'].'/top_nav', $this -> data['top_nav'] );
                $this -> template -> set('content', 'user/patient/feedback/'.$path.'/'.$page, $this->data['content']);
                $this -> template -> load('template');
            }//else
        }//else
    }//load()
}
?>