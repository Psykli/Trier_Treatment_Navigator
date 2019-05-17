<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

require_once 'questionnaire/ArrayList.php';
require_once 'questionnaire/PF_Math.php';
require_once 'questionnaire/PF_Utils.php';

class Questionnaire_model extends CI_Model
{
    
    private $psyeq_fep2 = 'fep-2';

    public function __construct()
    {
        $this-> pf_utils = new PF_Utils();
        $this-> pf_math = new PF_Math();

        //Verlauf 
        $this-> process_questionnaires = new ArrayList();
    }



    /**
     * Get all counts for the instances in FEP2.
     * 
     * <code>
     *  array( 'instance', 'count' );
     * </code>
     * 
     * @since 0.6.0
     * @access public
     * @return array[mixed] of all FEP2 counts, otherwise NULL.
     */
    public function get_fep2_count()
    {
        $instances = array( 'WZ', 'PR', 'Z05', 'Z10', 'Z15', 'Z20', 'Z25', 'Z30', 'Z35', 'Z40', 'Z45', 'Z50', 'Z55', 'Z60', 'Z65', 'Z70', 'Z75', 'PO', 'K1', 'K2', 'K3' );
        $data = NULL;
        foreach( $instances as $instance )
        {
            $count = $this->_get_fep2_count_for_instance($instance);
            $data[] = array( 'instance' => $instance, 'count' => $count );
        }//foreach
        
        return $data;
    }//get_fep2_count()

    /**
     * Get the count of an fep2-instance.
     * 
     * @since 0.6.0
     * @access public
     * @return integer count of FEP2, otherwise -1.
     */
    private function _get_fep2_count_for_instance( $instance )
    {
        $count = -1;
        //FIX no fix for CI 2.1.3
        //it's a crappy mixture of doing safe prepared_statements.
        $sql = "SELECT COUNT('code') as count
                FROM `$this->psyeq_fep2`
                WHERE instance=?";
        $query = $this->db->query( $sql, array( $instance ) );
        
        if( $query -> num_rows( ) > 0 )
        {
            $tmp = $query ->result_array();
            $count = $tmp[0]['count'];
        }//if
        
        return $count;
    }//_get_fep2_count_for_instance() 

    public function get_process_of_questionnaire($username, $patientcode ,$quest_name){
        $process_index = $this->find_process($quest_name);
        return $this->_get_process_data($username,$patientcode,$quest_name,$process_index);
    }

    private function find_process($quest_name){
        for( $i = 0; $i < $this -> process_questionnaires -> size( ); $i++ )
            {
                $name = $this -> process_questionnaires -> get( $i ) -> get_name( );
                if($name == $quest_name){
                    return $i;
                }
            }
    }

