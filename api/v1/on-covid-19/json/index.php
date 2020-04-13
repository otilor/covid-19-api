<?php

include ('../estimator.php');

// echo covid19ImpactEstimator($decoded);
$content = trim(file_get_contents("php://input"));

$decoded = json_decode($content, true);
header('Content-Type: application/json');

//var_dump($decoded);
echo json_encode(covid19ImpactEstimator($decoded));