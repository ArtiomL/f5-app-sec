<?php

if ($overview['inspectHttpUploads'] == "No" && $analyze_icap == "yes")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['icap']['severity'],
	  'section' => $viol_list['icap']['section'],
	  'score' =>$viol_list['icap']['score'],
	  'txt'=>$viol_list['icap']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($icap['hostname'] === "" && $analyze_icap == "yes")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['icap_server']['severity'],
	  'section' => $viol_list['icap_server']['section'],
	  'score' =>$viol_list['icap_server']['score'],
	  'txt'=>$viol_list['icap_server']['txt']
    );
	array_push($suggestions, $new_suggestion );
}


?>