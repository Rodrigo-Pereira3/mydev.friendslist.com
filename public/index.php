<?php
session_start();
// IMPORTS
require __DIR__ . '/../vendor/autoload.php';


require "../app/controllers/WebController.php";
require "../app/controllers/AuthController.php";
require "../app/controllers/UserController.php";
require "../app/services/Mailer.php";
require "../app/middleware/AuthMiddlewareWeb.php";


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//$uri = str_replace("mydevpiratas.com/public", "", $uri);

$method = $_SERVER['REQUEST_METHOD'];

if(isset($_SESSION['token'])){
  var_dump("Existe sessão iniciada");
  //var_dump($_SESSION['token']);

  var_dump($_SESSION['token']['id']);

  var_dump($_SESSION);

  echo('<br><br><br><br><br>');

} else {
  var_dump("Não existe sessão iniciada");
}


if($uri === '/' || $uri === '/index' || $uri === '/home') {
  
  (new WebController())->index();

} 

elseif($uri === '/pagina-privada' && $method === "GET") {
  //(new WebController())->paginaPrivada();
  $isLogin = AuthMiddlewareWeb::isLogin();
  // Se utilizador não estiver logado, redirecionar para a página de login
  if(!$isLogin) {
    header("Location: /login");
    exit;
  } else {
    die("Bem-vindo à página privada!");
    //(new WebController())->paginaPrivada();
  }
} 

elseif ($uri === '/pagina-privada-admin' && $method === "GET") {
  //(new WebController())->paginaPrivada();
  $isLogin = AuthMiddlewareWeb::isAdmin();
  // Se utilizador não estiver logado, redirecionar para a página de login
  
  if (!$isLogin) {
    die("Acesso negado. Apenas para admins.");
    header("Location: /login");
    exit;
  } else {
    die("Bem-vindo à página privada admin!");
    //(new WebController())->paginaPrivada();
  }
}


elseif ($uri === '/login' && $method === "GET") {
  $isLogin = AuthMiddlewareWeb::isLogin();
  // Se utilizador não estiver logado, redirecionar para a página de login
  if ($isLogin) {
    header("Location: /");
    exit;
  } else {
    (new WebController())->login();
  }

} elseif ($uri === '/login' && $method === "POST") {
  (new AuthController())->loginWeb();


} elseif ($uri === '/logout' && $method === "GET") {

  if(AuthMiddlewareWeb::isLogin()) {
    (new AuthController())->logoutWeb();
    exit;
  } else {
    header("Location: /");
  }

}


elseif ($uri === '/signup' && $method === "GET") {
  //var_dump("Entrar na página sigup");
  (new WebController())->signup();
}


elseif ($uri === '/signup' && $method === "POST") {
  //var_dump("Entrar na página signup post para submeter");
  try {
    (new AuthController())->signupWeb();

  } catch(Exception $e) {

    var_dump($e);
    var_dump("Erro: " . $e->getMessage());
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: /signup");
  }  
  
}

elseif($uri === '/verify-email' && $method === "GET") {
  //var_dump("Entrar na página de verificação de email");
  (new AuthController())->verifyEmailForm();
}

elseif ($uri === '/verify-email' && $method === "POST") {
  try {
    (new AuthController())->verifyEmailSubmit();

  } catch(Exception $e) {

    var_dump($e);
    var_dump("Erro: " . $e->getMessage());
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: /login");
    exit;
  }
}


elseif($uri === '/users' && $method === 'GET'){
  try {
    (new UserController())->getUsers();
    
  } catch (Exception $e) {
    $_SESSION['toast'] = [
      'type' => 'error',
      'message' => $e->getMessage(),
    ];

    header("Location: /users");
  }
}


elseif(preg_match('#^/users/(\d+)$#', $uri, $m) && $method === "GET") {
  // o id do user que queremo ver
  $userId = $m[1];

  (new UserController())->profile($userId);

} elseif (preg_match('#^/users/(\d+)/update$#', $uri, $m) && $method === "POST") {
  
  try{
    // o id do user que queremo ver
    $userId = $m[1];
    (new UserController())->update($userId);
    $_SESSION['toast'] = [
      'type' => 'success',
      'message' => 'Dados atualizados com sucesso!',
    ];

    header("Location: /users/$userId");

  } catch(Exception $e) {
    $_SESSION['toast'] = [
      'type' => 'error',
      'message' => $e->getMessage(),
    ];

    header("Location: /users/$userId");
  }
}





// Rotas das páginas de erro
elseif($uri === '/bad-request' && $method === "GET") {
  (new WebController())->badRequest();
}

else {
  echo "404";
}













