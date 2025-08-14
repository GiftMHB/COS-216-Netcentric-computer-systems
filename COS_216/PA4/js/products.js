//Gift Mohuba 23545527
window.onload = function () {
    setTimeout(fetchProducts, 2000);
};

var products = [];
var filteredProducts = [];
var filters = [];

function fetchProducts() {
    const productHolder = document.querySelector(".products");
    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            
            if (response.status === 'success' && response.data) {
                products = response.data.products;
                // filteredProducts = response.data;
                localStorage.setItem("allProducts", JSON.stringify(products));
                getCurrency();
                // displayProducts(products);
            } else {
                productHolder.innerHTML = '<p>No products found.</p>';
            }
        }
    };

    const url = "https://wheatley.cs.up.ac.za/u23545527/api.php";
    const data = JSON.stringify({
        type: "GetAllProducts",
        apikey: "7a5c10a62a1fa94ca7aa0cc9f6d44868",
        return: [
            "id", "title", "brand", "description", "initial_price", "final_price",
            "currency", "categories", "image_url", "product_dimensions", "date_first_available",
            "manufacturer", "department", "features", "is_available", "images",
            "country_of_origin", "created_at", "updated_at"
        ],
        limit: 50
    });

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(data);
}

function displayProducts(products, currency = "ZAR") 
{
    const productsContainer = document.querySelector('.products'); 

    let output = '';  
    products.forEach(product => {
        output += `
            <div class="product-holder">
                <p>${product.brand}</p>
                <img class="product-image" src="${product.image_url}" alt="${product.title}">
                <p>${product.title}</p>
                <p>${product.converted_price} ${currency}</p>
                <div class="product-actions">
                    <button class="wishlist-btn" onclick="addToWishlist(${product.id}, this)">❤️ Wishlist</button>
                    <button class="cart-btn">🛒 Add to Cart</button>
                </div>
                <button class="info-btn" onclick="viewProduct(${product.id})">ℹ️ More Info</button>
            </div>`;
    });

    productsContainer.innerHTML = output;
}

function addToWishlist(productId, btn) 
{
    const apikey = localStorage.getItem("apikey");
    if (!apikey) {
        alert("You must be logged in to use the wishlist.");
        return;
    }

    fetch("../../api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            type: "add_wishlist",
            apikey: apikey,
            product_id: productId,
        })
    })
    .then(res => res.text()) 
    .then(text => {
        console.log("Raw response:", text); 
        const data = JSON.parse(text); 
        if (data.status === "success") {
            btn.innerText = "💖 Wishlisted";
            btn.disabled = true;
        } else {
            alert(data.message || "Failed to add to wishlist.");
        }
    })
    .catch(error => {
        console.error("Error adding to wishlist:", error);
        alert("Something went wrong,Please try again.");
    });
}


function getCurrency() 
{
    var req = new XMLHttpRequest();
    
    const url = 'https://wheatley.cs.up.ac.za/api/';
    const data = JSON.stringify({
        studentnum: 'u23545527',
        apikey: '4c6a1afc39d7b529b7bf07a4d29bce7c',
        type: 'GetCurrencyList', 
    });

    req.open("POST", url, true);
    req.send(data);

    req.onreadystatechange = function() 
    {
        if(req.readyState == 4 && req.status == 200) 
        {
            var res = JSON.parse(req.responseText);  
            localStorage.setItem("rates", JSON.stringify(res.data));
            conversion(res.data); 
        }
    }
}

function conversion(rates) 
{
    const currencyDropdown = document.getElementById("currency");
    
    let curr;
    if(currencyDropdown) 
    {
        curr = currencyDropdown.value;
    } 
    else 
    {
        curr = "ZAR";
    }

    const exchangeRate = rates[curr]; 
    // console.log("Exchange rate for", curr, ">", exchangeRate);

    // console.log("here is before the map function " + products[0]);
    products = products.map(product => {
        return {
            ...product,
            converted_price: (product.final_price * exchangeRate ).toFixed(2),
        };
    });

    displayProducts(products, curr); 
}

