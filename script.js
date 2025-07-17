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

function signup() {
    const name =document.getElementById("name");
    const email =document.getElementById("email");
    const password =document.getElementById("password");


}