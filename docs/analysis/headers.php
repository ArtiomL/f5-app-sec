<?php
if ($analyze_header_length=="yes")
{
	if ($overview['maximumHttpHeaderLength'] =="any" )
	{
		$new_suggestion =  array (
			'severity' => $viol_list['maximumHttpHeaderLength']['severity'],
			'section' => $viol_list['maximumHttpHeaderLength']['section'],
			'score' =>$viol_list['maximumHttpHeaderLength']['score'],
			'txt'=>$viol_list['maximumHttpHeaderLength']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}	

}


$header_sig_overrides=0;
$header_sig_disabled=0;
foreach ($headers as $var)
{
	if ($var['num_of_sign_overides'] >0 )
	{
		$header_sig_overrides++;
	}
	if ($var['checkSignatures'] =="No" && $var['name'] != "cookie"  )
	{
		$header_sig_disabled++;
	}
}


if($header_sig_overrides>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['header_sig_overrides']['severity'],
		'section' => $viol_list['header_sig_overrides']['section'],
		'score' =>$viol_list['header_sig_overrides']['score'],
		'txt'=>$header_sig_overrides. ' ' . $viol_list['header_sig_overrides']['txt']
	);
	array_push($suggestions, $new_suggestion);
}
if($header_sig_disabled>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['header_sig_disabled']['severity'],
		'section' => $viol_list['header_sig_disabled']['section'],
		'score' =>$viol_list['header_sig_disabled']['score'],
		'txt'=>$header_sig_disabled. ' ' . $viol_list['header_sig_disabled']['txt']
	);
	array_push($suggestions, $new_suggestion);
}	


?>