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
    var e = document.getElementById("e").value;
    var p = document.getElementById("p").value;

    var form = new FormData();
    // FIX: Append the variables e and p directly, not e.value and p.value
    form.append("e", e);
    form.append("p", p);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        // Check if the request is complete AND successful
        if (request.readyState == 4 && request.status == 200) {
            var text = request.responseText;

            if (text == "success") {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "You have signed in successfully.",
                    confirmButtonText: "OK",
                }).then(() => {
                    window.location.href = "index.php"; // Redirect after successful sign-in
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


//admin-login ani,ations and functions

document.addEventListener('DOMContentLoaded', function () {

    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('adminPassword');

    /**
     * Toggles the password field visibility.
     */
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute of the password input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle the eye icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // The animations are handled by CSS, so no extra JavaScript is needed for them.
    // The form submission logic has been removed as requested.
    
});

  function loginAdmin() {
    //alert("Admin login functionality is not implemented yet.");

    var ae = document.getElementById("adminEmail").value;
    var ap = document.getElementById("adminPassword").value;

    //alert("Admin email: " + ae + ", Admin password: " + ap);

    var form = new FormData();
    form.append("ae", ae);
    form.append("ap", ap);

    var request = new XMLHttpRequest();
    
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var text = request.responseText;

            if (text == "success") {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "You have logged in successfully.",
                    confirmButtonText: "OK",
                }).then(() => {
                    window.location.href = "admin-dashboard.php"; // Redirect after successful login
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
    request.open("POST", "adminLoginProcess.php", true);
    request.send(form);

  }


  //add category process

  function addCategory() {
            //alert("Add category functionality is not implemented yet.");

            var cname = document.getElementById("category").value;
            
            //alert("Category name: " + cname);

            var form = new FormData();
            form.append("c", cname);
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var text = request.responseText;

                    if (text == "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: "Category added successfully.",
                            confirmButtonText: "OK",
                        }).then(() => {
                            window.location.reload(); // Reload the page to see the new category
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
            request.open("POST", "addCategory.php", true);
            request.send(form);
            
        }