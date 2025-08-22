<?php
// getUser.php
session_start();
header('Content-Type: application/json');

function send_json($code, $msg, $extra = []) {
    http_response_code($code);
    echo json_encode(array_merge(["ok"=>false,"msg"=>$msg], $extra));
    exit;
}

if (!isset($_SESSION['admin_id'])) {
    send_json(401, "Unauthorized");
}

require 'connection.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    send_json(422, "Invalid user id");
}

/** pick first avatar from user_img (column-agnostic) */
function get_first_avatar($connOrDb, $uid){
    $avatar = null;
    $q = "SELECT * FROM user_img WHERE user_id = ? ORDER BY id ASC LIMIT 1";

    if (isset($connOrDb) && $connOrDb instanceof mysqli) {
        if ($st = $connOrDb->prepare($q)) {
            $st->bind_param('i',$uid);
            if ($st->execute()) {
                $r = $st->get_result();
                if ($r && $row = $r->fetch_assoc()) {
                    foreach (['img_url','url','path','image','img','avatar','filename'] as $k) {
                        if (!empty($row[$k])) { $avatar = $row[$k]; break; }
                    }
                }
            } else {
                send_json(500, "Failed to execute avatar query", ["db_error"=>$connOrDb->error]);
            }
            $st->close();
        }
    } elseif (class_exists('Database') && method_exists('Database','search')) {
        $r = Database::search("SELECT * FROM user_img WHERE user_id = ".$uid." ORDER BY id ASC LIMIT 1");
        if ($r && $row = $r->fetch_assoc()) {
            foreach (['img_url','url','path','image','img','avatar','filename'] as $k) {
                if (!empty($row[$k])) { $avatar = $row[$k]; break; }
            }
        }
    }
    return $avatar;
}

/** profile + status + invoice count (via user_has_address) */
$sql = "
    SELECT
        u.id,
        u.name,
        u.email,
        u.mobile,
        u.joined_date,
        u.user_status_id,
        us.name AS status_name,
        (
          SELECT COUNT(*)
          FROM invoice i
          JOIN user_has_address uha ON uha.id = i.user_has_address_id
          WHERE uha.user_id = u.id
        ) AS total_orders
    FROM user u
    LEFT JOIN user_status us ON us.id = u.user_status_id
    WHERE u.id = $id
    LIMIT 1
";

$row = null;
try {
    if (class_exists('Database') && method_exists('Database','search')) {
        $rs = Database::search($sql);
        if (!$rs) send_json(500, "Query failed (Database helper).");
        $row = $rs->fetch_assoc();
    } elseif (isset($conn) && $conn instanceof mysqli) {
        $rs = $conn->query($sql);
        if ($rs === false) send_json(500, "Query failed", ["db_error"=>$conn->error]);
        $row = $rs->fetch_assoc();
    } else {
        send_json(500, "No database connector available");
    }
} catch (Throwable $t) {
    send_json(500, "Exception while querying user", ["error"=>$t->getMessage()]);
}

if (!$row) {
    send_json(404, "User not found");
}

/** addresses — FIXED FK NAMES (city.district_id, district.province_id) */
$addresses = [];
$addrSql = "
    SELECT 
        uha.id,
        uha.address_line_1,
        uha.address_line_2,
        uha.zip_code,
        c.name  AS city,
        d.name  AS district,
        p.name  AS province
    FROM user_has_address uha
    LEFT JOIN city     c ON c.id = uha.city_id
    LEFT JOIN district d ON d.id = c.district_id     -- correct
    LEFT JOIN province p ON p.id = d.province_id     -- correct
    WHERE uha.user_id = $id
    ORDER BY uha.id ASC
";
try {
    if (class_exists('Database') && method_exists('Database','search')) {
        $ar = Database::search($addrSql);
        if ($ar) { while ($a = $ar->fetch_assoc()) { $addresses[] = $a; } }
    } elseif ($conn instanceof mysqli) {
        $ar = $conn->query($addrSql);
        if ($ar === false) send_json(500, "Address query failed", ["db_error"=>$conn->error]);
        while ($a = $ar->fetch_assoc()) { $addresses[] = $a; }
    }
} catch (Throwable $t) {
    send_json(500, "Exception while querying addresses", ["error"=>$t->getMessage()]);
}

// success
echo json_encode([
    "ok" => true,
    "user" => [
        "id"            => (int)$row['id'],
        "name"          => $row['name'],
        "email"         => $row['email'],
        "mobile"        => $row['mobile'],
        "joined_date"   => $row['joined_date'],
        "user_status_id"=> (int)$row['user_status_id'],
        "status_name"   => $row['status_name'],
        "total_orders"  => (int)$row['total_orders'],
        "avatar"        => get_first_avatar($conn ?? null, (int)$row['id'])
    ],
    "addresses" => $addresses
]);
