// ============================================
// AL-NAAZ FOOD - Main JavaScript
// ============================================

// ===== NAVBAR TOGGLE =====
function toggleNav() {
    const navLinks = document.getElementById('navLinks');
    const navOverlay = document.getElementById('navOverlay');
    
    navLinks.classList.toggle('active');
    navOverlay.classList.toggle('active');
    
    // Prevent body scroll
    document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : '';
}

// Close nav on link click
document.querySelectorAll('.nav-links ul li a').forEach(link => {
    link.addEventListener('click', () => {
        const navLinks = document.getElementById('navLinks');
        const navOverlay = document.getElementById('navOverlay');
        
        if (window.innerWidth <= 768) {
            navLinks.classList.remove('active');
            navOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});

// ===== NAVBAR SCROLL EFFECT =====
window.addEventListener('scroll', () => {
    const navbar = document.getElementById('mainNav');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// ===== CART FUNCTIONS =====
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function addToCart(productId, name, price, image) {
    const existing = cart.find(item => item.id === productId);
    
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: name,
            price: price,
            image: image || 'placeholder.jpg',
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    openCart();
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    renderCartItems();
}

function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
            return;
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
        renderCartItems();
    }
}

function updateCartUI() {
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('cartCount').textContent = count;
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    document.getElementById('cartTotal').textContent = '₹' + total.toFixed(2);
}

function renderCartItems() {
    const container = document.getElementById('cartItems');
    const footer = document.querySelector('.cart-footer');
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-basket"></i>
                <p>Your cart is empty</p>
                <a href="${window.location.origin}/al-naaz-food/pages/products.php" class="btn-primary">Browse Products</a>
            </div>
        `;
        footer.style.display = 'none';
        return;
    }
    
    footer.style.display = 'block';
    
    container.innerHTML = cart.map(item => `
        <div class="cart-item">
            <img src="${window.location.origin}/al-naaz-food/assets/uploads/${item.image}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/70x70/1A1A1A/D4AF37?text=AL'">
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <div class="price">₹${item.price.toFixed(2)}</div>
                <div class="qty-control">
                    <button onclick="updateQuantity('${item.id}', -1)">−</button>
                    <span>${item.quantity}</span>
                    <button onclick="updateQuantity('${item.id}', 1)">+</button>
                    <button onclick="removeFromCart('${item.id}')" style="background:transparent;border:none;color:#8B1A1A;font-size:18px;margin-left:10px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    updateCartUI();
}

function openCart() {
    document.getElementById('cartSidebar').classList.add('active');
    document.body.style.overflow = 'hidden';
    renderCartItems();
}

function closeCart() {
    document.getElementById('cartSidebar').classList.remove('active');
    document.body.style.overflow = '';
}

function checkout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    
    // Check if user is logged in
    fetch(`${window.location.origin}/al-naaz-food/api/check-auth.php`)
        .then(response => response.json())
        .then(data => {
            if (!data.loggedIn) {
                if (confirm('Please login to proceed with checkout. Go to login page?')) {
                    window.location.href = `${window.location.origin}/al-naaz-food/auth/login.php`;
                }
                return;
            }
            
            // Proceed to checkout
            window.location.href = `${window.location.origin}/al-naaz-food/pages/checkout.php`;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        });
}

// Close cart on ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCart();
        document.getElementById('navLinks').classList.remove('active');
        document.getElementById('navOverlay').classList.remove('active');
    }
});

// ===== WHATSAPP ENQUIRY =====
function whatsappEnquiry(productName, productId) {
    const phone = '919876543210'; // From settings
    const message = `Hello AL-NAAZ FOOD! I'm interested in:\n\nProduct: ${productName}\nProduct ID: ${productId}\n\nPlease share more details and pricing.`;
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

function wholesaleEnquiry() {
    const phone = '919876543210';
    const message = `Hello AL-NAAZ FOOD! I'm interested in wholesale purchasing.\n\nPlease share your wholesale price list and minimum order quantity.`;
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

// ===== REVIEW STAR RATING =====
function setRating(rating) {
    const stars = document.querySelectorAll('.star-rating .star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
            star.innerHTML = '⭐';
        } else {
            star.classList.remove('active');
            star.innerHTML = '☆';
        }
    });
    document.getElementById('ratingInput').value = rating;
}

// ===== FORM SUBMISSION HANDLERS =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart UI
    updateCartUI();
    
    // Handle enquiry forms
    document.querySelectorAll('.enquiry-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            fetch(`${window.location.origin}/al-naaz-food/api/send-enquiry.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    this.reset();
                }
            })
            .catch(error => {
                alert('Something went wrong. Please try again.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    });
});

// ===== COUNTER ANIMATION =====
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = target + '+';
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start) + '+';
        }
    }, 16);
}

// Animate counters when visible
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.dataset.target);
                animateCounter(counter, target);
            });
        }
    });
});

document.querySelectorAll('.hero-card .number').forEach(card => {
    observer.observe(card);
});

// ===== LOADING ANIMATION =====
// Smooth page transitions
document.querySelectorAll('a').forEach(link => {
    if (link.href && !link.href.startsWith('#') && !link.href.startsWith('javascript:')) {
        link.addEventListener('click', function(e) {
            // Add loading effect if needed
        });
    }
});

console.log('✨ AL-NAAZ FOOD - Welcome!');
console.log('👑 Premium Spices & Food Essentials');