<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Modern Restaurant Design */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Color Palette - Elegant & Modern */
            --primary: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            --primary-solid: #ff6b35;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --gold: #f39c12;
            --dark: #1a1a1a;
            --gray-50: #fafafa;
            --gray-100: #f5f5f5;
            --gray-200: #eeeeee;
            --gray-300: #e0e0e0;
            --gray-400: #bdbdbd;
            --gray-500: #9e9e9e;
            --gray-600: #757575;
            --gray-700: #616161;
            --gray-800: #424242;
            --gray-900: #212121;
            --white: #ffffff;
            --black: #000000;
            --success: #4caf50;
            --warning: #ff9800;
            --error: #f44336;
            --info: #2196f3;
            
            /* Typography */
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Inter', sans-serif;
            
            /* Shadows */
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.15);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.2);
            --shadow-xl: 0 16px 40px rgba(0,0,0,0.25);
            
            /* Border Radius */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-xl: 32px;
            
            /* Spacing */
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
            --space-3xl: 4rem;
        }

        /* Base Styles */
        body {
            font-family: var(--font-body);
            line-height: 1.7;
            color: var(--gray-800);
            background: var(--white);
            overflow-x: hidden;
        }

        /* Container System */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 var(--space-lg);
        }

        .container-fluid {
            width: 100%;
            padding: 0 var(--space-lg);
        }

        /* Advanced Grid System */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 calc(var(--space-md) * -1);
        }

        .col {
            flex: 1;
            padding: 0 var(--space-md);
        }

        /* Responsive Grid Classes */
        .col-1 { flex: 0 0 8.333333%; max-width: 8.333333%; padding: 0 var(--space-md); }
        .col-2 { flex: 0 0 16.666667%; max-width: 16.666667%; padding: 0 var(--space-md); }
        .col-3 { flex: 0 0 25%; max-width: 25%; padding: 0 var(--space-md); }
        .col-4 { flex: 0 0 33.333333%; max-width: 33.333333%; padding: 0 var(--space-md); }
        .col-5 { flex: 0 0 41.666667%; max-width: 41.666667%; padding: 0 var(--space-md); }
        .col-6 { flex: 0 0 50%; max-width: 50%; padding: 0 var(--space-md); }
        .col-7 { flex: 0 0 58.333333%; max-width: 58.333333%; padding: 0 var(--space-md); }
        .col-8 { flex: 0 0 66.666667%; max-width: 66.666667%; padding: 0 var(--space-md); }
        .col-9 { flex: 0 0 75%; max-width: 75%; padding: 0 var(--space-md); }
        .col-10 { flex: 0 0 83.333333%; max-width: 83.333333%; padding: 0 var(--space-md); }
        .col-11 { flex: 0 0 91.666667%; max-width: 91.666667%; padding: 0 var(--space-md); }
        .col-12 { flex: 0 0 100%; max-width: 100%; padding: 0 var(--space-md); }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 600;
            line-height: 1.3;
            color: var(--dark);
            margin-bottom: var(--space-lg);
        }

        h1 { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 700; }
        h2 { font-size: clamp(2rem, 4vw, 3rem); }
        h3 { font-size: clamp(1.5rem, 3vw, 2.25rem); }
        h4 { font-size: clamp(1.25rem, 2.5vw, 1.75rem); }
        h5 { font-size: clamp(1.125rem, 2vw, 1.5rem); }
        h6 { font-size: clamp(1rem, 1.5vw, 1.25rem); }

        .display-1 { font-size: clamp(3rem, 6vw, 5rem); font-weight: 700; line-height: 1.1; }
        .display-2 { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 700; line-height: 1.2; }
        .display-3 { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 600; line-height: 1.2; }

        .lead {
            font-size: 1.25rem;
            font-weight: 300;
            line-height: 1.6;
            color: var(--gray-600);
        }

        /* Advanced Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow-md);
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--space-lg) var(--space-lg);
        }

        .navbar-brand {
            font-family: var(--font-heading);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-solid);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            align-items: center;
            gap: var(--space-xl);
            margin: 0;
        }

        .nav-link {
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-lg);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: -1;
            border-radius: var(--radius-lg);
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            transform: scaleX(1);
            transform-origin: left;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--white);
            transform: translateY(-2px);
        }

        /* Mobile Navigation */
        .navbar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--gray-700);
            cursor: pointer;
            padding: var(--space-sm);
            border-radius: var(--radius-sm);
            transition: all 0.3s ease;
        }

        .navbar-toggle:hover {
            background: var(--gray-100);
            transform: scale(1.1);
        }

        /* Modern Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
            padding: var(--space-md) var(--space-xl);
            font-size: 0.95rem;
            font-weight: 600;
            font-family: var(--font-body);
            text-decoration: none;
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            background: transparent;
            color: var(--gray-700);
            min-height: 48px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary);
            transform: scale(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: var(--radius-lg);
            z-index: -1;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline {
            border: 2px solid var(--primary-solid);
            color: var(--primary-solid);
            background: transparent;
        }

        .btn-outline::before {
            background: var(--primary-solid);
        }

        .btn-outline:hover::before {
            transform: scale(1);
        }

        .btn-outline:hover {
            color: var(--white);
            transform: translateY(-2px);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        .btn-lg {
            padding: var(--space-lg) var(--space-2xl);
            font-size: 1.1rem;
            min-height: 56px;
        }

        .btn-sm {
            padding: var(--space-sm) var(--space-lg);
            font-size: 0.875rem;
            min-height: 40px;
            border-radius: var(--radius-md);
        }

        /* Advanced Cards */
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--gray-200);
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            padding: var(--space-lg) var(--space-xl);
            border-bottom: 1px solid var(--gray-200);
            font-weight: 600;
        }

        .card-body {
            padding: var(--space-xl);
        }

        .card-img-top {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover .card-img-top {
            transform: scale(1.1);
        }

        /* Form Elements */
        .form-group {
            margin-bottom: var(--space-lg);
        }

        .form-label {
            display: block;
            margin-bottom: var(--space-sm);
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: var(--space-md) var(--space-lg);
            font-size: 1rem;
            font-family: var(--font-body);
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            background: var(--white);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 48px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-solid);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: var(--gray-400);
        }

        /* Alert System */
        .alert {
            padding: var(--space-lg) var(--space-xl);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-lg);
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: var(--space-md);
            animation: slideInDown 0.3s ease;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid var(--error);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            border-left: 4px solid var(--warning);
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid var(--info);
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, 
                rgba(26, 26, 26, 0.7) 0%, 
                rgba(255, 107, 53, 0.8) 50%, 
                rgba(247, 147, 30, 0.9) 100%
            ),
            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="0.5" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="0.3" fill="white" opacity="0.15"/><circle cx="50" cy="10" r="0.4" fill="white" opacity="0.12"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            background-size: cover, 100px 100px;
            background-position: center, 0 0;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            color: var(--white);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 70%, rgba(255, 107, 53, 0.3) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            animation: fadeInUp 1s ease;
        }

        .hero-section h1 {
            color: var(--white);
            text-shadow: 2px 4px 8px rgba(0,0,0,0.3);
            margin-bottom: var(--space-lg);
        }

        .hero-section .lead {
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 1px 2px 4px rgba(0,0,0,0.2);
            margin-bottom: var(--space-2xl);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Utility Classes */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-white { color: var(--white); }
        .text-muted { color: var(--gray-500); }
        .text-primary { color: var(--primary-solid); }

        .bg-primary { background: var(--primary); }
        .bg-light { background: var(--gray-50); }
        .bg-white { background: var(--white); }
        .bg-dark { background: var(--dark); }

        .d-none { display: none; }
        .d-block { display: block; }
        .d-flex { display: flex; }
        .d-inline { display: inline; }
        .d-inline-block { display: inline-block; }
        .d-grid { display: grid; }

        .justify-content-center { justify-content: center; }
        .justify-content-between { justify-content: space-between; }
        .justify-content-around { justify-content: space-around; }
        .align-items-center { align-items: center; }
        .align-items-start { align-items: flex-start; }
        .align-items-end { align-items: flex-end; }

        .gap-sm { gap: var(--space-sm); }
        .gap-md { gap: var(--space-md); }
        .gap-lg { gap: var(--space-lg); }
        .gap-xl { gap: var(--space-xl); }

        /* Spacing Utilities */
        .mt-1 { margin-top: var(--space-xs); }
        .mt-2 { margin-top: var(--space-sm); }
        .mt-3 { margin-top: var(--space-md); }
        .mt-4 { margin-top: var(--space-lg); }
        .mt-5 { margin-top: var(--space-xl); }

        .mb-1 { margin-bottom: var(--space-xs); }
        .mb-2 { margin-bottom: var(--space-sm); }
        .mb-3 { margin-bottom: var(--space-md); }
        .mb-4 { margin-bottom: var(--space-lg); }
        .mb-5 { margin-bottom: var(--space-xl); }

        .py-3 { padding: var(--space-md) 0; }
        .py-4 { padding: var(--space-lg) 0; }
        .py-5 { padding: var(--space-xl) 0; }
        .py-6 { padding: var(--space-2xl) 0; }
        .py-8 { padding: var(--space-3xl) 0; }

        .px-3 { padding: 0 var(--space-md); }
        .px-4 { padding: 0 var(--space-lg); }
        .px-5 { padding: 0 var(--space-xl); }

        .fw-light { font-weight: 300; }
        .fw-normal { font-weight: 400; }
        .fw-medium { font-weight: 500; }
        .fw-semibold { font-weight: 600; }
        .fw-bold { font-weight: 700; }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .fade-in { animation: fadeInUp 0.6s ease; }
        .pulse { animation: pulse 2s infinite; }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                padding: var(--space-lg) 0;
                box-shadow: var(--shadow-lg);
                gap: 0;
                border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            }

            .navbar-nav.show {
                display: flex;
            }

            .navbar-toggle {
                display: block;
            }

            .nav-link {
                display: block;
                padding: var(--space-md) var(--space-xl);
                border-radius: 0;
                width: 100%;
                text-align: center;
            }

            .hero-section {
                min-height: 80vh;
                background-attachment: scroll;
                padding: var(--space-3xl) 0;
            }

            .container {
                padding: 0 var(--space-md);
            }

            /* Stack columns on mobile */
            .col-1, .col-2, .col-3, .col-4, .col-5, .col-6,
            .col-7, .col-8, .col-9, .col-10, .col-11, .col-12 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: var(--space-lg);
            }
        }

        @media (min-width: 769px) {
            .col-md-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
            .col-md-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
            .col-md-3 { flex: 0 0 25%; max-width: 25%; }
            .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
            .col-md-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }
            .col-md-6 { flex: 0 0 50%; max-width: 50%; }
            .col-md-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }
            .col-md-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }
            .col-md-9 { flex: 0 0 75%; max-width: 75%; }
            .col-md-10 { flex: 0 0 83.333333%; max-width: 83.333333%; }
            .col-md-11 { flex: 0 0 91.666667%; max-width: 91.666667%; }
            .col-md-12 { flex: 0 0 100%; max-width: 100%; }
        }

        @media (min-width: 992px) {
            .col-lg-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
            .col-lg-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
            .col-lg-3 { flex: 0 0 25%; max-width: 25%; }
            .col-lg-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
            .col-lg-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }
            .col-lg-6 { flex: 0 0 50%; max-width: 50%; }
            .col-lg-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }
            .col-lg-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }
            .col-lg-9 { flex: 0 0 75%; max-width: 75%; }
            .col-lg-10 { flex: 0 0 83.333333%; max-width: 83.333333%; }
            .col-lg-11 { flex: 0 0 91.666667%; max-width: 91.666667%; }
            .col-lg-12 { flex: 0 0 100%; max-width: 100%; }
        }

        /* Page Body Padding for Fixed Navbar */
        body {
            padding-top: 80px;
        }

        /* Special Components */
        .price-tag {
            background: var(--primary);
            color: var(--white);
            padding: var(--space-sm) var(--space-lg);
            border-radius: var(--radius-xl);
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: var(--shadow-md);
            display: inline-flex;
            align-items: center;
            gap: var(--space-xs);
        }

        .section-title {
            position: relative;
            margin-bottom: var(--space-3xl);
            text-align: center;
            color: var(--dark);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .section-title::before {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 2px;
            background: var(--gold);
            border-radius: 1px;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--space-lg);
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }

        .feature-icon:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: var(--shadow-xl);
        }

        .feature-icon i {
            font-size: 2rem;
            color: var(--white);
        }

        /* Glassmorphism Effects */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .glass-dark {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Loading States */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Scroll Indicators */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: var(--white);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent);
        }

        /* Print Styles */
        @media print {
            .navbar, .btn, .alert {
                display: none !important;
            }
            
            .hero-section {
                background: none;
                color: var(--dark);
                min-height: auto;
                padding: var(--space-xl) 0;
            }
            
            .card {
                box-shadow: none;
                border: 1px solid var(--gray-300);
            }
        }
    </style>
</head>
<body>