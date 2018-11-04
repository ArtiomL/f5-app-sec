<?php

$cookie_staging=0;
$cookie_sig_overrides=0;
$cookie_sig_disabled=0;
$cookie_enforced=0;
foreach ($cookies as $var)
{
	if ($var['performStaging'] == "Yes" && $var['name'] == "*")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['cookie_star_staging']['severity'],
			'section' => $viol_list['cookie_star_staging']['section'],
			'score' =>$viol_list['cookie_star_staging']['score'],
			'txt'=>$viol_list['cookie_star_staging']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
	if ($var['performStaging'] == "Yes" && $var['name'] != "*")
	{
		$cookie_staging++;
	}	
	if ($var['num_of_sign_overides'] >0 && $var['attackSignaturesCheck'] == "Yes")
	{
		$cookie_sig_overrides++;
	}
	if ($var['attackSignaturesCheck'] == "No" && $var['name'] == "*")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['cookie_star_sig_disabled']['severity'],
			'section' => $viol_list['cookie_star_sig_disabled']['section'],
			'score' =>$viol_list['cookie_star_sig_disabled']['score'],
			'txt'=>$viol_list['cookie_star_sig_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
	if ($var['attackSignaturesCheck'] == "No" && $var['name'] != "*" && $var['enforcementType'] == "allow")
	{
		$cookie_sig_disabled++;
	}		

	if ($var['accessibleOnlyThroughTheHttpProtocol'] == "No" && $analyze_cookie_httponly=="yes")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['accessibleOnlyThroughTheHttpProtocol']['severity'],
			'section' => $viol_list['accessibleOnlyThroughTheHttpProtocol']['section'],
			'score' =>$viol_list['accessibleOnlyThroughTheHttpProtocol']['score'],
			'txt'=>$viol_list['accessibleOnlyThroughTheHttpProtocol']['txt'] .' "'. $var['name'].'".'
		);
		array_push($suggestions, $new_suggestion);		
	}
	if ($var['securedOverHttpsConnection'] == "No" && $analyze_cookie_secure=="yes")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['securedOverHttpsConnection']['severity'],
			'section' => $viol_list['securedOverHttpsConnection']['section'],
			'score' =>$viol_list['securedOverHttpsConnection']['score'],
			'txt'=>$viol_list['securedOverHttpsConnection']['txt'] .' "'. $var['name'].'".'
		);
		array_push($suggestions, $new_suggestion);		
	}
	if ($var['insertSameSiteAttribute'] == "none" && $analyze_cookie_same_site=="yes")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['insertSameSiteAttribute']['severity'],
			'section' => $viol_list['insertSameSiteAttribute']['section'],
			'score' =>$viol_list['insertSameSiteAttribute']['score'],
			'txt'=>$viol_list['insertSameSiteAttribute']['txt'] .' "'. $var['name'].'".'
		);
		array_push($suggestions, $new_suggestion);		
	}
	if ($var['enforcementType'] != "allow")
	{
		$cookie_enforced++;
	}
}

if($cookie_sig_overrides>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['cookie_sig_overrides']['severity'],
		'section' => $viol_list['cookie_sig_overrides']['section'],
		'score' =>$viol_list['cookie_sig_overrides']['score'],
		'txt'=>$cookie_sig_overrides. ' ' . $viol_list['cookie_sig_overrides']['txt']
	);
	array_push($suggestions, $new_suggestion);
}
if($cookie_sig_disabled>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['cookie_sig_disabled']['severity'],
		'section' => $viol_list['cookie_sig_disabled']['section'],
		'score' =>$viol_list['cookie_sig_disabled']['score'],
		'txt'=>$cookie_sig_disabled. ' ' . $viol_list['cookie_sig_disabled']['txt']
	);
	array_push($suggestions, $new_suggestion);
}

if($cookie_staging>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['staged_cookies']['severity'],
		'section' => $viol_list['staged_cookies']['section'],
		'score' =>$viol_list['staged_cookies']['score'],
		'txt'=>$cookie_staging. ' ' . $viol_list['staged_cookies']['txt']
	);
	array_push($suggestions, $new_suggestion);
}



if ($analyze_cookie_length=="yes" && $overview['maximumCookieHeaderLength'] =="any" )
{
	$new_suggestion =  array (
		'severity' => $viol_list['maximumCookieHeaderLength']['severity'],
		'section' => $viol_list['maximumCookieHeaderLength']['section'],
		'score' =>$viol_list['maximumCookieHeaderLength']['score'],
		'txt'=>$viol_list['maximumCookieHeaderLength']['txt']
	);
	array_push($suggestions, $new_suggestion);
}	

if ($analyze_cookie_enforcement=="yes" && $cookie_enforced==0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['enforced_cookies']['severity'],
		'section' => $viol_list['enforced_cookies']['section'],
		'score' =>$viol_list['enforced_cookies']['score'],
		'txt'=>$viol_list['enforced_cookies']['txt']
	);
	array_push($suggestions, $new_suggestion);
}


?>