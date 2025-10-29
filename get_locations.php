<?php
// This file provides the data for the cascading dropdowns
// (Province -> District -> City)

require_once 'connection.php';
header('Content-Type: application/json');

// Set a default error response
$response = ['status' => 'error', 'locations' => [], 'message' => 'Invalid request.'];

if (isset($_GET['province_id'])) {
    // --- Request for Districts ---
    $province_id = (int)$_GET['province_id'];
    $rs = Database::search("SELECT id, name FROM district WHERE province_id = $province_id ORDER BY name ASC");
    
    if ($rs) {
        $locations = [];
        while ($row = $rs->fetch_assoc()) {
            $locations[] = $row;
        }
        $response['status'] = 'success';
        $response['locations'] = $locations;
        $response['message'] = 'Districts fetched successfully.';
    } else {
        $response['message'] = 'Database query failed for districts.';
    }

} else if (isset($_GET['district_id'])) {
    // --- Request for Cities ---
    $district_id = (int)$_GET['district_id'];
    $rs = Database::search("SELECT id, name FROM city WHERE district_id = $district_id ORDER BY name ASC");
    
    if ($rs) {
        $locations = [];
        while ($row = $rs->fetch_assoc()) {
            $locations[] = $row;
        }
        $response['status'] = 'success';
        $response['locations'] = $locations;
        $response['message'] = 'Cities fetched successfully.';
    } else {
        $response['message'] = 'Database query failed for cities.';
    }
}

echo json_encode($response);
exit;
?>

