<?php

	$trusted_ip_count = 0;
	
	foreach ($whitelist as $var)
	{
		if($var['trustedByPolicyBuilder']=="Yes")
			$trusted_ip_count++;
	}

	if ($trusted_ip_count==0)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['trusted_ips']['severity'],
			'section' => $viol_list['trusted_ips']['section'],
			'score' =>$viol_list['trusted_ips']['score'],
			'txt'=>$viol_list['trusted_ips']['txt']
		);
		array_push($suggestions, $new_suggestion);	
	}

?>