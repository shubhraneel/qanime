<?php

session_start();

$user_id = htmlspecialchars($_SESSION['user_id']);

$json = file_get_contents('php://input');
$data = json_decode($json, TRUE);

$host = $_SERVER['SERVER_NAME'];
$user = 'root';
$password = '';
$dbname = 'quizzy';
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

$pdo = new PDO($dsn, $user, $password);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$question_id = htmlspecialchars($data['question']);
$answer = htmlspecialchars($data['answer']);
$correct = htmlspecialchars($data['correct']);

$stmt = $pdo->prepare('UPDATE user_questions SET answer=:answer, correct=:correct where user_id=:user_id and question_id=:question_id');
$stmt->execute(['user_id'=>$user_id, 'question_id'=>$question_id, 'answer'=>$answer, 'correct'=>$correct]);

?>