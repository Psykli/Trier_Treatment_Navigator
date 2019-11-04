<?php
defined('BASEPATH') || exit('No direct script access allowed');
class Setup extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->data = array(HEADER_STRING => array('title' => 'Portal'),
                            TOP_NAV_STRING => array(),
                            CONTENT_STRING => array(),
                            FOOTER_STRING => array()
		);
		
    }
    
    public function step1(){
        //remove autoloading of models because they'd currently crash the setup process
        /* Commented out because it doesn't seem to be required anymore, the setup works fine with autoloaded models.
        $autoload_config = file('application/config/autoload.php');
        
        $autoload_config = array_map(function($autoload_config) {
            return stristr($autoload_config, '$autoload[\'model\'] = array(\'session_model\',\'membership_model\');') ? "\$autoload['model'] = array();\n" : $autoload_config;
        }, $autoload_config);
        
        file_put_contents('application/config/autoload.php', implode('', $autoload_config));
        */

        if(file_exists(FCPATH.'dist/js/out.js')){
			redirect('setup/step2');
			return;
		}
		$this->template->set(CONTENT_STRING, 'setup/step1', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }

    public function install_libs(){
        chdir(FCPATH);

        exec('yarn install');
        print('Installed Yarn');
        exec('npx webpack');
        print('All Done');
    }

    public function step2(){
        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
        $this->template->set(CONTENT_STRING, 'setup/step2', $this->data[CONTENT_STRING]);
        $this->template->load('template');

        try{
			$this->db->db_select();
		} catch( Exception $e){
			return;
		}
		catch(Error $e){
			return;
		}
        redirect('setup/step3');
    }

    public function save_database(){
        $database = $this->input->post('database');
        $user = $this->input->post('user');
        $password = $this->input->post('password');

        if(empty($database) || empty($user)){
            http_response_code(400);
            return;
        }

        $mysqli = mysqli_connect('localhost',$user,$password);

        if(mysqli_connect_errno()){
            http_response_code(400);
            return;
        }
               
        $sql = "CREATE DATABASE ".$database;
        mysqli_query($mysqli,$sql);

        $handle = fopen(FCPATH.'schema.sql','r');
        $data = fread($handle, filesize(FCPATH.'schema.sql'));
        fclose($handle);

        $mysqli = mysqli_connect('localhost',$user,$password,$database);
        if(mysqli_connect_errno()){
            http_response_code(400);
            return;
        }

        if(!mysqli_multi_query($mysqli,$data)){
            http_response_code(400);
            return;
        }

        $file = "<?php
        defined('BASEPATH') OR exit('No direct script access allowed');
        
        /*
        | -------------------------------------------------------------------
        | DATABASE CONNECTIVITY SETTINGS
        | -------------------------------------------------------------------
        | This file will contain the settings needed to access your database.
        |
        | For complete instructions please consult the 'Database Connection'
        | page of the User Guide.
        |
        | -------------------------------------------------------------------
        | EXPLANATION OF VARIABLES
        | -------------------------------------------------------------------
        |
        |	['dsn']      The full DSN string describe a connection to the database.
        |	['hostname'] The hostname of your database server.
        |	['username'] The username used to connect to the database
        |	['password'] The password used to connect to the database
        |	['database'] The name of the database you want to connect to
        |	['dbdriver'] The database driver. e.g.: mysqli.
        |			Currently supported:
        |				 cubrid, ibase, mssql, mysql, mysqli, oci8,
        |				 odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
        |	['dbprefix'] You can add an optional prefix, which will be added
        |				 to the table name when using the  Query Builder class
        |	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
        |	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
        |	['cache_on'] TRUE/FALSE - Enables/disables query caching
        |	['cachedir'] The path to the folder where cache files should be stored
        |	['char_set'] The character set used in communicating with the database
        |	['dbcollat'] The character collation used in communicating with the database
        |				 NOTE: For MySQL and MySQLi databases, this setting is only used
        | 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
        |				 (and in table creation queries made with DB Forge).
        | 				 There is an incompatibility in PHP with mysql_real_escape_string() which
        | 				 can make your site vulnerable to SQL injection if you are using a
        | 				 multi-byte character set and are running versions lower than these.
        | 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
        |	['swap_pre'] A default table prefix that should be swapped with the dbprefix
        |	['encrypt']  Whether or not to use an encrypted connection.
        |
        |			'mysql' (deprecated), 'sqlsrv' and 'pdo/sqlsrv' drivers accept TRUE/FALSE
        |			'mysqli' and 'pdo/mysql' drivers accept an array with the following options:
        |
        |				'ssl_key'    - Path to the private key file
        |				'ssl_cert'   - Path to the public key certificate file
        |				'ssl_ca'     - Path to the certificate authority file
        |				'ssl_capath' - Path to a directory containing trusted CA certificates in PEM format
        |				'ssl_cipher' - List of *allowed* ciphers to be used for the encryption, separated by colons (':')
        |				'ssl_verify' - TRUE/FALSE; Whether verify the server certificate or not ('mysqli' only)
        |
        |	['compress'] Whether or not to use client compression (MySQL only)
        |	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
        |							- good for ensuring strict SQL while developing
        |	['ssl_options']	Used to set various SSL options that can be used when making SSL connections.
        |	['failover'] array - A array with 0 or more data for connections if the main should fail.
        |	['save_queries'] TRUE/FALSE - Whether to save all executed queries.
        | 				NOTE: Disabling this will also effectively disable both
        | 				\$this->db->last_query() and profiling of DB queries.
        | 				When you run a query, with this setting set to TRUE (default),
        | 				CodeIgniter will store the SQL statement for debugging purposes.
        | 				However, this may cause high memory usage, especially if you run
        | 				a lot of SQL queries ... disable this to avoid that problem.
        |
        | The \$active_group variable lets you choose which connection group to
        | make active.  By default there is only one group (the 'default' group).
        |
        | The \$query_builder variables lets you determine whether or not to load
        | the query builder class.
        */
        
        \$active_group = 'default';
        \$query_builder = TRUE;
        
        \$db['default'] = array(
            'dsn'	=> '',
            'hostname' => 'localhost',
            'username' => '".$user."',
            'password' => '".$password."',
            'database' => '".$database."',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => (ENVIRONMENT !== 'production'),
            'cache_on' => FALSE,
            'cachedir' => '',
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci',
            'swap_pre' => '',
            'encrypt' => FALSE,
            'compress' => FALSE,
            'stricton' => FALSE,
            'failover' => array(),
            'save_queries' => TRUE
        ); ?>";

        file_put_contents(FCPATH.'application/config/database.php',$file);
    }

    public function step3(){
        $this->load->Model('membership_model');
        $admins = $this->membership_model->get_all_admin_codes();
		if(isset($admins)){
			redirect('setup/step4');
			return;
		}
        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
        $this->template->set(CONTENT_STRING, 'setup/step3', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }

    public function save_admin(){
        
        $this->load->Model('membership_model');
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        
        if(!$this->membership_model->create_admin($name,$email,$password)){
            http_response_code(400);
            return;
        }

    }

    public function step4(){
        //add autoloading of models again
        /* Commented out because it doesn't seem to be required anymore, the setup works fine with autoloaded models.
        $autoload_config = file('application/config/autoload.php');
        
        $autoload_config = array_map(function($autoload_config) {
            return stristr($autoload_config, '$autoload[\'model\'] = array();') ? "\$autoload['model'] = array('session_model','membership_model');\n" : $autoload_config;
        }, $autoload_config);
        
        file_put_contents('application/config/autoload.php', implode('', $autoload_config));
        */

        $this->template->set(HEADER_STRING, 'all/header', $this->data[HEADER_STRING]);
        $this->template->set(FOOTER_STRING, 'all/footer', $this->data[FOOTER_STRING]);
        $this->template->set(CONTENT_STRING, 'setup/step4', $this->data[CONTENT_STRING]);
        $this->template->load('template');
    }
}
?>