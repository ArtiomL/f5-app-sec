<?php

if ($analyze_domains=="yes")
{
	if ($overview['redirectionProtectionEnabled'] != "Yes")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['redirectionProtectionEnabled']['severity'],
			'section' => $viol_list['redirectionProtectionEnabled']['section'],
			'score' =>$viol_list['redirectionProtectionEnabled']['score'],
			'txt'=>$viol_list['redirectionProtectionEnabled']['txt']
		);
		array_push($suggestions, $new_suggestion );
	}

	foreach ($domains as $var)
	{
		if ($var['domainName'] == "*")
		{
			$new_suggestion =  array (
				'severity' => $viol_list['wildcard_domain']['severity'],
				'section' => $viol_list['wildcard_domain']['section'],
				'score' =>$viol_list['wildcard_domain']['score'],
				'txt'=>$viol_list['wildcard_domain']['txt']
			);
			array_push($suggestions, $new_suggestion);	
		}
	}
}

?>