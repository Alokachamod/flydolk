<?php
require "connection.php";

if (isset($_POST["modelId"])) {
    $modelId = $_POST["modelId"];

    // Check if the model exists
    $result = Database::search("SELECT * FROM model WHERE id = '" . $modelId . "'");
    if ($result->num_rows == 1) {
        // Delete the model
        Database::iud("DELETE FROM model WHERE id = '" . $modelId . "'");
        echo "success";
    } else {
        echo "Model not found.";
    }
} else {
    echo "Invalid request.";
}