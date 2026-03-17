<?php
require 'config/db.php';
include 'includes/header.php';

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if (!$type || !$id) {
    die("Invalid item request.");
}

if ($type === 'Lost') {
    $stmt = $pdo->prepare("
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
            description
        FROM lost_items
        WHERE lost_item_id = ? AND is_public = 1
    ");
    $stmt->execute([$id]);
} elseif ($type === 'Found') {
    $stmt = $pdo->prepare("
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
            description
        FROM found_items
        WHERE found_item_id = ?
          AND status IN ('Available','Claim Pending','Claim Approved')
    ");
    $stmt->execute([$id]);
} else {
    die("Invalid item type.");
}

$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Item not found.");
}
?>

<div class="details-page">
    <div class="details-card-modern">

        <div class="details-banner">
            <span class="top-label large <?= strtolower($item['item_type']) ?>">
                <?= htmlspecialchars($item['item_type']) ?>
            </span>
        </div>

        <div class="details-content-layout">
            <div class="details-image-side">
                <?php if (!empty($item['image_path'])): ?>
                    <img src="/lost_found_system/<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image" class="details-main-image">
                <?php else: ?>
                    <div class="details-no-image">No Image Available</div>
                <?php endif; ?>
            </div>

            <div class="details-text-side">
                <h2><?= htmlspecialchars($item['item_name']) ?></h2>

                <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
                <p><strong>Color:</strong> <?= htmlspecialchars($item['color'] ?: 'N/A') ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($item['item_date']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($item['item_location']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($item['status']) ?></p>

                <div class="description-box">
                    <strong>Description:</strong>
                    <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                </div>

                <div class="btn-group">
                    <?php if ($item['item_type'] === 'Found'): ?>
                        <?php if (isset($_SESSION['student_id'])): ?>
                            <a href="/lost_found_system/student/claim_item.php?found_item_id=<?= $item['item_id'] ?>" class="btn">
                                Claim This Item
                            </a>
                        <?php else: ?>
                            <a href="/lost_found_system/login.php?redirect=<?= urlencode('item_details.php?type=' . $item['item_type'] . '&id=' . $item['item_id']) ?>" class="btn">
                            Login to Claim
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <a href="all_items.php" class="btn">Back to All Items</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>