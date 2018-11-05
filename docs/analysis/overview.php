<?php

if ($overview['enforcementMode'] != "blocking")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['enforcementMode']['severity'],
	  'section' => $viol_list['enforcementMode']['section'],
	  'score' =>$viol_list['enforcementMode']['score'],
	  'txt'=>$viol_list['enforcementMode']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($overview['virtualServers'] == "None")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['virtualServers']['severity'],
	  'section' => $viol_list['virtualServers']['section'],
	  'score' =>$viol_list['virtualServers']['score'],
	  'txt'=>$viol_list['virtualServers']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($overview['caseInsensitive'] == "No")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['caseInsensitive']['severity'],
	  'section' => $viol_list['caseInsensitive']['section'],
	  'score' =>$viol_list['caseInsensitive']['score'],
	  'txt'=>$viol_list['caseInsensitive']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($overview['brute_enabled'] == "No" && $analyze_brute == "yes")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['brute_force']['severity'],
	  'section' => $viol_list['brute_force']['section'],
	  'score' =>$viol_list['brute_force']['score'],
	  'txt'=>$viol_list['brute_force']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($overview['brute_enabled'] == "Yes" && $overview['Login_pages_totalItems']==0 && $analyze_brute == "yes")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['brute_force_login']['severity'],
	  'section' => $viol_list['brute_force_login']['section'],
	  'score' =>$viol_list['brute_force_login']['score'],
	  'txt'=>$viol_list['brute_force_login']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

	

?>