<?php

	if (isset($_GET["policy"])) 
	{	
		$policy_name = 'create_summary.py "'.$_GET["policy"] .'"';
		$command = escapeshellcmd($policy_name);
		$output = shell_exec($command);
	//		sleep(8);
		if(!(strpos($output, 'ok') !== false))
		{

			header('HTTP/1.1 501 Script Error');
		}
		else
		{ 
			//header("Location: reports/summary.docx");
			echo ("ok");
		}
	}
	else
	{  
		header('HTTP/1.1 500 No report set');
	}
?>