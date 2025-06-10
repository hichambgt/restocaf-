<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

redirectToLogin();

$pageTitle = 'Menu Management';
$isAdmin = true;
$message = null;
$messageType = 'success';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        $data = [
            'category_id' => (int)$_POST['category_id'],
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'price' => (float)$_POST['price'],
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0)
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadResult = uploadImage($_FILES['image']);
            if ($uploadResult['success']) {
                $data['image'] = $uploadResult['filename'];
            } else {
                $message = $uploadResult['message'];
                $messageType = 'danger';
            }
        }
        
        if (!$message && addEnhancedMenuItem($pdo, $data)) {
            $message = 'Menu item added successfully!';
        } elseif (!$message) {
            $message = 'Failed to add menu item.';
            $messageType = 'danger';
        }
    }
    
    if (isset($_POST['edit_item'])) {
        $id = (int)$_POST['item_id'];
        $data = [
            'category_id' => (int)$_POST['category_id'],
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'price' => (float)$_POST['price'],
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0)
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadResult = uploadImage($_FILES['image']);
            if ($uploadResult['success']) {
                $oldItem = getMenuItemById($pdo, $id);
                if ($oldItem && $oldItem['image'] !== 'default.jpg') {
                    $oldImagePath = MENU_UPLOAD_DIR . $oldItem['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $data['image'] = $uploadResult['filename'];
            } else {
                $message = $uploadResult['message'];
                $messageType = 'danger';
            }
        }
        
        if (!$message && updateEnhancedMenuItem($pdo, $id, $data)) {
            $message = 'Menu item updated successfully!';
        } elseif (!$message) {
            $message = 'Failed to update menu item.';
            $messageType = 'danger';
        }
    }
    
    if (isset($_POST['delete_item'])) {
        $id = (int)$_POST['item_id'];
        if (deleteMenuItem($pdo, $id)) {
            $message = 'Menu item deleted successfully!';
        } else {
            $message = 'Failed to delete menu item.';
            $messageType = 'danger';
        }
    }
    
    if (isset($_POST['toggle_availability'])) {
        $id = (int)$_POST['item_id'];
        $availability = (int)$_POST['availability'];
        $stmt = $pdo->prepare("UPDATE menu_items SET is_available = ? WHERE id = ?");
        if ($stmt->execute([$availability, $id])) {
            $message = 'Item availability updated!';
        }
    }
    
    if (isset($_POST['toggle_featured'])) {
        $id = (int)$_POST['item_id'];
        $featured = (int)$_POST['featured'];
        $stmt = $pdo->prepare("UPDATE menu_items SET is_featured = ? WHERE id = ?");
        if ($stmt->execute([$featured, $id])) {
            $message = 'Featured status updated!';
        }
    }
    
    if (isset($_POST['update_order'])) {
        $id = (int)$_POST['item_id'];
        $order = (int)$_POST['sort_order'];
        $stmt = $pdo->prepare("UPDATE menu_items SET sort_order = ? WHERE id = ?");
        if ($stmt->execute([$order, $id])) {
            $message = 'Sort order updated!';
        }
    }
}

// Enhanced menu items query with additional fields
$stmt = $pdo->prepare("
    SELECT mi.*, c.name as category_name 
    FROM menu_items mi 
    JOIN categories c ON mi.category_id = c.id 
    ORDER BY mi.sort_order ASC, c.name, mi.name
");
$stmt->execute();
$menuItems = $stmt->fetchAll();

$categories = getAllCategories($pdo);
$editItem = null;

// Get item for editing
if (isset($_GET['edit'])) {
    $editItem = getMenuItemById($pdo, (int)$_GET['edit']);
}

// Enhanced functions
function addEnhancedMenuItem($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO menu_items (category_id, name, description, price, image, is_featured, sort_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['category_id'],
        $data['name'],
        $data['description'],
        $data['price'],
        $data['image'] ?? 'default.jpg',
        $data['is_featured'] ?? 0,
        $data['sort_order'] ?? 0
    ]);
}

