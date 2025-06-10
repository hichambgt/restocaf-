<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

redirectToLogin();

$pageTitle = 'Admin Dashboard';
$isAdmin = true;

// Get statistics
$stats = [
    'total_orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'pending_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
    'total_menu_items' => $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn(),
    'total_categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'today_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()")->fetchColumn(),
    'today_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(order_date) = CURDATE()")->fetchColumn()
];

// Get recent orders
$recentOrders = getAllOrders($pdo, 5);

// Get order status distribution
$statusStats = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM orders 
    GROUP BY status
")->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                    <p class="mb-0">Welcome back, <?php echo $_SESSION['admin_username']; ?>! Here's your restaurant overview.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $stats['total_orders']; ?></h4>
                            <p class="mb-0">Total Orders</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $stats['pending_orders']; ?></h4>
                            <p class="mb-0">Pending Orders</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $stats['total_menu_items']; ?></h4>
                            <p class="mb-0">Menu Items</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-utensils fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $stats['total_categories']; ?></h4>
                            <p class="mb-0">Categories</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Performance -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-day me-2"></i>Today's Performance</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-primary"><?php echo $stats['today_orders']; ?></h3>
                            <p class="mb-0">Orders Today</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success"><?php echo formatPrice($stats['today_revenue']); ?></h3>
                            <p class="mb-0">Revenue Today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-pie me-2"></i>Order Status Distribution</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($statusStats as $status): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-capitalize">
                            <span class="badge <?php echo getStatusBadgeClass($status['status']); ?>">
                                <?php echo $status['status']; ?>
                            </span>
                        </span>
                        <span class="fw-bold"><?php echo $status['count']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Orders</h5>
                    <a href="orders.php" class="btn btn-outline-primary btn-sm">View All Orders</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentOrders)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-receipt fa-3x mb-3"></i>
                        <p>No orders yet</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><?php echo generateOrderNumber($order['id']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                                    </td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo formatPrice($order['total_amount']); ?></td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
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

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="menu_management.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                Add Menu Item
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="category_management.php" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-tags fa-2x d-block mb-2"></i>
                                Manage Categories
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="orders.php" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-receipt fa-2x d-block mb-2"></i>
                                View Orders
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="../index.php" target="_blank" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-external-link-alt fa-2x d-block mb-2"></i>
                                Visit Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>