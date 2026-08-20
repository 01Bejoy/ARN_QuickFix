<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/arn_features.php';
arn_require_role(['manager']);

$managerEmail = $_SESSION['email'];
$stmt = $pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1'); $stmt->execute([$managerEmail]); $managerId = $stmt->fetchColumn() ?: null;
$q = trim($_GET['q'] ?? '');
$filter = trim($_GET['specialization'] ?? '');
$sql = "SELECT u.id,u.name,u.email,u.phone,u.specialization,u.position,u.created_at,
    COUNT(DISTINCT CASE WHEN LOWER(sr.status)='processing' THEN sr.id END) AS assigned_jobs,
    COUNT(DISTINCT CASE WHEN LOWER(sr.status)='completed' THEN sr.id END) AS completed_jobs,
    COALESCE(AVG(r.rating),0) AS avg_rating, COUNT(DISTINCT r.id) AS review_count
    FROM users u LEFT JOIN service_requests sr ON sr.technician_id=u.id LEFT JOIN technician_reviews r ON r.technician_id=u.id
    WHERE u.role LIKE 'tech_%'";
$params=[];
if ($q !== '') { $sql .= ' AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)'; $like="%$q%"; array_push($params,$like,$like,$like); }
if ($filter !== '') { $sql .= ' AND u.specialization = ?'; $params[]=$filter; }
$sql .= ' GROUP BY u.id ORDER BY u.name ASC';
$stmt=$pdo->prepare($sql); $stmt->execute($params); $techs=$stmt->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Technician Management | ARN QuickFix</title><link href="css/bootstrap.min.css" rel="stylesheet"><link href="css/style.css" rel="stylesheet"><style>.metric{background:#fff;border:1px solid #E2E8F0;border-radius:16px}.rating{color:#D97706;font-weight:800}.asset-id{font-family:monospace;font-weight:800}</style></head>
<body style="background:#F8FAFC"><div class="container-fluid px-4 py-4"><div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold mb-1">Technician Management</h2><p class="text-secondary mb-0">Performance, ratings, reviews and promotion history.</p></div><a href="manager-dashboard.php" class="btn btn-outline-secondary rounded-pill">Back to Manager Hub</a></div>
<form class="row g-2 mb-4"><div class="col-md-5"><input name="q" value="<?=htmlspecialchars($q)?>" class="form-control" placeholder="Search name, email or phone"></div><div class="col-md-3"><select name="specialization" class="form-select"><option value="">All specializations</option><option value="AC" <?=$filter==='AC'?'selected':''?>>AC</option><option value="Elevator" <?=$filter==='Elevator'?'selected':''?>>Elevator</option><option value="Generator" <?=$filter==='Generator'?'selected':''?>>Generator</option></select></div><div class="col-md-2"><button class="btn btn-dark w-100">Filter</button></div></form>
<div class="metric p-3"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Technician</th><th>Position</th><th>Specialization</th><th>Assigned Jobs</th><th>Completed</th><th>Average Rating</th><th>Reviews</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($techs as $t): ?><tr><td><strong><?=htmlspecialchars($t['name'])?></strong><div class="small text-secondary"><?=htmlspecialchars($t['email'])?></div><div class="small text-secondary"><?=htmlspecialchars($t['phone'] ?? '')?></div></td><td><?=htmlspecialchars($t['position'] ?? 'Technician')?></td><td><?=htmlspecialchars($t['specialization'] ?? '—')?></td><td><?= (int)$t['assigned_jobs'] ?></td><td><?= (int)$t['completed_jobs'] ?></td><td><span class="rating">★ <?=number_format((float)$t['avg_rating'],1)?> / 5</span></td><td><?= (int)$t['review_count'] ?></td><td><span class="badge bg-success-subtle text-success">Active</span></td><td><a class="btn btn-sm btn-outline-primary rounded-pill" href="manager-technician.php?id=<?=(int)$t['id']?>">View</a></td></tr><?php endforeach; ?><?php if(!$techs): ?><tr><td colspan="9" class="text-center text-secondary py-5">No technicians found.</td></tr><?php endif; ?></tbody></table></div></div></div></body></html>
