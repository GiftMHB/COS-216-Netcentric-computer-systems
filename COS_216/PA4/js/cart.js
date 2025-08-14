//Gift Mohuba 
/* I used Asynchronous calls because of the following reasons:
Nonblocking
Parallel execution
Sends a request and does not wait/block until a reply comes back
Continue executing other code while waiting for the reply
 */
const API_KEY = '';
const STUDENT_NUM = '';

var products = [];

setTimeout(fetchProducts, 2000);

function fetchProducts() 
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
                products = response.data.map(function(product) {
                    return {
                        title: product.title,
                        final_price: product.final_price,
                        currency: product.currency,
                        image_url: product.image_url,
                        quantity: 1,
                        totalPrice: parseFloat(product.final_price)
                    };
                });
                displayProducts();
            }
            else 
            {
                console.log("Failed to fetch products.");
            }
        }
    };
}

function displayProducts() 
{
    const cartContainer = document.querySelector('.cart-container');
    let output = '';

    products.forEach((product, index) => {
        output += `
            <div class="cart-item" data-index="${index}">
                <img src="${product.image_url}" alt="${product.title}">
                <div class="item-info">
                    <p><strong>${product.title}</strong></p>
                    <p>Price: <span class="price">${product.final_price} ${product.currency}</span></p>
                    <p>
                        Quantity: 
                        <button class="decrease" data-index="${index}">➖</button>
                        <span class="quantity">${product.quantity}</span>
                        <button class="increase" data-index="${index}">➕</button>
                    </p>
                    <p>Total: <span class="total">${product.totalPrice.toFixed(2)} ${product.currency}</span></p>
                </div>
                <button class="remove-btn" data-index="${index}">❌ Remove</button>   
            </div>`;
    });

    cartContainer.innerHTML = output;
    updateCartSummary();

    attachEventListeners();
}

function attachEventListeners() 
{
    document.querySelectorAll('.increase').forEach(button => {
        button.addEventListener('click', function () {
            updateQuantity(this.dataset.index, 1);
        });
    });

    document.querySelectorAll('.decrease').forEach(button => {
        button.addEventListener('click', function () {
            updateQuantity(this.dataset.index, -1);
        });
    });

    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function () {
            removeProduct(this.dataset.index);
        });
    });
}

function updateQuantity(index, change) 
{
    index = parseInt(index);
    if(products[index]) 
    {
        products[index].quantity += change;
        if (products[index].quantity < 1) products[index].quantity = 1;
        products[index].totalPrice = products[index].quantity * parseFloat(products[index].final_price);

        displayProducts();
    }
}

function removeProduct(index) 
{
    index = parseInt(index);
    products.splice(index, 1);
    displayProducts();
}

function updateCartSummary() 
{
    let subtotal = products.reduce((sum, product) => sum + product.totalPrice, 0);
    document.querySelector('.cart-summary').innerHTML = `
        <p>Subtotal: R${subtotal.toFixed(2)}</p>
        <p>Grand Total: R${subtotal.toFixed(2)}</p>
        <button class="checkout-btn">💳 Checkout</button>
    `;
}

