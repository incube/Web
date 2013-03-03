<?php
namespace Incube;
/** @author incubatio 
  * @depandancy Incube_Pattern_SingleTime_Abstract
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
use Incube\Base\Pattern\ASingleTime,
    Incube\Base\DataObject;
Class Session extends ASingleTime {

	/** @var DataObject */
	protected $_data;

	public static function start() {
		if(!session_start()) {
			throw new Exception("Session failed to start");
		}
	}

	/** @return Session */
	public final static function getInstance() {
		if(!self::$_used) {
			self::$_used = true;
			$c = __CLASS__;
			return new $c;
		}
        trigger_error("You can get the instance of $c only one time.", E_USER_ERROR);
	}

	protected function __construct() {
		$this->_data = DataObject::array_to_dataobject($_SESSION);	
	}

	/** @param string $key
	  * @param mixed $value */
	public function __set($key, $value){
		$_SESSION[$key] = $this->_data->set($key, $value);
	}
	
	/** @param string $key 
	  * @return string */
	public function __get($key) {
		return $this->_data->get($key);
	}

	/** @param string $key */
	public function destroy($key = null) {
		if($key) {
			//$this->_data = null;
			session_destroy();
		} else unset($_SESSION[$key]);

		//// If it's desired to kill the session, also delete the session cookie.
		//// Note: This will destroy the session, and not just the session data!
		//if (ini_get("session.use_cookies")) {
			//$params = session_get_cookie_params();
			//setcookie(session_name(), '', time() - 42000,
					//$params["path"], $params["domain"],
					//$params["secure"], $params["httponly"]
					//);
		//}

		// Finally, destroy the session.
	}
	
}
