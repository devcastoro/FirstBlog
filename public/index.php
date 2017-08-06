<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

// NameSpace
spl_autoload_register(function ($classname){
    require ("../classes/" . $classname . ".php");
});

// Development Parameters
$config['displayErrorDetails'] = true;

$config['db']['host']   = "mysql";
$config['db']['user']   = "root";
$config['db']['pass']   = "pwd";
$config['db']['dbname'] = "firstblog";

// App Initialization (Container/Views)
$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer("../templates/");

// Container Logger
$container['logger'] = function($c){
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

// DB Connection
$container['db'] = function ($c){
    $db = $c['settings']['db'];

    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
};

// Route Homepage
$app->get('/', function (Request $request, Response $response){
    // Insert important info into logger
    $this->logger->addInfo("Genero Home");

    // DB connection
    $mapper = new PostMapper($this->db);

    // Obtain data of all posts
    $posts = $mapper->getPosts();

    // Send post data to VIEW that create the page
    $response = $this->view->render($response, "home.phtml", ["posts" => $posts, "router" => $this->router]);

    return $response;
});

// Route New Post Page GET
$app->get('/post/create', function (Request $request, Response $response) {

    // Routine code
    $this->logger->addInfo("Genero Pagina Creazione Post");
    $mapper = new PostMapper($this->db);

    // Get last post ID
    $lastPostId = $mapper->getLastPostsID();

    // Manage exception
    if($lastPostId == null){
        echo "Errore: non esistono post precedenti";
        $this->logger->addInfo("Non è stato possibile recuperare lastPostId");
    }

    // Send post data to VIEW that create the page
    $response = $this->view->render($response, "postadd.phtml", ["lastPostId" => $lastPostId]);
    return $response;

});

// Route New Post Page POST
$app->post('/post/create', function (Request $request, Response $response) {

    // Routine code
    $this->logger->addInfo("Invio dati nuovo Post al server");
    $postMapper = new PostMapper($this->db);

    // Get FORM content data
    $data = $request->getParsedBody();

    // Set an array filled with FORM conent data
    $post_data = [];
    $post_data['id'] = filter_var($data['id'], FILTER_SANITIZE_STRING);
    $post_data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $post_data['text'] = filter_var($data['text'], FILTER_SANITIZE_STRING);
    // Checkbox State control
    $postState = $_POST['state'] == 'state' ? 1 : 0;
    $post_data['state'] = filter_var($postState, FILTER_SANITIZE_STRING);
    $post_data['datapost'] =  date('Y-m-d H:i:s');

    // Transform Array in a Post Entity
    $postEntity = new PostEntity($post_data);

    // Pass Post Entity to a class that save it into DB
    $postMapper->save($postEntity);

    // Redirect to Homepage
    $response = $response->withRedirect("/");
    return $response;
});

// Route Edit Post GET
$app->get('/post/{id}/update', function (Request $request, Response $response, $args) {

    // Routine code
    $this->logger->addInfo("Genero Pagina Modifica Post");
    $mapper = new PostMapper($this->db);

    // Get the variable in the link
    $post_id = (int)$args['id'];

    //Get the post entity
    $post = $mapper->getPostById($post_id);

    // Send post data to VIEW that create the page
    $response = $this->view->render($response, "postedit.phtml", ["post" =>$post]);
    return $response;
});

// Route Edit Post POST
$app->post('/post/{id}/update', function (Request $request, Response $response) {

    // Routine code
    $this->logger->addInfo("Invio dati  Post modificato al server");
    $postMapper = new PostMapper($this->db);

    // Get FORM content data
    $data = $request->getParsedBody();

    // Set an array filled with FORM conent data
    $post_data = [];
    $post_data['id'] = filter_var($data['id'], FILTER_SANITIZE_STRING);
    $post_data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $post_data['text'] = filter_var($data['text'], FILTER_SANITIZE_STRING);
    // Checkbox state control
    $postState = $_POST['state'] == 'state' ? 1 : 0;
    $post_data['state'] = filter_var($postState, FILTER_SANITIZE_STRING);
    $post_data['datapost'] =  date('Y-m-d H:i:s');

    // Transform Array in a Post Entity
    $post = new postEntity($post_data);

    // Pass Post Entity to a class that update it into DB
    $postMapper->update($post);

    // Redirect to Homepage
    $response = $response->withRedirect("/");
    return $response;
});

// Route Delete Post GET
$app->get('/post/{id}/delete', function (Request $request, Response $response, $args) {

    // Routine code
    $this->logger->addInfo("Cancellazione Post");
    $mapper = new PostMapper($this->db);

    // Get the variable in the link
    $post_id = (int)$args['id'];

    //Get the post entity
    $post = $mapper->getPostById($post_id);

    // Send post data to VIEW that create the page
    $response = $this->view->render($response, "postdelete.phtml", ["post" =>$post]);
    return $response;

});

// Route Delete Post POST
$app->post('/post/{id}/delete', function (Request $request, Response $response) {

    // Routine code
    $this->logger->addInfo("Invio dati  Post cancellato al server");
    $postMapper = new postMapper($this->db);

    // Get FORM content data
    $data = $request->getParsedBody();

    // Set an array filled with FORM conent data
    $post_data = [];
    $post_data['id'] = filter_var($data['id'], FILTER_SANITIZE_STRING);
    $post_data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $post_data['text'] = filter_var($data['text'], FILTER_SANITIZE_STRING);
    // Checkbox state control
    $postState = $_POST['state'] == 'state' ? 1 : 0;
    $post_data['state'] = filter_var($postState, FILTER_SANITIZE_STRING);
    $post_data['datapost'] =  date('Y-m-d H:i:s');

    // Transform Array in a Post Entity
    $post = new postEntity($post_data);

    // Pass Post Entity to a class that delete it from DB
    $postMapper->delete($post);

    // Redirect to Homepage
    $response = $response->withRedirect("/");
    return $response;
});

// Route Show Post
$app->get('/posts/{id}', function (Request $request, Response $response, $args) {

    // Routine code
    $this->logger->addInfo("Visualizzo dati specifico Post");
    $mapper = new PostMapper($this->db);

    // Get the variable in the link
    $post_id = (int)$args['id'];

    //Get the post entity
    $post = $mapper->getPostById($post_id);

    // Send post data to VIEW that create the page
    $response = $this->view->render($response, "postdetail.phtml", ["post" =>$post]);
    return $response;
})->setName('post-detail');

//Run the app
$app->run();

