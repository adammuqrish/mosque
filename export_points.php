<?php
$pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=mosque;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== point_transactions schema ===\n";
foreach ($pdo->query("SHOW COLUMNS FROM point_transactions") as $r) echo $r["Field"]."\n";
echo "=== member_points schema ===\n";
foreach ($pdo->query("SHOW COLUMNS FROM member_points") as $r) echo $r["Field"]."\n";
echo "=== badge_earnings schema ===\n";
foreach ($pdo->query("SHOW COLUMNS FROM badge_earnings") as $r) echo $r["Field"]."\n";

echo "\n=== Ali Bin Abu (user_id 3) point_transactions ===\n";
foreach ($pdo->query("SELECT * FROM point_transactions WHERE user_id=3 ORDER BY id") as $r) {
    echo "id=".($r["id"] ?? null)." | user_id=".($r["user_id"] ?? null)." | type=".($r["type"] ?? null)." | pts=".($r["points"] ?? null)." | bal=".($r["balance_after"] ?? null)." | reason=".($r["reason"] ?? null)." | src=".($r["source_type"] ?? null)." | src_id=".($r["source_id"] ?? null)." | admin_id=".($r["admin_id"] ?? null)."\n";
}
echo "\n=== Ali member_points ===\n";
foreach ($pdo->query("SELECT * FROM member_points WHERE user_id=3") as $r) echo implode(" | ", $r)."\n";

echo "\n=== ALL badge_earnings by user ===\n";
foreach ($pdo->query("SELECT be.id, be.user_id, be.badge_id, b.code, b.name, be.earned_at, be.source_event_id FROM badge_earnings be JOIN badges b ON b.id=be.badge_id ORDER BY be.user_id, be.id") as $r) {
    echo "id=".($r["id"] ?? null)." | uid=".($r["user_id"] ?? null)." | bid=".($r["badge_id"] ?? null)." | ".$r["code"]." | ".$r["name"]." | earned=".($r["earned_at"] ?? null)." | ev=".($r["source_event_id"] ?? null)."\n";
}
echo "\n=== ALL member_points ===\n";
foreach ($pdo->query("SELECT user_id, total_points, available_points, redeemed_points, current_streak, longest_streak FROM member_points ORDER BY user_id") as $r) echo implode(" | ", $r)."\n";
