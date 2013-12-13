<?php 

function prepareQuery($endpoint,$query)
{
 $searchUrl = $endpoint.'?'.'query='.urlencode($query) ; // .'&output='.$format;
 return $searchUrl;
}
 
function request($url,$format){
   if (!function_exists('curl_init')){ 
      die('CURL is not installed!');
   }
   $curl_handle= curl_init();
   curl_setopt($curl_handle, CURLOPT_URL, $url);
   curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
   $header = array("Accept: " . $format);
   curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);
   if (!$response = curl_exec($curl_handle)) {
    echo("Error " . curl_error($curl_handle)) ; 
   } ;
   curl_close($curl_handle);
   return $response;
}

 
$query = file_get_contents("countries.sparql");
// $endpoint = "http://localhost:3030/data/query";
$endpoint = "http://data.webfoundation.org/sparql" ;

$requestURI = prepareQuery($endpoint,$query);

$response = request($requestURI,"application/sparql-results+json"); 
$jsonResult = json_decode($response,true);
if ($jsonResult == null) {
 echo "Cannot decode response into json." ;
 echo " Response: " + $response;
}

$result = array();
foreach($jsonResult["results"]["bindings"] as $r) {
 $result[] = array("uri" => $r["country"]["value"],
                   "name" => $r["countryName"]["value"],
				   "score" => (double) $r["score"]["value"],
				   "code" => $r["code3"]["value"]);
}
header("Content-type:application/json");
echo(json_encode($result));
?>