function allFilters() {
    const apikey = localStorage.getItem("apikey");
    if (!apikey) {
        alert("You must be logged in to view filtered products.");
        return;
    }

    fetch("../../api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            type: "GetAllProducts",
            apikey: apikey,
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== "success") {
            alert("Failed to load products: " + data.message);
            return;
        }

        let products = data.data.products || [];

        const mainFilter = document.getElementById("main-filter").value;
        const subFilter = document.getElementById("sub-filter").value.trim().toLowerCase();

        const currency = document.getElementById("currency").value;
        const rates = JSON.parse(localStorage.getItem("rates")) || {};
        const rate = rates[currency] || 1;

        const minPrice = parseFloat(document.getElementById("min-price").value);
        const maxPrice = parseFloat(document.getElementById("max-price").value);

        const sortBy = document.getElementById("sort-by").value;
        const sortOrder = document.getElementById("sort-order").value;

        // Filter by one type: brand, category, or country
        const fieldMap = {
            brand: "brand",
            category: "categories",
            country: "country_of_origin"
        };

        const fieldKey = fieldMap[mainFilter];

        if (fieldKey && subFilter) {
            products = products.filter(product => {
                let value = "";

                if (fieldKey === "categories") {
                    try {
                        const cats = JSON.parse(product.categories);
                        return cats.some(cat => cat.trim().toLowerCase().includes(subFilter));
                    } catch (e) {
                        return false;
                    }
                } else {
                    value = (product[fieldKey] || "").toString().toLowerCase();
                    return value.includes(subFilter);
                }
            });
        }

        // Filter by price range
        if (!isNaN(minPrice) && !isNaN(maxPrice)) {
            products = products.filter(p => p.final_price >= minPrice && p.final_price <= maxPrice);
        }

        // Sort
        products.sort((a, b) => {
            let valA = a[sortBy];
            let valB = b[sortBy];

            if (typeof valA === "string") valA = valA.toLowerCase();
            if (typeof valB === "string") valB = valB.toLowerCase();

            if (valA < valB) return sortOrder === "ASC" ? -1 : 1;
            if (valA > valB) return sortOrder === "ASC" ? 1 : -1;
            return 0;
        });

        // Apply currency conversion
        const finalProducts = products.map(p => ({
            ...p,
            converted_price: (p.final_price * rate).toFixed(2)
        }));

        displayProducts(finalProducts, currency);
    })
    .catch(error => {
        console.error("Error loading products:", error);
    });
}

function saveFilters()//must fix the country thing here!!!
{
    const apikey = localStorage.getItem("apikey");
    if(apikey === null){
        alert("You have to be logged in to save filters!!");
        return;
    }

    // console.log("Saving filters with API key:", apikey);

    let mainFilter = document.getElementById("main-filter").value;
    const subFilter = document.getElementById("sub-filter").value;
    let minPrice = parseFloat(document.getElementById("min-price").value);
    let maxPrice = parseFloat(document.getElementById("max-price").value);
    const currencyFilter = document.getElementById("currency").value;

    if (isNaN(minPrice)) minPrice = null; 
    if (isNaN(maxPrice)) maxPrice = null; 

    let category = null;
    let country = null;

    if (mainFilter === "Category") {
        category = subFilter; 
    } 
    else if (mainFilter === "Brand") {
        category = subFilter; 
    } 
    else {
        country = subFilter; 
    }


    fetch("../../api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            type: "save",
            apikey: apikey,
            filters: {
                category: category,    
                country_of_origin: country,   
                minPrice: minPrice,              
                maxPrice: maxPrice                
            },
            currency:  currencyFilter
        })
    })
    .then(response => response.json())  
    .then(data => {
        if (data.status === "success") 
        {
            // console.log('Preferences saved successfully');
            alert("filters successfully saved");
        } else {
            console.error('Error saving preferences:', data.message);
            alert("Failed to save filters,try again");
        }
    })
    .catch(error => {
        console.error('Error making request:', error);
    });    
}

