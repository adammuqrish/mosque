<?php
$pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=mosque;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$esc = function($v) use ($pdo) {
    if ($v === null) return "NULL";
    return "'" . $pdo->quote((string)$v) . "'";  // pdo->quote includes outer quotes, so wrap as-is
};
$q = function($v) use ($pdo) {
    if ($v === null) return "NULL";
    $q = $pdo->quote((string)$v);
    return $q;  // already includes quotes
};

$out = "-- Gamification data backfill for production\n";
$out .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

// --- Ali Bin Abu (user_id=3) point_transactions ---
$tx = $pdo->query("SELECT id,user_id,type,points,balance_after,reason,breakdown,source_type,source_id,admin_id,admin_notes,created_at,updated_at FROM point_transactions WHERE user_id=3 ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$out .= "-- Ali Bin Abu point_transactions (" . count($tx) . " rows)\n";
$out .= "INSERT INTO point_transactions (id,user_id,type,points,balance_after,reason,breakdown,source_type,source_id,admin_id,admin_notes,created_at,updated_at) VALUES\n";
$rows = [];
foreach ($tx as $r) {
    $rows[] = sprintf("(%d,%d,%s,%d,%d,%s,%s,%s,%s,%s,%s,%s,%s)",
        (int)$r['id'], (int)$r['user_id'], $q($r['type']), (int)$r['points'], (int)$r['balance_after'],
        $q($r['reason']), $q($r['breakdown']), $q($r['source_type']),
        $r['source_id'] === null ? "NULL" : (int)$r['source_id'],
        $r['admin_id'] === null ? "NULL" : (int)$r['admin_id'],
        $q($r['admin_notes']), $q($r['created_at']), $q($r['updated_at'])
    );
}
$out .= implode(",\n", $rows) . ";\n\n";

// --- Ali's member_points ---
$mp = $pdo->query("SELECT * FROM member_points WHERE user_id=3")->fetch(PDO::FETCH_ASSOC);
if ($mp) {
    $out .= "-- Ali Bin Abu member_points\n";
    $cols = "id,user_id,total_points,available_points,redeemed_points,current_streak,longest_streak,last_activity_date,created_at,updated_at";
    $out .= "INSERT INTO member_points ($cols) VALUES ($mp[id],$mp[user_id],$mp[total_points],$mp[available_points],$mp[redeemed_points],$mp[current_streak],$mp[longest_streak],$mp[last_activity_date],$mp[created_at],$mp[updated_at]);\n\n";

    // --- Ali's badge_earnings (he has First Step, badge_id=1 which exists on prod) ---
    $be = $pdo->query("SELECT id,user_id,badge_id,earned_at,source_event_id,created_at,updated_at FROM badge_earnings WHERE user_id=3")->fetchAll(PDO::FETCH_ASSOC);
    $out .= "-- Ali Bin Abu badge_earnings (" . count($be) . " rows)\n";
    $out .= "INSERT INTO badge_earnings (id,user_id,badge_id,earned_at,source_event_id,created_at,updated_at) VALUES\n";
    $r2 = [];
    foreach ($be as $b) {
        $r2[] = sprintf("(%d,%d,%d,%s,%s,%s,%s)",
            (int)$b['id'], (int)$b['user_id'], (int)$b['badge_id'],
            $q($b['earned_at']),
            $b['source_event_id'] === null ? "NULL" : (int)$b['source_event_id'],
            $q($b['created_at']), $q($b['updated_at']));
    }
    $out .= implode(",\n", $r2) . ";\n";
}

file_put_contents("ali_backfill.sql", $out);
echo "Wrote ali_backfill.sql\n";
echo "Transactions for Ali: " . count($tx) . "\n";
echo "member_points present: " . ($mp ? "yes" : "no") . "\n";
echo "badge_earnings for Ali: " . ($mp ? count($be) : 0) . "\n";
