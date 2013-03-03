<?php
namespace Incube\Web;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Router {

    /** @var array */
    protected $_directory_names =  array('view' => 'views', 'layout' => 'layouts', 'controller' => 'controllers');

    /** @var string */
    protected $_app_path;

    /** @var string */
    protected $_container_dir;
    
	/** @var string */
    protected $_base_url;
    
	/** @var array */
	// TODO: define default utls   
	protected $_urls = array(
			'images'        => 'static/images',
			'javascript'    => 'static/js',
			'styles'        => 'static/css',
			'files'         => 'static/selif',
			);
    
	/** Unified Resource names
	  * @var array */
	protected $urns;

    /** @param string $app_path
      * @param array $urns
      * @param array $options */
    public function __construct($app_path, array $urns, array $options = array()) {
        $this->init($options);
        $this->_app_path = $app_path;
        $this->_urns	 = $urns;


        //$this->_schemaParams = explode('/', $this->_URI->getScheme());
		//TOTHINK: if optional directory name are not define, we check the architecture dir exists ?, it exists we use this conf.
		//TOTHINK: Check if implementation of module is usefull
        if(array_key_exists('module', $urns)) {
            $this->_container_dir = $urns['module'];
            //$this->_names['module'] = $urns[$moduleDir];
        } else {
            $this->_container_dir = $this->_directory_names['controller'];
        }

        $this->_files['controller'] = $urns['controller'] . ucfirst('controller') . '.php';
        //$this->_view = $this->get_dirname('view') . DS . $this->_names['action'] . '.phtml';

    }

    /** @param string $options */
    protected function init(array $options) {
        foreach($options as $key => $option) {
            $this->{"_$key"} = $option;
        }
    }


    /** @param string $item
      * @return string */
    public function get_path($item) {
        $moduleDir = "";
        if($this->_container_dir == $this->get_dirname('module')) {
            $moduleDir = $this->_container_dir . $this->get_dirname('controller') . DS;
        }
        return $this->_app_path . DS . $moduleDir . $this->get_dirname($item);
    }

    /** @param string $key
      * @return string */
    public function get_dirname($key) {
        return array_key_exists($key, $this->_directory_names) ? $this->_directory_names[$key] : null;
    }

    /** @param string $item
     * @return string */
    public function get_file_path($item) {
       return $this->get_path($item) . DS . $this->_files[$item];
    }

    /** @param string $item
     * @return string */
    public function get_url($item = "") {
        $urlEnd = array_key_exists($item, $this->_urls) ? DS . $this->_urls[$item] :"";
        return $this->_base_url . $urlEnd;
    }

    /** @param string $url
     * @return string */
    public function set_base_url($url) {
        $this->_base_url = $url;
    }

    /** @param array $scheme_params
      * @param array $params
      * @return string */
    public function format_url(array $scheme_params = array(), array $params = array()) {
        $endUrl = array();
        foreach(array_keys($this->_urns) as $label) {
            $endUrl[] = array_key_exists($label, $scheme_params) ? $scheme_params[$label] : $this->_urns[$label];
        }
        foreach($params as $key => $param) {
            $endUrl[] = "$key/$param";
        }
        return '/' . implode('/', $endUrl);
    }
}