    public function get_data( $username, $patientcode, $instance, $table_name = null )
    {
        $data = NULL;

        if( isset( $username ) AND isset( $patientcode ) AND isset( $instance ) )
        {
            if(!isset($table_name)){
            //collect the output-data
                for( $i = 0; $i < $this -> _questionnaires -> size( ); $i++ )
                {
                    //get the databasetablename
                    $psychoeq_table = $this -> _questionnaires -> get( $i ) -> get_psychoeq_table( );
                    //get the db_data
                    $db_result = $this -> Patient_model -> get_questionnaire_data( $username, $patientcode, $psychoeq_table, $instance );
                    //perform the results (scales, ...)
                    $tmp = $this -> _questionnaires -> get( $i ) -> get_patient_data( $db_result );
                    $current_quest_name =  $this -> _questionnaires -> get( $i ) -> get_name( );

                    //if bow is IIP-32, use an other graph
                    if( $this -> _questionnaires -> get( $i ) -> get_name( ) == 'iip32' )
                    {	
                        $graph = array( 'graph' => $this -> jpgraph -> iip32_graph( $username, $instance, $tmp['name'], $tmp['graph_height'], $tmp['scales'], $tmp['title'] ) );
                        $graph2 = array( 'graph2' => $this -> jpgraph -> status_graph( $username, $instance, $tmp['name'], 800, $tmp['scales'], $tmp['title'] ) );
                    }//if
                    elseif ( $current_quest_name == 'hag-s' || $current_quest_name == 'hag-f')
                    {
                        $graph = array( 'graph' => $this -> jpgraph -> status_graph_haq( $username, $instance, $tmp['name'], $tmp['graph_height'], $tmp['scales'], $tmp['title'], 800 ) );
                    }
                    else 
                    {
                        $graph = array( 'graph' => $this -> jpgraph -> status_graph( $username, $instance, $tmp['name'], $tmp['graph_height'], $tmp['scales'], $tmp['title'] ) );
                    }//else

                    if( !is_null( $tmp ) )
                    {			
                        $data[$i] = array_merge( $tmp, $graph );
                    }//if
                }//foreach
            } else {
                
                    $i = $this-> find_questionnaire_index_by_name($table_name);
                    
                    if(isset($i)){
                        //get the databasetablename
                        $psychoeq_table = $this -> _questionnaires -> get( $i ) -> get_psychoeq_table( );
                        //get the db_data
                        $db_result = $this -> Patient_model -> get_questionnaire_data( $username, $patientcode, $psychoeq_table, $instance );
                        //perform the results (scales, ...)
                        
                        $tmp = $this -> _questionnaires -> get( $i ) -> get_patient_data( $db_result );
                        $current_quest_name =  $this -> _questionnaires -> get( $i ) -> get_name( );

                        //if bow is IIP-32, use an other graph
                        if( $this -> _questionnaires -> get( $i ) -> get_name( ) == 'iip32' )
                        {	
                            $graph = array( 'graph' => $this -> jpgraph -> iip32_graph( $username, $instance, $tmp['name'], $tmp['graph_height'], $tmp['scales'], $tmp['title'] ) );
                            $graph2 = array( 'graph2' => $this -> jpgraph -> status_graph( $username, $instance, $tmp['name'], 800, $tmp['scales'], $tmp['title'] ) );
                        }//if
                        elseif ( $current_quest_name == 'hag-s' || $current_quest_name == 'hag-f')
                        {
                            $graph = array( 'graph' => $this -> jpgraph -> status_graph_haq( $username, $instance, $tmp['name'], $tmp['graph_height'], $tmp['scales'], $tmp['title'], 800 ) );
                        }
                        else 
                        {
                            $graph = array( 'graph' => $this -> jpgraph -> status_graph( $username, $instance, $tmp['name'], $tmp['graph_height'], $tmp['scales'], $tmp['title'] ) );
                        }//else

                        if( !is_null( $tmp ) )
                        {			
                            $data = array_merge( $tmp, $graph );
                        }//if
                    }
            }

            switch( $instance )
            {
                case 'WZ':
                case 'PR':
                case 'PO':
                case 'K3':
                    $data['medi'] = $this -> medec_model -> get_medi( $patientcode, $instance );
                    break;
                default:
                    $data['medi'] = NULL;
            }//switch
        }//if
        return $data;
    }//get_data()
        /**
     * Returns all data of a patient and the process of therapy.
     *
     * Combines the different process data graphs:
     * <ul>
     *  <li>< 1078</li>
     *  <li>>= 1078</li>
     * </ul>
     * 
     * The data comes as assoc array, build for the associated JPGraph!
     * For Example:
     * <code>
     *  array( 'name', 'title', 'means[]', 'instances[]', 'desc', 'graph' )
     *  // or
     *  array( 'name', 'title', 'means['patient|therapeut]', 'instances['patient|therapeut], 'desc', 'graph' )
     * </code>
     * 
     * @since 0.5.0
     * @access public
     *
     * @param string $username initials of the user.
     * @param string $patientcode Patientcode of the patient.
     * @return array of the data for patient in instance, otherwithe NULL.
     */
    public function get_process_data($username, $patientcode, $therapy_type = NULL)
    {
        $data = NULL;

        if (isset($username) AND isset($patientcode))
        {
            $sbtype = 1;
            
            if (substr($patientcode, 0, 4) < 1078)
            {$sbtype = 2;}
            if (isset($therapy_type))
            {$sbtype = 3;}

            //collect the output-data
            for( $i = 0; $i < $this -> process_questionnaires -> size( ); $i++ )
            {
                $name = $this -> process_questionnaires -> get( $i ) -> get_name( );

                switch($sbtype)
                {
                    case 2:
                        switch( $name )
                        {
                            case 'hscl-11':
                            case 'fep-2':
                            case 'asq':
                            case 'ink-10':
                            case 'step-b':
                            case 'step-k':
                            case 'step-p':
                            case 'gad-7':
                            case 'phq-9':
                            case 'gas':
                                $tmp = $this -> _fetch_process_data( $username, $patientcode, $name, $i );
                                break;
                            default:
                                $tmp = NULL;
                        }//switch
                        break;
                    case 3:
                        $tmp = $this -> _get_therapy_specific_process( $username, $patientcode, $name, $i,$therapy_type );
                        break;
                    default:
                        switch( $name )
                        {
                            case 'hscl-11':
                            case 'fep-2':
                            case 'asq':
                            case 'ink-10':
                            case 'tsb-mk':
                            case 'tsb-pa':
                            case 'tsb-pb':
                            case 'tsb-ra':
                            case 'tsb-tb':
                            case 'tsb-ee1':
                            case 'tsb-ee2':
                            case 'tsb-ee3':
                            case 'tsb-ee4':
                            case 'tsb-ee5':
                            case 'tsb-ee6':
                            case 'tsb-ee7':
                            case 'tsb-wb':
                            case 'tsb-pbmo':
                            case 'gas':
                            case 'phq-9':
                            case 'gad-7':
								$tmp = $this -> _fetch_process_data( $username, $patientcode, $name, $i );
								break;
                            default:
                                $tmp = NULL;
                        }//switch
                }//switch

                if( !is_null( $tmp ) )
                {
                        $data[] = $tmp;
                }//if
            }//for    

        }//if
        return $data;
    }//get_process_data()

