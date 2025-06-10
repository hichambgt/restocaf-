<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

redirectToLogin();

$pageTitle = 'Orders Management';
$isAdmin = true;
$message = null;
$messageType = 'success';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = sanitizeInput($_POST['status']);
    
    if (updateOrderStatus($pdo, $orderId, $status)) {
        $message = 'Order status updated successfully!';
    } else {
        $message = 'Failed to update order status.';
        $messageType = 'danger';
    }
}

// Get filter parameters
$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$dateFilter = isset($_GET['date']) ? sanitizeInput($_GET['date']) : '';
$searchFilter = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query with filters
$sql = "SELECT * FROM orders WHERE 1=1";
$params = [];

if ($statusFilter) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

if ($dateFilter) {
    $sql .= " AND DATE(order_date) = ?";
    $params[] = $dateFilter;
}

if ($searchFilter) {
    $sql .= " AND (customer_name LIKE ? OR customer_phone LIKE ? OR id = ?)";
    $params[] = "%$searchFilter%";
    $params[] = "%$searchFilter%";
    $params[] = is_numeric($searchFilter) ? $searchFilter : 0;
}

$sql .= " ORDER BY order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Get order details if viewing specific order
$viewOrder = null;
$orderItems = [];
if (isset($_GET['view'])) {
    $viewOrder = getOrderById($pdo, (int)$_GET['view']);
    if ($viewOrder) {
        $orderItems = getOrderItems($pdo, $viewOrder['id']);
    }
}

// Get order statistics
$orderStats = $pdo->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN status = 'preparing' THEN 1 ELSE 0 END) as preparing,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
        SUM(total_amount) as total_revenue
    FROM orders
")->fetch();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2><i class="fas fa-receipt me-2"></i>Orders Management</h2>
                    <p class="mb-0">View and manage customer orders</p>
                </div>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Order Statistics -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-primary"><?php echo $orderStats['total_orders']; ?></h4>
                    <small>Total Orders</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-warning"><?php echo $orderStats['pending']; ?></h4>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-info"><?php echo $orderStats['confirmed']; ?></h4>
                    <small>Confirmed</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-primary"><?php echo $orderStats['preparing']; ?></h4>
                    <small>Preparing</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-success"><?php echo $orderStats['delivered']; ?></h4>
                    <small>Delivered</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-success"><?php echo formatPrice($orderStats['total_revenue']); ?></h4>
                    <small>Total Revenue</small>
                </div>
            </div>
        </div>
    </div>

    <?php if ($viewOrder): ?>
    <!-- Order Details -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Order Details - <?php echo generateOrderNumber($viewOrder['id']); ?>
                    </h5>
                    <a href="orders.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Orders
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h6>Customer Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?php echo htmlspecialchars($viewOrder['customer_name']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?php echo htmlspecialchars($viewOrder['customer_phone']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?php echo htmlspecialchars($viewOrder['customer_address']); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <h6>Order Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Order Date:</strong></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($viewOrder['order_date'])); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($viewOrder['status']); ?>">
                                            <?php echo ucfirst($viewOrder['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong><?php echo formatPrice($viewOrder['total_amount']); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <h6 class="mt-4">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../<?php echo getImagePath($item['image']); ?>" 
                                                 class="me-3" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                            <?php echo htmlspecialchars($item['item_name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo formatPrice($item['price']); ?></td>
                                    <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Status Update -->
                    <div class="mt-4">
                        <h6>Update Order Status</h6>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $viewOrder['id']; ?>">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <select name="status" class="form-select">
                                        <option value="pending" <?php echo $viewOrder['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $viewOrder['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="preparing" <?php echo $viewOrder['status'] == 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                        <option value="delivered" <?php echo $viewOrder['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $viewOrder['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" name="update_status" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Update Status
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $statusFilter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="preparing" <?php echo $statusFilter == 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                <option value="delivered" <?php echo $statusFilter == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $statusFilter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="<?php echo $dateFilter; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Customer name, phone, or order ID" value="<?php echo htmlspecialchars($searchFilter); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php if ($statusFilter || $dateFilter || $searchFilter): ?>
                    <div class="mt-3">
                        <a href="orders.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Clear Filters
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Orders</h5>
                    <span class="badge bg-primary"><?php echo count($orders); ?> orders</span>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-receipt fa-3x mb-3"></i>
                        <h5>No orders found</h5>
                        <p>No orders match your current filters.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date & Time</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <?php 
                                $items = getOrderItems($pdo, $order['id']);
                                $itemCount = array_sum(array_column($items, 'quantity'));
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo generateOrderNumber($order['id']); ?></strong>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($order['customer_phone']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div><?php echo date('M j, Y', strtotime($order['order_date'])); ?></div>
                                        <small class="text-muted"><?php echo date('g:i A', strtotime($order['order_date'])); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $itemCount; ?> items</span>
                                        <?php if (count($items) > 0): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($items[0]['item_name']); ?><?php echo count($items) > 1 ? ' +' . (count($items) - 1) . ' more' : ''; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="orders.php?view=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><h6 class="dropdown-header">Update Status</h6></li>
                                                    <li>
                                                        <form method="POST" class="dropdown-item-text">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <select name="status" class="form-select form-select-sm mb-2">
                                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                                <option value="preparing" <?php echo $order['status'] == 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                            <button type="submit" name="update_status" class="btn btn-primary btn-sm w-100">
                                                                Update
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
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
    <?php endif; ?>
</div>

<script>
// Auto-refresh for pending orders
setInterval(function() {
    if (window.location.search.includes('status=pending') || !window.location.search.includes('status=')) {
        // Only refresh if we're viewing pending orders or all orders
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('auto_refresh', '1');
        
        // Check if there are pending orders that need attention
        fetch(currentUrl.toString())
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newPendingCount = doc.querySelector('.text-warning h4')?.textContent || '0';
                const currentPendingCount = document.querySelector('.text-warning h4')?.textContent || '0';
                
                if (newPendingCount !== currentPendingCount) {
                    // Show notification
                    showNotification('New orders received!', 'info');
                }
            })
            .catch(console.error);
    }
}, 30000); // Check every 30 seconds

function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

// Sound notification for new orders (optional)
function playNotificationSound() {
    // You can add a notification sound here
    // const audio = new Audio('path/to/notification.mp3');
    // audio.play().catch(console.error);
}
</script>

<?php include '../includes/footer.php'; ?>