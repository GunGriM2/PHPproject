<?php
require_once "..\init.php";

$user = new User;
if (!$user->isLoggedIn()) {
  if (!$user->hasPermissions('admin')) {
    Redirect::to('..\index.php');
  }
}

$edit_id = $_GET['id'];
Database::getInstance()->delete('test', ['id', '=', $edit_id]);

Redirect::to('index.php');