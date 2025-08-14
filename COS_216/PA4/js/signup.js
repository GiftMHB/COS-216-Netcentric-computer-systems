document.getElementById("signupForm").addEventListener("submit", function (e) {
    e.preventDefault();
  
    const name = document.getElementById("name").value.trim();
    const surname = document.getElementById("surname").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const type = document.getElementById("type").value.trim();
    const errorField = document.getElementById("error");
    const successField = document.getElementById("success");
  
    errorField.textContent = "";
    successField.textContent = "";
  
    // Email regex (best practice from web)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
    // Password regex 
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{9,}$/;
  
    if (!name || !surname || !email || !password || !type) {
      errorField.textContent = "All fields are required.";
      return;
    }
  
    if (!emailRegex.test(email)) {
      errorField.textContent = "Invalid email address.";
      return;
    }
  
    if (!passwordRegex.test(password)) {
      errorField.textContent = "Password must be > 8 characters, contain uppercase, lowercase, a digit, and a symbol.";
      return;
    }
  
    // If validation passes, send POST to API
    fetch("../../api.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        type: "Register",
        name: name,
        surname: surname,
        email: email,
        password: password,
        user_type: type
      })
    })
      .then(response => response.json())
      .then(data => {
        // console.log("API Response:", data);
        if (data.status === "success") {
          successField.textContent = `${name} ${surname} registered successfully!☑️`;
        } else {
          errorField.textContent = data.message || "Registration failed. Please try again.";
        }
      })
      .catch(err => {
        console.error(err);
        errorField.textContent = "Something went wrong.";
      });
  });
  