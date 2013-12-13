<?php 
 
function prepareQuery($query,$format)
{
 $searchUrl = 'http://localhost:3030/data/query?' .'query='.urlencode($query) ; // .'&output='.$format;
 return $searchUrl;
}
 
function request($url){
   // is curl installed?
   if (!function_exists('curl_init')){ 
      die('CURL is not installed!');
   }
   $curl_handle= curl_init();
   curl_setopt($curl_handle, CURLOPT_URL, $url);
   curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
   $response = curl_exec($curl_handle);
   curl_close($curl_handle);
   return $response;
}
 
$query = <<<EndQuery
PREFIX cex: <http://purl.org/weso/ontology/computex#> 
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
PREFIX wi-index: <http://data.webfoundation.org/webindex/v2013/index/> 
PREFIX wf-onto: <http://data.webfoundation.org/ontology/> 

SELECT * WHERE {
 ?country a wf-onto:Country ;
    rdfs:label ?countryName ;
	wf-onto:has-iso-alpha2-code ?isoCode2 ;
	wf-onto:has-iso-alpha3-code ?isoCode3 ;
	.
 ?obs wf-onto:ref-area ?country ;
      wf-onto:ref-year 2013 ;
	  cex:indicator wi-index:index ;
	  cex:computation [ a cex:Score ] ;
      cex:value ?score .
}
ORDER BY DESC(?score) 
EndQuery;
 
$requestURI = prepareQuery($query,"JSON");

$response = request($requestURI); 

$json = json_decode($response,true);

?>
 
<html>
 
<head>
<title>SPARQL query</title>
</head>
 
<body>
<h3>List of countries</h3>
<table>
<tr><th>Country</th><th>Code 2</th><th>Code 3</th><th>Score</th></tr>
<?php foreach($json["results"]["bindings"] as $r) {
  echo "<tr>";
  echo("<td><a href=\"". $r["country"]["value"] ."\">" . $r["countryName"]["value"] . "</a></td>"); 
  echo("<td>" . $r["isoCode2"]["value"] . "</td>" ); 
  echo("<td>" . $r["isoCode3"]["value"] . "</td>" ); 
  echo("<td>" . $r["score"]["value"] . "</td>" ); 
  echo "</tr>";
}
?>
</table>
 
</body>
</html>