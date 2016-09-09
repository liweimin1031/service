<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR. 'lib' . DIRECTORY_SEPARATOR.'bootstrap.php';
use Clms\Tools\PhpDao\Mongo\MongoDao;

/* ---------------------------------------------------------------
 Constant definition
 --------------------------------------------------------------- */
$version = 20151126;
if (version_compare(phpversion(), '5.4.0', '<')) {
    if(session_id() == '') {
        session_start();
    }
}
else
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}
$msg = new \stdClass();
$msg->success = false;
$msg->error = new \stdClass();


if(isset($_SESSION) && isset($_SESSION['version']) && $_SESSION['version'] == $version){

}else {
    session_unset();
}


/* ---------------------------------------------------------------
 Function definition
 --------------------------------------------------------------- */
if ( isset($_SERVER['PATH_INFO']) ) {
    switch ($_SERVER['PATH_INFO']) {
        case '/getRecentPost':
            $result = getRecentPost();
            $msg->success = true;
            $msg->data = $result;
            break;

        case '/getPostComment':
            break;
        default:
            break;
    }
}

if($msg->success ==false && get_object_vars($msg->error) == false){
    $msg->error->reason = 'Unknown server error';
}
echo json_encode($msg);
exit;


function getRecentPost(){
    $cursor = MongoDao::search('discusshk', array());
    $result = array();
    foreach ($cursor as $doc) {
        $doc = json_decode(json_encode($doc));
        unset($doc->_id);
        $result[]= json_decode(json_encode($doc));
    }
    return $result;

}