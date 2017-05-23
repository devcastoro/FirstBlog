<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

//NameSpace
spl_autoload_register(function ($classname) {
    require ("../classes/" . $classname . ".php");
});

//Parametri di sviluppo
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

//Dati recuperabili in qualsiasi momento
$config['db']['host']   = "127.0.0.1";
$config['db']['user']   = "root";
$config['db']['pass']   = "";
$config['db']['dbname'] = "ticket";

//$app = new \Slim\App;
$app = new \Slim\App(["settings" => $config]);

//Container
$container = $app->getContainer();

$container['view'] = new \Slim\Views\PhpRenderer("../templates/");

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

//Connessione DB
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

//Ottiene i post del blog
$app->get('/', function (Request $request, Response $response) {
    echo "home";
});




//Esegue app
$app->run();