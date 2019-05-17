<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * An instance of the library includes all helpers for 
 * questionnaire process.
 * 
 * @package Questionnaire
 * @category Library
 * 
 * @author Martin Kock <code @ deeagle.de>
 */
class PF_Utils 
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // error_log( "[CONST] PF_utils");
    }//__construct()
    
    /**
     * Returns a scales item as array (assoc)
     * Data contains:
     * <code>
     * array( 'result', 'title', 'wtf', 'wtf2' );
     * </code>
     * 
     * @since 0.3.0
     * @access public 
     * @param int $bow_result The value of scale calc.
     * @param string $title The title of this scales item.
     * @param array[float] some values
     * @param array[float] some values
     * @param array[float] some values Comes with 0.5.0 and the IIP-32 bow (DEFAULT = NULL) @see Iip_32.php
     * @return array[mixed]
     * 
     * @todo Ask what is argument 3 and 4
     * 
     */
    public function get_scales_item( $bow_result, $title, $arg3, $arg4, $arg5 = NULL )
    {
        $data = array( 'result' => $bow_result,
                       'title'  => $title,
                       'wtf'    => $arg3,
                       'wtf2'   => $arg4,
                       'wtf3'   => $arg5
                      );
                      
        return $data;                        
    }//_get_scales_item()
    
    /**
     * Returns the calculated high items of a questionnaire.
     * <code>
     * array( 'title', 'value' );
     * </code>
     * 
     * @param array[mixed] $db_data The database data of the questionnaire.
     * @param array[mixed] $items The items of the questionnaire.
     * @return array[mixed] An array of high values, otherwise NULL. 
     */
    public function get_high_items( $db_data, $items )
    {
        $high_items = NULL;
        
        if( !isset( $db_data ) OR !isset( $items ) )
        {
            log_message( 'error', 'No data given.' );
        }//if
        else
        {
            if( !is_array( $db_data ) OR !is_array( $items ) )
            {
                log_message( 'error', "Given data isn't array." );
            }//if 
            else
            {
                //seems ok
                $bow = $db_data[0];
                // $high_items = array(); //here we go the way of php and new set new entries with array[] (--> so dask will be NULL without a value, else it a void array)
        
                foreach( $items as $item )
                {
                    if( (!is_null( $bow[$item[0]] ) ) AND ( $bow[$item[0]] >= $item[1] ) AND ( $bow[$item[0]] <= $item[2] ) )
                    {
                        $high_items[] = array( 'title' => $item[3], 'value' => $bow[$item[0]] );
                    }//else
                }//foreach
            }//else
        }//else
        
        return $high_items;
    }//get_high_values()
}//class PF_Utils