const API_KEY = localStorage.getItem("apikey");

setTimeout(fetchWishlist, 2000);

function fetchWishlist() 
{
    if (!API_KEY) {
        alert("You must be logged in to view your wishlist.");
        return;
    }

    fetch("../../api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            type: "get_wishlist",
            apikey: API_KEY
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success" && data.data) {
            displayWishlist(data.data);
        } else {
            console.error("Failed to fetch wishlist:", data.message);
        }
    })
    .catch(err => {
        console.error("Error fetching wishlist:", err);
    });
}

function displayWishlist(products) {
    const wishlistContainer = document.querySelector('.wishlist-container');
    let output = '<h4>My Wishlist</h4>';

    products.forEach(product => {
        output += `
            <div class="wishlist-item">
                <img src="${product.image_url}" alt="${product.title}">
                <div class="wishlist-info">
                    <h3>${product.title}</h3>
                    <p class="price">${product.final_price} ${product.currency}</p>
                    <button class="remove-btn" onclick="removeFromWishlist(${product.id},this)">❌ Remove</button>
                    <button class="cart-btn">🛒 Add to Cart</button>
                </div>
            </div>`;
    });

    wishlistContainer.innerHTML = output;
}

function removeFromWishlist(productId,btn)
{
    const apikey = localStorage.getItem("apikey");

    fetch("../../api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            type: "remove_wishlist",
            apikey: apikey,
            product_id: productId,
        })
    })
    .then(res => res.text()) 
    .then(text => {
        console.log("Raw response:", text); 
        const data = JSON.parse(text); 
        if (data.status === "success") {
            btn.innerText = "❤️ Wishlist";
            btn.disabled = true;
        } else {
            alert(data.message || "Failed to remove");
        }
    })
    .catch(error => {
        console.error("Error removing wishlist:", error);
        alert("Something went wrong,Please try again.");
    });
}