    /*
    * Replaces old _get_process_data
    * 
    *
    */
    private function _fetch_process_data($username, $patientcode, $name, $list_pos)
    {
        $returnedData = null;
        
        //get the first database tablename
        $db_table = $this-> process_questionaires -> get($list_pos) -> get_db_table();
        
        //get the second database tablename if one exists (some have two) otherwise set null by get_db_process_table()'s returnvalue
        // TEMPORÄRER WORKAROUND für HSCL-11, da BSI nicht mehr in Tabellenpool !!!! - WIRD ERSETZT 
        if ($name == 'hscl-11') {
            $db_table2 = NULL;
        } else {
            $db_table2 = $this->  process_questionaires -> get($list_pos) -> get_db_process_table();
        }

        //fetch data from DB
        $db_result = $this-> Patient_Model -> fetch_bow_data($username, $patientcode, $db_table);
        $db_result2 = $this-> Patient_Model -> fetch_bow_data($username, $patientcode, $db_table2);
        
        //perform the results(Scales, ...)
        $tmp = $this-> process_questionnaires -> get($list_pos) -> get_process_data($db_result, $db_result2);

        if (!is_null($tmp))
        {
            $tmp['desc'] = $this-> process_questionnaires -> get($list_pos) -> get_description();

            $counter = 0;

            for( $i=0; $i < sizeof( $tmp['means'] ); $i++ )
            {
                if( !is_null( $tmp['means'][$i] ) ) {
                    $counter++;
                }
            }    

            //alter DebugCase - Uebergibt daten asl Tabellen
            if( !is_null( $tmp ))
            {
                $tables = array();
                $tables['table1'] = $db_table;
                $tables['table2'] = $db_table2;
                $returnedData = array_merge( $tmp,$tables );
                log_message( 'debug', "ADD process data: " . $tmp['name'] );
            }//if
            
            // TODO: Hier statt Graph zu erzeugen Array für Chart.js-Funktionen aus dem View bereitstellen!
            // Schon mit altem DebugCase ausreichend?
        }

        return $returnedData;
    }

    public function get_distro_percentil( $ScaleResult, $ScaleTitle, $QName, $instance )
    {
        $data = NULL;		
        $title = $QName;
        
        $this->db->db_select();
        $sql = 'SELECT * FROM `distribution_percentile` WHERE messzeitpunkt = "PR" AND skala = "'.$ScaleTitle.'" AND fragebogen = "'.$QName.'" ORDER BY id DESC LIMIT 1';
        $query = $this -> db -> query($sql);

        if( $query ){
            $data = $query -> result( );
            $percentil = $this -> get_percentil($data, $ScaleResult);
            return $percentil;
        }
        else{
            return NULL;
        }
    }

    public function get_percentil($data, $ScaleResult)
    {
        $percentil = NULL;
        $tmp = [];
        $counter = 0;
        foreach($data[0] as $key => $value) {
            if ($counter > 5){
                array_push($tmp, $value);
            }
            $counter++;
        }

        for ($i = 0; $i < sizeof($tmp)-1; $i++){
            $min = floatval($tmp[$i]);
            $max = floatval($tmp[$i+1]);
            if ($ScaleResult >= $min && $ScaleResult < $max){
                $percentil = $i / 10;
                break;
            }
        }
        return $percentil;
    }

    //Gibt die Skalen eines Fragebogens zurück
    public function get_scales_of_questionnaire ($table_name) {
        $qid = $this->_get_id_of_questionnaire($table_name);

        $this -> db -> select ('*');
        $this -> db -> from ('questionnaire_scales');
        $this -> db -> where ('qid',$qid);

        $query = $this -> db -> get();

        if($query -> num_rows () >0) {
            return $query->result_array();
        }
    }

    //Gibt anhand des Tabellennamens die ID eines Fragebogens zurück
    private function _get_id_of_questionnaire ($table_name) {
        $id = 0;

        $this->db->select('id');
        $this->db->from('questionnaire');
        $this->db->where('table',$table_name);

        $query = $this -> db -> get();
        
        if( $query -> num_rows( ) == 1 ) {
            $id = $query->result_array()['0']['id'];
        }
        return $id;
    }

    //Liefert die Items einer Skala mit gegebener ID zurück
    public function get_items_of_scale($scaleId) {
        $this -> db -> select ('columnName');
        $this -> db -> from ('questionnaire_scales_hat');
        $this -> db -> where ('scaleId',$scaleId);
        
        $query = $this -> db -> get();
        //Ersetzung, damit der String direkt in einem Query verwendet werden kann
        //Der String entspricht den Columns, die zur gegebenen Skala gehören
        return  preg_replace('({"columnName":"|}|\[|\]|")','',json_encode($query->result_array()));
    }

