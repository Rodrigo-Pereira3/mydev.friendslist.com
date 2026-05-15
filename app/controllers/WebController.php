<?php

class WebController {

  private function view($name, $data = []) {
    require __DIR__ . '/../../public/views/' . $name . '.php';
  }



  public function index() {
    $this->view('home');
  }

  public function login()
  {
    $this->view('login');
  }

  public function signup()
  {
    $this->view('signup');
  }

  


  public function badRequest() {
    $this->view('errors/400');

  }
}