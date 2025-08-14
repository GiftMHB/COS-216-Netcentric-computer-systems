<!--Gift Mohuba 23545527-->
<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>deals</title>
        <link rel="stylesheet" type="text/css" href="../PA4/css/main.css">
        <link rel="stylesheet" type="text/css" href="../PA4/css/deals.css">
        <link rel="stylesheet" type="text/css" href="../PA4/css/products.css">
        <script src="../PA4/js/deals.js" defer></script>
    </head>

    <body>

        <div class="filters">
            <div class="filter-group">

                <div class="filter-group">
                    <label for="main-filter">Filter By:</label>
                    <select id="main-filter" class="filter-select" onchange="updateSubFilter()">
                        <option value="">Select a filter</option>
                        <option value="category">Category</option>
                        <option value="country">Country of Origin</option>
                        <option value="brand">Brand</option>
                    </select>

                    <select id="sub-filter" class="filter-select" style="display: none;"></select>
                </div>

                <div class="sort-group">
                    <select id="sort-by">
                        <option value="created_at">Newest</option>
                        <option value="final_price">Price</option>
                    </select>

                    <select id="sort-order">
                        <option value="ASC">Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>

                </div>

                <div class="currency-filter">
                    <select id="currency">
                        <option value="">Select a currency</option>
                        <option value="AUD">AUD - Australian Dollar</option>
                        <option value="BGN">BGN - Bulgarian Lev</option>
                        <option value="BRL">BRL - Brazilian Real</option>
                        <option value="CAD">CAD - Canadian Dollar</option>
                        <option value="CHF">CHF - Swiss Franc</option>
                        <option value="CNY">CNY - Chinese Yuan</option>
                        <option value="CZK">CZK - Czech Koruna</option>
                        <option value="DKK">DKK - Danish Krone</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="GBP">GBP - British Pound</option>
                        <option value="HKD">HKD - Hong Kong Dollar</option>
                        <option value="HRK">HRK - Croatian Kuna</option>
                        <option value="HUF">HUF - Hungarian Forint</option>
                        <option value="IDR">IDR - Indonesian Rupiah</option>
                        <option value="ILS">ILS - Israeli Shekel</option>
                        <option value="INR">INR - Indian Rupee</option>
                        <option value="ISK">ISK - Icelandic Krona</option>
                        <option value="JPY">JPY - Japanese Yen</option>
                        <option value="KRW">KRW - South Korean Won</option>
                        <option value="MXN">MXN - Mexican Peso</option>
                        <option value="MYR">MYR - Malaysian Ringgit</option>
                        <option value="NOK">NOK - Norwegian Krone</option>
                        <option value="NZD">NZD - New Zealand Dollar</option>
                        <option value="PHP">PHP - Philippine Peso</option>
                        <option value="PLN">PLN - Polish Zloty</option>
                        <option value="RON">RON - Romanian Leu</option>
                        <option value="RUB">RUB - Russian Ruble</option>
                        <option value="SEK">SEK - Swedish Krona</option>
                        <option value="SGD">SGD - Singapore Dollar</option>
                        <option value="THB">THB - Thai Baht</option>
                        <option value="TRY">TRY - Turkish Lira</option>
                        <option value="USD">USD - US Dollar</option>
                        <option value="ZAR" selected>ZAR - South African Rand</option> 
                    </select>
                </div>

                <label class="filter-label">Price Range :</label>
                <div class="manual-price-range">
                    <input type="number" id="min-price" placeholder="Min Price"/>
                    <input type="number" id="max-price" placeholder="Max Price"/>
                </div>

                <button type="button" onclick="allFilters()">Apply filters</button>
                <button type="button" onclick="saveFilters()">save filters</button>
            </div>
        </div>

            
        </div>
        <div class="banner">
            <h1>🔥 Best Deals - Up to 85% Off! 🔥</h1>
        </div>
        <div class="main-content">

            <div class="products">

                <div class="loader-container">
                    <div class="loader">
                    <span class="loader-text">loading</span>
                    <span class="load"></span>
                    </div>
                </div>

                <div class="product-holder">
                    
                </div>  
            </div>
        </div>

        <?php include('footer.php'); ?>
    </body>
</html>

