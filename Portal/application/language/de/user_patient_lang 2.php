<?php
// Überordner 
$lang['list_list1'] = 'Patientenliste';

// Navigation
$lang['list_overview'] = 'Meine Patientenübersicht';
$lang['list_list2'] = 'Patientenliste';
$lang['list_details'] = 'Patientendetails';

/*
*
* View: views\user\patient/list_all.php
*
*/

// Erklärung 
$lang['list_instruction_part1'] = 'Bitte auf den entsprechenden Therapiestatus 
klicken, um die Patientenlisten einzusehen. ';
$lang['list_instruction_part2'] = 'Anschließend können Sie dann den gewünschten 
Patientencode anklicken.';
$lang['list_patient_ex_instruction'] = 'Bitte auf den entsprechenden Übungsstatus 
klicken, um die Patientenlisten einzusehen. Anschließend können Sie dann den gewünschten 
Patientencode anklicken.';

// Laufend 
$lang['list_run'] = 'Laufend';
$lang['list_available'] = 'Verfügbar';
$lang['list_regularlyClosed'] = 'Regulär geschlossen';
$lang['list_aborted'] = 'Abgebrochen';
$lang['list_deactivated'] = 'Deaktiviert';
$lang['list_deletedByTherapist'] = 'Gelöscht vom Therapeuten';
$lang['list_code'] = 'Patientencode';
$lang['list_status'] = 'Therapiestatus';
$lang['list_date'] = 'Anmeldedatum';
$lang['list_last'] = 'Datum der letzten Sitzung';


// Abbruch
// Abbruchsfälle 
$lang['list_quit'] = 'Abgebrochen';
$lang['list_case1'] = 'Abbruch mit bewilligten Sitzungen';
$lang['list_case2'] = 'Abbruch in Probatorik';
$lang['list_case3'] = 'Abbruch in Probatorik durch Therapeut';
$lang['list_case4'] = 'Abbruch in Probatorik durch Patient';
$lang['list_case5'] = 'Abbruch mit bewilligten Sitzungen durch Therapeut';
$lang['list_case6'] = 'Abbruch mit bewilligten Sitzungen durch Patient';
$lang['list_case7'] = 'Abbruch aus formalen Gründen';


// Regulär beendet -->
$lang['list_normal'] = 'Regulär beendet';

// Unterbrechung -->
$lang['list_stop'] = 'Unterbrechung';


// Keine Datensätze vorhanden. -->
$lang['list_nodata'] = 'Keine Datensätze vorhanden.';

$lang['list_nopicture'] = 'Die Grafik konnte nicht erstellt werden.';

/*
*
* View: views\user\patient\details.php
*
*/


// Überordner
$lang['details_details'] = 'Patientendetails für ';

// 1. Spalte
$lang['details_details2'] = 'Patientendetails';

// 2. Spalte
$lang['details_question'] = 'Fragebögen';
$lang['details_exercise'] = 'Übungen';
$lang['details_even'] = 'Therapeuten mit ähnlichen Fällen';
$lang['details_comparison'] = 'Prä / Post Vergleich';

// eigentlicher Inhalt der Seite

$lang['details_statusreport'] = 'Statusreport';
$lang['details_nodata'] = 'Es wurden noch keine Daten erhoben.';
$lang['details_erhebung'] = 'Erhebung';
$lang['details_verlaufsreport'] = 'Verlaufsreport';
$lang['details_last'] = 'Letzte Erhebung';
$lang['details_verlauf'] = 'Verlauf';
$lang['details_verlauf_ot'] = 'Verlauf Online Therapie';
$lang['details_pr_po'] = 'PR-PO Vergleich';
$lang['details_feedback'] = 'Personalisierte Behandlungsanpassung';
$lang['details_date'] = 'Datum';
$lang['details_diagnostik'] = 'Personalisierte Behandlungsempfehlung';
$lang['details_diagnostik_button'] = 'Zum Tool';

$lang['details_ex_portal'] = 'Übungsportal';
$lang['details_ex_create'] = 'Übungen erstellen';
$lang['details_ex_manage'] = 'Übungen verwalten';

$lang['messages'] = 'Nachrichten';
$lang['admin_messages_patient'] = 'Nachricht an diesen Patienten';

