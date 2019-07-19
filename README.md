# Trier_Treatment_Navigator (TTN)

## Verwendete Ressourcen
* PHP-Framework: [Codeigniter](https://codeigniter.com/) (Version 2.1.3)
* CSS-Framework: [Bootstrap](http://holdirbootstrap.de/)(Version 3)
* User-Tracking: [Piwik](https://piwik.org/)
* Diverse Javascript Bibliotheken: siehe `application/js`

## Installationsverfahren

### Hinweis
Das Portal befindet sich zur Zeit noch in der laufenden Entwicklung, deshalb nicht im Produktivbetrieb einsetzen.

Das Feedbackportal wurde auf einem Windows Server 2012 R2 (Apache 2.4) gestestet sowie auf Windows 7 (XAMPP für Windows 5.6.31).

### Vorraussetzungen
* Webserver (z.B. Apache v.2.4+)
* PHP >= 7
* MySQL-Server oder MariaDB-Server
* R 3.2+
* OpenSSL >= 0.9.8 for the [PHP OpenSSL functions](https://www.php.net/manual/en/openssl.requirements.php)
* optional: phpMyAdmin

### Anpassungen
* in der nun vorhandenen php.ini müssen folgende Extension hinzugefügt werden
    * `extension=php_mbstring.dll`
    * `extension=php_mcrypt.dll`
    * `extension=php_mysql.dll`
    * `extension=php_mysqli.dll`
    * `extension=php_xsl.dll`
* Die Extension „php_mcrypt.dll“ muss zudem noch heruntergeladen werden und z.B. in C:\php\ext eingefügt werden.

### Installation
* Entpacken des ZIP oder TAR Archives
* Verschieben des entpackten Ordners in Webserver-Verzeichnis (Windows: htdocs)
* Erstellen der Datenbanken portal und piwik
* Konfiguieren der Dateien  `\application\config\database.php` und `\application\config\config.php`

### Änderungen in config.php
* R-Pfad hinzufügen

```
/*
|--------------------------------------------------------------------------
| Base Path R
|--------------------------------------------------------------------------
|
| Hier wird der Pfad zu den R-Skripten festgelegt. 
| Alle R Skripte muessen sich in diesem Ordner befinden.
|
|
*/
$config['r_path'] = 'R/bin/x64';
```

### Änderungen in database.php

Hierbei handelt es sich um die Minimalkonfiguration. Bei `$db['portal']['password'] = '';` sollte ein sicheres Passwort eingesetzt werden, welches dem Passwort entspricht, das bei dem SQL-Server verwendet wird. 

```
$active_group = 'portal';
$active_record = TRUE;

$db['psychoeq']['hostname'] = '127.0.0.1';
$db['psychoeq']['username'] = 'USERNAME';
$db['psychoeq']['password'] = 'PASSWORD';
$db['psychoeq']['database'] = 'portal';
$db['psychoeq']['dbdriver'] = 'mysql';
$db['psychoeq']['dbprefix'] = '';
$db['psychoeq']['pconnect'] = FALSE;
$db['psychoeq']['db_debug'] = TRUE;
$db['psychoeq']['cache_on'] = FALSE;
$db['psychoeq']['cachedir'] = '';
$db['psychoeq']['char_set'] = 'utf8';
$db['psychoeq']['dbcollat'] = 'utf8_general_ci';
$db['psychoeq']['swap_pre'] = '';
$db['psychoeq']['autoinit'] = TRUE;
$db['psychoeq']['stricton'] = FALSE;
```

### Importieren des Datenbankschemas

Die Datei schema.sql muss als Datenbank `portal` angelegt werden. Dazu in mysql:

```
create database portal;
use database portal;
source schema.sql
```

Das Gleiche auch muss auch für die piwik Datenbank erfolgen. Wenn diese nicht benötigt wird den Eintrag aus `database.php` entfernen und `Piwik_model.php` löschen.