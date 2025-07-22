function handleSearch() {
  // Find the input field relative to the button's parent
  const searchInput = document.querySelector(".search-input");
  const query = searchInput.value;

  // Only trigger the search if the input is focused and has a value
  if (document.activeElement === searchInput && query) {
    alert("Searching for: " + query);
    // You can replace the alert with your actual search logic,
    // like submitting a form or making an API call.
  } else if (document.activeElement !== searchInput) {
    // If the input is not focused, clicking the icon should focus it
    searchInput.focus();
  }
}

//sign up process

function signUp() {
  var n = document.getElementById("name");
  var e = document.getElementById("email");
  var p = document.getElementById("password");
  var m = document.getElementById("mobile");

  var form = new FormData();
  form.append("n", n.value);
  form.append("e", e.value);
  form.append("p", p.value);
  form.append("m", m.value);

  var request = new XMLHttpRequest();

  request.onreadystatechange = function () {
    if (request.readyState == 4) {
      var text = request.responseText;
      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "You have signed up successfully. You can now sign in.",
          confirmButtonText: "OK",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }
    }
  };

  request.open("POST", "signUpProcess.php", true);
  request.send(form);
}

//sign in process

function signin() {
  //alert("Sign in button clicked");

  var e = document.getElementById("email");
  var p = document.getElementById("password");

  var form = new FormData();
  form.append("e", e.value);
  form.append("p", p.value);
  var request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState == 4) {
      var text = request.responseText;
      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "You have signed in successfully.",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.href = "index.php"; // Redirect to index.php after successful sign-in
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }

    }
  };
  request.open("POST", "signInProcess.php", true);
  request.send(form);
}
