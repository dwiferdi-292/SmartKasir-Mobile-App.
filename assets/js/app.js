// app.js
document.addEventListener('DOMContentLoaded', () => {
    // Add micro animations for elements
    const inputs = document.querySelectorAll('.form-group input, .form-group select');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('focused');
        });
    });

    // POS Cart Logic (if on POS page)
    const cartItemsWrapper = document.getElementById('cartItems');
    if (cartItemsWrapper) {
        window.cart = [];

        window.addToCart = function(id, name, price, stock) {
            const existing = window.cart.find(i => i.id === id);
            
            // limit to stock 
            if (existing) {
                if (existing.qty + 1 <= stock) {
                    existing.qty += 1;
                } else {
                    alert('Stok tidak cukup!');
                    return;
                }
            } else {
                if (stock > 0) {
                    window.cart.push({ id, name, price, qty: 1, stock: stock });
                } else {
                    alert('Stok habis!');
                    return;
                }
            }
            renderCart();
        };

        window.removeFromCart = function(id) {
            window.cart = window.cart.filter(i => i.id !== id);
            renderCart();
        };

        window.updateQty = function(id, qty) {
            const item = window.cart.find(i => i.id === id);
            if (item) {
                if (parseInt(qty) > item.stock) {
                    alert('Stok tidak cukup! Maksimal ' + item.stock);
                    item.qty = item.stock;
                } else {
                    item.qty = parseInt(qty);
                }
                
                if (item.qty <= 0) removeFromCart(id);
                else renderCart();
            }
        };

        function renderCart() {
            cartItemsWrapper.innerHTML = '';
            let total = 0;
            window.cart.forEach(item => {
                const subtotal = item.price * item.qty;
                total += subtotal;
                cartItemsWrapper.innerHTML += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-title">${item.name}</div>
                            <div style="font-size: 0.85rem; color: #64748b;">Rp ${parseInt(item.price).toLocaleString()}</div>
                        </div>
                        <div class="cart-item-actions" style="display:flex; align-items:center; gap: 10px;">
                            <input type="number" value="${item.qty}" min="1" max="${item.stock}" onchange="updateQty(${item.id}, this.value)" style="width: 50px; padding: 5px; border:1px solid #ccc; border-radius:4px;">
                            <div style="font-weight:600; min-width: 80px; text-align:right;">Rp ${subtotal.toLocaleString()}</div>
                            <button onclick="removeFromCart(${item.id})" class="btn-danger" style="padding: 5px 10px;">&times;</button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('cartTotal').innerText = 'Rp ' + total.toLocaleString();
            document.getElementById('cartTotalInput').value = total;
            document.getElementById('cartDataInput').value = JSON.stringify(window.cart);
        }

        // Live Search Product POS
        const searchInput = document.getElementById('searchProduct');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.product-card');
                cards.forEach(card => {
                    const name = card.querySelector('h3').innerText.toLowerCase();
                    if (name.includes(term)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    }
});
