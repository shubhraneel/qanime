<?php

session_start();

$errors = [];
$flag = TRUE;

for($i = 1; $i <= 20; $i++) {
  if(empty($_POST['question'.$i.'option'])) {
    $errors['error'] = 'Fill all answers';
    $flag = FALSE;
    break;
  }
}

if($flag) {
  $user_id = $_SESSION['user_id'];

  $host = $_SERVER['SERVER_NAME'];
  $user = 'root';
  $password = '';
  $dbname = 'quizzy';
  $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

  $pdo = new PDO($dsn, $user, $password);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

  $stmt = $pdo->prepare('UPDATE users SET submitted=:submitted where user_id=:user_id');
  $stmt->execute(['submitted' => 1, 'user_id' => $user_id]);

  $stmt = $pdo->prepare('SELECT correct from user_questions where user_id=:user_id and question_id=:question_id');
  $score=0;

  for($i = 1; $i <= 20; $i++) {
    $stmt->execute(['user_id' => $user_id, 'question_id' => $i]);
    $correct = $stmt->fetch()->correct;
    if($correct==1) $score += (intdiv($i-1, 5) + 1);
  }

  $stmt = $pdo->prepare('UPDATE users set score=:score where user_id=:user_id');
  $stmt->execute(['user_id' => $user_id, 'score' => $score]);
}

echo json_encode($errors);

?>