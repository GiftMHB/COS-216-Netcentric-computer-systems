//Gift Mohuba u
/* I used Asynchronous calls because of the following reasons:
Nonblocking
Parallel execution
Sends a request and does not wait/block until a reply comes back
Continue executing other code while waiting for the reply
*/
const A';
const STUDENT_NUM;

setTimeout(fetchWishlist,2000);

function fetchWishlist() 
{
    const pro = new XMLHttpRequest();
    const url = 'https://wheatley.cs.up.ac.za/api/';
    const data = JSON.stringify({
        studentnum: STUDENT_NUM,
        apikey: API_KEY,
        type: 'GetAllProducts',
        sort: 'title',
        order: 'ASC',
        return: "*",
        limit: 5
    });

    pro.open('POST', url, true);
    pro.send(data);

    pro.onreadystatechange = function () 
    {
        if(pro.readyState === 4 && pro.status === 200) 
        {
            const response = JSON.parse(pro.responseText);
            if(response.status === 'success' && response.data) 
            {
                displayWishlist(response.data);
            } 
            else 
            {
                console.log("Failed to fetch wishlist products.");
            }
        }
    };
}

function displayWishlist(products) 
{
    const wishlistContainer = document.querySelector('.wishlist-container');
    let output = '<h4>My Wishlist</h4>';

    products.forEach(product => {
        output += `
            <div class="wishlist-item">
                <img src="${product.image_url}" alt="${product.title}">
                <div class="wishlist-info">
                    <h3>${product.title}</h3>
                    <p class="price">${product.final_price} ${product.currency}</p>
                    <button class="remove-btn">❌ Remove</button>
                    <button class="cart-btn">🛒 Add to Cart</button>
                </div>
            </div>`;
    });

    wishlistContainer.innerHTML = output;
}