    private function _scale_invert ($scaleId) {
        $this -> db -> select ('invert');
        $this -> db -> from ('questionnaire_scales');
        $this -> db -> where ('id',$scaleId);
        
        $query = $this -> db -> get();
        return $query->result_array()[0];
    }

    //Liefert die Werte für den Fragebogen zurück
    public function get_values($table_name,$scaleColumns,$instance,$patientcode,$sd,$mean,$scaleId) {
        $this -> db -> select ($scaleColumns);
        $this -> db -> from ($table_name);
        $this -> db -> where ('CODE',$patientcode);
        $this -> db -> where ('INSTANCE',$instance);

        $resArr = array();
        $query = $this -> db -> get();
        //Invertiert die Werte, sofern gewollt
        $appliedInversion = $this -> _invert_values($query->result_array()[0]);
        //Gibt den rohen Mittelwert zurück
        $raw = $this -> pf_math -> mean($appliedInversion);

        //Skaleninvertierung
        $scaleInv = $this -> _scale_invert($scaleId);
        if (intval($scaleInv['invert']) != 0) {
            $raw = intval($scaleInv['invert']) - $raw;
        }
        //Gibt den letztlich in der Graphik auftauchenden Wert zurück
        $result = $this -> pf_math -> pr_b($raw,$mean,$sd);
        $resArr['mean'] = $raw;
        $resArr['graphval'] = $result;
        return $resArr;
    }

    //liefert die Beschreibung des jeweiligen Fragebogens
    public function get_questionnaire_desc($table_name) {
        $this -> db -> select ('desc');
        $this -> db -> from ('questionnaire');
        $this -> db -> where ('table',$table_name);

        $query = $this -> db -> get();

        return $query->result_array()[0];
    }

    //Appliziert die Invertierung sofern vorhanden
    private function _invert_values($items) {
        $resArr = array();
        foreach ($items as $key => $value) {
            $this -> db -> select ('invert');
            $this -> db -> from ('questionnaire_items');
            $this -> db -> where ('columnName',$key);
            
            $query = $this -> db -> get();
            if ($query->result_array()[0]['invert'] != 0) {
                $resArr[] = $query->result_array()[0]['invert']-$value;
            } else {
                $resArr[] = $value;
            }
        }
        return $resArr;
    }

    public function get_all_items_for_questionnaire($table_name,$patientcode,$instance) {
        $qid = $this -> _get_id_of_questionnaire($table_name);

        $this -> db -> select ('columnName');
        $this -> db -> from ('questionnaire_items');
        $this -> db -> where ('qid',$qid);
        $this -> db -> order_by('columnName');
        $query = $this -> db -> get();
        //Ersetzung, damit der String direkt in einem Query verwendet werden kann
        //Der String entspricht den Columns, die zur gegebenen Skala gehören
        $scaleColumns = preg_replace('({"columnName":"|}|\[|\]|")','',json_encode($query->result_array()));

        $this -> db -> select ($scaleColumns);
        $this -> db -> from ($table_name);
        $this -> db -> where ('CODE',$patientcode);
        $this -> db -> where ('INSTANCE',$instance);
        $query = $this -> db -> get();

        return $query->result_array()[0];
    }

    //Gibt die Items für die Suizidgraphen zurück (Item 9 (phq) && Item 10 (hscl))
    //Falls keine Instanz spezifiziert ist, wird der jeweils aktuellste Wert zurückgegeben
    public function get_suicide_data($patientcode,$instance = null) {

        if ($instance == null) {
            $this -> db -> select ('PHQ009');
            $this -> db -> from ('phq-9');
            $this -> db -> where ('CODE',$patientcode);
            $this -> db -> order_by ('INSTANCE DESC');
            $query = $this -> db -> get ();

            $result[] = $query->result_array()[0];

            $this -> db -> select ('HSC010');
            $this -> db -> from ('hscl-11');
            $this -> db -> where ('CODE',$patientcode);
            $this -> db -> order_by ('INSTANCE DESC');
            $query = $this -> db -> get ();

            $result[] = $query->result_array()[0];
        } else {
            $this -> db -> select ('PHQ009');
            $this -> db -> from ('phq-9');
            $this -> db -> where ('CODE',$patientcode);
            $this -> db -> where ('INSTANCE',$instance);
            $query = $this -> db -> get ();

            $result[] = $query->result_array()[0];

            if (preg_match("/Z\d\d/",$instance)) {
                $this -> db -> select ('HSC010');
                $this -> db -> from ('hscl-11');
                $this -> db -> where ('CODE',$patientcode);
                $this -> db -> where ('INSTANCE',substr($instance,-2));
                $query = $this -> db -> get ();

                $result[] = $query->result_array()[0];
            }
        }
        return $result;
    }

