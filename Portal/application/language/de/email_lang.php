<?php
defined('BASEPATH') OR exit('Kein direkter Skriptzugriff erlaubt');
$lang['email_must_be_array'] = 'Der E-Mail-Validierungsmethode muss ein Array übergeben werden.';
$lang['email_invalid_address'] = 'Ung&uuml;ltige E-Mail-Adresse: %s';
$lang['email_attachment_missing'] = 'Der Dateianhang %s wurde nicht gefunden.';
$lang['email_attachment_unreadable'] = 'Der Dateianhang %s konnte nicht ge&ouml;ffnet werden.';
$lang['email_no_recipients'] = 'Es muss mindestens ein Empf&auml;nger (To, Cc oder Bcc) angegeben werden.';
$lang['email_send_failure_phpmail'] = 'Der E-Mail-Versand &uuml;ber die PHP-Funktion mail() ist fehlgeschlagen. Der Server muss m&ouml;glicherweise für diese Methode konfiguriert werden.';
$lang['email_send_failure_sendmail'] = 'Der E-Mail-Versand &uuml;ber sendmail ist fehlgeschlagen. Der Server muss m&ouml;glicherweise für diese Methode konfiguriert werden.';
$lang['email_send_failure_smtp'] = 'Der E-Mail-Versand &uuml;ber SMTP ist fehlgeschlagen. Der Server muss m&ouml;glicherweise für diese Methode konfiguriert werden.';
$lang['email_sent'] = 'Ihre Nachricht wurde erfolgreich mit dem Protokoll %s versandt.';
$lang['email_no_socket'] = 'Es konnte kein Socket f&uuml;r Sendmail ge&ouml;ffnet werden. Bitte pr&uuml;fen Sie die Einstellungen.';
$lang['email_no_hostname'] = 'Es wurde kein SMTP-Hostname angegeben.';
$lang['email_smtp_error'] = 'Der SMTP-Fehler %s ist aufgetreten';
$lang['email_no_smtp_unpw'] = 'SMTP-Benutzername und -Passwort m&uuml;ssen angegeben werden.';
$lang['email_failed_smtp_login'] = 'Der AUTH LOGIN-Befehl konnte nicht gesendet werden. Fehler: %s';
$lang['email_smtp_auth_un'] = 'Der Benutzername ist nicht g&uuml;ltig. Fehler: %s';
$lang['email_smtp_auth_pw'] = 'Das Passwort ist nicht g&uuml;ltig. Fehler: %s';
$lang['email_smtp_data_failure'] = 'Die Daten konnten nicht versandt werden: %s';
$lang['email_exit_status'] = 'Abbruch mit Statuscode: %s';
/* End of file email_lang.php */
/* Location: ./system/language/german/email_lang.php */