function updateEnhancedMenuItem($pdo, $id, $data) {
    $sql = "UPDATE menu_items SET category_id = ?, name = ?, description = ?, price = ?, is_featured = ?, sort_order = ?";
    $params = [$data['category_id'], $data['name'], $data['description'], $data['price'], $data['is_featured'], $data['sort_order']];
    
    if (isset($data['image'])) {
        $sql .= ", image = ?";
        $params[] = $data['image'];
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $id;
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 16px;">
                <div class="card-body">
                    <h2 style="margin: 0; font-weight: 700;"><i class="fas fa-utensils me-3"></i>Menu Management</h2>
                    <p style="margin: 0; opacity: 0.9;">Manage your restaurant's menu items with advanced features</p>
                </div>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" style="border-radius: 12px; border: none;">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Add/Edit Form -->
        <div class="col-lg-4 mb-4">
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 16px 16px 0 0; border: none;">
                    <h5 style="margin: 0; font-weight: 600;">
                        <i class="fas fa-<?php echo $editItem ? 'edit' : 'plus'; ?> me-2"></i>
                        <?php echo $editItem ? 'Edit Menu Item' : 'Add New Item'; ?>
                    </h5>
                </div>
                <div class="card-body" style="padding: 2rem;">
                    <form method="POST" enctype="multipart/form-data" id="menuItemForm">
                        <?php if ($editItem): ?>
                        <input type="hidden" name="item_id" value="<?php echo $editItem['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="category_id" class="form-label" style="font-weight: 600; color: #2d3748;">Category *</label>
                            <select class="form-control" id="category_id" name="category_id" required style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px;">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo ($editItem && $editItem['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="name" class="form-label" style="font-weight: 600; color: #2d3748;">Item Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $editItem ? htmlspecialchars($editItem['name']) : ''; ?>" 
                                   placeholder="e.g., Tacos Poulet" required style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px;">
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label" style="font-weight: 600; color: #2d3748;">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Describe the dish..." style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px;"><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label" style="font-weight: 600; color: #2d3748;">Price (DH) *</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" 
                                           value="<?php echo $editItem ? $editItem['price'] : ''; ?>" 
                                           placeholder="0.00" required style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order" class="form-label" style="font-weight: 600; color: #2d3748;">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" min="0" 
                                           value="<?php echo $editItem ? ($editItem['sort_order'] ?? 0) : 0; ?>" 
                                           placeholder="0" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check" style="padding: 1rem; background: #f7fafc; border-radius: 10px; border: 2px solid #e2e8f0;">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" 
                                       <?php echo ($editItem && ($editItem['is_featured'] ?? 0)) ? 'checked' : ''; ?>
                                       style="width: 20px; height: 20px;">
                                <label class="form-check-label" for="is_featured" style="font-weight: 600; color: #2d3748; margin-left: 10px;">
                                    <i class="fas fa-star text-warning me-2"></i>Featured Item
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="image" class="form-label" style="font-weight: 600; color: #2d3748;">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" 
                                   onchange="previewImage(this, 'imagePreview')" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px;">
                            <small class="text-muted">Max file size: 5MB. Formats: JPG, PNG, GIF, WebP</small>
                        </div>
                        
                        <!-- Image Preview -->
                        <?php if ($editItem): ?>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; color: #2d3748;">Current Image</label>
                            <div>
                                <img id="imagePreview" 
                                     src="../<?php echo getImagePath($editItem['image'], true); ?>" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px; max-height: 150px; object-fit: cover; border-radius: 12px; border: 3px solid #e2e8f0;"
                                     alt="Current image">
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="form-group">
                            <img id="imagePreview" src="#" class="img-thumbnail d-none" 
                                 style="max-width: 200px; max-height: 150px; object-fit: cover; border-radius: 12px; border: 3px solid #e2e8f0;">
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="<?php echo $editItem ? 'edit_item' : 'add_item'; ?>" 
                                    class="btn btn-primary" 
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 12px; font-weight: 600;">
                                <i class="fas fa-<?php echo $editItem ? 'save' : 'plus'; ?> me-2"></i>
                                <?php echo $editItem ? 'Update Item' : 'Add Item'; ?>
                            </button>
                            <?php if ($editItem): ?>
                            <a href="menu_management.php" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 12px; font-weight: 600;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Menu Items Table -->
        <div class="col-lg-8">
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 16px 16px 0 0; border: none; padding: 1.5rem;">
                    <h5 style="margin: 0; font-weight: 700; color: #2d3748;">
                        <i class="fas fa-list me-2"></i>Menu Items
                    </h5>
                    <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600;">
                        <?php echo count($menuItems); ?> items
                    </span>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($menuItems)): ?>
                    <div class="text-center py-5" style="color: var(--gray-500);">
                        <i class="fas fa-utensils" style="font-size: 3rem; margin-bottom: 1rem; color: #a0aec0;"></i>
                        <h5>No menu items found</h5>
                        <p>Add your first menu item to get started!</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="border-radius: 0 0 16px 16px; overflow: hidden;">
                            <thead style="background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);">
                                <tr>
                                    <th style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2d3748; border: none;">Image</th>
                                    <th style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2d3748; border: none;">Name</th>
                                    <th style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2d3748; border: none;">Category</th>
                                    <th style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2d3748; border: none;">Price</th>
                                    <th style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2d3748; border: none;">Status</th>
                                    <th style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2d3748; border: none;">Featured</th>
                                    <th style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2d3748; border: none;">Order</th>
                                    <th style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2d3748; border: none;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menuItems as $item): ?>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 1.2rem 1.5rem; vertical-align: middle;">
                                        <img src="../<?php echo getImagePath($item['image'], true); ?>" 
                                             class="rounded" 
                                             style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #e2e8f0;"
                                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </td>
                                    <td style="padding: 1.2rem 1.5rem; vertical-align: middle;">
                                        <div>
                                            <strong style="color: #2d3748; font-weight: 600;"><?php echo htmlspecialchars($item['name']); ?></strong>
                                            <?php if (!empty($item['description'])): ?>
                                            <br><small style="color: #718096;"><?php echo htmlspecialchars(substr($item['description'], 0, 50)) . (strlen($item['description']) > 50 ? '...' : ''); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="padding: 1.2rem 1.5rem; vertical-align: middle;">
                                        <span class="badge" style="background: #00d4aa; color: white; padding: 6px 12px; border-radius: 20px; font-weight: 500;">
                                            <?php echo htmlspecialchars($item['category_name']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1.2rem 1.5rem; vertical-align: middle;">
                                        <span style="color: #3182ce; font-weight: 700; font-size: 1.1rem;">
                                            <?php echo number_format($item['price'], 2); ?> DH
                                        </span>
                                    </td>
                                    <td style="padding: 1.2rem 1.5rem; vertical-align: middle;">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="availability" value="<?php echo $item['is_available'] ? 0 : 1; ?>">
                                            <button type="submit" name="toggle_availability" 
                                                    class="btn btn-sm" 
                                                    style="background: <?php echo $item['is_available'] ? '#48bb78' : '#a0aec0'; ?>; color: white; border: none; border-radius: 20px; padding: 6px 16px; font-weight: 500;"
                                                    title="Click to toggle availability">
                                                <?php echo $item['is_available'] ? 'Available' : 'Unavailable'; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td style="padding: 1.2rem 1.5rem; vertical-align: middle;">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="featured" value="<?php echo ($item['is_featured'] ?? 0) ? 0 : 1; ?>">
                                            <button type="submit" name="toggle_featured" 
                                                    class="btn btn-sm" 
                                                    style="background: transparent; border: 2px solid #ffd700; color: #ffd700; border-radius: 50%; width: 40px; height: 40px; padding: 0;"
                                                    title="Toggle featured status">
                                                <i class="fas fa-star" style="color: <?php echo ($item['is_featured'] ?? 0) ? '#ffd700' : '#e2e8f0'; ?>; font-size: 16px;"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td style="padding: 1.2rem 1.5rem; vertical-align: middle;">
                                        <form method="POST" class="d-inline" style="display: flex; align-items: center; gap: 8px;">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="sort_order" value="<?php echo $item['sort_order'] ?? 0; ?>" 
                                                   min="0" max="999" 
                                                   style="width: 60px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px; text-align: center;">
                                            <button type="submit" name="update_order" class="btn btn-sm" 
                                                    style="background: #4299e1; color: white; border: none; border-radius: 6px; padding: 4px 8px;">
                                                <i class="fas fa-check" style="font-size: 12px;"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td style="padding: 1.2rem 1.5rem; vertical-align: middle;">
                                        <div style="display: flex; gap: 8px;">
                                            <a href="menu_management.php?edit=<?php echo $item['id']; ?>" 
                                               class="btn btn-sm" 
                                               style="background: #4299e1; color: white; border: none; border-radius: 8px; padding: 8px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"
                                               title="Edit item">
                                                <i class="fas fa-edit" style="font-size: 14px;"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm" 
                                                    style="background: #9f7aea; color: white; border: none; border-radius: 8px; padding: 8px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"
                                                    onclick="viewItem(<?php echo $item['id']; ?>)"
                                                    title="View item">
                                                <i class="fas fa-eye" style="font-size: 14px;"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm" 
                                                    style="background: #f56565; color: white; border: none; border-radius: 8px; padding: 8px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"
                                                    onclick="deleteItem(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>')"
                                                    title="Delete item">
                                                <i class="fas fa-trash" style="font-size: 14px;"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <p>Are you sure you want to delete <strong id="itemName"></strong>?</p>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle me-1"></i>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer" style="border: none; padding: 1rem 2rem 2rem;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                <form method="POST" class="d-inline">
                    <input type="hidden" id="deleteItemId" name="item_id">
                    <button type="submit" name="delete_item" class="btn btn-danger" style="border-radius: 8px;">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteItem(id, name) {
    document.getElementById('deleteItemId').value = id;
    document.getElementById('itemName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function viewItem(id) {
    // Open frontend view of the item
    window.open('../menu.php?item=' + id, '_blank');
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        const fileType = file.type.toLowerCase();
        
        if (!allowedTypes.includes(fileType)) {
            alert('Please select a valid image file (JPG, PNG, GIF, or WebP)');
            input.value = '';
            return;
        }
        
        // Validate file size (5MB)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File size must be less than 5MB');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            preview.style.display = 'block';
        };
        
        reader.onerror = function() {
            alert('Error reading file');
            input.value = '';
        };
        
        reader.readAsDataURL(file);
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('menuItemForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const categoryId = document.getElementById('category_id').value;
            const price = document.getElementById('price').value;
            
            // Validate required fields
            if (!name || !categoryId || !price) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }
            
            // Validate price
            if (parseFloat(price) < 0) {
                e.preventDefault();
                alert('Price must be a positive number.');
                return;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            submitBtn.disabled = true;
            
            // Re-enable after 10 seconds (fallback)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 10000);
        });
    }
});

