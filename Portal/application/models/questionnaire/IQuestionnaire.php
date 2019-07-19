<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The interface of questionnaires to specify the basic questionnaire functions.
 * 
 * @package Questionnaire
 * @category Interface
 * 
 * @since 0.3.0
 * 
 * @author Martin Kock <code @deeagle.de> 
 */
interface IQuestionnaire
{
    const BOW_ERROR = -9999;
    
    /**
     * Constructor.
     * 
     * @since 0.3.0
     * @access public 
     * @param PF_Utils $pf_utils
     * @param PF_Math $pf_math
     */
    public function __construct( $pf_utils, $pf_math );
    
    /**
     * Getter of database table name.
     * 
     * @since 0.3.0
     * @access public
     */
    public function get_db_table();
    
    /**
     * Getter of a questionnaire name.
     *
     * @since 0.3.0
     * @access public
     */
    public function get_name();
    
    /**
     * Getter of the displayer title of a questionnaire.
     *
     * @since 0.3.0
     * @access public
     */
    public function get_title();
    
    /**
     * Getter of the displayer description of a questionnaire.
     *
     * @since 0.3.0
     * @access public
     */
    public function get_description();
    
    /**
     * Returns the graph height for jpgraph lib.
     * 
     * @since 0.5.0
     * @access public
     */
    public function get_graph_height();
    
    /**
     * Getter of the complete questionnaire data of a questionnaire.
     *
     * @since 0.3.0
     * @access public
     * @param array[mixed] $db_data Database set.
     */
    public function get_patient_data( $db_data );
}//interface IQuestionnaire

/* End of file iquestionnaire.php */
/* Location: ./application/model/questionnaire/iquestionnaire.php */
?>

