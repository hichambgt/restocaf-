<?php if (!isset($isAdmin)): ?>
<footer style="background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 100%); color: var(--white); position: relative; overflow: hidden; margin-top: var(--space-3xl);">
    <!-- Background Pattern -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"footerPattern\" width=\"50\" height=\"50\" patternUnits=\"userSpaceOnUse\"><circle cx=\"25\" cy=\"25\" r=\"1\" fill=\"white\" opacity=\"0.05\"/><circle cx=\"0\" cy=\"0\" r=\"0.5\" fill=\"white\" opacity=\"0.03\"/><circle cx=\"50\" cy=\"50\" r=\"0.5\" fill=\"white\" opacity=\"0.03\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23footerPattern)\"/></svg>'); opacity: 0.3;"></div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <!-- Main Footer Content -->
        <div style="padding: var(--space-3xl) 0 var(--space-xl);">
            <div class="row">
                <!-- Restaurant Info -->
                <div class="col-lg-4 mb-5">
                    <div style="margin-bottom: var(--space-xl);">
                        <h3 style="color: var(--white); font-family: var(--font-heading); font-size: 2rem; margin-bottom: var(--space-lg);">
                            <i class="fas fa-fire-flame-curved" style="color: #ff6b35; margin-right: var(--space-md);"></i>
                            <?php echo SITE_NAME; ?>
                        </h3>
                        <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.7; margin-bottom: var(--space-lg);">
                            <?php echo $restaurantInfo['description']; ?>
                        </p>
                        
                        <!-- Social Media Links -->
                        <div style="display: flex; gap: var(--space-md);">
                            <a href="https://www.facebook.com/" class="social-link" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); text-decoration: none; transition: all 0.3s ease; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); text-decoration: none; transition: all 0.3s ease; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); text-decoration: none; transition: all 0.3s ease; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); text-decoration: none; transition: all 0.3s ease; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="social-link" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); text-decoration: none; transition: all 0.3s ease; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="col-lg-4 mb-5">
                    <h4 style="color: #ff6b35; margin-bottom: var(--space-lg); font-family: var(--font-heading);">Contact Information</h4>
                    
                    <div style="margin-bottom: var(--space-lg);">
                        <div style="display: flex; align-items: flex-start; gap: var(--space-md); margin-bottom: var(--space-md);">
                            <div style="width: 40px; height: 40px; background: rgba(255, 107, 53, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-map-marker-alt" style="color: #ff6b35;"></i>
                            </div>
                            <div>
                                <h6 style="color: var(--white); margin-bottom: var(--space-xs);">Address</h6>
                                <p style="color: rgba(255, 255, 255, 0.8); margin: 0; line-height: 1.5;"><?php echo $restaurantInfo['address']; ?></p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: var(--space-md); margin-bottom: var(--space-md);">
                            <div style="width: 40px; height: 40px; background: rgba(255, 107, 53, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-phone" style="color: #ff6b35;"></i>
                            </div>
                            <div>
                                <h6 style="color: var(--white); margin-bottom: var(--space-xs);">Phone</h6>
                                <p style="margin: 0;">
                                    <a href="tel:<?php echo $restaurantInfo['phone']; ?>" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: color 0.3s ease;">
                                        <?php echo $restaurantInfo['phone']; ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: var(--space-md);">
                            <div style="width: 40px; height: 40px; background: rgba(255, 107, 53, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-envelope" style="color: #ff6b35;"></i>
                            </div>
                            <div>
                                <h6 style="color: var(--white); margin-bottom: var(--space-xs);">Email</h6>
                                <p style="margin: 0;">
                                    <a href="mailto:<?php echo $restaurantInfo['email']; ?>" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: color 0.3s ease;">
                                        <?php echo $restaurantInfo['email']; ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Opening Hours -->
                <div class="col-lg-4 mb-5">
                    <h4 style="color: #ff6b35; margin-bottom: var(--space-lg); font-family: var(--font-heading);">Opening Hours</h4>
                    
                    <div style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-radius: var(--radius-lg); padding: var(--space-lg); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <?php foreach ($restaurantInfo['hours'] as $day => $hours): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-sm) 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                            <span style="color: var(--white); font-weight: 600;"><?php echo $day; ?></span>
                            <span style="color: rgba(255, 255, 255, 0.8);"><?php echo $hours; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Newsletter Signup -->
                    <div style="margin-top: var(--space-xl);">
                        <h5 style="color: var(--white); margin-bottom: var(--space-md);">Stay Updated</h5>
                        <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; margin-bottom: var(--space-md);">Subscribe to get special offers and updates!</p>
                        <form style="display: flex; gap: var(--space-sm);">
                            <input type="email" placeholder="Enter your email" style="flex: 1; padding: var(--space-md); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: var(--radius-md); background: rgba(255, 255, 255, 0.1); color: var(--white); backdrop-filter: blur(10px);">
                            <button type="submit" style="background: var(--primary); color: var(--white); border: none; padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md); cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding: var(--space-xl) 0;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7);">
                        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-right">
                    <div style="display: flex; justify-content: flex-end; gap: var(--space-lg);">
                        <a href="#" style="color: rgba(255, 255, 255, 0.7); text-decoration: none; font-size: 0.9rem; transition: color 0.3s ease;">Privacy Policy</a>
                        <a href="#" style="color: rgba(255, 255, 255, 0.7); text-decoration: none; font-size: 0.9rem; transition: color 0.3s ease;">Terms of Service</a>
                        <a href="#" style="color: rgba(255, 255, 255, 0.7); text-decoration: none; font-size: 0.9rem; transition: color 0.3s ease;">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.social-link:hover {
    background: var(--primary) !important;
    transform: translateY(-3px) scale(1.1);
    box-shadow: var(--shadow-lg);
}