function search() 
{
    const currencyDropdown = document.getElementById("currency").value;
    const input = document.getElementById("myInput").value.trim();
    const searchTerm = input.toLowerCase();

    const apikey = localStorage.getItem("apikey");
    if (!apikey) {
        alert("You must be logged in to search.");
        return;
    }

    fetch("../../api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            type: "GetAllProducts",
            apikey: apikey,
            filter: {
                category: searchTerm
            }
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            const rates = JSON.parse(localStorage.getItem("rates"));
            const exchangeRate = rates[currencyDropdown] || 1;

            const processed = data.data.products.map(product => ({
                ...product,
                converted_price: (product.final_price * exchangeRate).toFixed(2)
            }));

            displayProducts(processed, currencyDropdown);
        } else {
            alert("Search failed: " + data.message);
        }
    })
    .catch(error => {
        console.error("Search error:", error);
    });
}

function priceRangeFilter() //should consider making it work alone!!!
{
    const minPrice = parseFloat(document.getElementById("min-price").value);
    const maxPrice = parseFloat(document.getElementById("max-price").value);
    const currencyDropdown = document.getElementById("currency").value;

    const allProducts = JSON.parse(localStorage.getItem("allProducts"));
    const rates = JSON.parse(localStorage.getItem("rates"));

    const exchangeRate = rates[currencyDropdown];

    const filterProducts = allProducts.map(product => {
        return {
            ...product,
            converted_price: parseFloat((product.final_price * exchangeRate).toFixed(2)),
        };
    });

    let filteredProducts = filterProducts;

    if (!isNaN(minPrice) && !isNaN(maxPrice)) {
        filteredProducts = filterProducts.filter(product => {
            return product.converted_price >= minPrice && product.converted_price <= maxPrice;
        });
    }

    displayProducts(filteredProducts, currencyDropdown);
}

/* function sortProducts() //might as well delete it!!
{
    const sortSelect = document.getElementById("sort-options").value;
    const currencyDropdown = document.getElementById("currency").value;

    // console.log("currency in sort products is " + currencyDropdown);
    // console.log("selected is " + sortSelect);

    if (!products || products.length === 0) return;

    switch (sortSelect) {
        case "newest":
            products.sort((a, b) => new Date(b.date_added) - new Date(a.date_added));//check this
            break;
        case "price-asc":
            products.sort((a, b) => a.converted_price - b.converted_price);
            break;
        case "price-desc":
            products.sort((a, b) => b.converted_price - a.converted_price);
            break;
        default:
            return;
    }
    displayProducts(products,currencyDropdown); 
} */

function updateSubFilter() 
{
    const mainFilter = document.getElementById("main-filter").value;
    const subFilter = document.getElementById("sub-filter");

    if (!products || products.length === 0) return;

    let options = new Set(); 

    // console.log("here are caategory options " + options);

    if (mainFilter === "category") 
    {
        // products.forEach(product => product.categories.forEach(cat => options.add(cat)));
        products.forEach(product => {
            let categoriesArray;
    
            categoriesArray = JSON.parse(product.categories); 

        if (Array.isArray(categoriesArray)) {
            categoriesArray.forEach(cat => options.add(cat.trim()));
        }});
    } 
    else if(mainFilter === "country") 
    {
        products.forEach(product => options.add(product.country_of_origin));
    } 
    else if(mainFilter === "brand") 
    {
        products.forEach(product => options.add(product.brand));
    } 
    else 
    {
        subFilter.style.display = "none";
        return;
    }

    // Populate the sub-filter dropdown
    subFilter.innerHTML = `<option value="">Select ${mainFilter}</option>`;
    options.forEach(option => {subFilter.innerHTML += `<option value="${option}">${option}</option>`;});

    subFilter.style.display = "block"; 
}

function viewProduct(productId)
{
    localStorage.setItem("selectedProduct",productId);
    window.location.href = "view.php";
}