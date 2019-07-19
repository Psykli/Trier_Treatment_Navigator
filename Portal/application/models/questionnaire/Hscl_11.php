<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'IQuestionnaire.php';
require_once 'IProcess.php';
require_once 'PF_Math.php';
require_once 'PF_Utils.php';

/**
 * An instance of the class represents a special questionnaire.
 * It contains all functions to manage this special questionnaire.
 *
 * @package Questionnaire
 * @category Questionnaires
 *
 * @since 0.5.0
 * @access public
 *
 * @author Martin Kock <code @ deeagle.de>
 */
class Hscl_11 implements IProcess
{
    /**
     * Database tablename.
     *
     * @since 0.5.0
     * @access private
     * @var string
     */
    private $db_table = 'hscl-11';

    /**
     * Database process diffs tablename.
     *
     * @since 0.5.0
     * @access private
     * @var string
     */
    private $db_process_table = 'bsi';

    /**
     * Name of questionnaire.
     *
     * @since 0.5.0
     * @access private
     * @var string
     */
    private $name = 'hscl-11';

    /**
     * Title of questionnaire (display public).
     *
     * @since 0.5.0
     * @access private
     * @var string
     */
    private $title = 'HSCL11';

    /**
     * Description of questionnaire (display public).
     * Can contain html elements.
     *
     * @since 0.5.0
     * @access private
     * @var string
     */
    private $desc = '';
    private $desc_patient = 'Die <b>Hopkins Symptom Checkliste (HSCL)</b> ist ein Instrument zur Erfassung subjektiver Beeinträchtigung und gibt einen Überblick über den globalen Belastungsgrad.';

    /**
     * PF_Math helper object.
     *
     * @since 0.5.0
     * @access private
     * @var PF_Math
     */
    private $pf_math = NULL;

    /**
     * PF_Utils helper object.
     *
     * @since 0.5.0
     * @access private
     * @var PF_Utils
     */
    private $pf_utils = NULL;

    /**
     * Constructor.
     * Init of the FEP-2 questionnaire.
     *
     * @param PF_Utils $pf_utils Reference to object of PF_utils @see
     * PF_Utils.php
     * @param PF_Math $pf_math Reference to object of PF_Math @see PF_Math.php
     */
    public function __construct( $pf_utils, $pf_math )
    {
        $this -> pf_utils = $pf_utils;
        $this -> pf_math = $pf_math;
        $this->ci =& get_instance();
        $this -> db = $this -> ci-> load -> database( 'default', TRUE );
    }//__construct()

    /**
     * Returns the database tablename.
     *
     * @since 0.5.0
     * @access public
     * @return string Name of databasetable,
     */
    public function get_db_table( )
    {
        return $this -> db_table;
    }//get_db_table()

    /**
     * Returns the process database tablename.
     *
     * @since 0.5.0
     * @access public
     * @return string Name of database, otherwise NULL.
     */
    public function get_db_process_table( )
    {
        return $this -> db_process_table;
    }//get_db_process_table()

    /**
     * Returns the intern name of the questionnaire.
     *
     * @since 0.5.0
     * @access public
     * @return string Intern name of the questionnaire.
     */
    public function get_name( )
    {
        return $this -> name;
    }//get_name()

    /**
     * Returns the title of the questionnaire (for public).
     *
     * @since 0.5.0
     * @access public
     * @return string Title of the questionnaire.
     */
    public function get_title( )
    {
        return $this -> title;
    }//get_title()

    /**
     * Returns the description of the questionnaire (for public).
     * Can contain html elements.
     *
     * @since 0.5.0
     * @access public
     * @return string Description of the questionnaire.
     */
    public function get_description( )
    {
        return $this -> desc;
    }//get_description()
    
    public function get_description_patient( )
    {
        return $this -> $desc_patient;
    }//get_description_patient()

    /**
     * Returns the data for therapy process to given db_data.
     * Data contains:
     *
     * <code>
     * array( 'name', 'title', 'means', 'instances' );
     * </code>
     *
     * @since 0.5.0
     * @access public
     * @param array[mixed] $db_data Database set.
     * @param array[mixed] $db_data2 Database2 set (DEFAULT = NULL)
     * @return array of the patientdata to the questionnaire.
     */
    public function get_process_data( $db_data, $db_data2 = NULL )
    {
        $data = NULL;

        if( is_null( $db_data ) AND is_null( $db_data2 ) )
        {
            //sometimes 1st query is NULL in DB
        }//if
        else
        {

            if( !is_null( $db_data ) ){
                foreach( $db_data as $row )
                {
                    $means[] = $this -> pf_math -> mean( array( $row['HSC001'], $row['HSC002'], $row['HSC003'], $row['HSC004'], $row['HSC005'], $row['HSC006'], $row['HSC007'], $row['HSC008'], $row['HSC009'], $row['HSC010'], $row['HSC011'] ) );
                    $instances[] = $row['INSTANCE'];
                    $dates[] = $row['HSCDAT'];
                }//foreach
            }

            //three kinds of values for ???
            if( !is_null( $db_data2 ) )
            {
                foreach( $db_data2 as $row )
                {
                    $means[] = $this -> pf_math -> mean( array( $this -> pf_math -> bsi_to_hscl( $row['BSI019'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI001'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI038'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI045'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI025'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI035'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI017'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI016'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI018'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI009'] ), $this -> pf_math -> bsi_to_hscl( $row['BSI050'] ) ) );
                    $instances[] = $row['INSTANCE'];
                    $dates[] = $row['BSIDAT'];
                }//for
            }//if

            $patientcode = $row['CODE'];
            $boundaries = $this->get_hscl_boundaries($patientcode);
            $expected_values = $this->get_hscl_expected_values($patientcode);
            //$data = array( 'name' => $this -> name, 'title' => $this -> title, 'means' => $means, 'instances' => $instances );
            $data = array( 'name' => $this -> name, 'title' => $this -> title, 'desc_patient' => $this-> desc_patient, 'means' => $means, 'instances' => $instances, 'dates' => $dates, 'boundaries' => $boundaries, 'expected_values' => $expected_values );


        }//else
		//var_dump($data);
        return $data;
    }//get_process_data()

   private function get_hscl_boundaries($patientcode){
        
        $this -> db -> from( 'entscheidungsregeln_hscl' );
        $this -> db -> where('CODE',$patientcode);
        $this -> db -> order_by( 'INSTANCE', 'ASC' );

        $query = $this -> db -> get( );

        if( $query -> num_rows( ) > 0  ){
            $result = $query->result();
        }//if

        return $result;
    }

    private function get_hscl_expected_values($patientcode){
        $this -> db -> from( 'entscheidungsregeln_hscl2' );
        $this -> db -> where('CODE',$patientcode);

        $query = $this -> db -> get( );

        if( $query -> num_rows( ) > 0  ){
            $result = $query->result();
        }//if

        return $result[0];
    }
}//class Hscl_11

/* End of file Hsc_11.php */
/* Location: ./application/model/questionnaire/Hsc_11.php */
