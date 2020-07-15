const validateForm = async (event, type) => {
  event.preventDefault();
  let form = event.target;
  let data = new FormData(form);
  let file;
  if (type === "register") file = "registerSubmit.php";
  else if (type === "login") file = "loginSubmit.php";
  try {
    const response = await fetch(file, {
      method: "POST",
      body: data,
    });
    if (response.ok) {
      const jsonResponse = await response.json();
      // console.log(jsonResponse)
      renderErrors(jsonResponse);
      if (jsonResponse.length === 0) {
        if (type === "login") window.location = "index.php";
        else if (type === "register") {
          insertRecords(document.getElementById("username").value);
          window.location = "login.php";
        }
      }
      return;
    }
    throw new Error("Request failed!");
  } catch (error) {
    console.log(error);
  }
};

const insertRecords = async (username) => {
  try {
    const response = await fetch("insertRecords.php", {
      method: "POST",
      body: JSON.stringify({ username: username }),
      headers: {
        "Content-type": "application/json",
      },
    });
    if (response.ok) {
      return;
    }
    throw new Error("Request failed!");
  } catch (error) {
    console.log(error);
  }
};

function renderErrors(errorList) {
  const errorElements = document.getElementsByClassName("form--error");
  for (let i = 0; i < errorElements.length; i++) {
    let element = errorElements[i];
    let id = element.id;
    let key = id.slice(0, -6);
    element.textContent = errorList[key];
  }
}
