How to use my website

I would like to think my website is very user-friendly. From the index page, when you click PA3 it takes you to the Products page, where you see all our available products and on your left, there are options for filtering. On the top top the is a navigation bar which can be used to navigate to home, best deals, cart and also the Wishlist page, You can also find the searching functionality there. Below the navbar, the is an option of logging in or registering. The following functionalities are found on the products page and also the best deal page: you can add a product to the Wishlist or cart, and you can also view more information about that product.

Any functionality not implemented

The functionality of adding to cart and also Wishlist, not forgetting the checkout functionality. The login functionality also


Password Validation

Regex Pattern Used:
/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{9,}$/

Explanation:
- (?=.*[a-z]) – Requires at least one lowercase letter
- (?=.*[A-Z]) – Requires at least one uppercase letter
- (?=.*\d) – Requires at least one digit
- (?=.*[\W_]) – Requires at least one special character (non-alphanumeric)
- .{9,} – Requires a minimum length of 9 characters

Reason:
This regex ensures strong passwords that resist brute-force attacks and protect user accounts effectively. The minimum length of 9 helps improve entropy.

Password Hashing & Salting

Method Used:
$salt = bin2hex(random_bytes(16)); // generates a 32-character salt
$hashedPassword = hash('sha256', $salt . $password);

Explanation:
- random_bytes(16) generates 16 bytes of cryptographically secure random data.
- bin2hex() converts it to a 32-character hexadecimal string.
- Passwords are hashed with SHA-256 after prepending the salt.

Reason:
Using a salt prevents two identical passwords from producing the same hash. Even if a user uses the same password as someone else, their hashes will be different. Hashing with SHA-256 ensures that even if the database is leaked, raw passwords are hard to recover.


API Key Generation

Method Used:
$apiKey = bin2hex(random_bytes(16)); // 32-character unique key

Explanation:
- random_bytes(16) provides secure random data.
- bin2hex() ensures the API key is readable and usable as a token.

Reason:
This guarantees that each user has a unique, secure API key for authenticating future requests, without using predictable or weak tokens.

Summary of Features Implemented
- [x] Validates and sanitizes input fields (name, surname, email, password, user_type)
- [x] Ensures password strength using regex
- [x] Prevents duplicate registrations via email check
- [x] Hashes and salts passwords securely before storage
- [x] Generates and returns a unique API key
- [x] Sends meaningful JSON error and success responses

