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

  $stmt = $pdo->prepare('SELECT fullname, questionnum, submitted FROM users WHERE username=:username');
  $stmt->execute(['username' => $username]);
  $row = $stmt->fetch();
  $fullname = $row->fullname;
  $questionnum = $row->questionnum;
  $submitted = $row->submitted;
  if ($submitted == 1) header('Location: result.php');

  $stmt = $pdo->prepare('SELECT question_id, answer FROM user_questions WHERE user_id=:user_id AND question_order=:question_order');
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

  <div class="question--container">
    <form method="POST" action="answerValidation.php" onsubmit="validateAnswer()">
      <div class="question--navigate question--navigate__back" id="back">
        <i class="question--navigate--icon far fa-chevron-double-up"></i>
        <p class="question--navigate--text">Back</p>
      </div>
      <?php
      for ($i = 1; $i <= 20; $i++) {
        $stmt->execute(['user_id' => $user_id, 'question_order' => $i]);
        $row = $stmt->fetch();
        $question_id = $row->question_id;
        $answer = $row->answer;
        $question = $questions[$question_id - 1]
      ?>
        <div class="question--container__inner" <?php if ($i == $questionnum) echo 'style = "z-index: 400;"' ?>>
          <div class="question<?php if ($i < $questionnum)
                                echo " question--back";
                              elseif ($i > $questionnum)
                                echo " question--next";
                              ?>" id="question<?php echo $i ?>">
            <div class="question--header<?php if ($i <= 5)
                                          echo " question--header__beginner";
                                        elseif ($i <= 10)
                                          echo " question--header__easy";
                                        elseif ($i <= 15)
                                          echo " question--header__medium";
                                        else
                                          echo " question--header__difficult";
                                        ?>">
              <div class="question--number"><?php echo $i ?></div>
              <div class="question--question"><?php echo $question['question'] ?></div>
              <p class="question--level">Level <?php $level = intdiv($i-1, 5);
                                                echo ($level + 1) . ": " . (['Beginner', 'Easy', 'Medium', 'Diffcult'][$level]); ?></p>
            </div>
            <hr class="horizontal-rule" />
            <div class="question--options">
              <div class="question--option-container">
                <label class="question--option-label" for="<?php echo "question" . $question_id ?>option1" onclick="updateAnswer(<?php echo $question_id ?>, '<?php echo $question['option1'] ?>')">
                  <input class="question--radio" id="<?php echo "question" . $question_id ?>option1" value="<?php echo $question['option1'] ?>" type="radio" name="<?php echo "question" . $question_id ?>option" <?php if ($question['option1'] == $answer) echo "checked" ?> />
                  <span class="question--checkmark"></span>
                  <span class="question--option"><?php echo $question['option1'] ?></span>
                </label>
              </div>
              <div class="question--option-container">
                <label class="question--option-label" for="<?php echo "question" . $question_id ?>option2" onclick="updateAnswer(<?php echo $question_id ?>, '<?php echo $question['option2'] ?>')">
                  <input class="question--radio" id="<?php echo "question" . $question_id ?>option2" value="<?php echo $question['option2'] ?>" type="radio" name="<?php echo "question" . $question_id ?>option" <?php if ($question['option2'] == $answer) echo "checked" ?> />
                  <span class="question--checkmark"></span>
                  <span class="question--option"><?php echo $question['option2'] ?></span>
                </label>
              </div>
              <div class="question--option-container">
                <label class="question--option-label" for="<?php echo "question" . $question_id ?>option3" onclick="updateAnswer(<?php echo $question_id ?>, '<?php echo $question['option3'] ?>')">
                  <input class="question--radio" id="<?php echo "question" . $question_id ?>option3" value="<?php echo $question['option3'] ?>" type="radio" name="<?php echo "question" . $question_id ?>option" <?php if ($question['option3'] == $answer) echo "checked" ?> />
                  <span class="question--checkmark"></span>
                  <span class="question--option"><?php echo $question['option3'] ?></span>
                </label>
              </div>
              <div class="question--option-container">
                <label class="question--option-label" for="<?php echo "question" . $question_id ?>option4" onclick="updateAnswer(<?php echo $question_id ?>, '<?php echo $question['option4'] ?>')">
                  <input class="question--radio" id="<?php echo "question" . $question_id ?>option4" value="<?php echo $question['option4'] ?>" type="radio" name="<?php echo "question" . $question_id ?>option" <?php if ($question['option4'] == $answer) echo "checked" ?> />
                  <span class="question--checkmark"></span>
                  <span class="question--option"><?php echo $question['option4'] ?></span>
                </label>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
      <div class="question--navigate question--navigate__next" id="next">
        <p class="question--navigate--text">Next</p>
        <i class="question--navigate--icon far fa-chevron-double-down"></i>
      </div>
      <div class="question--button__container">
        <p class="question--error" id="error"></p>
        <button type="submit" class="navbar--button form--button question--button">SUBMIT</button>
      </div>
    </form>
  </div>

  <script>
    let currentQuestion = <?php echo $questionnum ?>;
    if (currentQuestion === 1) {
      document.getElementById("back").classList.add("question--navigate__disabled");
    } else if (currentQuestion === 20) {
      document.getElementById("next").classList.add("question--navigate__disabled");
    }
    document.getElementById("back").addEventListener("click", async () => {
      if (currentQuestion > 1) {
        if (currentQuestion % 5 === 1) {
          let response = await updatequestionnum('back');
          console.log(response);
          if ('error' in response) {
            document.getElementById('error').textContent = response['error'];
            return;
          }
        } else {
          updatequestionnum('back');
        }
        document.getElementById('error').textContent = "";
        let currentElement = document.getElementById("question" + currentQuestion);
        currentElement.classList.add("question--next");
        currentElement.parentElement.style.zIndex = "200"
        let prevElement = document.getElementById("question" + (currentQuestion - 1));
        prevElement.classList.remove("question--back");
        prevElement.parentElement.style.zIndex = "400";
        if (currentQuestion === 20) {
          document.getElementById("next").classList.remove("question--navigate__disabled");
        }
        if (currentQuestion === 2) {
          document.getElementById("back").classList.add("question--navigate__disabled");
        }
        currentQuestion--;
      }
    })
    document.getElementById("next").addEventListener("click", async () => {
      if (currentQuestion < 20) {
        if (currentQuestion % 5 === 0) {
          let response = await updatequestionnum('next');
          console.log(response);
          if ('error' in response) {
            document.getElementById('error').textContent = response['error'];
            return;
          }
        } else {
          updatequestionnum('next');
        }
        document.getElementById('error').textContent = "";
        let currentElement = document.getElementById("question" + currentQuestion);
        currentElement.classList.add("question--back");
        currentElement.parentElement.style.zIndex = "200"
        let nextElement = document.getElementById("question" + (currentQuestion + 1));
        nextElement.classList.remove("question--next");
        nextElement.parentElement.style.zIndex = "400"
        if (currentQuestion === 1) {
          document.getElementById("back").classList.remove("question--navigate__disabled");
        }
        if (currentQuestion === 19) {
          document.getElementById("next").classList.add("question--navigate__disabled");
        }
        currentQuestion++;
      } else {
        document.getElementById('error').textContent = "This quiz has only 20 questions";
      }
    })

    const updatequestionnum = async (type) => {
      let data;
      if (type === 'back') {
        data = JSON.stringify({
          'type': 'back'
        });
      } else if (type === 'next') {
        data = JSON.stringify({
          'type': 'next'
        });
      }
      try {
        const response = await fetch('questionnum.php', {
          method: "POST",
          body: data,
          headers: {
            "Content-type": "application/json",
          },
        });
        if (response.ok) {
          const jsonResponse = await response.json();
          console.log(jsonResponse)
          return jsonResponse;
        }
      } catch (error) {
        console.log(error);
      }
    }

    const updateAnswer = async (question, answer) => {
      let questions = <?php echo $questionsjson ?>;
      let correct = 0;
      if (questions[question - 1]['correct'] === answer) {
        correct = 1;
      }
      let data = JSON.stringify({
        'question': question,
        'answer': answer,
        'correct': correct,
      })
      try {
        const response = await fetch('updateAnswer.php', {
          method: "POST",
          body: data,
          headers: {
            "Content-type": "application/json",
          },
        });
        if (response.ok) {
          return;
        }
      } catch (error) {
        console.log(error);
      }
    }

    const validateAnswer = async () => {
      event.preventDefault();
      let form = event.target;
      let data = new FormData(form);
      try {
        const response = await fetch('validateAnswer.php', {
          method: "POST",
          body: data,
        });
        if (response.ok) {
          const jsonResponse = await response.json();
          console.log(jsonResponse);
          if (jsonResponse.length === 0)
            window.location = "result.php";
          else {
            let error = jsonResponse['error'];
            document.getElementById('error').textContent = error;
          }
        }
        throw new Error("Request failed!");
      } catch (error) {
        console.log(error);
      }
    }
  </script>
</body>

</html>