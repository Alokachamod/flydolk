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



//delete category process

/**
 * Asks for confirmation and deletes a category using SweetAlert2.
 * @param {number} categoryId - The ID of the category to delete.
 * @param {string} categoryName - The name of the category for the confirmation dialog.
 */
function deleteCategory(categoryId, categoryName) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert the deletion of '" + categoryName + "'!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        // This code runs if the user clicks "Yes, delete it!"
        if (result.isConfirmed) {
            
            // Prepare the data to send (only the ID is needed)
            var form = new FormData();
            form.append("categoryId", categoryId); 

            // Create and send the AJAX request
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var text = request.responseText;

                    if (text.includes("success")) {
                        // Show success message and reload
                        Swal.fire({
                            title: "Deleted!",
                            text: "The category has been deleted.",
                            icon: "success"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        // Show error message from the server
                        Swal.fire({
                            title: "Error!",
                            text: text,
                            icon: "error"
                        });
                    }
                }
            };
            request.open("POST", "deleteCategory.php", true);
            request.send(form);
        }
    });
}

//add brand process

function addBrand() {
    //alert("Add brand functionality is not implemented yet.");

    var bname = document.getElementById("bname").value; 

    //alert("Brand name: " + bname);

    var Form = new FormData();
    Form.append("b", bname);
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var text = request.responseText;

            if (text == "success") {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "Brand added successfully.",
                    confirmButtonText: "OK",
                }).then(() => {
                    window.location.reload(); // Reload the page to see the new brand
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
    request.open("POST", "addBrand.php", true);
    request.send(Form);

}

/**
 * Asks for confirmation and deletes a Brand using SweetAlert2.
 * @param {number} brandId - The ID of the brand to delete.
 * @param {string} brandName - The name of the brand for the confirmation dialog.
 */
function deleteBrand(brandId, brandName) { // <-- FIX 1: Renamed from deletebrand to deleteBrand
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert the deletion of '" + brandName + "'!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        // This code runs if the user clicks "Yes, delete it!"
        if (result.isConfirmed) {
            
            // Prepare the data to send (only the ID is needed)
            var form = new FormData();
            form.append("brandId", brandId); 

            // Create and send the AJAX request
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var text = request.responseText;

                    if (text.includes("success")) {
                        // Show success message and reload
                        Swal.fire({
                            title: "Deleted!",
                            text: "The brand has been deleted.",
                            icon: "success"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        // Show error message from the server
                        Swal.fire({
                            title: "Error!",
                            text: text,
                            icon: "error"
                        });
                    }
                }
            };
            request.open("POST", "deleteBrand.php", true);
            request.send(form);

            // echo("success"); // <-- FIX 2: Removed invalid PHP code
        }
    });
}



