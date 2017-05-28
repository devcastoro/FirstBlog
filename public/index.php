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

$config['db']['host']   = "127.0.0.1";
$config['db']['user']   = "root";
$config['db']['pass']   = "";
$config['db']['dbname'] = "firstblog";


//Inizializza App (???) -  Container e Views
$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer("../templates/");


//Container Logger
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

//Container Connessione DB
$container['db'] = function ($c) {
    $db = $c['settings']['db'];

//   $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $config['pass']);

    $pdo = new PDO('mysql:host=127.0.0.1;dbname=firstblog', 'root', '');

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
};


//Routes



//Homepage
$app->get('/', function (Request $request, Response $response) {


    $this->logger->addInfo("Genero Home");

    //Connessione effettiva al DB
    $mapper = new PostMapper($this->db);

    //Richiama la classe che "chiede" i dati al DB
    $posts = $mapper->getPosts();

    //Invia i dati alla view che crea la struttura della pagina
    $response = $this->view->render($response, "home.phtml", ["posts" => $posts, "router" => $this->router]);

    //Mostra la pagina
    return $response;
});


//Route Pagina Nuovo Post
$app->get('/post/create', function (Request $request, Response $response) {

    $this->logger->addInfo("Genero Pagina Creazione Post");

    //Connessione effettiva al DB
    $mapper = new PostMapper($this->db);

    //Richiama la classe che "chiede" i dati al DB
    $lastPostId = $mapper->getLastPostsID();


    $response = $this->view->render($response, "postadd.phtml", ["lastPostId" => $lastPostId, "router" => $this->router]);
    return $response;

});


$app->post('/post/create', function (Request $request, Response $response) {

    $this->logger->addInfo("Invio dati nuovo Post al server");



    $data = $request->getParsedBody();
    $post_data = [];
    $post_data['id'] = filter_var($data['id'], FILTER_SANITIZE_STRING);
    $post_data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $post_data['text'] = filter_var($data['text'], FILTER_SANITIZE_STRING);

//Controllo sullo stato
    if ($_POST['state'] == 'state'){$postState = 1;}
    else {$postState = 0;}
    $post_data['state'] = $postState;

    $post_data['datapost'] =  date_default_timezone_get();

    // work out the component

//    $component_id = (int)$data['component'];
//    $component_mapper = new ComponentMapper($this->db);
//    $component = $component_mapper->getComponentById($component_id);

   // $post_data['component'] = $component->getName();
    $post = new postEntity($post_data);
    $post_mapper = new postMapper($this->db);
    $post_mapper->save($post);
    $response = $response->withRedirect("/");


    return $response;
});

$app->post('/post/delete', function (Request $request, Response $response) {

    $this->logger->addInfo("Cancellazione Post");



//    $data = $request->getParsedBody();
//    $ticket_data = [];
//    $ticket_data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
//    $ticket_data['text'] = filter_var($data['text'], FILTER_SANITIZE_STRING);
//    $ticket_data['state'] = $data['description'] //isset($_POST['state'])
//    $ticket_data['date'] =  //date_default_timezone_get();
//
//    // work out the component
//    $component_id = (int)$data['component'];
//    $component_mapper = new ComponentMapper($this->db);
//    $component = $component_mapper->getComponentById($component_id);
//    $ticket_data['component'] = $component->getName();
//    $ticket = new TicketEntity($ticket_data);
//    $ticket_mapper = new TicketMapper($this->db);
//    $ticket_mapper->save($ticket);
//    $response = $response->withRedirect("/");
//    return $response;
});

$app->post('/post/{id}/update', function (Request $request, Response $response) {
    //Invia dati form in DB. Funzione valida sia per nuovo post che per update
    $this->logger->addInfo("Invio dati modifica Post al server");

});

$app->get('/post/{id}', function (Request $request, Response $response, $args) {
    //Visualizza dati DB post
    //Crea view home.phtml
    $this->logger->addInfo("Visualizzo dati specifico Post");

   // $id = $request->getAttribute( 'id');

    $post_id = (int)$args['id'];
    $mapper = new PostMapper($this->db);
    $post = $mapper->getPostById($post_id);
    $response = $this->view->render($response, "postdetail.phtml", ["post" => $post]);
    return $response;
})->setName('post-detail');









//Esegue app
$app->run();