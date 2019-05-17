<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The interface of questionnaires to specify the questionnaires process functions.
 * 
 * @package Questionnaire
 * @category Interface
 * 
 * @since 0.5.0
 * 
 * @author Martin Kock <code @deeagle.de> 
 */
interface IProcess
{
    /**
     * Constructor.
     * 
     * @since 0.5.0
     * @access public 
     * @param PF_Utils $pf_utils
     * @param PF_Math $pf_math
     */
//    public function __construct( $pf_utils, $pf_math );
    
    /**
     * Getter of database table name.
     * 
     * @since 0.5.0
     * @access public
     */
//    public function get_db_table();
    
    /**
     * Getter of database table name for process.
     * 
     * @since 0.5.0
     * @access public
     */
     public function get_db_process_table();
     
    
    /**
     * Getter of a questionnaire name.
     *
     * @since 0.5.0
     * @access public
     */
//    public function get_name();
    
    /**
     * Getter of the displayer title of a questionnaire.
     *
     * @since 0.5.0
     * @access public
     */
//    public function get_title();
    
    /**
     * Getter of the displayer description of a questionnaire.
     *
     * @since 0.5.0
     * @access public
     */
//    public function get_description();
    
    /**
     * Returns the data for therapy process to given db_data.
     * 
     * @since 0.5.0
     * @access public
     */
    public function get_process_data( $db_data, $db_data2 = NULL );
}//interface IQuestionnaire

/* End of file IProcess.php */
/* Location: ./application/model/questionnaire/IProcess.php */
?>