    //Gibt die jeweiligen hohen Werte zum Vergleich zurück
    public function get_high($table_name) {
        $qid = $this -> _get_id_of_questionnaire($table_name);
        
        $this -> db -> select ('columnName,text,high_1,high_2');
        $this -> db -> from ('questionnaire_items');
        $this -> db -> where ('qid',$qid);
        $this -> db -> order_by ('columnName');
    
        $query = $this -> db -> get();

        return $query->result_array();
    }

    //Erstellt die Arrays, welche der JavaScript Funktion übergeben werden
    public function get_Statusdata($questionnaire) {
        $returnData = array();
        $graphdata = array(); 
        $graphlabel = array(); 
        $graphcol = array(); 
        $graphcut1 = array(); 
        $graphcut2 = array(); 
        $graphcut3 = array(); 
        $graphcut4 = array(); 
        $cutcol1 = array(); 
        $cutcol2 = array(); 
        $cutcol3 = array(); 
        $cutcol4 = array(); 
        foreach($questionnaire as $scale):
            $graphdata[] = $scale['result']['graphval'];
            $graphlabel[] = $scale['skala'];
            $graphcol[] = '#3e95cd';
            $graphcut1[] = round($scale['cutOff1']*1,2);
            $graphcut2[] = round($scale['cutOff2']-$scale['cutOff1'],2);
            $graphcut3[] = round($scale['cutOff3']-$scale['cutOff2'],2);
            $graphcut4[] = round(100 - $scale['cutOff3'],2);
            $cutcol1[] = '#cccccc';
            $cutcol2[] = '#999999';
            $cutcol3[] = '#666666';
            $cutcol4[] = '#333333';
        endforeach;
        $returnData[0] = $graphdata;
        $returnData[1] = $graphlabel;
        $returnData[2] = $graphcol;
        $returnData[3] = $graphcut1;
        $returnData[4] = $graphcut2;
        $returnData[5] = $graphcut3;
        $returnData[6] = $graphcut4;
        $returnData[7] = $cutcol1;
        $returnData[8] = $cutcol2;
        $returnData[9] = $cutcol3;
        $returnData[10] = $cutcol4;

        return $returnData;
    }

    //Drei Optionen: No Data, Grün, Rot
    //Werden übers Patienmodel aufgerufen
    public function get_risk ($patientcode) {
        $highCount = 0;
        $noDataCount = 0;

        //PHQ Item 9
        $phqItem = 'PHQ009';
        $highTreshold = 2;
        $this -> db -> select ($phqItem);
        $this -> db -> from ('phq-9');
        $this -> db -> where ('CODE',$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);

        $query = $this -> db -> get ();
        if( $query -> num_rows( ) == 0 ) {
            $noDataCount++;
        } elseif (array_values($query->result_array()[0])[0]>=$highTreshold) {
            $highCount++;
        }
        
        $hscItem = 'HSC010';
        $highTreshold = 3;
        $this -> db -> select ($hscItem);
        $this -> db -> from ('hscl-11');
        $this -> db -> where ('CODE',$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);

        $query = $this -> db -> get ();

        $testVAR = array_values($query->result_array()[0])[0];

        if( $query -> num_rows( ) == 0 ) {
            $noDataCount++;
        } elseif (array_values($query->result_array()[0])[0]>=$highTreshold) {
            $highCount++;
        }

        if ($highCount > 0 && $noDataCount == 0) {
            return "red";
        } elseif ($highCount == 0 && $noDataCount == 0) {
            return "green";
        } elseif ($noDataCount != 0) {
            return "missing";
        }
    }

    //WÄHLT LETZTEN EINTRAG AUS
    public function get_motivation ($patientcode) {
        $highCount = 0;
        $noDataCount = 0;
        //HAQ-S 8
        $highTreshold = 5;
        $hqsItem = 'HQS008';
        $this -> db -> select ($hqsItem);
        $this -> db -> from ('haq-s');
        $this -> db -> where ('CODE',$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);

        $query = $this -> db -> get ();
        if( $query -> num_rows( ) == 0 ) {
            $noDataCount++;
        } elseif (array_values($query->result_array()[0])[0]>=$highTreshold) {
            $highCount++;
        }

        if ($highCount > 0 && $noDataCount == 0) {
            return "red";
        } elseif ($highCount == 0 && $noDataCount == 0) {
            return "green";
        } elseif ($noDataCount != 0) {
            return "missing";
        }
    }

