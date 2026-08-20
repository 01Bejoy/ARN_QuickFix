<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/arn_features.php';
arn_require_role(['client','user']);

$email = $_SESSION['email'];
$requestId = (int)($_GET['id'] ?? $_POST['service_request_id'] ?? 0);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_review') {
    $rating = (int)($_POST['rating'] ?? 0);
    $review = trim($_POST['review'] ?? '');
    if ($requestId < 1 || $rating < 1 || $rating > 5) {
        $error = 'Please provide a valid 1–5 star rating.';
    } else {
        $stmt = $pdo->prepare("SELECT sr.*, a.id AS asset_db_id, a.asset_id AS canonical_asset_id, u.id AS technician_db_id, u.name AS technician_name
            FROM service_requests sr
            LEFT JOIN assets a ON a.id=sr.asset_ref_id
            LEFT JOIN users u ON u.id=sr.technician_id
            WHERE sr.id=? AND sr.client_email=? AND LOWER(sr.status)='completed' LIMIT 1");
        $stmt->execute([$requestId, $email]);
        $job = $stmt->fetch();
        if (!$job) {
            $error = 'You can only review a completed service belonging to your account.';
        } elseif (empty($job['technician_db_id'])) {
            $error = 'This completed job has no technician assignment recorded yet.';
        } else {
            $check = $pdo->prepare('SELECT id FROM technician_reviews WHERE service_request_id=? LIMIT 1');
            $check->execute([$requestId]);
            if ($check->fetch()) {
                $error = 'A review has already been submitted for this completed service.';
            } else {
                $customer = $pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1'); $customer->execute([$email]); $customerId = $customer->fetchColumn() ?: null;
                $ins = $pdo->prepare('INSERT INTO technician_reviews (service_request_id, asset_id, customer_id, customer_email, technician_id, rating, review) VALUES (?,?,?,?,?,?,?)');
                $ins->execute([$requestId, $job['asset_db_id'] ?: null, $customerId, $email, $job['technician_db_id'], $rating, $review]);
                $success = 'Review submitted successfully.';
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT sr.id, sr.asset_id, sr.asset_ref_id, sr.status, sr.problem_category, sr.created_at, sr.updated_at,
    u.name AS technician_name, r.id AS review_id, r.rating, r.review
    FROM service_requests sr
    LEFT JOIN users u ON u.id=sr.technician_id
    LEFT JOIN technician_reviews r ON r.service_request_id=sr.id
    WHERE sr.id=? AND sr.client_email=? AND LOWER(sr.status)='completed' LIMIT 1");
$stmt->execute([$requestId, $email]);
$job = $stmt->fetch();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Service Review | ARN QuickFix</title><link href="css/bootstrap.min.css" rel="stylesheet"><link href="css/style.css" rel="stylesheet"></head>
<body style="background:#F8FAFC"><div class="container py-5" style="max-width:760px"><a href="client-dashboard.php" class="btn btn-outline-secondary rounded-pill mb-4">Back to Dashboard</a><div class="bg-white border rounded-4 shadow-sm p-4"><h3 class="fw-bold">Rate Completed Service</h3><?php if($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?><?php if($success): ?><div class="alert alert-success"><?=htmlspecialchars($success)?></div><?php endif; ?><?php if(!$job): ?><div class="alert alert-warning">Completed service not found.</div><?php elseif($job['review_id']): ?><div class="mb-3"><strong>Asset ID:</strong> <?=htmlspecialchars($job['asset_id'])?></div><div class="mb-3"><strong>Technician:</strong> <?=htmlspecialchars($job['technician_name'] ?? 'Assigned Technician')?></div><div class="display-6 mb-2"><?=str_repeat('★',(int)$job['rating']) . str_repeat('☆',5-(int)$job['rating'])?></div><p class="text-secondary mb-0"><?=nl2br(htmlspecialchars($job['review'] ?? 'No written review.'))?></p><?php else: ?><div class="mb-3"><strong>Asset ID:</strong> <span class="font-monospace fw-bold"><?=htmlspecialchars($job['asset_id'])?></span></div><div class="mb-4"><strong>Technician:</strong> <?=htmlspecialchars($job['technician_name'] ?? 'Assigned Technician')?></div><form method="post"><input type="hidden" name="action" value="submit_review"><input type="hidden" name="service_request_id" value="<?= $requestId ?>"><div class="mb-3"><label class="form-label fw-semibold">Rating</label><select name="rating" class="form-select" required><option value="">Select rating</option><option value="5">★★★★★ — Excellent</option><option value="4">★★★★☆ — Very Good</option><option value="3">★★★☆☆ — Good</option><option value="2">★★☆☆☆ — Fair</option><option value="1">★☆☆☆☆ — Poor</option></select></div><div class="mb-3"><label class="form-label fw-semibold">Written Review</label><textarea name="review" class="form-control" rows="5" maxlength="2000" placeholder="Tell us about the service..."></textarea></div><button class="btn btn-primary rounded-pill px-4 fw-bold">Submit Review</button></form><?php endif; ?></div></div></body></html>
