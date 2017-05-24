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
$config['db']['dbname'] = "firstblog";

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


$app->get('/', function (Request $request, Response $response) {
    //Crea Home con Lista post in ordine decrescente
    //$post_mapper = new PostMapper($this->db);
    //Preleva dati e chiama view con struttura home.phtml

    //Primo tentativo
    echo "Container Homepage richiede dati a classe <br>";
    $this->logger->addInfo("Home Container Caricato");

    //Connessione effettiva al DB
    $mapper = new PostMapper($this->db);

    //Richiama la classe che "chiede" i dati al DB
    $post = $mapper->getPosts();

    //Invia i dati alla view che crea la struttura della pagina
    $response = $this->view->render($response, "posts.phtml", ["posts" => $post, "router" => $this->router]);

    //Mostra la pagina
    return $response;
});


$app->get('/post/create', function (Request $request, Response $response) {
    //Crea pagina creazione post e invia dati

});

$app->post('/post/{id}/update', function (Request $request, Response $response) {
    //Invia dati form in DB. Funzione valida sia per nuovo post che per update

});

$app->get('/post/{id}', function (Request $request, Response $response) {
    //Visualizza dati DB post
    //Crea view posts.phtml
});





//Esegue app
$app->run();