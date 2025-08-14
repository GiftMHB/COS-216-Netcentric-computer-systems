<?php
// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set JSON response header
header("Content-Type: application/json");

// Load database config
require_once(__DIR__ . '/COS_216/PA3/config.php');

class API {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $db = DatabaseConnection::getInstance();
        $this->conn = $db->getConnection();
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new API();
        }
        return self::$instance;
    }

    public function handleRequest() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['type'])) {
            $this->respondWithError("Missing 'type' in request", 400);
            return;
        }

        switch ($input['type']) {
            case "Register":
                $this->registerUser($input);
                break;

            case "GetAllProducts":
                $this->getAllProducts($input); 
                break;

            case "login":
                $this->login($input);
                break;
            
            case "save":
                $this->savePreferences($input);
                break;

            case "add_wishlist":
            case "remove_wishlist":
            $this->handleWishlist($input);
            break;

            case "get_wishlist":
            $this->getWishlist($input);
            break;

            default:
                $this->respondWithError("Unknown API type", 400);
        }
    }

    private function getWishlist($data) 
    {
        if (!isset($data['apikey'])) {
            $this->respondWithError("Missing API key", 400);
            return;
        }

        $userId = $this->getUserId($data['apikey']);
        if (!$userId) {
            $this->respondWithError("User not found", 401);
            return;
        }

        $query = "
            SELECT p.id, p.title, p.image_url, p.initial_price, p.final_price, p.currency
            FROM wishlists w
            JOIN products p ON w.product_id = p.id
            WHERE w.customer_id = ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $wishlist = [];
        while ($row = $result->fetch_assoc()) {

            $wishlist[] = $row;
        }

        $this->respondWithSuccess($wishlist);
    }

    public function handleWishlist($data) 
    {
        $required = ['apikey', 'product_id'];
        foreach ($required as $field) 
        {
            if (!isset($data[$field])) {
                $this->respondWithError("Missing required field: $field", 400);
                return;
            }
        }

        $userId = $this->getUserId($data['apikey']);
        if (!$userId) {
            $this->respondWithError("User not found", 401);
            return;
        }

        $productId = (int)$data['product_id'];

        if ($data['type'] === 'add_wishlist') {
            $this->addToWishlist($userId, $productId);
        } elseif ($data['type'] === 'remove_wishlist') {
            $this->removeFromWishlist($userId, $productId);
        } else {
            $this->respondWithError("Invalid wishlist type", 400);
        }
    }

    public function addToWishlist($userId, $productId) 
    {
        $stmt = $this->conn->prepare("SELECT id FROM products WHERE id = ?");
        if (!$stmt) {
            $this->respondWithError("Prepare failed (product check): " . $this->conn->error, 500);
            return;
        }
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        if (!$stmt->get_result()->num_rows) {
            $this->respondWithError("Product not found", 404);
            return;
        }

        $stmt = $this->conn->prepare("SELECT temp_id FROM wishlists WHERE customer_id = ? AND product_id = ?");
        if (!$stmt) {
            $this->respondWithError("Prepare failed (duplicate check): " . $this->conn->error, 500);
            return;
        }
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows) {
            $this->respondWithError("Product already in wishlist", 409);
            return;
        }

        $stmt = $this->conn->prepare("INSERT INTO wishlists (customer_id, product_id, created_at) VALUES (?, ?, NOW())");
        if (!$stmt) {
            $this->respondWithError("Prepare failed (insert): " . $this->conn->error, 500);
            return;
        }
        $stmt->bind_param("ii", $userId, $productId);
        
        if ($stmt->execute()) {
            $this->respondWithSuccess(["message" => "Product added to wishlist"]);
        } else {
            $this->respondWithError("Failed to add to wishlist: " . $stmt->error, 500);
        }
    }

    private function removeFromWishlist($userId, $productId) 
    {
        $stmt = $this->conn->prepare("DELETE FROM wishlists WHERE customer_id = ? AND product_id = ?");
        if (!$stmt) {
            $this->respondWithError("Prepare failed (delete): " . $this->conn->error, 500);
            return;
        }
        
        $stmt->bind_param("ii", $userId, $productId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $this->respondWithSuccess(["message" => "Product removed from wishlist"]);
            } else {
                $this->respondWithError("Product not in wishlist", 404);
            }
        } else {
            $this->respondWithError("Failed to remove from wishlist: " . $stmt->error, 500);
        }
    }

    public function getUserId($apiKey)
    {
        error_log("getUserId() called with API key: '$apiKey'");

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
        if (!$stmt) {
            error_log("Failed to prepare statement: " . $this->conn->error);
            return null;
        }

        $apiKey = trim($apiKey); // remove whitespace just in case
        $stmt->bind_param("s", $apiKey);

        if (!$stmt->execute()) {
            error_log("Failed to execute: " . $stmt->error);
            return null;
        }

        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            error_log("User found with ID: " . $row['id']);
            return $row['id'];
        }

        error_log("No matching user found for API key: '$apiKey'");
        return null;
    }

    public function savePreferences($data) 
    {
        // Extract filters and other data
        $filters = $data['filters'] ?? [];
        $category = $filters['category'] ?? null;
        $country_of_origin = $filters['country_of_origin'] ?? null;
        $minPrice = $filters['minPrice'] ?? null;
        $maxPrice = $filters['maxPrice'] ?? null;
        $currency = $data['currency'] ?? null;

        $apiKey = $data['apikey'];
        
        // Get user ID from authentication or session
        $userId = $this->getUserId($apiKey); 

        $userId = $this->getUserId($apiKey);
        if (!$userId) {
            $this->respondWithError("User not found.", 401);
            return;
        }


        // Check if $minPrice and $maxPrice are numeric, if not, set to NULL
        $minPrice = is_numeric($minPrice) ? (float) $minPrice : null;
        $maxPrice = is_numeric($maxPrice) ? (float) $maxPrice : null;

        // Update the preferences in the users table
        $stmt = $this->conn->prepare("
            UPDATE users 
            SET 
                category = ?, 
                country_of_origin = ?, 
                currency = ?, 
                minPrice = ?, 
                maxPrice = ? 
            WHERE id = ?
        ");
        
        // For NULL values, we need to adjust the type string
        $types = '';
        $params = [];
        
        $types .= $category !== null ? 's' : 's'; 
        $params[] = $category;
        
        $types .= $country_of_origin !== null ? 's' : 's';
        $params[] = $country_of_origin;
        
        $types .= $currency !== null ? 's' : 's';
        $params[] = $currency;
        
        $types .= $minPrice !== null ? 'd' : 'd'; // 'd' for double
        $params[] = $minPrice;
        
        $types .= $maxPrice !== null ? 'd' : 'd';
        $params[] = $maxPrice;
        
        $types .= 'i'; // 'i' for integer user ID
        $params[] = $userId;
        
        // Bind parameters
        $stmt->bind_param($types, ...$params);

        // Execute the query
        if ($stmt->execute()) {
            $this->respondWithSuccess(["message" => "Preferences saved successfully"]);
        } else {
            $this->respondWithError("Failed to save preferences!", 500);
        }
    }  

    public function login($data) {
        
        $required = ['email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->respondWithError("Empty field: $field", 400);
                return;
            }
        }

        $email = trim($data['email']);
        $password = $data['password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respondWithError("Invalid email format", 422);
            return;
        }

        $stmt = $this->conn->prepare("SELECT id,name, password, salt, api_key FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $this->respondWithError("wrong email", 401);
            return;
        }

        $stmt->bind_result($userId,$userName, $hashedPassword, $salt, $apiKey);
        $stmt->fetch();

        $checkPassword = hash('sha256', $salt . $password);

        if ($checkPassword !== $hashedPassword) {
            $this->respondWithError("wrong password", 401);
            return;
        }
        
        if(session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }

        $_SESSION['user'] = [
            'id' => $userId,
            'email' => $email,
            'name' => $userName, 
        ];

        $this->respondWithSuccess(["apikey" => $apiKey]);
    }


    public function registerUser($data) {
        
        $required = ['name', 'surname', 'email', 'password', 'user_type'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->respondWithError("Missing or empty field: $field", 400);
                return;
            }
        }

        $name = trim($data['name']);
        $surname = trim($data['surname']);
        $email = trim($data['email']);
        $password = $data['password'];
        $user_type = $data['user_type'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respondWithError("Invalid email format", 422);
            return;
        }

        // Validate password (at least 9 characters, includes upper/lower/digit/symbol)
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{9,}$/", $password)) {
            $this->respondWithError("Password does not meet complexity requirements", 422);
            return;
        }

        // Check if email already exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $this->respondWithError("Email already registered", 409);
            return;
        }

        // Secure password with salt + hash
        $salt = bin2hex(random_bytes(16));
        $hashedPassword = hash('sha256', $salt . $password);

        // Generate unique API key
        $apiKey = bin2hex(random_bytes(16));

        // Save new user
        $stmt = $this->conn->prepare("INSERT INTO users (name, surname, email, password, type, api_key,salt) VALUES (?, ?, ?, ?, ?, ?,?)");
        $stmt->bind_param("sssssss", $name, $surname, $email, $hashedPassword, $user_type, $apiKey,$salt);
        $success = $stmt->execute();

        if (!$success) {
            $this->respondWithError("Failed to register user", 500);
            return;
        }

        $this->respondWithSuccess(["apikey" => $apiKey]);
    }

    /* public function getAllProducts($data) {

        error_log("=== START getAllProducts ===");
        error_log("Incoming data: " . print_r($data, true));

        // Validate API key
        error_log("Validating API key...");
        if (!isset($data['apikey']) || empty($data['apikey'])) {
            error_log("API key missing!");
            $this->respondWithError("Missing API key", 400);
            return;
        }

        // Database check
        error_log("Checking database...");
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
        $stmt->bind_param("s", $data['apikey']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            error_log("Invalid API key!");
            $this->respondWithError("Invalid API key", 403);
            return;
        }

        error_log("API key valid. Proceeding...");

        $selectFields = "*";

        $allowedFields = [ 
            "id", "title", "brand", "description", "initial_price", "final_price", "currency",
            "categories", "image_url", "product_dimensions", "date_first_available", "manufacturer",
            "department", "features", "is_available", "images", "country_of_origin", "created_at", "updated_at"
        ];

        // Safely handle 'return' — force it to be an array if it isn't
        $returnFieldRaw = $data['return'] ?? '*';
        $returnFields = is_array($returnFieldRaw) ? $returnFieldRaw : [];

        if (!empty($returnFields)) {
            $safeFields = array_intersect($returnFields, $allowedFields);

            if (!in_array("id", $safeFields)) {
                array_unshift($safeFields, "id");
            }

            if (!empty($safeFields)) {
                $selectFields = implode(",", $safeFields);
            }
        }


        $query = "SELECT $selectFields FROM products WHERE is_available = 1";
        $params = [];
        $types = "";

       $filter = [];
        if (!empty($data['filter']) && is_array($data['filter'])) {
            $filter = $data['filter'];
        }
        if (!empty($data['search']) && is_array($data['search'])) {
            $filter = array_merge($filter, $data['search']);
        }

        if (!empty($filter)) 
        {
            if (!empty($filter['brand'])) {
                $query .= " AND brand = ?";
                $types .= "s";
                $params[] = $filter['brand'];
            }

            if (!empty($filter['country_of_origin'])) {
                $query .= " AND country_of_origin = ?";
                $types .= "s";
                $params[] = $filter['country_of_origin'];
            }

            if (!empty($filter['category'])) {
                $query .= " AND categories LIKE ?";
                $types .= "s";
                $params[] = "%" . $filter['category'] . "%";
            }

            if (!empty($filter['department'])) {
                $query .= " AND department = ?";
                $types .= "s";
                $params[] = $filter['department'];
            }

            if (isset($filter['min_price']) && isset($filter['max_price'])) {
                $query .= " AND final_price BETWEEN ? AND ?";
                $types .= "dd";
                $params[] = $filter['min_price'];
                $params[] = $filter['max_price'];
            }
        }

        // Sorting and limit
        $sort = !empty($data['sort']) ? $data['sort'] : "created_at";
        $order = strtoupper($data['order'] ?? "DESC");
        if (!in_array($order, ["ASC", "DESC"])) $order = "DESC";
        $query .= " ORDER BY $sort $order";

        $limit = !empty($data['limit']) ? intval($data['limit']) : 100;
        $query .= " LIMIT $limit";

        // Step 3: Execute
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        // Step 4: Currency conversion (only if requested currency ≠ ZAR)
        $requestedCurrency = strtoupper($data['currency'] ?? "ZAR");
        if ($requestedCurrency !== "ZAR") {
            $conversionRate = $this->fetchConversionRate($requestedCurrency, "ZAR");
            if ($conversionRate) {
                foreach ($products as &$product) {
                    $product['initial_price'] *= $conversionRate;
                    $product['final_price'] *= $conversionRate;
                    $product['currency'] = $requestedCurrency;;  
                }
            }
        }

        // Step 5: Return
        $this->respondWithSuccess(["products" => $products]);
    }

    //Helper to fetch currency conversion from Wheatley API
    public function fetchConversionRate($from, $to) {
        $payload = json_encode([
            "studentnum" => "u23545537",  
            "apikey" => "$",  
            "type" => "convert",
            "from" => $from,
            "to" => $to
        ]);

        $ch = curl_init("https://wheatley.cs.up.ac.za/api/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($response, true);
        if (!$decoded || !isset($decoded['status']) || $decoded['status'] !== 'success') {
            error_log("Conversion API failed or returned invalid response: " . $response);
            return null;
        }
    
        return $decoded['data']['value'];
    } */
    private function getAllProducts($data) {
        // Step 1: Validate API key
        if (!isset($data['apikey']) || empty($data['apikey'])) {
            $this->respondWithError("Missing or empty 'apikey'", 400);
            return;
        }

        $apikey = $data['apikey'];
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
        $stmt->bind_param("s", $apikey);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $this->respondWithError("Invalid API key", 403);
            return;
        }

        // Step 2: Build SQL query
        $query = "SELECT * FROM products WHERE is_available = 1";
        $params = [];
        $types = "";

        if (!empty($data['filter'])) {
            $filter = $data['filter'];

            if (!empty($filter['brand'])) {
                $query .= " AND brand = ?";
                $types .= "s";
                $params[] = $filter['brand'];
            }

            if (!empty($filter['country_of_origin'])) {
                $query .= " AND country_of_origin = ?";
                $types .= "s";
                $params[] = $filter['country_of_origin'];
            }

            if (!empty($filter['category'])) {
                $query .= " AND categories LIKE ?";
                $types .= "s";
                $params[] = "%" . $filter['category'] . "%";
            }

            if (isset($filter['min_price']) && isset($filter['max_price'])) {
                $query .= " AND final_price BETWEEN ? AND ?";
                $types .= "dd";
                $params[] = $filter['min_price'];
                $params[] = $filter['max_price'];
            }
        }

        // Sorting and limit
        $sort = !empty($data['sort']) ? $data['sort'] : "created_at";
        $order = strtoupper($data['order'] ?? "DESC");
        if (!in_array($order, ["ASC", "DESC"])) $order = "DESC";
        $query .= " ORDER BY $sort $order";

        $limit = !empty($data['limit']) ? intval($data['limit']) : 100;
        $query .= " LIMIT $limit";

        // Step 3: Execute
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        // Step 4: Currency conversion (only if requested currency ≠ ZAR)
        /* $requestedCurrency = strtoupper($data['currency'] ?? "ZAR");
        if ($requestedCurrency !== "ZAR") {
            $conversionRate = $this->fetchConversionRate($requestedCurrency, "ZAR");
            if ($conversionRate) {
                foreach ($products as &$product) {
                    $product['initial_price'] *= $conversionRate;
                    $product['final_price'] *= $conversionRate;
                    $product['currency'] = "ZAR";  //is this line of code correct??
                }
            }
        } */

        // Step 5: Return
        $this->respondWithSuccess(["products" => $products]);
    }

    //Helper to fetch currency conversion from Wheatley API
    /* private function fetchConversionRate($from, $to) {
        $payload = json_encode([
            "studentnum" => "u23545537",  
            "apikey" => "4c6a1afc39d7b529b7bf07a4d29bce7c",  
            "type" => "convert",
            "from" => $from,
            "to" => $to
        ]);

        $ch = curl_init("https://wheatley.cs.up.ac.za/api/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($response, true);
        if (isset($decoded['status']) && $decoded['status'] === 'success') {
            return $decoded['data']['value'];
        }
        return null;
    } */


    // Generic error response
    public function respondWithError($message, $code) {
        http_response_code($code);
        echo json_encode([
            "status" => "error",
            "timestamp" => time(),
            "message" => $message
        ]);
    }

    // Generic success response
    public function respondWithSuccess($data) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "timestamp" => time(),
            "data" => $data
        ]);
    }
}

//Run the API
API::getInstance()->handleRequest();
?>
