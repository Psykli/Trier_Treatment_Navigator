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
        $this->data = array(HEADER_STRING => array('title' => 'Feedback'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
        );

        $this -> load -> Helper( 'text' );
        $this->load->Model('membership_model');
        $this->load->Model('session_model');
        $this->load->Model( 'Patient_model' );
        $this->load->Model( 'Questionnaire_model' );
        $this->load->Model( 'Supervisor_model' );
        
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
    }//__construct()

    private function _check_permissions( $username, $patientcode ) {
        return $this->data[CONTENT_STRING]['userrole'] === 'admin' || $this->Patient_model->is_therapist_or_supervisor_of_patient( $username, $patientcode );
    }

    public function overview( $patientcode ) {
        if( !$this -> _check_permissions( $this->data[TOP_NAV_STRING]['username'], $patientcode ) ) {
            show_error( 'Access denied. It\'s not a patient of yours!', 403 );
        }
        else {
            $lastHscl = $this->Patient_model->get_last_hscl( $patientcode);
            $this->data[CONTENT_STRING]['lastHscl'] = $lastHscl;
            
            $colorArr = array("Beziehung" => $this->Questionnaire_model->get_bez($patientcode),
            "Motivation" => $this->Questionnaire_model->get_motivation($patientcode),
            "Risiko" => $this->Questionnaire_model->get_risk($patientcode),
            "SocSupLife" => $this->Questionnaire_model->get_soc_sup_life($patientcode),
            "Emo" => $this->Questionnaire_model->get_emo($patientcode));

            $acc = $this->Questionnaire_model->get_accept ($patientcode);
            $sup = $this->Questionnaire_model->get_suppress ($patientcode);
            $adapt = $this->Questionnaire_model->get_adapt ($patientcode);

            $this->data[CONTENT_STRING]['accept'] = $acc;
            $this->data[CONTENT_STRING]['suppress'] = $sup;
            $this->data[CONTENT_STRING]['adapt'] = $adapt;
            $this->data[CONTENT_STRING]['supportColors'] = $colorArr;

            //HSCL PROCESS DATA
            $hsclData = $this->Questionnaire_model->get_hscl_process_data($patientcode);
            $this->data[CONTENT_STRING]['hsclData'] = $hsclData;
            //Verlauf
            $therapist = $this->Patient_model->get_therapist_of_patient( $this->data[TOP_NAV_STRING]['username'], $patientcode, false );
            if( empty( $therapist ) AND $this->data[CONTENT_STRING]['userrole'] === 'admin' ){
                $therapist = 'admin';
            }
            $this->data[CONTENT_STRING]['process'] = $process;

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

            $this->data[CONTENT_STRING]['last'] = $lastNormal;
            $this->data[CONTENT_STRING]['normal'] = $lastInstance;


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
            
            $this->data[CONTENT_STRING]['symptomtext'] = $symptomtext;
            $this->data[CONTENT_STRING]['signcolor'] = $signcolor;

            $sorted_colors = array(array('farbe_oq',$color['farbe_oq']),
                array('farbe_risk_suicide',$color['farbe_risk_suicide']),
                array('farbe_asc_motivation',$color['farbe_asc_motivation']),
                array('farbe_asc_alliance',$color['farbe_asc_alliance']),
                array('farbe_asc_socsup',$color['farbe_asc_socsup']),
                array('farbe_asc_lifeevents',$color['farbe_asc_lifeevents']),
                array('farbe_asq_emotion',$color['farbe_asq_emotion']));

            if($color['farbe_oq'] == 'red'){
                $boundary = $this->Patient_model->get_boundary($patientcode, $lastHscl->instance, "BOUNDARY_UEBERSCHRITTEN");
                // -1 um die aktuelle Sitzung mit zu zählen 
                $over = $boundary->BOUNDARY_UEBERSCHRITTEN-1;
            } else {
                // Wir wollen hier herausfinden wann die Boundary das letzte mal überschritten war. 
                // In BOUNDARY_UEBERSCHRITTEN steht nur wann sie das erste mal überschritten wurde
                for($i = $lastHscl->instance; $i >= $lastHscl->instance-3; $i--){
                    $boundary = $this->Patient_model->get_boundary($patientcode, $i, "BOUNDARY_UEBERSCHRITTEN");
                    
                    if($boundary->BOUNDARY_UEBERSCHRITTEN > 0){
                        $over = $i;
                        break;
                    }
                }
            }
            
            $means = array();
            $searched_scales = array('Traurig','Beschämt','Ängstlich','Wütend','Zufrieden','Energiegeladen','Entspannt','Symptomeinschätzung','TSB Therapiebeziehung','TSB Korrektive Erfahrungen (Bewältigung + Klärung)','TSB Problemaktualisierung');
            foreach($searched_scales as $scale){
                $tmp = $this->Questionnaire_model->get_process_scales_data($patientcode,$scale);
                $means = array_merge($means,$tmp);
            }

            $infos = array();
            foreach(array_keys($means) as $key){
                $tmp = $this->Questionnaire_model->get_process_scales_info_by_scale($key);
                $infos[$key] = $tmp;
            }

            $this->data[CONTENT_STRING]['means'] = $means;
            $this->data[CONTENT_STRING]['infos'] = $infos;

            $boundary_over = $over > 0 ? $lastHscl->instance - $over : -1;
            $this->data[CONTENT_STRING]['boundary_over'] = $boundary_over;

            $this->data[CONTENT_STRING]['color'] = $sorted_colors;
            $this->data[CONTENT_STRING]['show_cs_tools'] = $color['farbe_oq'] === 'red' || ($boundary_over > 0 && $boundary_over <= 3 && $color['farbe_oq'] !== 'white' && explode('_',$color['farbe_oq'])[0] !== 'missing');

            //For showing
            $this->data[CONTENT_STRING]['show_cs_tools'] = TRUE;

        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set(CONTENT_STRING, 'user/patient/feedback/overview', $this->data[CONTENT_STRING]);
        $this -> template -> load('template');
        }
    }//overview()

    /*
    $name ist der angezeigte Dateiname beim Download
    $path ist der Pfad ab /pdf/clinical_support_tools/... 
    $type ist entweder 'pdf' oder 'octet-stream' für mp3
    wird in den Views folgendermaßen aufgerufen:
    form_open('user/feedback/download/$name/$path/$file/$type')
    Siehe Beispiel:
    form_open('user/feedback/download/Vertrag.pdf/risiko/Vertrag_von_mir_selbst.pdf/pdf')
    */
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
    echo anchor('user/feedback/load/feedbackAlliance/step_4/'.$patient[0]->code, 'Weiter <span class="fas fa-arrow-right"></span> ', array('class' => 'btn btn-outline-secondary'))
    */
    public function load ( $path, $page, $patientcode ) {
        if( !$this -> _check_permissions( $this->data[TOP_NAV_STRING]['username'], $patientcode ) ) {
            show_error( 'Access denied. It\'s not a patient of yours!', 403 );
        }
        else {
            $this->data[CONTENT_STRING]['suicideItems'] = $this->Questionnaire_model->get_suicide_data($patientcode);
            $this->data[CONTENT_STRING]['riskColor'] = $this->Questionnaire_model->get_risk($patientcode);
        }
            
        $this->data[CONTENT_STRING]['patientcode'] = $patientcode;
        
        $this -> template -> set( TOP_NAV_STRING, 'all/top_nav', $this -> data[TOP_NAV_STRING] );
        $this -> template -> set(CONTENT_STRING, 'user/patient/feedback/'.$path.'/'.$page, $this->data[CONTENT_STRING]);
        $this -> template -> load('template');

    }//load()
}//class Feedback

/* End of file feedback.php */
/* Location: ./application/controllers/user/feedback.php */
?>