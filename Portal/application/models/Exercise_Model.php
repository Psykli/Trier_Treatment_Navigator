<?php
if( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

/**
 * An instance of the class represents a exercise model.
 * It contains all functions for exercise management.
 *
 * @package Model
 *
 * @since 0.8.0
 * @access public
 *
 * @author Ruven Martin
 */
class Exercise_model extends CI_Model
{
	/** 
	 *
	 * Entspricht dem ISO-8601 Format 	
	 * Beispiel: 2005-08-14T16:13:03+00:00
	 * @since 0.8.0
	*/
	
	private $dateFormat = 'DATE_ISO8601';
	
	
    /**
     * Constructer
     * Init of the Psychoeq-Database-Connection.
     */
    public function __construct( )
    {
        $this -> default = $this -> load -> database( 'default', TRUE );
		$this -> load -> Model( 'Patient_model' );
		#$this -> load -> Model( 'Modul_model' );
		#$this -> load -> Model( 'Questionnaire_tool_model' );
		$this -> load -> helper('date');
        
        // ----------------- wird durch Migration der Datenbank ersetzt---------------------------------------
        // Liefert die Datenbankeinträge für die Kategorien, 
        //Einteilung der Kategorien und Eintrag der neuen Uebungen
        // $this -> load -> model( 'exercises/ex_kategorien_model' ); //DB Insert fuer ex_kategorien, ex_kategorien_hat, exercises
        // $this -> load -> model( 'exercises/adding_ex_model'); //DB Create fuer neue Uebungen
        
        
        //Migration der Datenbank
        // In der version(x) wird die Version der Migration angegeben. Siehe Migration Class (CI) 
        // Die Dateien zur Migration befinden sich in application/migration
        // verschoben in: Membership_model
        
        
	}//__construct()
	
	/**
     * Liefert alle Übungen (Hausaufgaben) zurück.
     * 
     * In der Tabelle psychoeq->patient_questionnaire
     * 
     * @access private
     * @since 0.7.0
     * @param $patientcode The code of a patient.
     */

}
?>