//Feedback
$lang['details_fb_motivation'] = 'Motivation / Therapieziele';
$lang['details_fb_emotion'] = 'Emotionsregulation / Selbstregulation';
$lang['details_fb_relation'] = 'Therapeutische Beziehung';
$lang['details_fb_social'] = 'Soziale Unterstützung';
$lang['details_fb_life'] = 'Kritische Lebensereignisse';
$lang['details_fb_risk_suicide'] = 'Risiko / Suizidalität';
$lang['details_fb_risk'] = 'Risk-Tool';
$lang['details_fb_kongruenz'] = 'Kongruenz-Tool';
$lang['details_fb_socialLife'] = 'Soziale Unterstützung / Kritische Lebensereignisse';

$lang['details_notice'] = 'Hinweis';
$lang['details_not_allowed'] = 'Sie nicht berechtigt sich die Details des Patienten %s anzuschauen. <br/>
					Bitte kehren Sie zur Patientenliste ';
$lang['details_back'] = 'zurück';



/*
*
* View: views\user\patient\questionairs.php
*
*/

// Überschrift
$lang['questionairs_spezif'] = 'Spefizische Fragebögen für ';

// Navigation

$lang['questionairs_spezif1'] = 'Spefizische Fragebögen';

// Inhalt

$lang['questionairs_stoerung'] = 'Störungsspezifische Fragebögen freischalten';
$lang['questionairs_noquestionnaires'] = 'Es stehen noch keine störungsspezifische Fragebögen zur Verfügung.';
$lang['questionairs_questionnaires'] = 'Fragebögen';
$lang['questionairs_freigabe'] = 'Freigabe';
$lang['questionairs_save'] = 'Speichern';
$lang['questionairs_aktuellefrageboegen'] = 'Aktuell freigeschaltete störungsspezifische Fragebögen:';
$lang['questionairs_keinefrageboegen'] = 'Zur Zeit sind keine störungsspezifische Fragebögen freigeschaltet.';
$lang['questionairs_deaktivierung'] = 'Deaktivierung';
$lang['questionairs_deaktivieren'] = 'deaktivieren';

/*
*
* View: views\user\patient\nearest_neighbors.php
*
*/

// Überschrift
$lang['neighbors_ueberschrift'] = 'Nearest Neighbors von ';

// Navigation

$lang['neighbors_navigation'] = 'Therapeuten mit ähnlichen Fällen';

// Text
$lang['neighbors_text1'] = 'In der folgenden Tabelle werden Patienten angezeigt, die ebenfalls die Primärdiagnose ';
$lang['neighbors_text2'] = ' aufweisen und sich in Bezug auf Symptombelastung und Interpersonelle Probleme ähneln. In der grünen Zeile werden die Werte des ausgewählten Patienten angezeigt. ';

$lang['neighbors_nothing'] = 'Es konnten keine ähnliche Fälle gefunden werden.';

// Tabelle
$lang['neighbors_code'] = 'Code';
$lang['neighbors_therapeut'] = 'Therapeut';
$lang['neighbors_date'] = 'Datum erste Sitzung';
$lang['neighbors_bsi'] = 'BSI Gesamt';
$lang['neighbors_iip'] = 'IIP Gesamt';
$lang['neighbors_autokratisch'] = 'Autokratisch';
$lang['neighbors_streitsüchtig'] = 'Streitsüchtig';
$lang['neighbors_abweisend'] = 'Abweisend';
$lang['neighbors_introvertiert'] = 'Introvertiert';
$lang['neighbors_unterwürfig'] = 'Unterwürfig';
$lang['neighbors_ausnutzbar'] = 'Ausnutzbar';
$lang['neighbors_fürsorglich'] = 'Fürsorglich';
$lang['neighbors_expressiv'] = 'Expressiv';

/*
*
* View: views\user\patient\process.php
*
*/

$lang['therapy_all'] = 'Alle anzeigen (standard)';
$lang['therapy_single_exclude'] = 'Einzeltherapie';
$lang['therapy_group'] = 'Gruppentherapie';
$lang['therapy_online'] = 'Onlinetherapie';
$lang['therapy_seminar'] = 'Seminartherapie';
$lang['no_therapy_graph_data'] = 'Mit dem eingestellten Filter wurden alle Daten gefiltert, weshalb kein Graph gezeichnet werden kann.';
