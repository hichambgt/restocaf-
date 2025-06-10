<nav class="navbar" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="<?php echo isset($isAdmin) ? '../' : ''; ?>index.php">
            <i class="fas fa-fire-flame-curved"></i>
            <span><?php echo SITE_NAME; ?></span>
        </a>
        
        <button class="navbar-toggle" onclick="toggleMobileMenu()" aria-label="Toggle navigation">
            <i class="fas fa-bars" id="hamburger-icon"></i>
        </button>
        
        <ul class="navbar-nav" id="navbarNav">
            <?php if (!isset($isAdmin)): ?>
            <!-- Public Navigation -->
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>" href="menu.php">
                    <i class="fas fa-utensils"></i>
                    <span>Menu</span>
                </a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'order.php' ? 'active' : ''; ?>" href="order.php">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Order</span>
                    <span class="cart-badge" id="cart-count" style="display: none;">0</span>
                </a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">
                    <i class="fas fa-envelope"></i>
                    <span>Contact</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="admin/index.php">
                    <i class="fas fa-user-crown"></i>
                    <span>Admin</span>
                </a>
            </li>
            <?php else: ?>
            <!-- Admin Navigation -->
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu_management.php' ? 'active' : ''; ?>" href="menu_management.php">
                    <i class="fas fa-utensils"></i>
                    <span>Menu</span>
                </a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'category_management.php' ? 'active' : ''; ?>" href="category_management.php">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                    <i class="fas fa-receipt"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Site</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
.nav-link {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    position: relative;
}

.cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--accent);
    color: var(--white);
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.75rem;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@media (max-width: 768px) {
    .nav-link {
        padding: var(--space-lg) var(--space-xl);
        justify-content: center;
    }
    
    .cart-badge {
        position: relative;
        top: 0;
        right: 0;
        margin-left: var(--space-sm);
    }
}
</style>

<script>
function toggleMobileMenu() {
    const nav = document.getElementById('navbarNav');
    const icon = document.getElementById('hamburger-icon');
    
    nav.classList.toggle('show');
    
    // Animate hamburger icon
    if (nav.classList.contains('show')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
    } else {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const nav = document.getElementById('navbarNav');
    const toggle = document.querySelector('.navbar-toggle');
    const navbar = document.getElementById('mainNavbar');
    
    if (!navbar.contains(event.target)) {
        nav.classList.remove('show');
        document.getElementById('hamburger-icon').classList.remove('fa-times');
        document.getElementById('hamburger-icon').classList.add('fa-bars');
    }
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('mainNavbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Update cart count
function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartBadge = document.getElementById('cart-count');
    
    if (cartBadge) {
        cartBadge.textContent = cartCount;
        cartBadge.style.display = cartCount > 0 ? 'flex' : 'none';
    }
}

// Initialize cart display
document.addEventListener('DOMContentLoaded', function() {
    updateCartDisplay();
});
</script>