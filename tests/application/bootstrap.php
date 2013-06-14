<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
 
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));
 
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/models'),
    get_include_path(),
)));
 
/** Zend_Application */
require_once 'Zend/Application.php';
#include_once 'Op/Util.php';
#include_once 'Op/Debug/Console.php';


require_once 'Zend/Loader/Autoloader.php';
   
//Autoload Op_:
$loader=Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('Op_');
$loaderResource= new Zend_Loader_Autoloader_Resource(array(
	'namespace'=>'Op_',
	'basePath'=>'/var/www/cloud/data/zend/job/library/'
));
//Autoload Application_Model_*:
$loader->registerNamespace('Application_Model_');
$loaderResource= new Zend_Loader_Autoloader_Resource(array(
	'namespace'=>'Application_',
	'basePath'=>'/var/www/cloud/data/zend/job/application/'

));
$loaderResource->addResourceType('model', 'models', 'Model')
 	->addResourceType('model', 'models', 'Model')
	->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable');
//#
$adapter= new Zend_Db_Adapter_Pdo_Mysql(#
array(
    'host'     => '127.0.0.1',
    'username' => 'cloud',
    'password' => 'inkindiz',
    'dbname'   => 'job'
));
?>