    public function get_bez ($patientcode) {
        $this -> db -> select ('HQS001,HQS002,HQS003,HQS004,HQS005,HQS006,HQS007,HQS008,HQS009,HQS010,HQS011');
        $this -> db -> from ('haq-s');
        $this -> db -> where ('CODE',$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);
        $query = $this -> db -> get ();
        //Wenn >= 4; dann high (soll langfristig durch einen ermittelten Cutoff ersetzt werden)
        $highTreshold = 4;

        //nochmal nachfragen
        //Falls eins hoch, dann alles hoch ? Falls ja, wäre Beziehung immer hoch, wenn auch Motivation hoch ist
        if ($this->pf_math->mean(array_values($query->result_array()[0])) < $highTreshold) {
            return "green";
        } elseif ($this->pf_math->mean(array_values($query->result_array()[0])) >= $highTreshold) {
            return "red";
        } elseif ($this->pf_math->mean(array_values($query->result_array()[0])) == null) {
            return "missing";
        }
    }

    public function get_soc_sup ($patientcode) {
        $this -> db -> select ('SOS001,SOS002,SOS003,SOS004,SOS005,SOS006,SOS007,SOS008,SOS009,SOS010,SOS011,SOS012');
        $this -> db -> from ('social_support');
        $this -> db -> where ('CODE', $patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);
        $query = $this -> db -> get ();

        //Wenn Wert niedriger als 2 -> high
        $highTreshold = 2;

        $highCount = 0;
        $noDataCount = 0;
        foreach (array_values($query->result_array()[0]) as $var) {
            if ($var <= $highTreshold) {
                $highCount++;
            } elseif( is_null( $var ) || !is_numeric( $var ) || ($var = $this -> bow_error) ) {
                $noDataCount++;
            }
        }

        if ($highCount > 0 && $noDataCount < 9) {
            return "red";
        } elseif ($highCount == 0 && $noDataCount < 9) {
            return "green";
        } elseif ($noDataCount > 9) {
            return "missing";
        }
    }

    public function get_life_events ($patientcode) {
        $this -> db -> select ("LEN001,LEN002,LEN003,LEN004,LEN005,LEN006,LEN007,LEN008,LEN009,LEN010,LEN011");
        $this -> db -> from ("life_events");
        $this -> db -> where ("CODE",$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);
        $query = $this -> db -> get ();

        //Es gibt nur 0 und 1
        $highTreshold = 1;

        $highCount = 0;
        $noDataCount = 0;
        foreach (array_values($query->result_array()[0]) as $var) {
            if ($var <= $highTreshold) {
                $highCount++;
            } elseif( is_null( $var ) || !is_numeric( $var ) || ($var = $this -> bow_error) ) {
                $noDataCount++;
            }
        }

        if ($highCount > 0 && $noDataCount < 8) {
            return "red";
        } elseif ($highCount == 0 && $noDataCount < 8) {
            return "green";
        } elseif ($noDataCount > 8) {
            return "missing";
        }
    }

    public function get_soc_sup_life ($patientcode) {
        $lifecolor = $this -> get_life_events($patientcode);
        $socsupcolor = $this -> get_soc_sup($patientcode);

        if ($lifecolor === "green" && $socsupcolor === "green") {
            return "green";
        } elseif ($lifecolor === "red" || $socsupcolor === "red") {
            return "red";
        } elseif ($lifecolor === "missing" && $socsupcolor === "missing") {
            return "missing";
        }

    }

    //FUNKTION FÜR HOHE WERTE IMPLEMENTIEREN, DA DIESE BEI SOCSUPLIFE UND EMOTIONSREGULATION EINZELN ANGEZEIGT WERDEN SOLLEN

    //ASQ!!! Gesamt und einzeln nach Subskalen 
    public function get_emo ($patientcode) {
        $suppress = $this -> get_suppress($patientcode);
        $adapt = $this -> get_adapt($patientcode);
        $accept = $this -> get_accept($patientcode);

        if ($suppress === "green" && $adapt === "green" && $accept === "green") {
            return "green";
        } elseif ($suppress === "red" || $adapt === "red" || $accept === "red") {
            return "red";
        } elseif ($suppress === "missing" && $adapt === "missing" && $accept === "missing") {
            return "missing";
        }
        
    }

    public function get_suppress ($patientcode) {
        //MEAN = 3,000
        $this -> db -> select ("ASQ001,ASQ002,ASQ005,ASQ009,ASQ010,ASQ013,ASQ015,ASQ018,ASQ020");
        $this -> db -> from ("asq");
        $this -> db -> where ("CODE",$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);
        $query = $this -> db -> get ();
        if (6-$this->pf_math->mean($this->_invert_values($query->result_array()[0])) < 4) {
            return "green";
        } elseif (6-$this->pf_math->mean($this->_invert_values($query->result_array()[0])) >= 2) {
            return "red";
        } elseif ($this->pf_math->mean($this->_invert_values($query->result_array()[0])) == null) {
            return "missing";
        }
    }

