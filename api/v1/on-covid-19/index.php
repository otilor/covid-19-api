<?php

include ('estimator.php');

// echo covid19ImpactEstimator($decoded);
$content = trim(file_get_contents("php://input"));

$decoded = json_decode($content, true);
header('Content-Type: application/json');

echo json_encode(covid19ImpactEstimator($decoded));