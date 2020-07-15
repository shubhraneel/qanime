<?php 

include 'userValidation.php';

$validation = new UserValidator($_POST, 'register');
$errors = $validation->validateForm();

$host = $_SERVER['SERVER_NAME'];
$user = 'root';
$password = '';
$dbname = 'quizzy';

$dsn = 'mysql:host='.$host.';dbname='.$dbname;

$pdo = new PDO($dsn, $user, $password);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

if(!array_key_exists('username', $errors)) {
  $sql = 'SELECT user_id FROM users WHERE username=:username';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['username' => $_POST['username']]);

  if($stmt->rowCount() > 0) {
      $errors['username'] = 'Username already taken!';
  }
}

if(!array_key_exists('email', $errors)) {
  $sql = 'SELECT user_id FROM users WHERE email=:email';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['email' => $_POST['email']]);
  
  if($stmt->rowCount() > 0) {
      $errors['email'] = 'Account with the email already exists!';
  }
}

if(empty($errors)) {
  $sql = 'INSERT INTO users (fullname, username, email, password, questionnum, score) VALUES (:fullname, :username, :email, :password, :questionnum, :score)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    'fullname' => $_POST['fullname'],
    'username' => $_POST['username'],
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'questionnum' => 1,
    'score' => 0
  ]);

  setcookie('hasAccount', TRUE, time() + 30*86400);

}

echo json_encode($errors);


?>