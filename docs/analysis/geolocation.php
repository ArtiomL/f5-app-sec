<?php
if ($analyze_geolocation=="yes")
{
	if (count($disallowed_geolocations)==0)
	{
		$new_suggestion =  array (
		  'severity' => $viol_list['geo_count']['severity'],
		  'section' => $viol_list['geo_count']['section'],
		  'score' =>$viol_list['geo_count']['score'],
		  'txt'=>$viol_list['geo_count']['txt']
		);
		array_push($suggestions, $new_suggestion);		
	}
}

?>