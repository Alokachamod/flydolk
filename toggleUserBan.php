<?php
// toggleUserBan.php
session_start();
header('Content-Type: application/json');

function respond($ok, $msg, $extra = [], $code = 200){
    http_response_code($code);
    echo json_encode(array_merge(['ok'=>$ok, 'msg'=>$msg], $extra));
    exit;
}

if (!isset($_SESSION['admin_id'])) {
    respond(false, 'Unauthorized', [], 401);
}

require 'connection.php';

$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$action = isset($_POST['action']) ? strtolower(trim($_POST['action'])) : ''; // 'ban' | 'unban'

if ($id <= 0 || !in_array($action, ['ban','unban'], true)) {
    respond(false, 'Invalid parameters', [], 422);
}

/** helpers */
function qrow($sql){
    if (class_exists('Database') && method_exists('Database','search')) {
        $r = Database::search($sql);
        return $r ? $r->fetch_assoc() : null;
    }
    global $conn;
    if ($conn instanceof mysqli) {
        $r = $conn->query($sql);
        if ($r === false) return ['__error__' => $conn->error];
        return $r->fetch_assoc();
    }
    return null;
}
function qiud($sql){
    if (class_exists('Database') && method_exists('Database','iud')) {
        return Database::iud($sql);
    }
    global $conn;
    if ($conn instanceof mysqli) {
        return $conn->query($sql);
    }
    return false;
}

try {
    // 1) Ensure user exists
    $u = qrow("SELECT id, user_status_id FROM user WHERE id = $id LIMIT 1");
    if (!$u || isset($u['__error__'])) {
        respond(false, isset($u['__error__']) ? 'Failed to read user' : 'User not found',
            ['db_error' => $u['__error__'] ?? null], isset($u['__error__']) ? 500 : 404);
    }

    // 2) Resolve ACTIVE and BANNED ids by name
    $activeId = null;
    $bannedId = null;          // preferred exact name 'Banned'
    $fallbackBan = null;       // fallback names

    $readStatusesSql = "SELECT id, name FROM user_status";
    if (class_exists('Database') && method_exists('Database','search')) {
        $rs = Database::search($readStatusesSql);
        while ($rs && $r = $rs->fetch_assoc()) {
            $n = strtolower(trim($r['name']));
            if ($n === 'active') $activeId = (int)$r['id'];
            if ($n === 'banned') $bannedId = (int)$r['id'];
            if (in_array($n, ['blocked','suspended','blacklisted','deactivated','inactive'], true)) {
                $fallbackBan = (int)$r['id'];
            }
        }
    } else {
        global $conn;
        if (!($conn instanceof mysqli)) respond(false, 'No database connector available', [], 500);
        $rs = $conn->query($readStatusesSql);
        if ($rs === false) respond(false, 'Failed to read statuses', ['db_error'=>$conn->error], 500);
        while ($r = $rs->fetch_assoc()) {
            $n = strtolower(trim($r['name']));
            if ($n === 'active') $activeId = (int)$r['id'];
            if ($n === 'banned') $bannedId = (int)$r['id'];
            if (in_array($n, ['blocked','suspended','blacklisted','deactivated','inactive'], true)) {
                $fallbackBan = (int)$r['id'];
            }
        }
    }

    if ($activeId === null)  $activeId  = 1;                 // safe default
    if ($bannedId === null)  $bannedId  = $fallbackBan ?? 2; // fallback

    // 3) Target status by action
    if ($action === 'ban') {
        $targetId   = $bannedId;
        $targetName = 'Banned';
    } else { // unban
        $targetId   = $activeId;
        $targetName = 'Active';
    }

    // 4) Update
    $ok = qiud("UPDATE user SET user_status_id = $targetId WHERE id = $id");
    if ($ok === false) {
        global $conn;
        respond(false, 'Failed to update user status', ['db_error'=>$conn->error ?? null], 500);
    }

    respond(true, "User is now $targetName.", [
        'new_status_id'   => $targetId,
        'new_status_name' => $targetName
    ]);

} catch (Throwable $t) {
    respond(false, 'Exception while toggling ban', ['error'=>$t->getMessage()], 500);
}
