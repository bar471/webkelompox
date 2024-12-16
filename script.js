document.addEventListener("DOMContentLoaded", () => {
  const addToCartButton = document.getElementById("addToCart");
  const cartList = document.getElementById("cartList");
  const clearCartButton = document.getElementById("clearCart");
  const checkoutButton = document.getElementById("checkoutBtn");

  // Function to get cart from localStorage
  const getCart = () => JSON.parse(localStorage.getItem("cart")) || [];

  // Function to save cart to localStorage
  const setCart = (cart) => localStorage.setItem("cart", JSON.stringify(cart));

  // Jika di halaman pembelian
  if (addToCartButton) {
    addToCartButton.addEventListener("click", () => {
      const coffeeType = document.getElementById("coffeeType").value;
      const temperature = document.getElementById("temperature").value;
      const sugar = document.getElementById("sugar").value;
      const quantity = parseInt(document.getElementById("quantity").value, 10);

      const coffeeOption = document.querySelector(`#coffeeType option[value='${coffeeType}']`);
      const price = parseInt(coffeeOption.getAttribute("data-price"));

      // Validate input
      if (!coffeeType || !temperature || !sugar || !quantity) {
        alert("Pastikan semua data terisi dengan benar!");
        return;
      }

      const cart = getCart();

      cart.unshift({
        coffeeType,
        temperature,
        sugar,
        quantity,
        price,
        totalPrice: price * quantity,
        time: new Date().toISOString()
      });

      setCart(cart);

      alert("Item berhasil ditambahkan ke keranjang!");
    });
  }

  // Jika di halaman invoice
  if (cartList) {
    const cart = getCart();
    let totalAmount = 0;

    cartList.innerHTML = ''; // Clear previous items in cart list

    cart.forEach((item, index) => {
      const li = document.createElement("li");
      li.innerHTML = `
        ${item.quantity}x ${item.coffeeType} 
        Suhu: ${item.temperature}, Gula: ${item.sugar} 
        - Rp ${item.totalPrice.toLocaleString()} 
        - Pesanan: ${new Date(item.time).toLocaleString()}
        <button class="delete-btn" data-index="${index}">Delete</button>
      `;
      cartList.appendChild(li);
      totalAmount += item.totalPrice;
    });

    // Display total price
    if (!document.getElementById("totalPriceDisplay")) {
      const totalPriceDisplay = document.createElement("div");
      totalPriceDisplay.id = "totalPriceDisplay";
      totalPriceDisplay.textContent = `Total Harga: Rp ${totalAmount.toLocaleString()}`;
      cartList.appendChild(totalPriceDisplay);
    }

    // Event listener untuk tombol Delete
    cartList.addEventListener("click", (event) => {
      if (event.target.classList.contains("delete-btn")) {
        const index = event.target.getAttribute("data-index");
        cart.splice(index, 1); // Hapus item berdasarkan index
        setCart(cart); // Simpan perubahan
        event.target.closest("li").remove(); // Remove item from DOM directly
      }
    });
  }

  // Menangani tombol Clear Cart
  if (clearCartButton) {
    clearCartButton.addEventListener("click", () => {
      localStorage.removeItem("cart");
      cartList.innerHTML = '';
      alert("Keranjang telah dibersihkan!");
    });
  }

  // Menangani tombol Checkout
  if (checkoutButton) {
    checkoutButton.addEventListener("click", () => {
      const cart = getCart();
      const totalAmount = cart.reduce((total, item) => total + item.totalPrice, 0);

      if (cart.length === 0) {
        alert("Keranjang kosong, tidak dapat melakukan checkout!");
        return;
      }

      // Persiapkan data yang akan dikirim ke server
      const orderData = {
        cart, // Semua item di keranjang
        totalAmount, // Total harga
        timestamp: new Date().toISOString() // Waktu checkout
      };

      fetch('http://localhost:8000/pembelian.php', { 
        method: 'POST',
        headers: {
          
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData),
      })
      .then(response => {
        // Cek apakah status HTTP adalah 200 OK
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json(); // Parse JSON response
      })
      .then(data => {
        if (data.orderId) {
          alert("Checkout berhasil! ID Pesanan: " + data.orderId);
        } else {
          alert("Terjadi kesalahan saat memproses pesanan.");
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert("Terjadi kesalahan saat checkout. Coba lagi nanti.");
      });
      
    });
  }
});
