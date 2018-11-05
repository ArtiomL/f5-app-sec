<?php

if ($policy_builder['learningMode'] != "manual")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['learningMode']['severity'],
	  'section' => $viol_list['learningMode']['section'],
	  'score' =>$viol_list['learningMode']['score'],
	  'txt'=>$viol_list['learningMode']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($policy_builder['learnExplicitFiletypes'] != "always")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['learnExplicitFiletypes']['severity'],
	  'section' => $viol_list['learnExplicitFiletypes']['section'],
	  'score' =>$viol_list['learnExplicitFiletypes']['score'],
	  'txt'=>$viol_list['learnExplicitFiletypes']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($policy_builder['learnExplicitUrls'] != "selective")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['learnExplicitUrls']['severity'],
	  'section' => $viol_list['learnExplicitUrls']['section'],
	  'score' =>$viol_list['learnExplicitUrls']['score'],
	  'txt'=>$viol_list['learnExplicitUrls']['txt']
    );
	array_push($suggestions, $new_suggestion );
}


if ($policy_builder['parameterLearningLevel'] != "global")
{
	$new_suggestion =  array (
 	  'severity' => $viol_list['parameterLearningLevel']['severity'],
	  'section' => $viol_list['parameterLearningLevel']['section'],
	  'score' =>$viol_list['parameterLearningLevel']['score'],
	  'txt'=>$viol_list['parameterLearningLevel']['txt']
    );
	array_push($suggestions, $new_suggestion );
}


if ($policy_builder['learnExplicitCookies'] != "selective")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['learnExplicitCookies']['severity'],
	  'section' => $viol_list['learnExplicitCookies']['section'],
	  'score' =>$viol_list['learnExplicitCookies']['score'],
	  'txt'=>$viol_list['learnExplicitCookies']['txt']
    );
	array_push($suggestions, $new_suggestion );
}


if ($policy_builder['learnExplicitParameters'] != "selective")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['learnExplicitParameters']['severity'],
	  'section' => $viol_list['learnExplicitParameters']['section'],
	  'score' =>$viol_list['learnExplicitParameters']['score'],
	  'txt'=>$viol_list['learnExplicitParameters']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($policy_builder['parametersIntegerValue'] != "No")
{
	$new_suggestion =  array (
	  'severity' => $viol_list['parametersIntegerValue']['severity'],
	  'section' => $viol_list['parametersIntegerValue']['section'],
	  'score' =>$viol_list['parametersIntegerValue']['score'],
	  'txt'=>$viol_list['parametersIntegerValue']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($policy_builder['learnExplicitRedirectionDomains'] != "always")
{
	$new_suggestion =  array (
		'severity' => $viol_list['learnExplicitRedirectionDomains']['severity'],
		'section' => $viol_list['learnExplicitRedirectionDomains']['section'],
		'score' =>$viol_list['learnExplicitRedirectionDomains']['score'],
		'txt'=>$viol_list['learnExplicitRedirectionDomains']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($policy_builder['trusted_loosen_source'] <100)
{
	$new_suggestion =  array (
		'severity' => $viol_list['trusted_loosen_source']['severity'],
		'section' => $viol_list['trusted_loosen_source']['section'],
		'score' =>$viol_list['trusted_loosen_source']['score'],
		'txt'=>$viol_list['trusted_loosen_source']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($policy_builder['untrusted_loosen_hours'] !=1 )
{
	$new_suggestion =  array (
		'severity' => $viol_list['untrusted_loosen_hours']['severity'],
		'section' => $viol_list['untrusted_loosen_hours']['section'],
		'score' =>$viol_list['untrusted_loosen_hours']['score'],
		'txt'=>$viol_list['untrusted_loosen_hours']['txt']
    );
	array_push($suggestions, $new_suggestion );
}

if ($policy_builder['trusted_loosen_hours'] !=0 )
{
	$new_suggestion =  array (
		'severity' => $viol_list['trusted_loosen_hours']['severity'],
		'section' => $viol_list['trusted_loosen_hours']['section'],
		'score' =>$viol_list['trusted_loosen_hours']['score'],
		'txt'=>$viol_list['trusted_loosen_hours']['txt']
    );
	array_push($suggestions, $new_suggestion );
}


?>