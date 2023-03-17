<?php 
error_reporting(0);
session_start();
//root path
 define('APPLICATION_PATH',realpath(dirname(__FILE__).'/../app'));

 //directory separator
 const DS = DIRECTORY_SEPARATOR;

 //open path config
 require APPLICATION_PATH. DS . "config" . DS . "config.php";

 //timezone

 require APPLICATION_PATH. DS . "timezone" . DS . "timezone.php";

 //database

require APPLICATION_PATH. DS . "database" . DS . "connection.php";

//css

require APPLICATION_PATH. DS . "links" . DS . "links.php";

 //page route
 function page($name, $def = ''){

    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $def;

 }

 //get page name
$getPage = page('page','home');

 //get model
$getModel = $config["PAGE"] . $getPage . DS . $getPage . ".php";

 //get view
$getView = $config["PAGE"]. $getPage . DS . $getPage . ".phtml";

//check page
if(file_exists($getModel)){

    require $getModel;
}

if(file_exists($getView)){

    require $getView;
}

?>