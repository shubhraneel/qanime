<?php

session_start();

$username = htmlspecialchars($_SESSION['username']);
$user_id = htmlspecialchars($_SESSION['user_id']);

$json = file_get_contents('php://input');
$type = json_decode($json, TRUE)['type'];

$host = $_SERVER['SERVER_NAME'];
$user = 'root';
$password = '';
$dbname = 'quizzy';
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

$pdo = new PDO($dsn, $user, $password);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$stmt = $pdo->prepare('SELECT questionnum FROM users WHERE username=:username');
$stmt->execute(['username' => $username]);

$questionnum = $stmt->fetch()->questionnum;

if($type === 'back' && $questionnum%5 == 1 && $questionnum != 1) {
  echo json_encode(['error' => 'Cannot go back to previous level']);
  exit();
} elseif($type === 'next' && $questionnum%5 == 0 && $questionnum != 20) {
  $stmt = $pdo->prepare('SELECT answer FROM user_questions WHERE user_id=:user_id and question_id=:question_id');
  $q = intdiv($questionnum-1, 5)*5 + 1;
  for($i = 0; $i < 5; $i++) {
    $stmt->execute(['user_id' => $user_id, 'question_id' => $q]);
    if(empty($stmt->fetch()->answer)) {
      echo json_encode(['error' => 'Answer all questions in this level']);
      exit();
    }
    $q++;
  }
}

$stmt = $pdo->prepare('UPDATE users SET questionnum=:questionnum WHERE username=:username');

if($type === 'back') {
  $stmt->execute(['questionnum' => $questionnum - 1, 'username' => $username]);
} elseif($type === 'next') {
  $stmt->execute(['questionnum' => $questionnum + 1, 'username' => $username]);
}

echo json_encode([]);

?>