footer a:hover {
    color: #ff6b35 !important;
}

footer input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

footer input:focus {
    outline: none;
    border-color: #ff6b35;
    box-shadow: 0 0 0 2px rgba(255, 107, 53, 0.2);
}

footer button:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

@media (max-width: 768px) {
    .text-right {
        text-align: center !important;
        margin-top: var(--space-lg);
    }
    
    footer form {
        flex-direction: column;
    }
    
    footer button {
        align-self: stretch;
    }
}
</style>
<?php endif; ?>

<!-- Global JavaScript -->
<script>
// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Enhanced cart functionality
function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    
    const cartBadges = document.querySelectorAll('#cart-count');
    cartBadges.forEach(badge => {
        badge.textContent = cartCount;
        badge.style.display = cartCount > 0 ? 'flex' : 'none';
    });
}

// Form validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'var(--error)';
            isValid = false;
        } else {
            input.style.borderColor = 'var(--gray-300)';
        }
    });
    
    return isValid;
}

// Image preview functionality
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Utility function to format price
function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartDisplay();
    
    // Add scroll animations to elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all animated elements
    document.querySelectorAll('.fade-in, .card, .feature-icon').forEach(el => {
        observer.observe(el);
    });
});

// Back to top functionality
const backToTop = document.createElement('button');
backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
backToTop.className = 'back-to-top';
backToTop.style.cssText = `
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--primary);
    color: var(--white);
    border: none;
    box-shadow: var(--shadow-lg);
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
    z-index: 1000;
`;

document.body.appendChild(backToTop);

window.addEventListener('scroll', function() {
    if (window.scrollY > 300) {
        backToTop.style.opacity = '1';
        backToTop.style.visibility = 'visible';
    } else {
        backToTop.style.opacity = '0';
        backToTop.style.visibility = 'hidden';
    }
});

backToTop.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

backToTop.addEventListener('mouseenter', function() {
    this.style.transform = 'translateY(-3px) scale(1.1)';
});

backToTop.addEventListener('mouseleave', function() {
    this.style.transform = 'translateY(0) scale(1)';
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    // Escape key to close mobile menu
    if (e.key === 'Escape') {
        const nav = document.getElementById('navbarNav');
        if (nav && nav.classList.contains('show')) {
            toggleMobileMenu();
        }
    }
});

// Performance optimization - lazy loading
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}
</script>

</body>
</html>
