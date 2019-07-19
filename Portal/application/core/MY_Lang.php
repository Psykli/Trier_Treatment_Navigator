<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

// Originaly CodeIgniter i18n library by J�r�me Jaglale
// http://maestric.com/en/doc/php/codeigniter_i18n
// modification by Yeb Reitsma

/*
in case you use it with the HMVC modular extension
uncomment this and remove the other lines
load the MX_Loader class */

//require APPPATH."third_party/MX/Lang.php";

//class MY_Lang extends MX_Lang {

class MY_Lang extends CI_Lang {


  /**************************************************
   configuration
  ***************************************************/

  // languages
  private $languages = array(
    'de' => 'german',
    'en' => 'english'
  );

  // special URIs (not localized)
  private $special = array (
    "admin"
  );

  // where to redirect if no language in URI
  private $uri;
  private $default_uri = 'portal';
  private $lang_code = 'de';

  /**************************************************/


  function __construct()
  {
    parent::__construct();
  }

  function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '', $load_first_lang = false) {
    $ci = &get_instance();
    $ci->load->library("session");
    if(empty($idiom)){
      if(array_key_exists('language',$_COOKIE)){
        $lang = $_COOKIE["language"];
      }
      $idiom = isset($lang) ? $lang : $this->lang_code;
    }

    parent::load($langfile,$idiom,$return,$add_suffix,$alt_path,$load_first_lang);
  }
} 

// END MY_Lang Class

/* End of file MY_Lang.php */
/* Location: ./application/core/MY_Lang.php */