<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('HEADER_STRING','header');
define('TOP_NAV_STRING', 'top_nav');
define('CONTENT_STRING', 'content');
define('FOOTER_STRING', 'footer');
/**
 * An instance of this class represents the template engine.
 * It's called at all controllers to build one view out of some subviews.
 * 
 * @package Library
 * @category View 
 * 
 * @since 0.1.0
 * @access public
 * 
 * @author Martin Kock <code @deeagle.de>
 */
class Template 
{
    /**
     * Array of the view data.
     * 
     * @since 0.1.0
     * @access private
     * @var array[mixed] $views
     */
    private $views;
    
    /**
     * Instance of CodeIgniter
     * 
     * @since 0.1.0
     * @access private
     * @var CodeIgniter $ci
     */
    private $ci;

    /**
    * Constructor
    *
    * @since 0.1.0
    * @access public
    */
    public function __construct()
    {
        $this->ci =& get_instance();
    }//__construct()

    /**
     * Setter of view data.
     * 
     * @since 0.1.0
     * @access public
     * @param string $name The name of the subview.
     * @param string $view The name of the view.
     * @param array[mixed] $data The data to load into the view.
     */
    public function set($name, $view, $data)
    {
        $this->views[$name] = $this->ci->load->view($view, $data, true);
        return $this;
    }//set()

    /**
     * Loads the main view.
     * 
     * @param string $master_template Name of the view to load.
     */
    public function load($master_template)
    {
        $this->ci->load->view($master_template, $this->views);
    }//load()
}//class Template
/* Location: ./application/librairies/template.php */