document.getElementById("login-form").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent the form from refreshing the page

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    console.log("here is before request is sent to api");    

    fetch("../../api.php", { 
        method: "POST",
        headers: { "Content-Type": 'application/json' },
        body: JSON.stringify({
            type: "login",
            email: email,
            password: password,
        })
    })
    .then(function(response) {
        console.log("here is after request is sent to api");
        return response.json();
    })
    .then(function(responseData) {
        console.log("here is the response from the api", responseData);

        if (responseData.status === 'success') {
            localStorage.setItem('apikey', responseData.data.apikey);

            alert('Login successful');

            window.location.href = 'products.php'; 
        } else {
            alert('Login failed: ' + responseData.message);
        }
    })
    .catch(function(error) {
        console.error('Error during login request:', error);
        alert('Something went wrong. Please try again.');
    });
});
