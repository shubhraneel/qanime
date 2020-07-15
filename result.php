<?php
session_start();
if (isset($_SESSION['username'])) {
  $username = htmlspecialchars($_SESSION['username']);
  $user_id = htmlspecialchars($_SESSION['user_id']);

  $host = $_SERVER['SERVER_NAME'];
  $user = 'root';
  $password = '';
  $dbname = 'quizzy';
  $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

  $pdo = new PDO($dsn, $user, $password);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

  $stmt = $pdo->prepare('SELECT fullname, score, submitted  FROM users WHERE username=:username');
  $stmt->execute(['username' => $username]);
  $row = $stmt->fetch();
  $fullname = $row->fullname;
  $score = $row->score;
  $submitted = $row->submitted;
  if ($submitted == 0) header("Location: index.php");

  $stmt = $pdo->prepare('SELECT question_id, answer, correct FROM user_questions WHERE user_id=:user_id AND question_order=:question_order');
  $questionsjson = file_get_contents('questions.json');
  $questions = json_decode($questionsjson, TRUE);
  if ($score <= 10) {
    $remark = "Sorry!";
    $text = "Looks like you are not much of an anime fan!";
  } elseif ($score <= 20) {
    $remark = "Not bad!";
    $text = "You do watch anime it seems, but very rarely!";
  } elseif ($score <= 30) {
    $remark = "Satisfactory!";
    $text = "Looks like you watch a lot of anime but don’t rewatch them";
  } elseif ($score <= 40) {
    $remark = "Good!";
    $text = "You are a regular anime watcher it seems";
  } else {
    $remark = "Great!";
    $text = "You are addicted to anime it seems";
  }

  $stmt = $pdo->prepare('SELECT * from user_questions where user_id=:user_id order by question_order');
  $stmt->execute(['user_id' => $user_id]);
  $allAnswers = $stmt->fetchAll();

  $questionsjson = file_get_contents('questions.json');
  $questions = json_decode($questionsjson, TRUE);
} else
  header('Location: login.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@700&family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
  <link rel="stylesheet" href="style.css">
  <link rel="shortcut icon" href="favicon.svg" type="image/svg">
  <title>Qanime - The Anime quiz</title>
</head>

<body>
  <nav class="navbar">
    <h1 class="navbar--logo">Qanime</h1>
    <div class="navbar--right">
      <p class="navbar--text"><?php echo $fullname ?></p>
      <a href="logout.php"><button class="navbar--button">LOGOUT</button></a>
    </div>
  </nav>

  <div class="overall">
    <div class="overall--remark"><?php echo $remark ?></div>
    <div class="overall--text"><?php echo $text ?></div>
    <div class="overall--yourscore">Your Score</div>
    <div class="overall--score">
      <div class="overall--score--number overall--score--score"><?php echo $score ?></div>
      <div class="overall--score--number overall--score--total">50</div>
    </div>
  </div>

  <div class="breakdown">
    <h2 class="breakdown--title">Score breakdown</h2>
    <table>
      <tr>
        <th>Sl. No.</th>
        <th class="breakdown--question">Question</th>
        <th class="breakdown--answer">Answer given</th>
        <th class="breakdown--correct">Correct answer</th>
        <th class="breakdown--score">Score</th>
      </tr>
      <?php
      for ($i = 0; $i < 20; $i++) {
        $question_id = $allAnswers[$i]->question_id;
        $question = $questions[$question_id - 1]['question'];
        $correct_answer = $questions[$question_id - 1]['correct'];
        $answer = $allAnswers[$i]->answer;
        $correct = $allAnswers[$i]->correct;
      ?>
      <tr class = "<?php if ($i < 5) echo "breakdown__beginner";
            elseif ($i < 10) echo "breakdown__easy";
            elseif ($i < 15) echo "breakdown__medium";
            else echo "breakdown__difficult"; ?>">
        <td><?php echo ($i + 1) ?></td>
        <td class="breakdown--question"><?php echo $question ?></td>
        <td class="breakdown--answer"><?php echo $answer ?></td>
        <td class="breakdown--correct"><?php echo $correct_answer ?></td>
        <td class="breakdown--score <?php if ($correct == 1) echo 'correct-answer';
                    else echo 'wrong-answer' ?>">
          <?php if ($i < 5) $sub_score = 1;
            elseif ($i < 10) $sub_score = 2;
            elseif ($i < 15) $sub_score = 3;
            else $sub_score = 4;
            echo $sub_score*$correct;
          ?>
        </td>
      </tr>
      <?php
      }
      ?>
    </table>
  </div>

  <?php

  $stmt = $pdo->prepare('SELECT username, score from users order by score desc');
  $stmt->execute();
  $allUsers = $stmt->fetchAll();

  ?>

<div class="breakdown leaderboard">
    <h2 class="breakdown--title leaderboard--title">Leaderboard</h2>
    <table>
      <tr>
        <th>Rank</th>
        <th class="leaderboard--user">User</th>
        <th class="leaderboard--score">Score</th>
      </tr>
      <?php
      $i = 1;
      $prevScore = 0;
      $previi = 1;
      $ii = 1;
      foreach($allUsers as $allUser) {
        $currusername = $allUser->username;
        $score = $allUser->score;
        $ii=$i;
        if($prevScore == $score) $ii = $previi;
        if($ii == 11) break;
      ?>
      <tr class="<?php if($currusername == $username) echo "leaderboard--user__handle" ?>">
        <td><?php echo $ii ?></td>
        <td class="leaderboard--user"><?php echo $currusername ?></td>
        <td class="leaderboard--score"><?php echo $score ?></td>
      </tr>
      <?php
      $previi=$ii;
      $prevScore=$score;
      $i++;

      }
      ?>
    </table>
  </div>
</body>

</html>