    public function get_adapt ($patientcode) {
        //MEAN = 2,484
        $this -> db -> select ("ASQ019,ASQ012,ASQ016,ASQ007,ASQ004");
        $this -> db -> from ("asq");
        $this -> db -> where ("CODE",$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);
        $query = $this -> db -> get ();
        if ($this->pf_math->mean($this->_invert_values($query->result_array()[0])) > 2) {
            return "green";
        } elseif ($this->pf_math->mean($this->_invert_values($query->result_array()[0])) <= 2) {
            return "red";
        } elseif ($this->pf_math->mean($this->_invert_values($query->result_array()[0])) == null) {
            return "missing";
        }
    }

    public function get_accept ($patientcode) {
        //MEAN = 2,951
        $this -> db -> select ("ASQ017,ASQ014,ASQ011,ASQ008,ASQ006,ASQ003");
        $this -> db -> from ("asq");
        $this -> db -> where ("CODE",$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);
        $query = $this -> db -> get ();
        if ($this->pf_math->mean($this->_invert_values($query->result_array()[0])) > 2) {
            return "green";
        } elseif ($this->pf_math->mean($this->_invert_values($query->result_array()[0])) <= 2) {
            return "red";
        } elseif ($this->pf_math->mean($this->_invert_values($query->result_array()[0])) == null) {
            return "missing";
        }
    }

    public function get_motivation_value_and_text ($patientcode) {
        $this -> db -> select ('HQS008');
        $this -> db -> from ('haq-s');
        $this -> db -> where ('CODE',$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);

        $query = $this -> db -> get ();
        $result[] = array_values($query->result_array()[0])[0];

        $this -> db -> select ('text');
        $this -> db -> from ('questionnaire_items');
        $this -> db -> where ('columnName','HQS008');

        $text = $this -> db -> get ();
        $result[] = array_values($text->result_array()[0])[0];

        return ($result);
    }

    public function get_alliance_values_and_texts ($patientcode) {
        $this -> db -> select ('HQS001,HQS002,HQS003,HQS004,HQS005,HQS006,HQS007,HQS008,HQS009,HQS010,HQS011');
        $this -> db -> from ('haq-s');
        $this -> db -> where ('CODE',$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);
        $vals = $this -> db -> get ();

        $result['values'] = array_values($vals->result_array()[0]);

        $this -> db -> select ('text');
        $this -> db -> from ('questionnaire_items');
        $this -> db -> where ('columnName LIKE','HQS%');
        $texts = $this -> db -> get();

        $temp = array();
        foreach($texts->result_array() as $sub) {
            $temp[] = array_values($sub)[0];
        }
        //$result[] = ($texts->result_array());
        $result['texts'] = $temp;

        return $result;
    }

    public function get_emotion_values_and_texts ($patientcode) {
        $this -> db -> select ("ASQ001,ASQ002,ASQ003,ASQ004,ASQ005,ASQ006,ASQ007,ASQ008,ASQ009,ASQ010,ASQ011,ASQ012,ASQ013,ASQ014,ASQ015,ASQ016,ASQ017,ASQ018,ASQ019,ASQ020");
        $this -> db -> from ("asq");
        $this -> db -> where ("CODE",$patientcode);
        $this -> db -> order_by ('INSTANCE DESC');
        $this -> db -> limit (1);
        $vals = $this -> db -> get ();

        $result['values'] = array_values($vals->result_array()[0]);

        $this -> db -> select ('text');
        $this -> db -> from ('questionnaire_items');
        $this -> db -> where ('columnName LIKE','ASQ%');
        $texts = $this -> db -> get();

        $temp = array();
        foreach($texts->result_array() as $sub) {
            $temp[] = array_values($sub)[0];
        }
        $result['texts'] = $temp;
        $supRES = array();
        $adaRES = array();
        $accRES = array();
        for ($i = 0; $i <= 20; $i++) {
            //suppress
            if (in_array($i, [0,1,4,8,9,12,14,17,19])) {
                $tempTwo[0] = $result['texts'][$i];
                $tempTwo[1] = $result['values'][$i];
                $supRES[] = $tempTwo;
            //adapt
            } elseif (in_array($i,[18,11,15,6,3])) {
                $tempTwo[0] = $result['texts'][$i];
                $tempTwo[1] = $result['values'][$i];
                $adaRES[] = $tempTwo;
            //accept
            } elseif (in_array($i,[16,7,5,13,2,10])) {
                $tempTwo[0] = $result['texts'][$i];
                $tempTwo[1] = $result['values'][$i];
                $accRES[] = $tempTwo;
            }
        }
        $output['unterdrücken'] = $supRES;
        $output['anpassen'] = $adaRES;
        $output['akzeptieren'] = $accRES;

        return $output;
    }

