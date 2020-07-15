<?php

$json = file_get_contents('php://input');
$username = json_decode($json, TRUE)['username'];

function permute($perm) {
  $n = count($perm);
  for($i = $n-1; $i >= 1; $i--) {
    $j = mt_rand(0, $i);
    $temp = $perm[$i];
    $perm[$i] = $perm[$j];
    $perm[$j] = $temp;
  }
  return $perm;
}

$question_order = array_merge(
  permute([1, 2, 3, 4, 5]),
  permute([6, 7, 8, 9, 10]),
  permute([11, 12, 13, 14, 15]),
  permute([16, 17, 18, 19, 20])
);

$host = $_SERVER['SERVER_NAME'];
$user = 'root';
$password = '';
$dbname = 'quizzy';

$dsn = 'mysql:host='.$host.';dbname='.$dbname;

$pdo = new PDO($dsn, $user, $password);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$sql = 'SELECT user_id from users where username=:username';
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username]);
$user_id = $stmt->fetch()->user_id;

$sql = 'INSERT INTO user_questions(user_id, question_id, question_order) values ( :user_id, :question_id, :question_order)';
$stmt = $pdo->prepare($sql);

for($i=1; $i<=20; $i++) {
  $stmt->execute(['user_id' => $user_id, "question_id" => $i, "question_order" => $question_order[$i-1]]);
}

?>