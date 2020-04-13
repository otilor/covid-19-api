<?php

include ("../estimator.php");

function array2xml($array, $xml = false){

if($xml === false){
    $xml = new SimpleXMLElement('<result/>');
}

foreach($array as $key => $value){
    if(is_array($value)){
        array2xml($value, $xml->addChild($key));
    } else {
        $xml->addChild($key, $value);
    }
}

return $xml->asXML();
}
//$jSON = covid19ImpactEstimator($decoded);
$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);
//$jSON = covid19ImpactEstimator($raw_data);

//$jSON = json_decode($raw_data, true);
$result = covid19ImpactEstimator($decoded);
$xml = array2xml($result, false);   

//echo '<pre>';
header('Content-Type: text/xml');
print_r($xml);
