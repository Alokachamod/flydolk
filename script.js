function handleSearch() {
            // Find the input field relative to the button's parent
            const searchInput = document.querySelector('.search-input');
            const query = searchInput.value;

            // Only trigger the search if the input is focused and has a value
            if (document.activeElement === searchInput && query) {
                alert('Searching for: ' + query);
                // You can replace the alert with your actual search logic,
                // like submitting a form or making an API call.
            } else if (document.activeElement !== searchInput) {
                // If the input is not focused, clicking the icon should focus it
                searchInput.focus();
            }
        }

//sign in process 

function signUp() {
    var f =document.getElementById("fname");
    var l =document.getElementById("lname");
    var e =document.getElementById("email");
    var p =document.getElementById("password");
    var m =document.getElementById("mobile");
    var g =document.getElement("gender")

    var formData = new FormData();
    formData.append("f", f.value);
    formData.append("l", l.value);
    formData.append("e", e.value);
    formData.append("p", p.value);
    formData.append("m", m.value);
    formData.append("g", g.value);

    var xhr = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            var text = request.responseText;
            if (text == "success") {
                document.getElementById("msg").innerHTML = text;
                document.getElementById("msg").className = "bi bi-check2-circle fs-5";
                document.getElementById("alertdiv").className = "alert alert-success";
                document.getElementById("msgdiv").className = "d-block";
            } else {
                document.getElementById("msg").innerHTML = text;
                document.getElementById("msgdiv").className = "d-block"
            }
        }
    }

    request.open("POST", "", true);
    request.send(form);


}