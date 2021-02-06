<?php 
require_once "init.php";

if (Input::exists()) {
  if (Token::check(Input::get('token'))) {
    $validate = new Validate;

    $validation = $validate->check($_POST, [
      'email' => ['required' => true, 'email' => true],
      'password' => ['required' => true]
    ]);

    if ($validation->passed()) {
      $user = new User;
      $remember = (Input::get('remember')) === 'on' ? true : false;
      
      $login = $user->login(Input::get('email'), Input::get('password'), $remember);

      if ($login) {
        Redirect::to("profile.php");
      } else {
        Session::flash('info', 'Логин или пароль неверны');
      }

    } else {
      $danger = '';
      foreach ($validation->errors() as $error) {
        $danger .= '<li>'. $error .'</li> ' . ' <br>';
      }
      $danger = rtrim($danger, "<br>");
      Session::flash('danger', $danger);
    }
  }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
  </head>

  <body class="text-center">
    <form class="form-signin" action="login.php" method="POST">
    	  <img class="mb-4" src="images/apple-touch-icon.png" alt="" width="72" height="72">
    	  <h1 class="h3 mb-3 font-weight-normal">Авторизация</h1>

        <?php if (Session::exists('danger')): ?>
          <div class="alert alert-danger">
            <ul>
              <?php echo Session::flash('danger'); ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if (Session::exists('success')): ?>
          <div class="alert alert-success">
            <?php echo Session::flash('success'); ?>
          </div>
        <?php endif; ?>

        <?php if (Session::exists('info')): ?>
          <div class="alert alert-info">
            <?php echo Session::flash('info'); ?>
          </div>
        <?php endif; ?>

    	  <div class="form-group">
          <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo Input::get('email'); ?>">
        </div>
        <div class="form-group">
          <input type="password" class="form-control" name="password" id="password" placeholder="Пароль">
        </div>

        <div class="form-group">
          <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
        </div>

    	  <div class="checkbox mb-3">
    	    <label>
    	      <input type="checkbox" name="remember"> Запомнить меня
    	    </label>
    	  </div>
    	  <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
    	  <p class="mt-5 mb-3 text-muted">&copy; 2017-2021</p>
    </form>
</body>
</html>
