<?php 

include 'userValidation.php';

$validation = new UserValidator($_POST, 'login');
$errors = $validation->validateForm();

$host = $_SERVER['SERVER_NAME'];
$user = 'root';
$password = '';
$dbname = 'quizzy';

$dsn = 'mysql:host='.$host.';dbname='.$dbname;

$pdo = new PDO($dsn, $user, $password);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$sql = 'SELECT password FROM users WHERE username=:username';
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => htmlspecialchars($_POST['username'])]);

if(!array_key_exists('username', $errors)) {
  if($stmt->rowCount() === 0) {
    $errors['username'] = 'No account with entered username exists!';
  } else if(!array_key_exists('password', $errors)) {
    $user = $stmt->fetch();
    if($user->password !== htmlspecialchars($_POST['password']))
      $errors['password'] = 'Wrong password!';
  }
}

if(empty($errors)) {
  session_start();
  $_SESSION['username'] = htmlspecialchars($_POST['username']);
  $stmt = $pdo->prepare('SELECT user_id from users where username=:username');
  $stmt->execute(['username' => htmlspecialchars($_POST['username'])]);
  $_SESSION['user_id'] = $stmt->fetch()->user_id;
  setcookie('hasAccount', TRUE, time() + 30*86400);
}

echo json_encode($errors);

?>