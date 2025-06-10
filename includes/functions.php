<?php
// Menu functions
function getAllCategories($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getMenuItemsByCategory($pdo, $categoryId = null) {
    if ($categoryId) {
        $stmt = $pdo->prepare("
            SELECT mi.*, c.name as category_name 
            FROM menu_items mi 
            JOIN categories c ON mi.category_id = c.id 
            WHERE mi.category_id = ? AND mi.is_available = 1 
            ORDER BY mi.name
        ");
        $stmt->execute([$categoryId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT mi.*, c.name as category_name 
            FROM menu_items mi 
            JOIN categories c ON mi.category_id = c.id 
            WHERE mi.is_available = 1 
            ORDER BY c.name, mi.name
        ");
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

function getMenuItemById($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT mi.*, c.name as category_name 
        FROM menu_items mi 
        JOIN categories c ON mi.category_id = c.id 
        WHERE mi.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCategoryById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Order functions
function createOrder($pdo, $customerData, $items) {
    try {
        $pdo->beginTransaction();
        
        // Calculate total
        $total = 0;
        foreach ($items as $item) {
            $menuItem = getMenuItemById($pdo, $item['id']);
            $total += $menuItem['price'] * $item['quantity'];
        }
        
        // Insert order
        $stmt = $pdo->prepare("
            INSERT INTO orders (customer_name, customer_phone, customer_address, total_amount) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $customerData['name'],
            $customerData['phone'],
            $customerData['address'],
            $total
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        foreach ($items as $item) {
            $menuItem = getMenuItemById($pdo, $item['id']);
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, menu_item_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $orderId,
                $item['id'],
                $item['quantity'],
                $menuItem['price']
            ]);
        }
        
        $pdo->commit();
        return ['success' => true, 'order_id' => $orderId];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getAllOrders($pdo, $limit = null) {
    $sql = "SELECT * FROM orders ORDER BY order_date DESC";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getOrderById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getOrderItems($pdo, $orderId) {
    $stmt = $pdo->prepare("
        SELECT oi.*, mi.name as item_name, mi.image 
        FROM order_items oi 
        JOIN menu_items mi ON oi.menu_item_id = mi.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}

function updateOrderStatus($pdo, $orderId, $status) {
    $allowedStatuses = ['pending', 'confirmed', 'preparing', 'delivered', 'cancelled'];
    if (!in_array($status, $allowedStatuses)) {
        return false;
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $orderId]);
}

// Admin functions
function addMenuItem($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO menu_items (category_id, name, description, price, image) 
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['category_id'],
        $data['name'],
        $data['description'],
        $data['price'],
        $data['image'] ?? 'default.jpg'
    ]);
}

function updateMenuItem($pdo, $id, $data) {
    $sql = "UPDATE menu_items SET category_id = ?, name = ?, description = ?, price = ?";
    $params = [$data['category_id'], $data['name'], $data['description'], $data['price']];
    
    if (isset($data['image'])) {
        $sql .= ", image = ?";
        $params[] = $data['image'];
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $id;
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function deleteMenuItem($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    return $stmt->execute([$id]);
}

function addCategory($pdo, $name, $description = null) {
    $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    return $stmt->execute([$name, $description]);
}

function updateCategory($pdo, $id, $name, $description = null) {
    $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
    return $stmt->execute([$name, $description, $id]);
}

function deleteCategory($pdo, $id) {
    // Check if category has menu items
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM menu_items WHERE category_id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->fetchColumn() > 0) {
        return ['success' => false, 'message' => 'Cannot delete category with existing menu items'];
    }
    
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return ['success' => $stmt->execute([$id])];
}

// Utility functions
function getImagePath($filename) {
    if (empty($filename) || $filename === 'default.jpg') {
        // Try to find default image
        $defaultPaths = [
            'assets/images/menu/default.jpg',
            'assets/images/default.jpg',
            'uploads/menu/default.jpg'
        ];
        
        foreach ($defaultPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        // Return placeholder SVG if no default image found
        return 'data:image/svg+xml;base64,' . base64_encode('
            <svg width="400" height="300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300">
                <rect width="400" height="300" fill="#f8f9fa" stroke="#dee2e6"/>
                <circle cx="200" cy="130" r="30" fill="#6c757d"/>
                <rect x="170" y="170" width="60" height="40" rx="5" fill="#6c757d"/>
                <text x="200" y="230" font-family="Arial, sans-serif" font-size="16" fill="#6c757d" text-anchor="middle">Menu Item</text>
            </svg>
        ');
    }
    
    // Check uploaded image
    $uploadPath = 'uploads/menu/' . $filename;
    if (file_exists($uploadPath)) {
        return $uploadPath;
    }
    
    // Check assets folder
    $assetPath = 'assets/images/menu/' . $filename;
    if (file_exists($assetPath)) {
        return $assetPath;
    }
    
    // Fallback to default
    return getImagePath('default.jpg');
}

// Updated database.php - Fix sample data insertion
// Add this function to create sample images

function createSampleDefaultImage($path) {
    if (!file_exists($path)) {
        // Create directory if it doesn't exist
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Create a simple SVG image and convert to JPG-like content
        $svg = '
        <svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#f8f9fa;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#e9ecef;stop-opacity:1" />
                </linearGradient>
            </defs>
            <rect width="400" height="300" fill="url(#bg)"/>
            <circle cx="200" cy="120" r="35" fill="#d4a574" opacity="0.8"/>
            <rect x="165" y="160" width="70" height="50" rx="8" fill="#d4a574" opacity="0.6"/>
            <rect x="180" y="175" width="40" height="20" rx="3" fill="#fff"/>
            <text x="200" y="250" font-family="Arial, sans-serif" font-size="18" font-weight="bold" fill="#6c757d" text-anchor="middle">Delicious Food</text>
            <text x="200" y="270" font-family="Arial, sans-serif" font-size="12" fill="#adb5bd" text-anchor="middle">Restaurant Menu Item</text>
        </svg>';
        
        file_put_contents($path, $svg);
    }
}
?>