//Gift Mohuba u23545527
document.addEventListener("DOMContentLoaded", function() 
{
    const apikey = localStorage.getItem("apikey");
    if (!apikey) {
        alert("You must be logged in to view more information");
        return;
    }

    let products = JSON.parse(localStorage.getItem("allProducts")) || [];
    // console.log("Stored Products:", products);

    let productId = localStorage.getItem("selectedProduct");
    // console.log("Stored Product ID:", productId);

    let selectedProduct = products.find(product => product.id == productId);
    // console.log("Selected Product:", selectedProduct);

    document.querySelector(".description").innerText = selectedProduct.description;

    let detailsList = document.querySelector(".extra-details");
    
    detailsList.innerHTML = `
        <li><strong>Title : </strong> ${selectedProduct.title} </li>
        <li><strong>Initial Price : </strong> ${selectedProduct.initial_price} ${selectedProduct.currency}</li>
        <li><strong>Final Price : </strong> ${selectedProduct.final_price} ${selectedProduct.currency}</li>
        <li><strong>Brand : </strong> ${selectedProduct.brand}</li>
        <li><strong>Product dimensions : </strong> ${selectedProduct.product_dimensions}</li>
        <li><strong>Product manufacturer : </strong> ${selectedProduct.manufacturer}</li>
        <li><strong>Country of Origin : </strong> ${selectedProduct.country_of_origin}</li>
        <li><strong>features : </strong> ${JSON.parse(selectedProduct.features).join(", ")}</li>
        <li><strong>Categories : </strong> ${JSON.parse(selectedProduct.categories).join(", ")}</li>
    `;

    let productHolder = document.querySelector(".product-holder");
    productHolder.innerHTML = ""; // Clear previous content
    // console.log(selectedProduct.image_url);
    let images = JSON.parse(selectedProduct.images);
    if (images.length === 0) return; 

    let currentIndex = 0;
    const imageElement = document.createElement("img");
    imageElement.src = selectedProduct.image_url; 
    imageElement.alt = selectedProduct.title;
    imageElement.style.width = "500px"; 
    imageElement.style.margin = "5px";
    imageElement.style.height = "450px";
    imageElement.classList.add("carousel-image"); 
    productHolder.appendChild(imageElement);

    
    function cycleImages() {
        currentIndex = (currentIndex + 1) % images.length;
        imageElement.src = images[currentIndex]; 
    }

    setInterval(cycleImages, 5000);
});

