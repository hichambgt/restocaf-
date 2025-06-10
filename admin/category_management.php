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
            'price' => (float)$_POST['price']
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
        
        if (!$message && addMenuItem($pdo, $data)) {
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
            'price' => (float)$_POST['price']
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadResult = uploadImage($_FILES['image']);
            if ($uploadResult['success']) {
                // Delete old image if it exists and is not default
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
        
        if (!$message && updateMenuItem($pdo, $id, $data)) {
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
}

// Get all categories and menu items
$categories = getAllCategories($pdo);
$stmt = $pdo->prepare("
    SELECT mi.*, c.name as category_name 
    FROM menu_items mi 
    JOIN categories c ON mi.category_id = c.id 
    ORDER BY c.name, mi.name
");
$stmt->execute();
$menuItems = $stmt->fetchAll();

$editItem = null;

// Get item for editing
if (isset($_GET['edit'])) {
    $editItem = getMenuItemById($pdo, (int)$_GET['edit']);
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background: var(--primary); color: var(--white); border: none;">
                <div class="card-body">
                    <h2><i class="fas fa-utensils me-2"></i>Menu Management</h2>
                    <p style="margin: 0; opacity: 0.9;">Add, edit, and manage your restaurant's menu items</p>
                </div>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Debug Information (if enabled) -->
    <?php if (isset($_GET['debug']) && $_GET['debug'] === 'true'): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-bug me-2"></i>Debug Information</h5>
                <?php
                echo "<p><strong>Upload Directory:</strong> " . MENU_UPLOAD_DIR . "</p>";
                echo "<p><strong>Directory Exists:</strong> " . (is_dir(MENU_UPLOAD_DIR) ? '✅ Yes' : '❌ No') . "</p>";
                echo "<p><strong>Directory Writable:</strong> " . (is_writable(MENU_UPLOAD_DIR) ? '✅ Yes' : '❌ No') . "</p>";
                echo "<p><strong>Directory Permissions:</strong> " . substr(sprintf('%o', fileperms(MENU_UPLOAD_DIR)), -4) . "</p>";
                
                if (is_dir(MENU_UPLOAD_DIR)) {
                    $files = array_diff(scandir(MENU_UPLOAD_DIR), ['.', '..']);
                    echo "<p><strong>Files in upload directory (" . count($files) . "):</strong></p>";
                    if (!empty($files)) {
                        echo "<ul>";
                        foreach ($files as $file) {
                            $fullPath = MENU_UPLOAD_DIR . $file;
                            $size = filesize($fullPath);
                            $permissions = substr(sprintf('%o', fileperms($fullPath)), -4);
                            echo "<li>$file ($size bytes, permissions: $permissions)</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No files in upload directory.</p>";
                    }
                }
                ?>
                <a href="?debug=false" class="btn btn-sm btn-secondary">Hide Debug</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Add/Edit Form -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 style="margin: 0;">
                        <i class="fas fa-<?php echo $editItem ? 'edit' : 'plus'; ?> me-2"></i>
                        <?php echo $editItem ? 'Edit Menu Item' : 'Add New Item'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="menuItemForm">
                        <?php if ($editItem): ?>
                        <input type="hidden" name="item_id" value="<?php echo $editItem['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-control" id="category_id" name="category_id" required>
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
                            <label for="name" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $editItem ? htmlspecialchars($editItem['name']) : ''; ?>" 
                                   placeholder="e.g., Caesar Salad" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Describe the dish..."><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="price" class="form-label">Price ($) *</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" 
                                   value="<?php echo $editItem ? $editItem['price'] : ''; ?>" 
                                   placeholder="0.00" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" 
                                   onchange="previewImage(this, 'imagePreview')">
                            <small class="text-muted">Max file size: 5MB. Formats: JPG, PNG, GIF, WebP</small>
                        </div>
                        
                        <!-- Image Preview -->
                        <?php if ($editItem): ?>
                        <div class="form-group">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img id="imagePreview" 
                                     src="../<?php echo getImagePath($editItem['image'], true); ?>" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px; max-height: 150px; object-fit: cover; border-radius: 8px;"
                                     alt="Current image"
                                     onerror="this.src='data:image/svg+xml;base64,<?php echo base64_encode('<svg width="200" height="150" xmlns="http://www.w3.org/2000/svg"><rect width="200" height="150" fill="#f8f9fa" stroke="#dee2e6"/><text x="100" y="80" font-family="Arial" font-size="14" fill="#666" text-anchor="middle">No Image</text></svg>'); ?>'">
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="form-group">
                            <img id="imagePreview" src="#" class="img-thumbnail d-none" 
                                 style="max-width: 200px; max-height: 150px; object-fit: cover; border-radius: 8px;">
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="<?php echo $editItem ? 'edit_item' : 'add_item'; ?>" class="btn btn-primary">
                                <i class="fas fa-<?php echo $editItem ? 'save' : 'plus'; ?> me-2"></i>
                                <?php echo $editItem ? 'Update Item' : 'Add Item'; ?>
                            </button>
                            <?php if ($editItem): ?>
                            <a href="menu_management.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 style="margin: 0;"><i class="fas fa-tools me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="category_management.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-tags me-1"></i>Manage Categories
                        </a>
                        <a href="?debug=true" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-bug me-1"></i>Debug Info
                        </a>
                        <a href="../index.php" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>View Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Menu Items List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 style="margin: 0;"><i class="fas fa-list me-2"></i>Menu Items</h5>
                    <span class="badge" style="background: var(--primary); color: var(--white); padding: 8px 12px; border-radius: 20px;">
                        <?php echo count($menuItems); ?> items
                    </span>
                </div>
                <div class="card-body">
                    <?php if (empty($menuItems)): ?>
                    <div class="text-center py-5" style="color: var(--gray-500);">
                        <i class="fas fa-utensils" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <h5>No menu items found</h5>
                        <p>Add your first menu item to get started!</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menuItems as $item): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo getImagePath($item['image'], true); ?>" 
                                             class="img-thumbnail" 
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             onerror="this.src='data:image/svg+xml;base64,<?php echo base64_encode('<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg"><rect width="60" height="60" fill="#f8f9fa" stroke="#dee2e6"/><text x="30" y="35" font-family="Arial" font-size="10" fill="#666" text-anchor="middle">No Image</text></svg>'); ?>'">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                        <?php if (!empty($item['description'])): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($item['description'], 0, 50)) . (strlen($item['description']) > 50 ? '...' : ''); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: var(--gray-200); color: var(--gray-700); padding: 4px 8px; border-radius: 12px;">
                                            <?php echo htmlspecialchars($item['category_name']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="price-tag" style="font-size: 0.9rem; padding: 4px 8px;">
                                            <?php echo formatPrice($item['price']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="availability" value="<?php echo $item['is_available'] ? 0 : 1; ?>">
                                            <button type="submit" name="toggle_availability" 
                                                    class="btn btn-sm <?php echo $item['is_available'] ? 'btn-success' : 'btn-outline-secondary'; ?>"
                                                    title="Click to toggle availability">
                                                <i class="fas fa-<?php echo $item['is_available'] ? 'check' : 'times'; ?> me-1"></i>
                                                <?php echo $item['is_available'] ? 'Available' : 'Unavailable'; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="menu_management.php?edit=<?php echo $item['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit item">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteItem(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>')"
                                                    title="Delete item">
                                                <i class="fas fa-trash"></i>
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="itemName"></strong>?</p>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle me-1"></i>This action cannot be undone. The image file will also be deleted.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" class="d-inline">
                    <input type="hidden" id="deleteItemId" name="item_id">
                    <button type="submit" name="delete_item" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for menu management */
.table th {
    background: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
    font-weight: 600;
    color: var(--gray-700);
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-200);
}

.table-hover tbody tr:hover {
    background: rgba(255, 107, 53, 0.05);
}

.modal-content {
    border-radius: var(--radius-lg);
    border: none;
    box-shadow: var(--shadow-xl);
}

.modal-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: var(--radius-md);
}

.form-control:focus {
    border-color: var(--primary-solid);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}
</style>

<script>
// Delete confirmation function
function deleteItem(id, name) {
    document.getElementById('deleteItemId').value = id;
    document.getElementById('itemName').textContent = name;
    
    // Create and show modal (without Bootstrap dependency)
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    document.body.classList.add('modal-open');
    
    // Add backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.onclick = closeModal;
    document.body.appendChild(backdrop);
}

// Close modal function
function closeModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
    modal.classList.remove('show');
    document.body.classList.remove('modal-open');
    
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
}

// Image preview function
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
            const imageInput = document.getElementById('image');
            
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
            
            // Validate image if uploading
            if (imageInput.files.length > 0) {
                const file = imageInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                const fileType = file.type.toLowerCase();
                
                if (!allowedTypes.includes(fileType)) {
                    e.preventDefault();
                    alert('Please select a valid image file (JPG, PNG, GIF, or WebP)');
                    return;
                }
                
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    e.preventDefault();
                    alert('File size must be less than 5MB');
                    return;
                }
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
    
    // Close modal handlers
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
});

// Auto-refresh debug info if enabled
<?php if (isset($_GET['debug']) && $_GET['debug'] === 'true'): ?>
setInterval(function() {
    // Auto-refresh debug info every 30 seconds
    if (document.querySelector('.alert-info')) {
        console.log('Debug mode active - checking upload directory...');
    }
}, 30000);
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
