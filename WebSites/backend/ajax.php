<?php
header('Content-Type: text/json; charset=utf-8');
ini_set("default_charset", "UTF-8");
mb_internal_encoding("UTF-8");

// Include our configuration
$config = include('../config.php');

// Init session
session_start();

// create empty output array
$data = array();
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_REQUEST['resp'])) {
		@$response = json_decode(utf8_encode($_REQUEST['resp']), true)[0];
		@$ctrl = $response['ctrl'];
		@$func = $response['func'];
		@$parms = $response['parms'];

		if(!empty($ctrl) && mb_stripos($ctrl, "..") === false && !empty($func)) {
			$ctrl = basename(strtolower($ctrl), ".php");
			$func = basename(strtolower($func));

			$path = '../classes/ctrlLayer/'.$ctrl.'Ctrl.php';

			if (realpath($path)) {
				// Load Include files.
				require_once('../classes/util/connectionDB.php');

				// Connect to Mysql (MariaDB)
				$dbCtrl = new connectionDB();
				$conn = $dbCtrl->getConnection();

				// Load Include files.
				require_once($path);
				// Lav instans af klassen og afvikel funktionen med parameterne
				$theClass = ucfirst($ctrl)."Ctrl";
				if (class_exists($theClass)) {
					$theCtrl = $theClass::getInstance();

					foreach (get_class_methods($theCtrl) as $method) {
					    if (strtolower($method) != strtolower($func)) {
							continue;
					    }
						else {
							errorMsg('Ajax.php', '','Found func: '.$func.' as '.$method);
							$func = $method;
						}
					}

					if(method_exists($theClass, $func)) {
						@call_user_func_array(array($theCtrl, $func), $parms);
					}
					else {
						errorMsg('Ajax.php', $func, 'Did not find methode: '.$func.' on class: '. $theClass);
					}
				} else {
					errorMsg('Ajax.php', $func, 'Did not find class: '.$theClass);
				}
			} else { errorMsg($ctrl, $func, 'Controller fil findes ikke.'); }
		} else { errorMsg($ctrl, $func, 'Ikke valid eller manglende Ctrl eller Func parameter!'); }
	} else { errorMsg(null, null, 'Json ikke korrekt posted.'); }
} else { errorMsg(null, null, 'Request var ikke en HTTP POST.'); }

// Hvis ingen fejl, send OK status med tilbage
if(empty($data['errors'])) {
	$data['status'] = "OK";
}

// output data array as Json
echo json_encode($data, JSON_PRETTY_PRINT);

function errorMsg($ctrl, $func , $msg) {
	global $data;
	if(!isset($data['errors'])) { $data['errors'] = array(); }
	array_push($data['errors'], array( 'ERRCTRL' => $ctrl, 'ERRFUNC' => $func, 'ERRMSG' => $msg	));
}
?>