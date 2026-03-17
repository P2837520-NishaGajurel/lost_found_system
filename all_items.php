<?php
require 'config/db.php';
include 'includes/header.php';

$search = trim($_GET['search'] ?? '');
$type = trim($_GET['type'] ?? '');
$category = trim($_GET['category'] ?? '');

/* Lost items query */
$lostSql = "
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
    WHERE is_public = 1
";
$lostParams = [];

/* Found items query */
$foundSql = "
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
    WHERE status IN ('Available','Claim Pending','Claim Approved')
";
$foundParams = [];

/* Search */
if ($search !== '') {
    $lostSql .= " AND (item_name LIKE ? OR category LIKE ? OR color LIKE ? OR location_lost LIKE ?)";
    $foundSql .= " AND (item_name LIKE ? OR category LIKE ? OR color LIKE ? OR location_found LIKE ?)";
    $searchValue = "%$search%";
    $lostParams = array_merge($lostParams, [$searchValue, $searchValue, $searchValue, $searchValue]);
    $foundParams = array_merge($foundParams, [$searchValue, $searchValue, $searchValue, $searchValue]);
}

/* Category filter */
if ($category !== '') {
    $lostSql .= " AND category = ?";
    $foundSql .= " AND category = ?";
    $lostParams[] = $category;
    $foundParams[] = $category;
}

$items = [];

if ($type === '' || $type === 'Lost') {
    $stmt = $pdo->prepare($lostSql);
    $stmt->execute($lostParams);
    $items = array_merge($items, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($type === '' || $type === 'Found') {
    $stmt = $pdo->prepare($foundSql);
    $stmt->execute($foundParams);
    $items = array_merge($items, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

/* Sort newest first */
usort($items, function ($a, $b) {
    return strtotime($b['item_date']) - strtotime($a['item_date']);
});

/* Categories */
$categoryStmt = $pdo->query("
    SELECT DISTINCT category FROM (
        SELECT category FROM lost_items WHERE is_public = 1
        UNION
        SELECT category FROM found_items WHERE status IN ('Available','Claim Pending','Claim Approved')
    ) AS categories
    ORDER BY category ASC
");
$categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="card">
    <h2>All Items</h2>

    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search items..." value="<?= htmlspecialchars($search) ?>">

        <select name="type">
            <option value="">All Types</option>
            <option value="Lost" <?= $type === 'Lost' ? 'selected' : '' ?>>Lost</option>
            <option value="Found" <?= $type === 'Found' ? 'selected' : '' ?>>Found</option>
        </select>

        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn">Search</button>
        <a href="all_items.php" class="btn">Reset</a>
    </form>
</div>

<div class="course-grid">
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <a class="course-card" href="item_details.php?type=<?= urlencode($item['item_type']) ?>&id=<?= urlencode($item['item_id']) ?>">
                
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
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <p>No items found matching your search/filter.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>