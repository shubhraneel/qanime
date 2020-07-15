<?php

session_start();

if((empty($_COOKIE['hasAccount']) || !$_COOKIE['hasAccount']) && !$_SESSION['openRegister']) {
  header('Location: register.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@700&family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="shortcut icon" href="favicon.svg" type="image/svg">
  <title>Qanime - The Anime quiz</title>
</head>

<body>
  <nav class="navbar">
    <h1 class="navbar--logo">Qanime</h1>
    <div class="navbar--right">
      <p class="navbar--text">Not logged in</p>
      <a href="register.php"><button class="navbar--button">REGISTER</button></a>
    </div>
  </nav>
  <div class="form">
    <div class="form--header">
      <h2 class="form--heading">
        LOGIN
      </h2>
    </div>
    <div class="form--container">
      <form method="POST" action="" onsubmit="validateForm(event, 'login')">
        <div class="form--field">
          <label for="username" class="form--label">User Name</label>
          <input type="text" id="username" class="form--input" name="username">
          <p class="form--error" id="username-error"></p>
        </div>
        <div class="form--field">
          <label for="password" class="form--label">Password</label>
          <input type="password" id="password" class="form--input" name="password">
          <p class="form--error" id="password-error"></p>
        </div>
        <p class="form--label form--footer">Don't have an account? Register <a class="form--footer__link" href="register.php">here</a></p>
        <button type="submit" class="navbar--button form--button">LOGIN</button>
      </form>
    </div>
  </div>

  <script src="validation.js"></script>
</body>

</html>