<?php
require_once "..\init.php";

$user = new User;
if (!$user->isLoggedIn()) {
  if (!$user->hasPermissions('admin')) {
    Redirect::to('..\index.php');
  }
}

$edit_user = Database::getInstance()->get('test', ['id', '=', $_GET['id']])->first();

if ($edit_user->group_id == 1)
    $user->update(['group_id' => 2], $_GET['id']);
else
    $user->update(['group_id' => 1], $_GET['id']);

Redirect::to('index.php');
