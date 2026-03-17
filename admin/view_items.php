<?php
require '../config/db.php';
require '../includes/auth_admin.php';
include '../includes/header.php';

$type = $_GET['type'] ?? 'all';
$title = 'All Items';

if ($type === 'lost') {
    $title = 'Lost Items';
    $stmt = $pdo->query("
        SELECT 
            lost_item_id AS item_id,
            'Lost' AS item_type,
            item_name,
            category,
            color,
            image_path,
            date_lost AS item_date,
            location_lost AS item_location,
            status
        FROM lost_items
        ORDER BY created_at DESC
    ");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($type === 'found') {
    $title = 'Found Items';
    $stmt = $pdo->query("
        SELECT 
            found_item_id AS item_id,
            'Found' AS item_type,
            item_name,
            category,
            color,
            image_path,
            date_found AS item_date,
            location_found AS item_location,
            status
        FROM found_items
        ORDER BY created_at DESC
    ");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    $title = 'All Items';

    $lostStmt = $pdo->query("
        SELECT 
            lost_item_id AS item_id,
            'Lost' AS item_type,
            item_name,
            category,
            color,
            image_path,
            date_lost AS item_date,
            location_lost AS item_location,
            status,
            created_at
        FROM lost_items
    ");
    $lostItems = $lostStmt->fetchAll(PDO::FETCH_ASSOC);

    $foundStmt = $pdo->query("
        SELECT 
            found_item_id AS item_id,
            'Found' AS item_type,
            item_name,
            category,
            color,
            image_path,
            date_found AS item_date,
            location_found AS item_location,
            status,
            created_at
        FROM found_items
    ");
    $foundItems = $foundStmt->fetchAll(PDO::FETCH_ASSOC);

    $items = array_merge($lostItems, $foundItems);

    usort($items, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}
?>

<div class="card">
    <h2><?= htmlspecialchars($title) ?></h2>
    <div class="btn-group">
        <a class="btn" href="dashboard.php">Back to Dashboard</a>
    </div>
</div>

<div class="course-grid">
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <div class="course-card">
                <div class="course-image-wrap">
                    <?php if (!empty($item['image_path'])): ?>
                        <img src="/lost_found_system/<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image" class="course-image">
                    <?php else: ?>
                        <div class="course-image placeholder-image">No Image</div>
                    <?php endif; ?>

                    <span class="top-label <?= strtolower($item['item_type']) ?>">
                        <?= htmlspecialchars($item['item_type']) ?>
                    </span>
                </div>

                <div class="course-body">
                    <h3><?= htmlspecialchars($item['item_name']) ?></h3>
                    <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
                    <p><strong>Color:</strong> <?= htmlspecialchars($item['color'] ?: 'N/A') ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($item['item_date']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($item['item_location']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($item['status']) ?></p>

                    <div class="btn-group">
                        <?php if ($item['item_type'] === 'Lost'): ?>
                            <a class="btn" href="../item_details.php?type=Lost&id=<?= $item['item_id'] ?>">View Details</a>
                        <?php else: ?>
                            <a class="btn" href="../item_details.php?type=Found&id=<?= $item['item_id'] ?>">View Details</a>
                            <a class="btn" href="edit_found_item.php?id=<?= $item['item_id'] ?>">Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <p>No items found.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>