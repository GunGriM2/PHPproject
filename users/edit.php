<?php
require_once "..\init.php";

$user = new User;
if (!$user->isLoggedIn()) {
  if (!$user->hasPermissions('admin')) {
    Redirect::to('..\index.php');
  }
}

$edit_id = $_GET['id'];

if (Input::exists()) {
  if (Token::check(Input::get('token'))) {
    $validate = new Validate;

    $validation = $validate->check($_POST, [
      'username' => ['min' => 3, 'max' => 15],
      'status' => ['max' => 500]
    ]);

    if ($validation->passed()) {
      $user->update([
        'username' => Input::get('username'),
        'profile_status' => Input::get('status')
      ], $edit_id);
      Session::flash('success', 'Профиль обновлен');
    } else {
      $danger = '';
      foreach ($validation->errors() as $error) {
        $danger .= '<li>' . $error . '</li> ' . ' <br>';
      }
      $danger = rtrim($danger, "<br>");
      Session::flash('danger', $danger);
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Profile</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="..\index.php">User Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="..\index.php">Главная</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php">Управление пользователями</a>
        </li>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item">
        <li class="nav-item">
          <a href="..\profile.php" class="nav-link">Профиль</a>
        </li>
        <a href="..\logout.php" class="nav-link">Выйти</a>
        </li>
      </ul>
    </div>
  </nav>
  <?php 
    $edit_user = Database::getInstance()->get('test', ['id', '=', $_GET['id']])->first(); 
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <h1>Профиль пользователя - <?php echo $edit_user->username; ?></h1>

        <?php if (Session::exists('danger')) : ?>
          <div class="alert alert-danger">
            <ul>
              <?php echo Session::flash('danger'); ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if (Session::exists('success')) : ?>
          <div class="alert alert-success">
            <?php echo Session::flash('success'); ?>
          </div>
        <?php endif; ?>

        <form action="edit.php?id=<?php echo $_GET['id']; ?>" class="form" method="POST">
          <div class="form-group">
            <label for="username">Имя</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo $edit_user->username; ?>">
          </div>
          <div class="form-group">
            <label for="status">Статус</label>
            <input type="text" id="status" name="status" class="form-control" value="<?php echo $edit_user->profile_status; ?>">
          </div>

          <div class="form-group">
            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
          </div>

          <div class="form-group">
            <button class="btn btn-warning">Обновить</button>
          </div>
        </form>


      </div>
    </div>
  </div>
</body>

</html>