<!doctype html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row mt-5">
            <!-- Card Views -->
            @foreach ($products as $product)
                <div class="col-md-3">
                    <div class="card">
                        <img src="{{ $product->image }}" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title" id="name-{{ $product->id }}">{{ $product->name }}</h5>
                            <p class="card-text">Price: $<span id="price-{{ $product->id }}">{{ $product->price }}</span></p>
                            <a href="#" class="btn btn-primary" data-product-id="{{ $product->id }}" data-product-quantity="{{ $product->quantity }}">Tambah</a>
                            <button class="btn btn-danger btn-delete" data-product-id="{{ $product->id }}">Hapus</button> <!-- Tombol Hapus -->
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Total Table -->
        <div class="row mt-5">
            <div class="col-md-4">
                @if (count($products) > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Action</th> <!-- Kolom Aksi -->
                        </tr>
                    </thead>
                    <tbody id="total-table-body">
                        <!-- Table rows for selected products -->
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product['name'] }}</td>
                                <td>{{ $product['price'] }}</td>
                                <td id="quantity-{{ $product['id'] }}">{{ $product['quantity'] }}</td>
                                <td><button class="btn btn-danger btn-delete" data-product-id="{{ $product['id'] }}">Hapus</button></td> <!-- Tombol Hapus -->
                            </tr>
                        @endforeach
                        <!-- Table row for total price -->
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong>${{ $totalPrice }}</strong></td>
                            <td></td>
                            <td></td> <!-- Kolom kosong untuk konsistensi tampilan -->
                        </tr>
                    </tbody>
                </table>
                @else
                <p>No products selected.</p>
                @endif
                <div class="text-center">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Total</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total</td>
                                <td id="total-price"></td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-primary">Save Bill</button>
                    <button class="btn btn-primary">Print Bill</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Mendapatkan semua elemen tombol "Tambah"
        var addButtons = document.querySelectorAll('.btn-primary');

        // Menambahkan event listener untuk setiap tombol "Tambah"
        addButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                var productId = button.getAttribute('data-product-id');
                var quantityToAdd = parseInt(button.getAttribute('data-product-quantity'));
                addToTotal(productId, quantityToAdd);
            });
        });

        // Fungsi untuk mengirim permintaan POST ke metode addToTotal di controller
        function addToTotal(productId, quantityToAdd) {
            fetch('/add-to-total', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantityToAdd
                })
            })
                .then(function (response) {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Error: ' + response.status);
                    }
                })
                .then(function (data) {
                    console.log(data.message);
                    updateQuantity(productId, quantityToAdd);
                    updatePrice(productId, quantityToAdd, data.newQuantity);
                    updateTotalPrice();
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        // Fungsi untuk memperbarui jumlah produk di tabel total
        function updateQuantity(productId, quantityToAdd) {
            var quantityElement = document.getElementById('quantity-' + productId);
            var currentQuantity = parseInt(quantityElement.textContent);
            var newQuantity = currentQuantity + quantityToAdd;

            quantityElement.textContent = newQuantity;
        }

        // Fungsi untuk memperbarui harga produk di tabel total
        function updatePrice(productId, quantityToAdd) {
            var priceElement = document.getElementById('price-' + productId);
            var currentPrice = parseFloat(priceElement.textContent);
            var newPrice = currentPrice * quantityToAdd;

            priceElement.textContent = newPrice.toFixed(2);
        }

        // Fungsi untuk memperbarui total harga di tabel total
        function updateTotalPrice() {
            var totalPrice = 0;
            var priceElements = document.querySelectorAll('#total-table-body td:nth-child(2)');
            
            priceElements.forEach(function (priceElement) {
                totalPrice += parseFloat(priceElement.textContent);
            });

            var totalPriceElement = document.getElementById('total-price');
            totalPriceElement.textContent = '$' + totalPrice.toFixed(2);
        }

        // Mendapatkan semua elemen tombol "Hapus"
        var deleteButtons = document.querySelectorAll('.btn-delete');

        // Menambahkan event listener untuk setiap tombol "Hapus"
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                var productId = button.getAttribute('data-product-id');
                deleteFromTotal(productId);
            });
        });

        // Fungsi untuk mengirim permintaan POST ke metode deleteFromTotal di controller
        function deleteFromTotal(productId) {
            fetch('/delete-from-total', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
                .then(function (response) {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Error: ' + response.status);
                    }
                })
                .then(function (data) {
                    console.log(data.message);
                    removeProduct(productId);
                    updateTotalPrice();
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        // Fungsi untuk menghapus produk dari tabel total
        function removeProduct(productId) {
            var productRow = document.querySelector('#total-table-body tr[data-product-id="' + productId + '"]');
            productRow.remove();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
