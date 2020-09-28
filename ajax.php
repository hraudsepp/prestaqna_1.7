<?php 
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

require_once(dirname(__FILE__).'/prestaqna.php');
$request = PrestaQnA::sendRequest($_POST);

exit($request);