// Enhanced table interactions
document.querySelectorAll('input[name="sort_order"]').forEach(input => {
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.closest('form').querySelector('button[name="update_order"]').click();
        }
    });
});

// Auto-save sort order after 2 seconds of inactivity
let sortOrderTimeout;
document.querySelectorAll('input[name="sort_order"]').forEach(input => {
    input.addEventListener('input', function() {
        clearTimeout(sortOrderTimeout);
        const form = this.closest('form');
        
        sortOrderTimeout = setTimeout(() => {
            form.querySelector('button[name="update_order"]').click();
        }, 2000);
    });
});

// Success animations
function showSuccessAnimation(element) {
    element.style.transform = 'scale(1.1)';
    element.style.transition = 'all 0.3s ease';
    
    setTimeout(() => {
        element.style.transform = 'scale(1)';
    }, 300);
}

// Enhanced button interactions
document.querySelectorAll('button[name="toggle_availability"], button[name="toggle_featured"]').forEach(btn => {
    btn.addEventListener('click', function() {
        showSuccessAnimation(this);
    });
});
</script>

<style>
/* Enhanced table hover effects */
.table-hover tbody tr:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    transform: translateY(-1px);
    transition: all 0.3s ease;
}

/* Custom form controls */
.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Featured star animation */
button[name="toggle_featured"]:hover {
    transform: scale(1.1);
    transition: all 0.3s ease;
}

/* Action buttons hover effects */
.btn-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

/* Sort order input styling */
input[name="sort_order"]:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
}

/* Modal enhancements */
.modal-content {
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

/* Badge animations */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* Table responsiveness */
@media (max-width: 768px) {
    .table-responsive {
        border-radius: 12px;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    th, td {
        padding: 0.8rem 1rem !important;
        font-size: 0.9rem;
    }
    
    .btn-sm {
        padding: 6px !important;
        width: 32px !important;
        height: 32px !important;
    }
    
    input[name="sort_order"] {
        width: 50px !important;
    }
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spinner {
    animation: spin 1s linear infinite;
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

/* Form enhancements */
.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.form-check-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}
</style>

<?php include '../includes/footer.php'; ?>