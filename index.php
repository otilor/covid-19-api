<?php


function covid19ImpactEstimator($data)
{
  $reportedCases = $data["reportedCases"];
  // Currently Infected for both cases
  $currentlyInfected = currentlyInfected($reportedCases, 10);
  $currentlyInfectedWorstCase = currentlyInfected($reportedCases, 50);

  $timeToElapse = $data["timeToElapse"];
  $totalBeds = $data["totalHospitalBeds"];
  $periodType = $data["periodType"];

  $avgDailyIncomePopulation = $data["region"]["avgDailyIncomePopulation"];
  $avgDailyIncomeInUSD = $data["region"]["avgDailyIncomeInUSD"];
  

      $severeCasesByRequestedTime = intval(severeCasesByRequestedTime($currentlyInfected, $timeToElapse, $periodType));
      $severeCasesByRequestedTimeWorstCase = intval(severeCasesByRequestedTime($currentlyInfectedWorstCase, $timeToElapse, $periodType));
  
      $availableBeds = 0.35 * $totalBeds;
      $hospitalBedsByRequestedTime = intval($availableBeds - $severeCasesByRequestedTime);
      $hospitalBedsByRequestedTimeWorstCase = intval($availableBeds - $severeCasesByRequestedTimeWorstCase);


      $infectionsByRequestedTime = intval(infectionsByRequestedTime($currentlyInfected, $timeToElapse, $periodType));
      $infectionsByRequestedTimeWorstCase = intval(infectionsByRequestedTime($currentlyInfectedWorstCase, $timeToElapse, $periodType));


      $casesForICUByRequestedTime = intval(0.05 * $infectionsByRequestedTime);
      $casesForICUByRequestedTimeWorstCase = intval(0.05 * $infectionsByRequestedTimeWorstCase);


      $casesForVentilatorsByRequestedTime = intval(0.02 * $infectionsByRequestedTime);;
      $casesForVentilatorsByRequestedTimeWorstCase = intval(0.02 * $infectionsByRequestedTimeWorstCase);


      $dollarsInFlight = intval(dollarsInFlight($infectionsByRequestedTime, $avgDailyIncomePopulation, $avgDailyIncomeInUSD, $periodType, $timeToElapse));
      $dollarsInFlightWorstCase = intval(dollarsInFlight($infectionsByRequestedTimeWorstCase, $avgDailyIncomePopulation, $avgDailyIncomeInUSD, $periodType, $timeToElapse));

  $data = [
    "data" => $data,
    "impact" => [
      "currentlyInfected" => $currentlyInfected,
      "infectionsByRequestedTime" => $infectionsByRequestedTime,
      "severeCasesByRequestedTime" => $severeCasesByRequestedTime,
      "hospitalBedsByRequestedTime" => $hospitalBedsByRequestedTime,
      "casesForICUByRequestedTime" => $casesForICUByRequestedTime,
      "casesForVentilatorsByRequestedTime" => $casesForVentilatorsByRequestedTime,
      "dollarsInFlight" => $dollarsInFlight
      
    ],
    "severeImpact" => [
      "currentlyInfected" => $currentlyInfectedWorstCase,
      "infectionsByRequestedTime" => $infectionsByRequestedTimeWorstCase,
      "severeCasesByRequestedTime" => $severeCasesByRequestedTimeWorstCase,
      "hospitalBedsByRequestedTime" => $hospitalBedsByRequestedTimeWorstCase,
      "casesForICUByRequestedTime" => $casesForICUByRequestedTimeWorstCase,
      "casesForVentilatorsByRequestedTime" => $casesForVentilatorsByRequestedTimeWorstCase,
      "dollarsInFlight" => $dollarsInFlightWorstCase
      
    ]
  ];

  return $data;
  
}

// challenge - 1
// currentlyInfected function

function currentlyInfected($reportedCases, $multiplier)
{
  $currentlyInfected = $reportedCases * $multiplier;
  return $currentlyInfected; // Number of currentlyInfected
}


// normalize date function
function normalizeDate($periodType, $timeToElapse)
{
  switch(strtolower($periodType))
  
  {
    case 'days':
      return $timeToElapse;
      break;
    case 'weeks':
      return $timeToElapse * 7;
      break;
    case 'months':
      return $timeToElapse * 30;
      break;
  }
}

// infectionsByRequestedTime function
function infectionsByRequestedTime($currentlyInfected, $timeToElapse, $periodType)
{
  $factor = intval(normalizeDate($periodType, $timeToElapse) / 3);
  return $currentlyInfected * pow(2, $factor);
}
function severeCasesByRequestedTime($currentlyInfected, $timeToElapse, $periodType)
{
  $severeCasesByRequestedTime = 0.15 * intval(infectionsByRequestedTime($currentlyInfected, $timeToElapse, $periodType));
  return $severeCasesByRequestedTime;
}

function dollarsInFlight($infectionsByRequestedTime, $avgDailyIncomePopulation, $avgDailyIncomeInUSD, $periodType, $timeToElapse)
{
  $dollarsInFlight = ($infectionsByRequestedTime * $avgDailyIncomePopulation * $avgDailyIncomeInUSD) / normalizeDate($periodType, $timeToElapse);
  return $dollarsInFlight;
}

$content = trim(file_get_contents("php://input"));

$decoded = json_decode($content, true);
header('Content-Type: application/json');
echo covid19ImpactEstimator($decoded);