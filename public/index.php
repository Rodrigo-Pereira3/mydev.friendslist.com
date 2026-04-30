<?php
session_start();
// Imports
require __DIR__ . "/../vendor/autoload.php";
require "../app/controllers/WebController.php";
require "../app/controllers/AuthController.php";
require "../app/services/Mailer.php";


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// $uri = str_replace("mydevpiratas.com/public", "", $uri);

$method = $_SERVER['REQUEST_METHOD'];


if ($uri === '/' || $uri === '/index' || $uri === '/home') {

    $wc = new WebController();

    $wc->index();
} elseif ($uri === '/about' && $method === 'GET') {

    $wc = new WebController();
    $wc->about();


} elseif ($uri === '/login' && $method === 'GET') {

    $wc = new WebController();
    $wc->login();


} elseif ($uri === '/login' && $method === 'POST') {

    // // Apanhar os dados do formulário
    // $email = $_POST['email'] ?? '';
    // $password = $_POST['password'] ?? '';
    // var_dump($email);
    // var_dump($password);
    // var_dump($_POST);
    // var_dump("Estou a validar o login");

    (new AuthController())->validateLogin();

} elseif ($uri === '/signup' && $method === 'GET') {

    (new WebController())->signup();

} elseif ($uri === '/signup' && $method === 'POST') {
    try {
        (new AuthController())->validateSignup();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /signup");
        exit;
    }

} elseif ($uri === '/verify-email' && $method === 'GET') {


    (new AuthController())->verifyEmailForm($token);

} elseif ($uri === '/verify-email' && $method === 'POST') {

    try {
        (new AuthController())->verifyEmailSubmit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /verify-email?token=" . urlencode($_POST['token']));
        exit();
    }

} elseif ($uri === '/send-email/test' && $method === 'GET') {
    var_dump('/send-email/test');
    $html = file_get_contents(__DIR__ . '/views/emails/welcome.php');

    var_dump($html);
    die;

    (new Mailer())->send(
        "37608@esjaloures.org",
        "Teste de email",
        $html
    );

} elseif ($uri === '/bad-request' && $method === 'GET') {
    (new WebController())->badRequest();

} else {
    http_response_code(404);
    echo "Página não encontrada";
}