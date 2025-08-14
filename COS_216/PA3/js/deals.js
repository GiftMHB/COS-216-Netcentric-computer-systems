//Gift Mohuba u23545527
/* I used Asynchronous calls because of the following reasons:
Nonblocking
Parallel execution
Sends a request and does not wait/block until a reply comes back
Continue executing other code while waiting for the reply
*/

const API_KEY = '4c6a1afc39d7b529b7bf07a4d29bce7c';
const STUDENT_NUM = 'u23545527';

var products = [];

setTimeout(fetchProducts,2000);

function fetchProducts()
{
    const items = new XMLHttpRequest();

    const url = 'https://wheatley.cs.up.ac.za/api/';
    const data = JSON.stringify({
        studentnum : STUDENT_NUM,
        apikey : API_KEY,
        type : 'GetAllProducts',
        sort: 'title',
        order: 'ASC',
        return: "*",
        limit: 50 
    });

    items.open('POST',url,true);
    items.send(data);

    items.onreadystatechange = function()
    {
        if(items.readyState === 4 && items.status === 200)
        {
            const response = JSON.parse(items.responseText);
            if(response.status === 'success' && response.data)
            {
                getCurrency();
                const currencyDropdown = document.getElementById("currency").value;
                products = response.data;
                products = products.map(product => {
                    let updatedProduct = { ...product }; 
                
                    updatedProduct.discount_percentage = ((updatedProduct.initial_price - updatedProduct.final_price) / updatedProduct.initial_price * 100).toFixed(2);
                
                    return updatedProduct;
                });
                products = products.filter(product => product.discount_percentage >= 10);
                displayProducts(products,currencyDropdown);
            }
            else
            {
                console.log("products array is empty");
            }
        }
    }
}

function displayProducts(products, currency = "ZAR") 
{
    const productsContainer = document.querySelector('.products'); 
     
    // console.log(products[0]);
    
    let output = '';  
    products.forEach(product => {
        output += `
            <div class="product-holder">
                <p>${product.brand}</p>
                <img class="product-image" src="${product.image_url}" alt="${product.title}">
                <p>${product.title}</p>

                <div style="margin-bottom: 10px;">
                    <span style="text-decoration: line-through; color: darkgray; margin: 0 10px; display: inline-block;">${product.initial_price} ${currency}</span>
                    <span style="color: darkgray; font-weight: bold; margin: 0 10px; display: inline-block;">${product.final_price} ${currency}</span>
                    <span style="color: red; font-weight: bold; margin: 0 10px; display: inline-block;">${product.discount_percentage}%</span>
                </div>

                <div class="product-actions">
                    <button class="wishlist-btn">❤️ Wishlist</button>
                    <button class="cart-btn">🛒 Add to Cart</button>
                </div>
                <button class="info-btn">ℹ️ More Info</button>
            </div>`;
    });

    productsContainer.innerHTML = output;
}
function getCurrency() 
{
    var req = new XMLHttpRequest();
    
    const url = 'https://wheatley.cs.up.ac.za/api/';
    const data = JSON.stringify({
        studentnum: STUDENT_NUM,
        apikey: API_KEY,
        type: 'GetCurrencyList', 
    });

    req.open("POST", url, true);
    req.send(data);

    req.onreadystatechange = function() 
    {
        if(req.readyState == 4 && req.status == 200) 
        {
            var res = JSON.parse(req.responseText);  
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

    // console.log("Selected currency:", curr);
    // console.log("Rates of selected currency:", rates[curr]);

    const exchangeRate = rates[curr]; 
    // console.log("Exchange rate for", curr, ">", exchangeRate);

    products = products.map(product => {
        return {
            ...product,
            converted_price: (product.final_price * exchangeRate ).toFixed(2),
        };
    });

    displayProducts(products, curr); 
}

//absolute path 
//use relative path

function search() 
{
    var input = document.getElementById("myInput").value.trim();
    // const sortSelect = document.getElementById("sort-options").value;
    const currencyDropdown = document.getElementById("currency").value;
    var productHolder = document.querySelector(".products");
    // var notFound = document.getElementById("notFound");

    /* if(!notFound) 
    {
        console.warn("⚠️ Warning: #notFound element not found in the DOM.");
    } */

    var searchTerm = input.toLowerCase();
    var filteredProducts = products.filter(product => product.title.toLowerCase().includes(searchTerm));

    if(filteredProducts.length <= 0) 
    {
        //here product is not found
        productHolder.innerHTML = '<p>No products found.</p>';
    } 
    displayProducts(filteredProducts,currencyDropdown); 
}


function sortProducts() 
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
        case "percentage-asc":
            products.sort((a, b) => a.discount_percentage - b.discount_percentage);
            break;
        case "percentage-desc":
            products.sort((a, b) => b.discount_percentage - a.discount_percentage);
            break;
        default:
            return;
    }
    displayProducts(products,currencyDropdown); 
}

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
    
            categoriesArray = JSON.parse(product.categories); // Convert string to array

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

    subFilter.style.display = "block"; // Show the dropdown
}

function filterProducts() 
{
    const mainFilter = document.getElementById("main-filter").value;
    const subFilter = document.getElementById("sub-filter").value;
    const currencyDropdown = document.getElementById("currency").value;

    // console.log("Filtering by:", mainFilter, "Value:", subFilter);
    // console.log("Products:", products);

    if (!products || products.length === 0 || !subFilter) return;

    let filteredProducts = products;

    if (mainFilter === "category") {
        filteredProducts = products.filter(product => product.categories.includes(subFilter));
    } 
    else if (mainFilter === "country") {
        filteredProducts = products.filter(product => product.country_of_origin === subFilter);
    } 
    else if (mainFilter === "brand") {
        filteredProducts = products.filter(product => product.brand === subFilter);
    }

    displayProducts(filteredProducts,currencyDropdown); 
}