    //Gibt die Daten für den Verlaufsgraphen in richtiger Reihenfolge zurück
    public function get_hscl_process_data ($patientcode) {
        //green line
        $this -> db -> select ("EXPECTED_VALUE1,EXPECTED_VALUE2,EXPECTED_VALUE3,EXPECTED_VALUE4,EXPECTED_VALUE5,EXPECTED_VALUE6,EXPECTED_VALUE7,EXPECTED_VALUE8,EXPECTED_VALUE9,EXPECTED_VALUE10,EXPECTED_VALUE11,EXPECTED_VALUE12,EXPECTED_VALUE13,EXPECTED_VALUE14,EXPECTED_VALUE15,EXPECTED_VALUE16,EXPECTED_VALUE17,EXPECTED_VALUE18,EXPECTED_VALUE19,EXPECTED_VALUE20,EXPECTED_VALUE21,EXPECTED_VALUE22,EXPECTED_VALUE23,EXPECTED_VALUE24,EXPECTED_VALUE25,EXPECTED_VALUE26,EXPECTED_VALUE27,EXPECTED_VALUE28,EXPECTED_VALUE29,EXPECTED_VALUE30");
        $this -> db -> from ("entscheidungsregeln_hscl2");
        $this -> db -> where ("CODE", $patientcode);
        $expected = $this -> db -> get();

        //mean, red and instances
        $this -> db -> select ("INSTANCE, HSCL_MEAN, BOUNDARY_NEXT");
        $this -> db -> from ("entscheidungsregeln_hscl");
        $this -> db -> where ("CODE",$patientcode);
        $data = $this -> db -> get();

        //Umformung des Arrays in Array von Arrays und initialisierung der Felder mit null
        $i = 1;
        foreach (array_values($expected-> result_array()[0]) as $entry) {
            $temp['EXPECTED'] = floatval($entry);
            $temp['HSCL_MEAN'] = null;
            $temp['BOUNDARY'] = null;
            $temp['INSTANCE'] = $i;
            $i++;
            $result[] = $temp;
            unset($temp);
        }
        //Wegen späterer Wiederverwendung
        unset($expected);

        //Füllung des Subarrays über Index (entspricht Instanz-1); überschreibung von null, wenn keine Daten vorhanden
        foreach ($data->result_array() as $entry) {
            //speichert Instanz des aktuellen Eintrages
            $instance = intval($entry['INSTANCE']);
            //speichert die Daten für die jeweilige Instanz, zu der sie im Graphen eingezeichnet werden sollen
            $result[$instance-1]['HSCL_MEAN'] = floatval($entry['HSCL_MEAN']);
            $result[$instance]['BOUNDARY'] = floatval($entry['BOUNDARY_NEXT']);
        }
        //Restructure Array
        foreach ($result as $subarray) {
            $means[] = $subarray['HSCL_MEAN'];
            $expected[] = $subarray['EXPECTED'];
            $instances[] = $subarray['INSTANCE'];
            $boundaries[] = $subarray['BOUNDARY'];
        }
        unset($result);

        $result['INSTANCES'] = $instances;
        $result['BOUNDARIES'] = $boundaries;
        $result['EXPECTED'] = $expected;
        $result['MEANS'] = $means;

        return $result;
    }

    public function get_process_scales_data($patientcode){
        $this->db->from('questionnaire_process_scales');
        $this->db->order_by('name');

        $query = $this->db->get();
        $means = array();
        if($query->num_rows() > 0 ){
            $result = $query->result();
            foreach($result as $entry){
                $items = json_decode($entry->items);
                $item_invert = (array)json_decode($entry->item_invert);
                $res = $this->get_means_of_items($items,$item_invert,$entry->table_name,$patientcode);
                $means[$entry->name][$entry->table_name] = $res[$entry->table_name];
            }
        }

        return $means;
    }

    public function get_process_scales_info($name){
        $this->db->from('questionnaire_process_scales');
        $this->db->where('name',$name);

        $query = $this->db->get();

        if($query->num_rows() > 0){
            $result = $query->result();
            return $result;
        }

        return null;
    }

    private function get_means_of_items($items,$item_invert,$table,$patientcode){
        $pre = substr($items[0],0,3);
        $this->db->select(implode(',',$items).', INSTANCE, '.$pre.'DAT as date');
        $this->db->from($table);
        $this->db->where('CODE',$patientcode);
        $this->db->order_by('date');
        
        $query = $this->db->get();
        $means = array();
        if($query->num_rows() > 0){
            $result = $query->result();
            foreach($result as $entry){
                $instance = $entry->INSTANCE;
                $date = strtotime($entry->date);
                unset($entry->INSTANCE);
                unset($entry->date);
                $entry_list = array();
                foreach($entry as $key => $val){
                    if(array_key_exists($key,$item_invert)){
                        $entry_list[] = $this->pf_math->diff($item_invert[$key], $val);
                    } else {
                        $entry_list[] = $val;
                    }
                }
                $mean = $this->pf_math->mean($entry_list);
                if(!isset($means[$table])){
                    $means[$table] = array();
                }
                $means[$table][$instance] = array($date => $mean);
            }
        }

        return $means;
    }
}

?>