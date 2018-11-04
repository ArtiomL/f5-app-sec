<?php 

$asm = [];
$ltm = []; 
$ltm_go = 0;
$asm_go = 0;
$dir = getcwd() . '/config_files/';
$scan = scandir($dir);

foreach($scan as $file)
{
    if (is_dir($dir.$file) and !($file=="." || $file==".."))
    {
		array_push ($asm, $file);
    }
	if (!is_dir($dir.$file) and !($file=="." || $file==".."))
    {
		array_push ($ltm, $file);
    }
}

$policies_count = sizeof($asm);
if ($policies_count >0 )
{
	$asm_go = 1;
}
else
{
	Header("Location: index.php");
	exit();
}

if (in_array("device_details.txt", $ltm) and in_array("monitor.txt", $ltm) and in_array("partitions.txt", $ltm) and in_array("pools.txt", $ltm) and in_array("profile.txt", $ltm) and in_array("provisioned_modules.txt", $ltm) and in_array("route_domain.txt", $ltm) and in_array("routes.txt", $ltm) and  in_array("vlans.txt", $ltm) and in_array("virtual_servers.txt", $ltm) and in_array("trunk.txt", $ltm) and in_array("ssl_cert.txt", $ltm)) {
   $ltm_go = 1;
}

if(!isset($_GET["policy"]))
{
	Header("Location: index.php");
	exit();
}	


$config_files = "config_files/" . $_GET["policy"];

$string = file_get_contents($config_files."/blocking_settings.txt");
$blocking_settings = "var blocking_settings = " . $string . " ;";

$string = file_get_contents($config_files."/evasions.txt");
$evasion = "var evasion = " . $string . " ;";

$string = file_get_contents($config_files."/compliance.txt");
$compliance = "var compliance = " . $string . " ;";

$string = file_get_contents($config_files."/methods.txt");
$methods = "var methods = " . $string . " ;";

$ipi = file_get_contents($config_files."/ipi.txt");

$string = file_get_contents($config_files."/ipi_categories.txt");
$ipi_categories = "var ipi_categories = " . $string . " ;";

$string = file_get_contents($config_files."/signature_sets.txt");
$signature_sets = "var signature_sets = " . $string . " ;";

$string = file_get_contents($config_files."/urls.txt");
$url = "var url = " . $string . " ;";

$string = file_get_contents($config_files."/file_types_allowed.txt");
$file_types = "var file_types = " . $string . " ;";

$string = file_get_contents($config_files."/file_types_disallowed.txt");
$file_types_disallowed = "var file_types_disallowed = " . $string . " ;";

$string = file_get_contents($config_files."/parameters.txt");
$parameters = "var parameters = " . $string . " ;";

$string = file_get_contents($config_files."/cookies.txt");
$cookies = "var cookies = " . $string . " ;";

$string = file_get_contents($config_files."/domains.txt");
$domains = "var domains = " . $string . " ;";

$string = file_get_contents($config_files."/headers.txt");
$headers = "var headers = " . $string . " ;";

$string = file_get_contents($config_files."/disallowed_geolocations.txt");
$disallowed_geolocations = "var disallowed_geolocations = " . $string . " ;";

$string = file_get_contents($config_files."/sensitive_param.txt");
$sensitive_param = "var sensitive_param = " . $string . " ;";

$string = file_get_contents($config_files."/response_pages.txt");
$response_pages = "var response_pages = " . $string . " ;";

$string = file_get_contents($config_files."/session_tracking.txt");
$session_tracking = json_decode($string, true);

$string = file_get_contents($config_files."/overview.txt");
$overview = json_decode($string, true);

$string = file_get_contents($config_files."/whitelist.txt");
$whitelist = json_decode($string, true);
$ip_exceptions = "var ip_exceptions = " . $string . " ;";

if (sizeof($whitelist)==0)
{
	$whitelist_ips = array("No IPs configured");	
}
else
{
	$whitelist_ips = array();
	foreach ($whitelist as $key) {
		$ip_mask = $key['ipAddress'] . " / " . $key['ipMask'];
		if ($key['trustedByPolicyBuilder']=="Yes")
			array_push($whitelist_ips,$ip_mask);
		
	}
}

$string = file_get_contents($config_files."/policy_builder.txt");
$policy_builder = json_decode($string, true);

$string = file_get_contents($config_files."/signatures_overview.txt");
$signatures_overview = json_decode($string, true);

$string = file_get_contents($config_files."/allowed_responses.txt");
$string = str_replace('[','[{"name":"',$string);
$string = str_replace(',','"}, {"name":"',$string);
$string = str_replace(']','"}]', $string);

$allowed_responses = "var allowed_responses = " . $string . " ;";

$string = file_get_contents($config_files."/results.txt");
$results = json_decode($string, true);

if (file_exists($config_files."/suggestions.txt"))
{
	$suggestions_exist=true;
	$string = file_get_contents($config_files."/suggestions.txt");
	$suggestions = "var suggestions = " . $string . " ;";

	$string = file_get_contents($config_files."/suggestions.txt");
	$suggestions_initial = json_decode($string, true);

	$error = 0;
	$info = 0;
	$warning = 0;
	$score = 0;
	foreach ($suggestions_initial as $key) {
		if ($key['severity'] == 'info')
			$info++;
		if ($key['severity'] == 'warning')
			$warning++;
		if ($key['severity'] == 'error')
			$error++;
		$score = $score + (int)$key['score'];
	}
	
	$final_score = 100 - $score;
	if ($final_score <60)
	{
		$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:red">F</span>';
	}
	if ($final_score >=60 && $final_score <70)
	{
		$bar_class = '<span class="badge " style="font-size:128px; padding:20px 36px; background-color:orange ">D</span>';
	}
	if ($final_score >=70 && $final_score <80)
	{
		$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:gray;">C</span>';
	}
	if ($final_score >=80 && $final_score <90)
	{
		$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:#1D9B1E">B</span>';
	}			
	if ($final_score >=90)
	{
		$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:#30CE31">A</span>';
	}		
		
}
else
{
	$suggestions_exist=false;
	$suggestions = "var suggestions = []";
	$error = 0;
	$info = 0;
	$warning = 0;
	$score = 0;
	$final_score = 100;
	$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:#6a6c6d">-</span>';	
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="images/favicon.ico" type="image/ico" />

    <title>ASM Policy Review </title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.css" rel="stylesheet">
   <!-- Switchery -->
    <link href="vendors/switchery/dist/switchery.min.css" rel="stylesheet">
 
    <!-- Datatables -->
    <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="additional.css" />	

  </head>
  <body class="nav-sm">
    <div class="container body">
      <div class="main_container">

		<div class="col-md-3 left_col">
		  <div class="left_col scroll-view">
			<!-- sidebar menu -->
			<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

			<div class="menu_section active">
				<ul class="nav side-menu" style="">
				  <li><a href="index.php"><img src="images/f5_2.png" height=48px></a>
					
				  </li>
				  
				  <li ><a href="ltm.php"><i class="fa fa-edit"></i> LTM <span class="fa fa-chevron-down"></span></a>
				  </li>
				  <li class="current-page"><a><i class="fa fa-shield"></i> ASM <span class="fa fa-chevron-down"></span></a>
					<ul class="nav child_menu">
					  <?php 
							if($asm_go == 1)
							{
								foreach ($asm as $item)
								{
									echo '<li><a href="asm.php?policy='.$item.'">'.$item.'</a></li>';
								}
							}	
					  ?>
					</ul>
				  </li>
				  <li><a href="report.php"><i class="fa fa-file-text"></i> Report </a>
				  </li>
				  <li><a href="settings.php"><i class="fa fa-cog"></i> Settings</a>
				  </li>
			  </ul>
			  </div>
			</div>
			<!-- /sidebar menu -->

			
		  </div>
		</div>	  
	  
        <!-- page content -->
        <div class="right_col" role="main">
          <!-- top tiles -->
		<div class="row">
		<div class="x_title" style="font-size:24px">ASM Policy: <b><?php echo $_GET['policy']; ?></b></div>
		</div>
		<div class="row">
            <div class="col-md-3 col-sm-3 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Overview</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
                    </li>
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">

					<div class="row tile_count" style="margin-bottom: 20px; text-align:center" >
						<div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
						  <span class="count_top red"><i class="fa fa-times-circle"></i> <b>Error </b></span>
						  <div id="var_error" class="count red"><?php echo $error; ?></div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
						  <span class="count_top orange" ><i class="fa fa-warning"></i> <b>Warning </b></span>
						  <div id="var_warning" class="count orange"><?php echo $warning; ?></div>
						</div>
						
						<div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
						  <span class="count_top text-info"><i class="fa fa-info"></i> <b>Info </b></span>
						  <div id="var_info" class="count text-info"><?php echo $info; ?></div>
						</div>
					</div>

					<div class="row tile_count" style="margin-bottom: 20px; text-align:center" >
						<div class="col-md-4 col-sm-4 col-xs-4" style="padding-top: 62px; font-size: 24px">
							<span class="current_score hidden"><?php echo $final_score; ?></span>Score: 
						</div>
						<div class="col-md-4 col-sm-4 col-xs-4 ">
							<span class="score"> <?php echo $bar_class; ?> </span>
						</div>
						
						<div class="col-md-4 col-sm-4 col-xs-4 ">

						</div>
					</div>
				  
                </div>
              </div>
            </div>
		

		     <div class="col-md-9 col-sm-9 col-xs-12 <?php if ($suggestions_exist) echo "hidden"?>" id="analyze_tab">
                <div class="x_panel">
                  <span style="font-size:18px">The Policy has not been analyzed yet. <br>  <br> </span><span style="font-size:14px">Please select if you want to analyze it: &nbsp;&nbsp;</span><button type="button" id="analyze_auto" data-toggle="modal" data-target="#Modal_analyze" class="btn btn-success">Automatic</button> or &nbsp; <button type="button" id="analyze_manual"  class="btn btn-primary">Manual</button>
                </div>
              </div>

			  
            <div class="col-md-9 col-sm-9 col-xs-12 <?php if (!$suggestions_exist) echo "hidden"?>" id="suggestion_tab">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Suggestions</h2> 
                    <ul class="nav navbar-right panel_toolbox">
                    <li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
                    </li>
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
					<ul class="nav navbar-right panel_toolbox" style="margin-right:30px"><button type="button" class="btn btn-success btn-sm" id="save" disabled>Save</button></ul>
                    <ul class="nav navbar-right panel_toolbox" style="margin-right:20px"><button type="button" class="btn btn-info btn-sm"  data-toggle="modal" data-target="#MyModal">Add New</button></ul>
                    <ul class="nav navbar-right panel_toolbox" style="margin-right:20px"><button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#Modal_analyze" id="analyze_again">Re-discover</button></ul>
                    <ul class="nav navbar-right panel_toolbox" style="margin-right:20px"><button type="button" class="btn btn-basic btn-sm"  id="download_summary">Download Summary</button></ul>					
					<div class="clearfix"></div>
                  </div>
                  <div class="x_content">

					<table id="suggestions" class="table table-striped table-bordered" style="width:100%">
						<thead>
						  <tr>
							<th style="width: 10px; text-align: center;"></th>
							<th>Suggestions</th>
							<th style="width: 15%; text-align: center;">Severity</th>
							<th style="width: 15%; text-align: center;">Category</th>
							<th style="width: 15px; text-align: center;"></th>
						  </tr>
						</thead>
					 </table>
						<!-- end content  -->

                  </div>
                </div>
              </div>
   
          </div>

		 
	  <div class="row">
		  <div class="x_panel tile">
			<div class="x_title">
			  <h2>Configuration Review</h2>
			  <ul class="nav navbar-right panel_toolbox">
				<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
				</li>
				<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
				</li>
				<li><a class="close-link"><i class="fa fa-close"></i></a>
				</li>
			  </ul>
			  <div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
						<li role="presentation" class="active"><a href="#tab_config" role="tab" data-toggle="tab" aria-expanded="true">Overview</a>
						</li>
						<li role="presentation" class=""><a href="#tab_blocking" role="tab" data-toggle="tab" aria-expanded="true">General</a>
						</li>
						<li role="presentation" class=""><a href="#tab_signatures" role="tab" data-toggle="tab" aria-expanded="false">Signatures</a>
						</li>
						<li role="presentation" class=""><a href="#tab_compliance" role="tab" data-toggle="tab" aria-expanded="false">Compliance/Evasion/IPI</a>
						</li>
						<li role="presentation" class=""><a href="#tab_file_type" role="tab" data-toggle="tab" aria-expanded="false">File Types</a>
						</li>
						<li role="presentation" class=""><a href="#tab_urls" role="tab" data-toggle="tab" aria-expanded="false">URLs</a>
						</li>
						<li role="presentation" class=""><a href="#tab_parameters" role="tab" data-toggle="tab" aria-expanded="false">Parameters</a>
						</li>
						<li role="presentation" class=""><a href="#tab_headers" role="tab" data-toggle="tab" aria-expanded="false">Cookies/Headers</a>
						</li>
						<li role="presentation" class=""><a href="#tab_methods" role="tab" data-toggle="tab" aria-expanded="false">Methods/Response/Domains</a>
						</li>
						<li role="presentation" class=""><a href="#tab_other" role="tab" data-toggle="tab" aria-expanded="false">Other</a>
						</li>
					</ul>
						<div id="myTabContent" class="tab-content">
						
							<div role="tabpanel" class="tab-pane fade active in" id="tab_config" aria-labelledby="home-tab">
						
								<div class="col-md-6 col-sm-6 col-xs-12">						 
									<div class="x_panel">
									  <div class="x_title">
										<h2>Overview </h2>
										<div class="clearfix"></div>
									  </div>
									  <div class="x_content">
										 <table id="overview" class="table table-striped table-bordered">
											<thead>
											  <tr>
												<th>Entity Type</th>
												<th>Total</th>
												<th>Staging</th>
											  </tr>
											</thead>
											<tbody>
												<tr >
													<td>File Types</td>
													<td><?php echo $results['file_type_total']; ?></td>
													<td><?php echo $results['file_type_not_enforced']; ?></td>
												</tr>
												<tr>
													<td>URLs</td>
													<td><?php echo $results['urls_total']; ?></td>
													<td><?php echo $results['urls_not_enforced']; ?></td>
												</tr>
												<tr>
													<td>Parameters</td>
													<td><?php echo $results['param_total']; ?></td>
													<td><?php echo $results['param_not_enforced']; ?></td>
												</tr>
												<tr>
													<td>Signatures</td>
													<td><?php echo $results['sig_total']; ?></td>
													<td><?php echo $results['sig_not_enforced']; ?></td>
												</tr>
												<tr>
													<td>Cookies</td>
													<td><?php echo $results['cookies_total']; ?></td>
													<td><?php echo $results['cookies_not_enforced']; ?></td>
												</tr>
												<tr>
													<td>HTTP Compliance</td>
													<td><?php echo $results['compliance_total']; ?></td>
													<td><?php echo $results['compliance_not_enforced']; ?></td>
												</tr>
												<tr>
													<td>Evasion</td>
													<td><?php echo $results['evasion_total']; ?></td>
													<td><?php echo $results['evasion_not_enforced']; ?></td>
												</tr>
											</tbody>
									
										 </table>

									  </div>
									</div>
								</div>							

								<div class="col-md-6 col-sm-6 col-xs-12">						 
									<div class="x_panel">
									  <div class="x_title">
										<h2>Blocking Settings</h2>
										<div class="clearfix"></div>
									  </div>
									  <div class="x_content">
										 <table id="blocking" class="table table-striped table-bordered" style="width:100%">
											<thead>
											  <tr>
												<th>Blocking Settings</th>
												<th style="width: 45px; text-align: center;">Learn</th>
												<th style="width: 45px; text-align: center;">Alarm</th>
												<th style="width: 45px; text-align: center;">Block</th>
											
											  </tr>
											</thead>
										 </table>
									  </div>
									</div>
								</div>
								
						</div>
							<div role="tabpanel" class="tab-pane fade " id="tab_blocking" aria-labelledby="home-tab">
							 <!-- start content -->

							 <div class="col-md-6 col-sm-6 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>General Settings</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
									<div class="col-md-6 col-sm-6 col-xs-12">
										 <table class="table" style="width:100%">
											<thead>
											  <tr> 
													<td>Enforcement: </td>
													<td><?php echo $overview['enforcementMode']; ?></td>
											  </tr>
											  <tr> 
													<td>Applied to vServers: </td>
													<td><?php echo  implode("<br>",$overview['virtualServers']); ?></td>
											  </tr>
											  <tr> 
													<td>IP Intelligence: </td>
													<td><?php if ($ipi == '"Yes"') echo "Enabled"; else echo "Disabled"; ?></td>
											  </tr>
											  <tr> 
													<td>Brute force: </td>
													<td><?php echo $overview['default_brute_enabled'] . " / (" .  $overview['Login_pages_totalItems']. " Login Pages)"; ?></td>
											  </tr>
											  <tr> 
													<td>DataGuard: </td>
													<td><?php echo $overview['data_guard_enabled']; ?></td>
											  </tr>
											  <tr> 
													<td>Antivirus: </td>
													<td><?php echo $overview['inspectHttpUploads']; ?></td>
											  </tr>												  
											  <tr> 
													<td>Redirection Domains: </td>
													<td><?php echo $overview['redirectionProtectionEnabled']; ?></td>
											  </tr>											  
											  <tr> 
													<td>Max Cookie Length: </td>
													<td><?php echo $overview['maximumCookieHeaderLength']; ?></td>
											  </tr>	
											  <tr> 
													<td>Max Header Length: </td>
													<td><?php echo $overview['maximumHttpHeaderLength']; ?></td>
											  </tr>	
											  <tr> 
													<td>ASM iRule Events: </td>
													<td><?php echo $overview['triggerAsmIruleEvent']; ?></td>
											  </tr>
											  </thead>
										 </table>
										 <br>
										 <br>
										 
									 </div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										 <table class="table" style="width:100%">
											<thead>
												<tr> 
													<td>Partition: </td>
													<td><?php echo $overview['partition']; ?></td>
											  </tr>
											  <tr> 
													<td>Created By: </td>
													<td><?php echo $overview['creatorName']; ?></td>
											  </tr>
											  <tr> 
													<td>Created Date: </td>
													<td><?php echo $overview['createdDatetime']; ?></td>
											  </tr>
											  <tr> 
													<td>Application Language: </td>
													<td><?php echo $overview['applicationLanguage']; ?></td>
											  </tr>
											  <tr> 
													<td>Last Updated: </td>
													<td><?php echo $overview['lastUpdateMicros']; ?></td>
											  </tr>
											  <tr> 
													<td>Case sensitive: </td>
													<td><?php echo $overview['caseInsensitive']; ?></td>
											  </tr>
											  <tr> 
													<td>Mask Credit Card: </td>
													<td><?php echo $overview['maskCreditCardNumbersInRequest']; ?></td>
											  </tr>
											  <tr> 
													<td>Trust XFF: </td>
													<td><?php echo $overview['trustXff']; ?></td>
											  </tr>											  
											  <tr> 
													<td>Custom XFF: </td>
													<td><?php echo  implode("<br>",$overview['customXffHeaders']); ?></td>
											  </tr>
											</thead>
										 </table>
									 </div>


								  </div>
								</div>

							</div>
							  
							 
							 <div class="col-md-6 col-sm-6 col-xs-12">
							 
								<div class="x_panel">
								  <div class="x_title">
									<h2>Learning Settings</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
									<div class="col-md-6 col-sm-6 col-xs-12">
									
										 <table class="table" style="width:100%">
											<thead>
												<tr > 
													<td>Learning Mode:</td>
													<td><?php echo $policy_builder['learningMode']; ?></td>
											  </tr>
											  <tr> 
													<td>Trust All IPs: </td>
													<td><?php echo $policy_builder['trustAllIps']; ?></td>
											  </tr>		
											  <tr> 
													<td>Trusted sources for learning: </td>
													<td><?php echo $policy_builder['trusted_loosen_source']; ?></td>
											  </tr>
											  <tr> 
													<td>Trusted hours for learning: </td>
													<td><?php echo $policy_builder['trusted_loosen_hours']; ?></td>
											  </tr>
											  <tr> 
													<td>Untrusted sources for learning: </td>
													<td><?php echo $policy_builder['untrusted_loosen_source']; ?></td>
											  </tr>
											  <tr> 
													<td>Untrusted hours for learning: </td>
													<td><?php echo $policy_builder['untrusted_loosen_hours']; ?></td>
											  </tr>											  
											  <tr> 
													<td>Full Inspection: </td>
													<td><?php echo $policy_builder['enableFullPolicyInspection']; ?></td>
											  </tr>
											  <tr> 
													<td>Learn Inactive Entities: </td>
													<td><?php echo $policy_builder['learnInactiveEntities']; ?></td>
											  </tr>
											  <tr> 
													<td>Trusted IPs configured: </td>
													<td><?php echo  implode("<br>",$whitelist_ips); ?></td>
											  </tr>
											  </thead>
										 </table>
										 <br>
										 <br>
										 
									 </div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										 <table class="table" style="width:100%">
											<thead>
												<tr> 
													<td>Learn New File Types: </td>
													<td><?php echo $policy_builder['learnExplicitFiletypes']; ?></td>
											  </tr>
											  <tr> 
													<td>Max Learned File Types: </td>
													<td><?php echo $policy_builder['maximumFileTypes']; ?></td>
											  </tr>
											  <tr> 
													<td>Learn New URLs: </td>
													<td><?php echo $policy_builder['learnExplicitUrls']; ?></td>
											  </tr>
											  <tr> 
													<td>Max Learned URLs: </td>
													<td><?php echo $policy_builder['maximumUrls']; ?></td>
											  </tr>
											  <tr> 
													<td>Learn New Parameters: </td>
													<td><?php echo $policy_builder['learnExplicitParameters']; ?></td>
											  </tr>
											  <tr> 
													<td>Max Learned Parameters: </td>
													<td><?php echo $policy_builder['maximumParameters']; ?></td>
											  </tr>
											  <tr> 
													<td>Parameter Learning Level: </td>
													<td><?php echo $policy_builder['parameterLearningLevel']; ?></td>
											  </tr>
											  <tr> 
													<td>Learn Integer Values: </td>
													<td><?php echo $policy_builder['parametersIntegerValue']; ?></td>
											  </tr>
											  <tr> 
													<td>Classify Value Content: </td>
													<td><?php echo $policy_builder['classifyParameters']; ?></td>
											  </tr>
											  <tr> 
													<td>Learn New Cookies: </td>
													<td><?php echo $policy_builder['learnExplicitCookies']; ?></td>
											  </tr>
											  <tr> 
													<td>Max Learned Cookies: </td>
													<td><?php echo $policy_builder['maximumCookies']; ?></td>
											  </tr>
											  <tr> 
													<td>Learn Redirection Domains: </td>
													<td><?php echo $policy_builder['learnExplicitRedirectionDomains']; ?></td>
											  </tr>											  
											  </thead>
										 </table>
									 </div>

								  </div>
								</div>



								
						  </div>

							<div class="col-md-3 col-sm-3 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Sensitive Parameters</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
									 <table id="sensitive_param" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>Parameter Name</th>
											</tr>
										</thead>
									</table>
								  </div>
						 		</div>
							</div>						  
						  
						  
							<div class="col-md-9 col-sm-9 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>IP Address Exceptions</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
									 <table id="ip_exceptions" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>IP</th>
												<th>Mask</th>
												<th style="text-align:center;">Desc <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Details of the IP address"></i></th>
												<th style="width:55px; text-align:center;">Block <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title=" "></i></th>
												<th style="width:65px; text-align:center;">Anomalies <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Ignore Anomalies for this IP address"></i></th>
												<th style="width:50px; text-align:center;">Learn <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Never learn from this IP address"></i></th>
												<th style="width:50px; text-align:center;">Log <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Never Log for this IP address"></i></th>
												<th style="width:50px; text-align:center;">IPI <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Ignore IP Intelligence for this IP address"></i></th>
												<th style="width:55px; text-align:center;">Trusted <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Trusted by the Policy Builder"></i></th>
											</tr>
										</thead>
									</table>
								  </div>
						 		</div>
							</div>
							 
							<!-- end content -->

						  </div>
							<div role="tabpanel" class="tab-pane fade" id="tab_compliance" aria-labelledby="profile-tab">
							<!-- start content -->
							
							 <div class="col-md-4 col-sm-4 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>HTTP Compliance </h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									 <table id="compliance" class="table table-striped table-bordered" style="width:100%">
									  <thead>
										<tr>
											<th>HTTP Protocol Compliance</th>
											<th style="width: 35px; text-align: center;">Learn</th>
											<th style="width: 45px; text-align: center;">Enabled</th>

										</tr>
									  </thead>
									</table>

								  </div>
								</div>
							  </div>
 
							 <div class="col-md-4 col-sm-4 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Evasion</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

							
										<table id="evasion" class="table table-striped table-bordered" style="width:100%">
										  <thead>
											<tr>
												<th>Evasion Technique Name</th>
												<th style="width: 35px; text-align: center;">Learn</th>
												<th style="width: 45px; text-align: center;">Enabled</th>
											</tr>
										  </thead>
										</table>
										  <!-- end content -->
								  </div>
								</div>
							  </div>							
							
							 <div class="col-md-4 col-sm-4 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>IP Intelligence</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									 <table id="ipi" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>IP Intelligence Category</th>
												<th style="width: 40px; text-align: center;">Alarm</th>
												<th style="width: 40px; text-align: center;">Block</th>										
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
							  </div>								

							<!-- end content -->
						  </div>
							<div role="tabpanel" class="tab-pane fade" id="tab_methods" aria-labelledby="profile-tab">
							<!-- start content -->
							
							 <div class="col-md-3 col-sm-3 col-xs-6">
								<div class="x_panel">
								  <div class="x_title">
									<h2>HTTP Response Codes</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									 <table id="response" class="table table-striped table-bordered" style="width:100%">
										<thead>
										  <tr>
											<th>HTTP Response Codes</th>
										  </tr>
										</thead>
									  </table>

								  </div>
								</div>
							  </div>
 
							 <div class="col-md-4 col-sm-4 col-xs-6">
								<div class="x_panel">
								  <div class="x_title">
									<h2>HTTP Methods</h2>
									<ul class="nav navbar-right panel_toolbox">
									<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
									</li>
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									  </li>
									  <li><a class="close-link"><i class="fa fa-close"></i></a>
									  </li>
									</ul>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									 <table id="methods" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>Allowed HTTP Methods</th>									
												<th style="width: 55px; text-align: center;">Act As</th>										
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
							  </div>							
							
														
							 <div class="col-md-5 col-sm-5 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Redirection Domains</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									 <table id="redirection" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>Domain Name</th>
												<th style="width: 60px; text-align: center;">Sub. <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Subdomains included" ></i></th>
												<th style="width: 45px; text-align: center;">Type</th>
												<th style="width: 80px; text-align: center;">Last Update</th>

											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
							  </div>							
						</div>
							<div role="tabpanel" class="tab-pane fade" id="tab_file_type" aria-labelledby="profile-tab">
					
							<div class="col-md-8 col-sm-8 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Allowed File Types</h2>
									<ul class="nav navbar-right panel_toolbox">
									<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
									</li>
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									  </li>
									  <li><a class="close-link"><i class="fa fa-close"></i></a>
									  </li>
									</ul>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									<table id="file_type" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>File Type</th>
												<th style="width:60px; text-align:center;">Staging </th>
												<th style="width:70px; text-align:center;">URI Len <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Allowed URI Length for each File Type"></i></th>
												<th style="width:70px; text-align:center;">Query Len <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Allowed Query String Length for each File Type"></i></th>
												<th style="width:70px; text-align:center;">Post Len <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Allowed Post Data Length for each File Type"></i></th>
												<th style="width:80px; text-align:center;">Request Len <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Allowed Request Length for each File Type"></i></th>
												<th style="width: 90px; text-align:center;">Last Modified <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Last time the entity was modified"></i></th>									
											</tr>
										</thead>
									</table>

								  </div>
								</div>
							  </div>

							<div class="col-md-4 col-sm-4 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Disallowed File Types</h2>
									<ul class="nav navbar-right panel_toolbox">
									<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
									</li>
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									  </li>
									  <li><a class="close-link"><i class="fa fa-close"></i></a>
									  </li>
									</ul>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									<table id="file_types_disallowed" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>File Type</th>
												<th style="width: 90px; text-align:center;">Last Modified <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Last time the entity was modified"></i></th>									
											</tr>
										</thead>
									</table>

								  </div>
								</div>
							  </div>
							  
						  </div>
							<div role="tabpanel" class="tab-pane fade" id="tab_urls" aria-labelledby="profile-tab">
							 <!-- start content -->
							 <table id="urls" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<th style="width:10px;"></th>
										<th style="width:45px; text-align:center;">Protocol</th>
										<th>URL</th>
										<th style="width:45px; text-align:center;">Staging</th>
										<th style="width:75px; text-align:center;">Sig. Status <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="If Attack Signatures have been enabled"></i></th>
										<th style="width:75px; text-align:center;">MC Status <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="If checking on Meta-characters has been enabled"></i></th>
										<th style="width:85px; text-align:center;">Sig. Over <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="How many Attack Signatures have been overriden"></i></th>
										<th style="width:95px; text-align:center;">MC Over <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="How many Meta-characters have been overriden"></i></th>
										<th style="width:135px; text-align:center;">Last Modified <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="What is the last time the entity was modified"></i></th>										
									</tr>
								</thead>
							</table>
							<!-- end content -->
						</div>
							<div role="tabpanel" class="tab-pane fade" id="tab_parameters" aria-labelledby="profile-tab">
							<!-- start content -->
							 <table id="parameters" class="table table-striped table-bordered" style="width:100%">
								<thead>
								  <tr>
									<th style="width:10px;"></th>
									<th>Parameter Name</th>
									<th>Enforcement</th>
									<th style="width:45px; text-align:center;">Type</th>
									<th style="width:65px; text-align:center;">Data</th>
									<th style="width:55px; text-align:center;">Staging</th>
									<th style="width:70px; text-align:center;">Signatures <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="If Attack Signatures have been enabled"></i></th>
									<th style="width:70px; text-align:center;">MetaChr <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="If checking on Meta-characters has been enabled"></i></th>
									<th style="width:65px; text-align:center;">Sig. Over <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="How many Attack Signatures have been overriden"></i></th>
									<th style="width:65px; text-align:center;">MC Over <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="How many Meta-characters have been overriden"></i></th>
									<th style="width:70px; text-align:center;">Sensitive <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Is the parameter conifgured as Sensitive"></i></th>
								  </tr>
								</thead>
							  </table>
							<!-- end content -->
						</div>
							<div role="tabpanel" class="tab-pane fade" id="tab_signatures" aria-labelledby="profile-tab">
							<!-- start content -->

								 <div class="col-md-4 col-sm-4 col-xs-12">
									<div class="x_panel">
									  <div class="x_title">
										<h2>Signatures Overview</h2>
										<div class="clearfix"></div>
									  </div>
									  <div class="x_content">
										 <table class="table" style="width:100%">
											<thead>
												<tr > 
													<td>Total Signatures: </td>
													<td><?php echo $signatures_overview['total']; ?></td>
											  </tr>
											  <tr> 
													<td>Staging Signatures: </td>
													<td><?php echo $signatures_overview['staging']; ?></td>
											  </tr>		
											  <tr> 
													<td>Disabled Signatures: </td>
													<td><?php echo $signatures_overview['enabled']; ?></td>
											  </tr>
											  <tr> 
													<td>Latest Signature update: </td>
													<td><?php echo $signatures_overview['latest_sig_update']; ?></td>
											  </tr>
											  <tr> 
													<td>Signature Staging: </td>
													<td><?php echo $signatures_overview['signatureStaging']; ?></td>
											  </tr>
											  <tr> 
													<td>Place New Signature in Staging: </td>
													<td><?php echo $signatures_overview['placeSignaturesInStaging']; ?></td>
											  </tr>											  
											  </thead>
										 </table>
									  </div>
									</div>
								  </div>	
								  
								 <div class="col-md-8 col-sm-8 col-xs-12">
									<div class="x_panel">
									  <div class="x_title">
										<h2>Signature Sets</h2>
										<div class="clearfix"></div>
									  </div>
									  <div class="x_content">

										<table id="signatures" class="table table-striped table-bordered" style="width:100%">
												<thead>
												<tr>
													<th>Signature Sets</th>
													<th style="width: 45px; text-align: center;">Learn</th>
													<th style="width: 45px; text-align: center;">Alarm</th>
													<th style="width: 45px; text-align: center;">Block</th>
													<th style="width:135px; text-align:center;">Last Modified <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="What is the last time the entity was modified"></i></th>										
									
												</tr>
											</thead>
										</table>
											  <!-- end content -->
									  </div>
									</div>
								  </div>							

							<!-- end content -->
						</div>
							<div role="tabpanel" class="tab-pane fade" id="tab_headers" aria-labelledby="profile-tab">

							 <div class="col-md-6 col-sm-6 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Cookies</h2>
									<ul class="nav navbar-right panel_toolbox">
									<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
									</li>
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									  </li>
									  <li><a class="close-link"><i class="fa fa-close"></i></a>
									  </li>
									</ul>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									 <table id="cookies" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th style="width:45px; text-align:center;">Staging</th>
												<th style="width:80px; text-align: center;">Enforcement <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Whether the cookie is on Enforced or Allowed mode"></i></th>
												<th style="width:75px; text-align:center;">Sig. Status <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="If Attack Signatures have been enabled"></i></th>
												<th style="width:75px; text-align:center;">Sig. Over <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="How many Attack Signatures have been overriden"></i></th>
											</tr>
										</thead>
									</table>


								  </div>
								</div>
							  </div>
 
							 <div class="col-md-6 col-sm-6 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Headers</h2>
									<ul class="nav navbar-right panel_toolbox">
									<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
									</li>
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									  </li>
									  <li><a class="close-link"><i class="fa fa-close"></i></a>
									  </li>
									</ul>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
									 <table id="headers" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th style="width:125px; text-align:center;">Sig. Status <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="If Attack Signatures have been enabled"></i></th>
												<th style="width:125px; text-align:center;">Sig. Over <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="How many Attack Signatures have been overriden"></i></th>
											</tr>
										</thead>
									</table>
								  <!-- end content -->
								  </div>
								</div>
							  </div>							
							
	

					<!-- start content -->

						<!-- end content -->					
					</div>				
							<div role="tabpanel" class="tab-pane fade" id="tab_other" aria-labelledby="profile-tab">
								
								<div class="col-md-4 col-sm-4 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Disallowed Geolocations</h2>
									<ul class="nav navbar-right panel_toolbox">
									<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
									</li>
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									  </li>
									  <li><a class="close-link"><i class="fa fa-close"></i></a>
									  </li>
									</ul>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
									 <table id="geolocation" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>Country Name</th>
											</tr>
										</thead>
									</table>


								  </div>
								</div>
							  </div>
 
								<div class="col-md-4 col-sm-4 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Response Pages</h2>
									<ul class="nav navbar-right panel_toolbox">
									<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
									</li>
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									  </li>
									  <li><a class="close-link"><i class="fa fa-close"></i></a>
									  </li>
									</ul>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
									 <table id="response_pages" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>Page Type</th>
												<th style="width:125px; text-align:center;">Action Type</th>
											</tr>
										</thead>
									</table>
								  <!-- end content -->
								  </div>
								</div>
							  </div>							

								<div class="col-md-4 col-sm-4 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Session Tracking</h2>
									<ul class="nav navbar-right panel_toolbox">
									<li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
									</li>
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									  </li>
									  <li><a class="close-link"><i class="fa fa-close"></i></a>
									  </li>
									</ul>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
									 <table id="response_pages" class="table" style="width:100%">
										<thead>
											<tr > 
												<td>Session Awareness:</td>
												<td><?php if ($session_tracking['enableSessionAwareness'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Session Tracking : </td>
												<td><?php if ($session_tracking['trackViolationsAndPerformActions'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>		
										  <tr> 
												<td>User Name: </td>
												<td><?php echo $session_tracking['userNameSource']; ?></td>

										  </tr>	
										  <tr> 
												<td>Device ID: </td>
												<td><?php if ($session_tracking['enableTrackingSessionHijackingByDeviceId'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Log All - User: </td>
												<td><?php if ($session_tracking['logAll_user'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Log All - IP: </td>
												<td><?php if ($session_tracking['logAll_ip'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Log All - Session: </td>
												<td><?php if ($session_tracking['logAll_user'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>											  
										  <tr> 
												<td>Log All - Device: </td>
												<td><?php if ($session_tracking['logAll_device'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Block All - User: </td>
												<td><?php if ($session_tracking['blockAll_user'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Block All - IP: </td>
												<td><?php if ($session_tracking['blockAll_ip'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Block All - Session: </td>
												<td><?php if ($session_tracking['blockAll_session'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Block All - Device: </td>
												<td><?php if ($session_tracking['blockAll_device'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Delay Blocking - User: </td>
												<td><?php if ($session_tracking['delayBlocking_user'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Block Blocking - IP: </td>
												<td><?php if ($session_tracking['delayBlocking_ip'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Block Blocking - Session: </td>
												<td><?php if ($session_tracking['delayBlocking_session'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>
										  <tr> 
												<td>Block Blocking - Device: </td>
												<td><?php if ($session_tracking['delayBlocking_device'] == "Yes") echo '<i class="fa fa-check-square-o fa-2x green"></i>'; else echo 'Disabled';?></td>
										  </tr>

										  </thead>
									</table>
								  <!-- end content -->
								  </div>
								</div>
							  </div>							
							  
	
							</div>										
					
						</div>
				  </div>
			</div>
		  </div>
        </div>
        <!-- /page content -->

		<div class="modal fade" id="MyModal" tabindex="-1" role="dialog" aria-labelledby="MyModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="MyModalLabel">Add New Suggesion</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				 
				</button>
			  </div>
			  <div class="modal-body">
				
				<form>
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label" style="font-size: 14px;">Suggesion</label>
					<div class="col-sm-10">
					  <input class="form-control" id="input_suggestion">
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label" style="font-size: 14px;">Score</label>
					<div class="col-sm-10">
					  <input class="form-control" id="input_score">
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-2  col-form-label" style="font-size: 14px;">Category</label>
					<div class="col-sm-10">
					  <select class="custom-select custom-select-lg mb-3" size="16" style="font-size: 16px; width:100%; cursor:pointer" id="input_category">
						<option value="Overview" selected>Overview</option>
						<option value="Evasions">Evasions</option>
						<option value="HTTP Compliance">HTTP Compliance</option>
						<option value="File Types">File Types</option>
						<option value="URLs">URLs</option>
						<option value="Parameters">Parameters</option>
						<option value="Signatures">Signatures</option>
						<option value="Policy Builder">Policy Builder</option>
						<option value="Cookies">Cookies</option>
						<option value="Headers">Headers</option>
						<option value="IP Intelligence">IP Intelligence</option>
						<option value="Methods">Methods</option>
						<option value="Geolocation">Geolocation</option>
						<option value="Redirection Domains">Redirection Domains</option>
						<option value="Sensitive Parameters">Sensitive Parameters</option>
						<option value="Response Codes">Response Codes</option>
						</select>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label" style="font-size: 14px;">Severity</label>
					<div class="col-sm-10">
					  <select class="custom-select custom-select-lg mb-3" size="3" style="font-size: 16px; width:100%; cursor:pointer" id="input_severity">
						<option value="error" selected>Error</option>
						<option value="warning">Warning</option>
						<option value="info">Info</option>
						</select>
					</div>
				  </div>
				</form>
							
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="add_row" data-dismiss="modal">Save changes</button>
			  </div>
			</div>
		  </div>
		</div>	  
        <!-- footer content -->
        
		<div class="modal fade" id="Modal_analyze" tabindex="-1" role="dialog" aria-labelledby="Modal_analyzeLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document" style="width:900px">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="Modal_analyzeLabel">Security Controls</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				 
				</button>
			  </div>
			  <div class="modal-body">
				
					<form class="form-horizontal form-label-left" novalidate="" action="audit.php" method="post" enctype="multipart/form-data">

                      <p style="font-size:14px"> Select the ASM security controls that need to be analyzed
                      </p>
                      <div class="item form-group">


                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Attack Signatures                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked disabled="disabled" name="signatures" id="k_signatures"/>
                            </label>
                        </div>


                         <label class="control-label col-md-3 col-sm-3 col-xs-12">Protocol compliance 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked disabled="disabled" name="compliance" id="k_compliance"/>
                            </label>
                        </div>
 
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Evasion techniques 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked disabled="disabled" name="evasion" id="k_evasion"/>
                            </label>
                        </div>  
                        
                      </div>
 
                       <div class="item form-group">

                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Cookie RFC-compliant 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="cookie_compliance" id="k_cookie_compliance"/>
                            </label>
                        </div>

						<label class="control-label col-md-3 col-sm-3 col-xs-12">Malformed JSON/XML 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked disabled="disabled" name="brute_force" id="k_xml"/>
                            </label>
                        </div>
						
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">File Types
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="file_type" id="k_file_type"/>
                            </label>
                        </div> 
  
           
                      </div>

                          <label class="control-label col-md-3 col-sm-3 col-xs-12">File Type Lengths
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="file_length" id="k_file_length"/>
                            </label>
                        </div>
                        
                       <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">IP Intelligence
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="ipi" id="k_ipi"/>
                            </label>
                        </div>
 
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Illegal methods 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="method" id="k_method"/>
                            </label>
                        </div>
 
                               
                      </div>

                       <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Http_only Cookie(s) 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="http_only" id="k_http_only"/>
                            </label>
                        </div>

 
                         <label class="control-label col-md-3 col-sm-3 col-xs-12">HTTP Response Codes
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="response_code" id="k_response_code"/>
                            </label>
                        </div>
 
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Modified cookie(s) 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="domain_cookie" id="k_domain_cookie"/>
                            </label>
                        </div>   
                       </div>


                       <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Secure Cookie(s) 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="secure" id="k_secure"/>
                            </label>
                        </div>

                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Cookie length 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="cookie_length" id="k_cookie_length"/>
                            </label>
                        </div>
 
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">HTTP header length 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="header_length" id="k_header_length"/>
                            </label>
                        </div>
                        
                      </div>

                       <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Same Site Cookie(s) 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="same_site" id="k_same_site"/>
                            </label>
                        </div>

 
 
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Redirection attempts
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="redirection_attempt" id="k_redirection_attempt"/>
                            </label>
                        </div> 
                        
 
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Failed to convert character
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="convert" id="k_convert"/>
                            </label>
                        </div>

                      </div>

                       <div class="item form-group">

                        <label class="control-label col-md-3 col-sm-3 col-xs-12">IP is blacklisted 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="blacklisted" id="k_blacklisted"/>
                            </label>
                        </div>

                        <label class="control-label col-md-3 col-sm-3 col-xs-12">ICAP 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="icap" id="k_icap"/>
                            </label>
                        </div>

                        <label class="control-label col-md-3 col-sm-3 col-xs-12">File Uploads 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="file_uploads" id="k_file_uploads"/>
                            </label>
                        </div>

						
                      </div>

					  
                       <div class="item form-group">

						<label class="control-label col-md-3 col-sm-3 col-xs-12">Modified ASM cookie 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="asm_cookie" id="k_asm_cookie"/>
                            </label>
                        </div>

						<label class="control-label col-md-3 col-sm-3 col-xs-12">Disallowed Countries 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="geolocation" id="k_geolocation"/>
                            </label>
                        </div>
						
						<label class="control-label col-md-3 col-sm-3 col-xs-12">Brute Force 
                        </label>
                        <div class="col-md-1 col-sm-1 col-xs-6">
                            <label style="margin-top:5px">
                              <input type="checkbox" class="js-switch" checked name="brute_force" id="k_brute_force"/>
                            </label>
                        </div>						
						
                      </div>					  
					  
					  
                    </form>						
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success" id="analyze_policy" data-dismiss="modal">Analyze</button>
			  </div>
			</div>
		  </div>
		</div>	  
        <!-- footer content -->
        
        
		</div>
        <!-- /footer content -->
      </div>
    </div>


   <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
     <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>
    <!-- Datatables -->
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!-- Switchery -->
    <script src="vendors/switchery/dist/switchery.min.js"></script>
	
<script type="text/javascript">
function tooltip_init () {
  $(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip({
    placement : 'top'
  });
});
}
</script>


<script>
$( "#download_summary" ).click(function() {
	$("#download_summary").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
	$("#download_summary").attr("disabled","disabled");
	
	$.get("create_summary.php?policy=<?php echo $_GET["policy"]; ?>", function(data, status){
		if (data=="ok")
		{
			$("#download_summary").removeAttr("disabled");
			$("#download_summary").html('Download Summary');
			window.location.href = 'reports/<?php echo $_GET["policy"]; ?>_summary.docx';
		}
		
	});	
});
</script>


<?php echo $_GET['policy']; ?>
<script>

$( "#analyze_manual" ).click(function() {
	$("#analyze_tab").addClass("hidden");
	$("#suggestion_tab" ).removeClass("hidden");
});
</script>

<script>
$( "#analyze_policy" ).click(function() {
	var query_string = "";
	if(!$("#k_asm_cookie").is(':checked'))
	{
		query_string = query_string + '&asm_cookie=disabled'
	}
	else
	{
		query_string = query_string + '&asm_cookie=enabled'
	}
	if(!$("#k_file_uploads").is(':checked'))
	{
		query_string = query_string + '&file_uploads=disabled'
	}
	else
	{
		query_string = query_string + '&file_uploads=enabled'
	}
	if(!$("#k_icap").is(':checked'))
	{
		query_string = query_string + '&icap=disabled'
	}
	else
	{
		query_string = query_string + '&icap=enabled'
	}
	if(!$("#k_blacklisted").is(':checked'))
	{
		query_string = query_string + '&blacklisted=disabled'
	}
	else
	{
		query_string = query_string + '&blacklisted=enabled'
	}
	if(!$("#k_convert").is(':checked'))
	{
		query_string = query_string + '&convert=disabled'
	}
	else
	{
		query_string = query_string + '&convert=enabled'
	}
	if(!$("#k_redirection_attempt").is(':checked'))
	{
		query_string = query_string + '&redirection_attempt=disabled'
	}
	else
	{
		query_string = query_string + '&redirection_attempt=enabled'
	}
	if(!$("#k_same_site").is(':checked'))
	{
		query_string = query_string + '&same_site=disabled'
	}
	else
	{
		query_string = query_string + '&same_site=enabled'
	}
	if(!$("#k_cookie_length").is(':checked'))
	{
		query_string = query_string + '&cookie_length=disabled'
	}
	else
	{
		query_string = query_string + '&cookie_length=enabled'
	}
	if(!$("#k_header_length").is(':checked'))
	{
		query_string = query_string + '&header_length=disabled'
	}	
	else
	{
		query_string = query_string + '&header_length=enabled'
	}
	if(!$("#k_secure").is(':checked'))
	{
		query_string = query_string + '&secure=disabled'
	}
	else
	{
		query_string = query_string + '&secure=enabled'
	}
	if(!$("#k_domain_cookie").is(':checked'))
	{
		query_string = query_string + '&domain_cookie=disabled'
	}
	else
	{
		query_string = query_string + '&domain_cookie=enabled'
	}
	if(!$("#k_response_code").is(':checked'))
	{
		query_string = query_string + '&response_code=disabled'
	}
	else
	{
		query_string = query_string + '&response_code=enabled'
	}
	if(!$("#k_http_only").is(':checked'))
	{
		query_string = query_string + '&http_only=disabled'
	}
	else
	{
		query_string = query_string + '&http_only=enabled'
	}
	if(!$("#k_method").is(':checked'))
	{
		query_string = query_string + '&method=disabled'
	}
	else
	{
		query_string = query_string + '&method=enabled'
	}
	if(!$("#k_ipi").is(':checked'))
	{
		query_string = query_string + '&ipi=disabled'
	}
	else
	{
		query_string = query_string + '&ipi=enabled'
	}
	if(!$("#k_file_length").is(':checked'))
	{
		query_string = query_string + '&file_length=disabled'
	}
	else
	{
		query_string = query_string + '&file_length=enabled'
	}
	if(!$("#k_file_type").is(':checked'))
	{
		query_string = query_string + '&file_type=disabled'
	}
	else
	{
		query_string = query_string + '&file_type=enabled'
	}
	if(!$("#k_brute_force").is(':checked'))
	{
		query_string = query_string + '&brute_force=disabled'
	}
	else
	{
		query_string = query_string + '&brute_force=enabled'
	}
	if(!$("#k_cookie_compliance").is(':checked'))
	{
		query_string = query_string + '&cookie_compliance=disabled'
	}	
	else
	{
		query_string = query_string + '&cookie_compliance=enabled'
	}
	if(!$("#k_geolocation").is(':checked'))
	{
		query_string = query_string + '&geolocation=disabled'
	}	
	else
	{
		query_string = query_string + '&geolocation=enabled'
	}
	
	$("#analyze_auto").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
	$("#analyze_auto").attr("disabled","disabled");
	$("#analyze_again").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
	$("#analyze_again").attr("disabled","disabled");
	$("#add_row").attr("disabled","disabled");	
	$.get( "analysis.php?policy=<?php echo $_GET["policy"]; ?>"+query_string, function( data ) {
		$("#analyze_tab").addClass("hidden");
		$("#suggestion_tab" ).removeClass("hidden");
		$('#suggestions').DataTable().ajax.url("get_suggestions.php?policy=<?php echo $_GET["policy"]; ?>").load();
		 $.get("get_suggestions_count.php?policy=<?php echo $_GET["policy"]; ?>", function(data, status){
			var results = JSON.parse(data);
			$("#var_error").html(results["error"]);
			$("#var_warning").html(results["warning"]);
			$("#var_info").html(results["info"]);
			var new_score = 100 - results["score"];
			$(".current_score").html(new_score);
			
			if (new_score <60)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:red">F</span>');
			}
			if (new_score >=60 && new_score <70)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:orange ">D</span>');
			}
			if (new_score >=70 && new_score <80)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:gray;">C</span>');
			}
			if (new_score >=80 && new_score <90)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#1D9B1E">B</span>');
			}	
			if (new_score >=90 )
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#30CE31">A</span>');
			}
			
			$("#analyze_auto").html('Automatic');
			$("#analyze_auto").removeAttr("disabled");
			$("#analyze_again").html('Re-discover');
			$("#analyze_again").removeAttr("disabled");
			$("#add_row").removeAttr("disabled");			
		});		
	});
});
</script>

<script>
$( "#analyze_auto1" ).click(function() {
	$("#analyze_auto").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
	$("#analyze_auto").attr("disabled","disabled");
	$.get( "analysis.php?policy=<?php echo $_GET["policy"]; ?>", function( data ) {
		$("#analyze_tab").addClass("hidden");
		$("#suggestion_tab" ).removeClass("hidden");
		$('#suggestions').DataTable().ajax.url("get_suggestions.php?policy=<?php echo $_GET["policy"]; ?>").load();
		 $.get("get_suggestions_count.php?policy=<?php echo $_GET["policy"]; ?>", function(data, status){
			var results = JSON.parse(data);
			$("#var_error").html(results["error"]);
			$("#var_warning").html(results["warning"]);
			$("#var_info").html(results["info"]);
			var new_score = 100 - results["score"];
			$(".current_score").html(new_score);
			
			if (new_score <60)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:red">F</span>');
			}
			if (new_score >=60 && new_score <70)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:orange ">D</span>');
			}
			if (new_score >=70 && new_score <80)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:gray;">C</span>');
			}
			if (new_score >=80 && new_score <90)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#1D9B1E">B</span>');
			}	
			if (new_score >=90 )
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#30CE31">A</span>');
			}	
		});	
	});
});
</script>


<script>
$( "#analyze_again1" ).click(function() {
	$("#analyze_again").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
	$("#analyze_again").attr("disabled","disabled");
	$("#add_row").attr("disabled","disabled");
	$.get( "analysis.php?policy=<?php echo $_GET["policy"]; ?>", function( data ) {
		$('#suggestions').DataTable().ajax.url("get_suggestions.php?policy=<?php echo $_GET["policy"]; ?>").load();
		 $.get("get_suggestions_count.php?policy=<?php echo $_GET["policy"]; ?>", function(data, status){
			var results = JSON.parse(data);
			$("#var_error").html(results["error"]);
			$("#var_warning").html(results["warning"]);
			$("#var_info").html(results["info"]);
			var new_score = 100 - results["score"];
			$(".current_score").html(new_score);
			
			if (new_score <60)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:red">F</span>');
			}
			if (new_score >=60 && new_score <70)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:orange ">D</span>');
			}
			if (new_score >=70 && new_score <80)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:gray;">C</span>');
			}
			if (new_score >=80 && new_score <90)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#1D9B1E">B</span>');
			}	
			if (new_score >=90 )
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#30CE31">A</span>');
			}	
			$("#analyze_again").html('Re-discover');
			$("#analyze_again").removeAttr("disabled");
			$("#add_row").removeAttr("disabled");
		});	
	});
});
</script>

<script>

$( "#save" ).click(function() {
var table = $('#suggestions').DataTable();
var payload = "["
var i;
for (i = 0; i < table.rows().count(); i++) { 
	var txt = table.cell( i, 1).data();
	var severity = table.cell( i, 2).data();
	var section = table.cell( i, 3).data();
	var score = table.cell( i, 5).data();
	if(i>0)
	{
		payload = payload + ', ';
	}
	payload = payload + '{"severity":"'+severity+'","section":"'+section+'","score":'+score+',"txt":'+JSON.stringify(txt)+'}';

}
payload = payload + "]"

// console.log(payload);
 $.post( "save_asm.php",  { name: payload, policy:"<?php echo $_GET['policy']; ?>"});
});
</script>


<!-- Fetch the details for the Statistics Panel -->			
<script>

$( "#add_row" ).click(function() {
	var table = $('#suggestions').DataTable();
	var input_category = $( "#input_category option:selected" ).val();
	var input_suggestion = $( "#input_suggestion" ).val();
	var input_severity = $( "#input_severity option:selected" ).val();
	var input_score = $( "#input_score" ).val();
	$( "#input_score" ).val("");
	$( "#input_suggestion" ).val("");

	table.row.add( {
			"severity": input_severity,
			"txt": input_suggestion,
			"severity": input_severity,
			"section": input_category,
			"score": input_score
		} ).draw();
	$('#save').removeAttr("disabled");
	
		var warning = parseInt($("#var_warning").text());
		var error = parseInt($("#var_error").text());
		var info = parseInt($("#var_info").text());
		var current_score = parseInt($(".current_score").text());
		var new_score = current_score - input_score;
		if(input_severity=="error")
		{
			error = error + 1;
			$("#var_error").html(error); 
		}
			   
		if(input_severity=="warning")
		{
			warning = warning + 1;
			$("#var_warning").html(warning); 
		}    	   	
		if(input_severity=="info")
		{
			info = info + 1;
			$("#var_info").html(info); 
		}   
		$(".current_score").html(new_score);
		
		if (new_score <60)
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:red">F</span>');
		}
		if (new_score >=60 && new_score <70)
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:orange ">D</span>');
		}
		if (new_score >=70 && new_score <80)
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:gray;">C</span>');
		}
		if (new_score >=80 && new_score <90)
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#1D9B1E">B</span>');
		}	
		if (new_score >=90 )
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#30CE31">A</span>');
		}

		table.row(this).remove().draw( false );
		$('#save').removeAttr("disabled");	
	
});
</script>
<script>

	<?php echo $evasion; ?>
	
	$(document).ready(function() {
		var table = $('#evasion').DataTable( {
			"data": evasion,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['learn'] == "Yes" )
				  $('td', row).eq(1).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(1).html("<i class='fa fa-minus-square-o fa-2x red' ></i>");
				if ( data['enabled'] == "Yes" )
				  $('td', row).eq(2).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(2).html("<i class='fa fa-minus-square-o fa-2x red' ></i>");
			},
			  "columns": [
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks', "data": "learn"},
				{ "className": 'attacks', "data": "enabled"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " },
				"order": [[1, 'desc']]
		} );	

	} );
</script>
<script>
	<?php echo $compliance; ?>

	$(document).ready(function() {
		var table = $('#compliance').DataTable( {
			"data": compliance,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['learn'] == "Yes" )
				  $('td', row).eq(1).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(1).html("<i class='fa fa-minus-square-o fa-2x red' ></i>");
				if ( data['enabled'] == "Yes" )
				  $('td', row).eq(2).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(2).html("<i class='fa fa-minus-square-o fa-2x red' ></i>");
			  },
			  "columns": [
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks', "data": "learn"},
				{ "className": 'attacks', "data": "enabled"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " },
				"order": [[1, 'desc']]
		} );	

	} );
</script>	
<script>

	<?php echo $blocking_settings; ?>

	$(document).ready(function() {
		var table = $('#blocking').DataTable( {
			"data": blocking_settings,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['learn'] == "Yes" )
				  $('td', row).eq(1).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else
				  $('td', row).eq(1).html("<i class='fa fa-minus-square fa-2x red' ></i>");
				if ( data['learn'] == "-" )
				  $('td', row).eq(1).html("<i class='fa fa-times fa-2x black' ></i>");			  
				if ( data['alarm'] == "Yes" )
				  $('td', row).eq(2).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(2).html("<i class='fa fa-minus-square fa-2x red' ></i>");
				if ( data['block'] == "Yes" )
				  $('td', row).eq(3).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(3).html("<i class='fa fa-minus-square fa-2x red' ></i>");
			  },
			  "columns": [
				{ "className": 'bold', "data":"name" },
				{  "className": 'attacks', "data":"alarm"},
				{  "className": 'attacks', "data":"learn"},
				{  "className": 'attacks', "data":"block"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " },
				"order": [[1, 'desc']]
		} );	
	} );
</script>	
<script>
	<?php echo $allowed_responses; ?>

	$(document).ready(function() {
		var table = $('#response').DataTable( {
			"data": allowed_responses,
			"columns": [
				{"className": 'bold', "data": "name"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
	} );
</script>	
<script>

	<?php echo $file_types; ?>

	$(document).ready(function() {
		var table = $('#file_type').DataTable( {
			"data": file_types,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['performStaging'] == "Yes" )
				  $('td', row).eq(1).html("<i class='fa fa-flag fa-2x red'></i>");
				else 
				  $('td', row).eq(1).html("<i class='fa fa-times fa-2x' ></i>");
			  },
			  "columns": [
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks', "data": "performStaging"},
				{ "className": 'attacks',"data": "urlLength"},
				{ "className": 'attacks',"data": "queryStringLength"},
				{ "className": 'attacks',"data": "postDataLength"},
				{ "className": 'attacks',"data": "requestLength"},
				{ "className": 'attacks',"data": "lastUpdateMicros"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[1, 'asc']]
		} );	

	} );
</script>
<script>

function format_url ( d ) {
    // `d` is the original data object for the row
var table_add = "";
var line_add = "";


d.urlContentProfiles.forEach((product, index) => {
  line_add = line_add + '"Type" : ' + product['type'] + ', &nbsp&nbsp&nbsp"Header Name" : ' + product['headerName'] + ', &nbsp&nbsp&nbsp"Header Value" : ' + product['headerValue'] + '<br>';         

});
table_add = '<tr>'+
  '<td style="width:150px"><b>Content Profiles:</b></td>'+
      '<td >'+line_add+'</td>'+
        '</tr>';

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Type:</b></td>'+
          '<td >'+d.type+'</td>'+
        '</tr>'+ 
		'<tr>'+
          '<td style="width:150px"><b>Signature Overrides:</b></td>'+
          '<td >'+d.signatureOverrides.join('<br>')+'</td>'+
        '</tr>'+
         '<tr>'+
          '<td style="width:150px"><b>Meta Characters Overrides:</b></td>'+
          '<td >The Actual Meta Characters can only be reviewed from the BIGIP device itself.</td>'+
        '</tr>'+
		table_add +
        '</table>';
}

	<?php echo $url; ?>

	$(document).ready(function() {
		var table = $('#urls').DataTable( {
			"data": url,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['performStaging'] == "Yes" )
				  $('td', row).eq(3).html("<i class='fa fa-flag fa-2x red'></i>");
				else 
				  $('td', row).eq(3).html("<i class='fa fa-times fa-2x' ></i>");
				if ( data['attackSignaturesCheck'] == "Yes" )
				  $('td', row).eq(4).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(4).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['metacharsOnUrlCheck'] == "Yes" )
				  $('td', row).eq(5).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(5).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
			  },
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'attacks',"data": "protocol"},
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks',"data": "performStaging"},
				{ "className": 'attacks',"data": "attackSignaturesCheck"},
				{ "className": 'attacks',"data": "metacharsOnUrlCheck"},
				{ "className": 'attacks',"data": "num_of_sign_overides"},
				{ "className": 'attacks',"data": "metacharOverrides"},
				{ "className": 'attacks',"data": "lastUpdateMicros"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#urls tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_url(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>
<script>

function format_parameter ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Signature Overrides:</b></td>'+
          '<td >'+d.signatureOverrides.join('<br>')+'</td>'+
        '</tr>'+
        '<tr>'+
          '<td style="width:150px"><b>Type:</b></td>'+
          '<td >'+d.type+'</td>'+
        '</tr>'+
         '<tr>'+
          '<td style="width:150px"><b>Meta Characters Overrides:</b></td>'+
          '<td >The Actual Meta Characters can only be reviewed from the BIGIP device itself.</td>'+
        '</tr>'+ 
         '<tr>'+
          '<td style="width:150px"><b>Last Modified:</b></td>'+
          '<td >'+d.lastUpdateMicros+'</td>'+
        '</tr>'+ 		
 '</table>';
}

	<?php echo $parameters; ?>

	$(document).ready(function() {
		var table = $('#parameters').DataTable( {
			"data": parameters,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['performStaging'] == "Yes" )
				  $('td', row).eq(5).html("<i class='fa fa-flag fa-2x red'></i>");
				else 
				  $('td', row).eq(5).html("<i class='fa fa-times fa-2x' ></i>");
				if ( data['attackSignaturesCheck'] == "Yes" )
				  $('td', row).eq(6).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(6).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['metacharsOnParameterValueCheck'] == "Yes" )
				  $('td', row).eq(7).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(7).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['sensitiveParameter'] == "Yes" )
				  $('td', row).eq(10).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(10).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
			  },
			  "columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{"data": "enforcement"},
				{ "className": 'attacks',"data": "valueType"},
				{ "className": 'attacks',"data": "dataType"},
				{ "className": 'attacks',"data": "performStaging"},
				{ "className": 'attacks',"data": "attackSignaturesCheck"},
				{ "className": 'attacks',"data": "metacharsOnParameterValueCheck"},
				{ "className": 'attacks',"data": "num_of_sign_overides"},
				{ "className": 'attacks',"data": "valueMetacharOverrides"},
				{ "className": 'attacks',"data": "sensitiveParameter"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " },
				"order": [[1, 'asc']]
		} );	

    $('#parameters tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_parameter(row.data()) ).show();
            tr.addClass('shown');
        }
    } );


	} );
</script>
<script>

	<?php echo $signature_sets; ?>

	$(document).ready(function() {
		var table = $('#signatures').DataTable( {
			"data": signature_sets,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['learn'] == "Yes" )
				  $('td', row).eq(1).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(1).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['alarm'] == "Yes" )
				  $('td', row).eq(2).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(2).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['block'] == "Yes" )
				  $('td', row).eq(3).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(3).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
			  },
			  "columns": [
				{ "className": 'bold',"data": "name" },
				{  "className": 'attacks',"data": "learn"},
				{  "className": 'attacks',"data": "alarm"},
				{  "className": 'attacks',"data": "block"},
				{  "className": 'attacks',"data": "lastUpdateMicros"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<script>

function format_cookie ( d ) {
    // `d` is the original data object for the row
			if ( d.accessibleOnlyThroughTheHttpProtocol == "Yes" )
			  var accessibleOnlyThroughTheHttpProtocol ="<i class='fa fa-check-circle fa-2x green'></i>";
			else 
			   var accessibleOnlyThroughTheHttpProtocol ="<i class='fa fa-minus-circle fa-2x red' ></i>";
		  if ( d.securedOverHttpsConnection == "Yes" )
			  var securedOverHttpsConnection ="<i class='fa fa-check-circle fa-2x green'></i>";
			else 
			   var securedOverHttpsConnection ="<i class='fa fa-minus-circle fa-2x red' ></i>";

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Insert Same Site Attribute</b></td>'+
          '<td>'+d.insertSameSiteAttribute+'</td>'+
        '</tr>'+
        '<tr>'+
          '<td style="width:150px"><b>Insert HTTPOnly Attribute</b></td>'+
          '<td>'+accessibleOnlyThroughTheHttpProtocol +'</td>'+
        '</tr>'+		
        '<tr>'+
          '<td style="width:150px"><b>Insert Secure Attribute </b></td>'+
          '<td>'+securedOverHttpsConnection+'</td>'+
        '</tr>'+ 
       '<tr>'+
          '<td style="width:150px"><b>Signature Overrides:</b></td>'+
          '<td>'+d.signatureOverrides.join('<br>')+'</td>'+
        '</tr>'+
        '</table>';
}
 

	<?php echo $cookies; ?>
 

$(document).ready(function() {
	var table = $('#cookies').DataTable( {
		"data": cookies,
		"createdRow": function( row, data, dataIndex ) {
			if ( data['performStaging'] == "Yes" )
			  $('td', row).eq(2).html("<i class='fa fa-flag fa-2x red'></i>");
			else 
			  $('td', row).eq(2).html("<i class='fa fa-times fa-2x' ></i>");				  
			if ( data['attackSignaturesCheck'] == "Yes" )
			  $('td', row).eq(4).html("<i class='fa fa-check-circle fa-2x green'></i>");
			else 
			  $('td', row).eq(4).html("<i class='fa fa-minus-circle fa-2x red' ></i>");

		  },
		  "columns": [
		    {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
			{ "className": 'bold',"data": "name" },
			{  "className": 'attacks',"data": "performStaging"},
			{  "className": 'attacks',"data": "enforcementType"},
			{  "className": 'attacks',"data": "attackSignaturesCheck"},
			{  "className": 'attacks',"data": "num_of_sign_overides"}
			],
			"autoWidth": false,
			"processing": true,
			"language": {"processing": "Waiting.... " },
			"order": [[1, 'asc']]
		} );	

    $('#cookies tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_cookie(row.data()) ).show();
            tr.addClass('shown');
        }
    } );


	} );
</script>
<script>

function format_header ( d ) {
    // `d` is the original data object for the row

			if ( d.normalizationViolations == "Yes" )
			  var normalizationViolations ="<i class='fa fa-check-circle fa-2x green'></i>";
			else 
			   var normalizationViolations ="<i class='fa fa-minus-circle fa-2x red' ></i>";
			if ( d.urlNormalization == "Yes" )
			  var urlNormalization ="<i class='fa fa-check-circle fa-2x green'></i>";
			else 
			   var urlNormalization ="<i class='fa fa-minus-circle fa-2x red' ></i>";
			if ( d.htmlNormalization == "Yes" )
			  var htmlNormalization ="<i class='fa fa-check-circle fa-2x green'></i>";
			else 
			   var htmlNormalization ="<i class='fa fa-minus-circle fa-2x red' ></i>";
		  if ( d.percentDecoding == "Yes" )
			  var percentDecoding ="<i class='fa fa-check-circle fa-2x green'></i>";
			else 
			   var percentDecoding ="<i class='fa fa-minus-circle fa-2x red' ></i>";

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Evasion Techniques</b></td>'+
          '<td>'+normalizationViolations+'</td>'+
        '</tr>'+
        '<tr>'+
          '<td style="width:150px"><b>URL Normalization</b></td>'+
          '<td>'+urlNormalization+'</td>'+
        '</tr>'+
        '<tr>'+
          '<td style="width:150px"><b>HTML Normalization</b></td>'+
          '<td>'+htmlNormalization+'</td>'+
        '</tr>'+		
        '<tr>'+
          '<td style="width:150px"><b>Percent Decoding</b></td>'+
          '<td>'+percentDecoding+'</td>'+
        '</tr>'+
		'<tr>'+
          '<td style="width:150px"><b>Signature Overrides</b></td>'+
          '<td>'+d.signatureOverrides.join('<br>')+'</td>'+
        '</tr>'+
        '</table>';
}
 

	<?php echo $headers; ?>
 

$(document).ready(function() {
	var table = $('#headers').DataTable( {
		"data": headers,
		"createdRow": function( row, data, dataIndex ) {
			if ( data['checkSignatures'] == "Yes" )
			  $('td', row).eq(2).html("<i class='fa fa-check-circle fa-2x green'></i>");
			else 
			  $('td', row).eq(2).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
		  },
		  "columns": [
		    {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
			{ "className": 'bold',"data": "name" },
			{  "className": 'attacks',"data": "checkSignatures"},
			{  "className": 'attacks',"data": "num_of_sign_overides"}
			],
			"autoWidth": false,
			"processing": true,
			"language": {"processing": "Waiting.... " },
			"order": [[1, 'asc']]
		} );	

    $('#headers tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_header(row.data()) ).show();
            tr.addClass('shown');
        }
    } );


	} );
</script>
<script>

	<?php echo $ipi_categories; ?>
	
	$(document).ready(function() {
		var table = $('#ipi').DataTable( {
			"data": ipi_categories,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['alarm'] == "Yes" )
				  $('td', row).eq(1).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(1).html("<i class='fa fa-minus-square-o fa-2x red' ></i>");
				if ( data['block'] == "Yes" )
				  $('td', row).eq(2).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(2).html("<i class='fa fa-minus-square-o fa-2x red' ></i>");

			  },			
			"columns": [
				{ "className": 'bold',"data": "name" },
				{  "className": 'attacks',"data": "alarm"},
				{  "className": 'attacks',"data": "block"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>
<script>

	<?php echo $domains; ?>

	$(document).ready(function() {
		var table = $('#redirection').DataTable( {
			"data": domains,
			"columns": [
				{ "className": 'bold',"data": "domainName" },
				{  "className": 'attacks',"data": "includeSubdomains"},
				{  "className": 'attacks',"data": "type"},
				{  "className": 'attacks',"data": "lastUpdateMicros"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>
<script>

	<?php echo $methods; ?>

	$(document).ready(function() {
		var table = $('#methods').DataTable( {
			"data": methods,
			"columns": [
				{ "className": 'bold',"data": "name" },
				{  "className": 'attacks',"data": "actAsMethod"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>
<script>

	<?php echo $file_types_disallowed; ?>

	$(document).ready(function() {
		var table = $('#file_types_disallowed').DataTable( {
			"data": file_types_disallowed,
			"columns": [
				{ "className": 'bold',"data": "name" },
				{  "className": 'attacks',"data": "lastUpdateMicros"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>
<script>

	<?php echo $suggestions; ?>
	
	$(document).ready(function() {
			var table = $('#suggestions').DataTable( {
			"data": suggestions,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['severity'] == "error" )
				  $('td', row).eq(0).html("<i class='fa fa-times-circle fa-2x red'></i>");
				if ( data['severity'] == "warning" )
				  $('td', row).eq(0).html("<i class='fa fa-warning fa-2x orange' ></i>");				  
				if ( data['severity'] == "info" )
				  $('td', row).eq(0).html("<i class='fa fa-info-circle fa-2x' ></i>");
				  $('td', row).eq(4).html("<i class='fa fa-trash fa-2x' ></i>");  
			  },

			"columns": [
				{"className":'attacks',"data":"severity"},
				{"data": "txt" },
				{ "className": 'attacks',"data": "severity"},
				{ "className": 'attacks',"data": "section"},
				{ "className": 'delete_button',"data": null},
				{ "data": "score"}
				],
				"columnDefs": [
				{
				  "targets": [5],
				  "visible": false
				}
				],				
				"autoWidth": false,
				"processing": true,
				"order": [[5, 'desc']]
		} );	

		$('#suggestions tbody').on( 'click', '.delete_button', function () {

    	   	var idx = table.row(this).index();
    	   	var data = table.cell( idx, 2).data();
    	   	var delta_score = parseInt(table.cell( idx, 5).data());
    	   	var error = parseInt($("#var_error").text());   	   
    	   	var warning = parseInt($("#var_warning").text());
    	   	var info = parseInt($("#var_info").text());
    	   	var current_score = parseInt($(".current_score").text());
    	   	var new_score = current_score + delta_score;
    	   	if(data=="error")
    	   	{
    	   		error = error - 1;
    	   		$("#var_error").html(error); 
    	   	}
    	   	   	   
    	   	if(data=="warning")
    	   	{
    	   		warning = warning - 1;
    	   		$("#var_warning").html(warning); 
    	   	}    	   	
    	   	if(data=="info")
    	   	{
    	   		info = info - 1;
    	   		$("#var_info").html(info); 
    	   	}   
    	   	$(".current_score").html(new_score);
			
			if (new_score <60)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:red">F</span>');
			}
			if (new_score >=60 && new_score <70)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:orange ">D</span>');
			}
			if (new_score >=70 && new_score <80)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:gray;">C</span>');
			}
			if (new_score >=80 && new_score <90)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#1D9B1E">B</span>');
			}			
			if (new_score >=90 )
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#30CE31">A</span>');
			}	

    	   	table.row(this).remove().draw( false );
			$('#save').removeAttr("disabled");
    	   	
    } );

	} );
</script>

<script>

	<?php echo $ip_exceptions; ?>

	$(document).ready(function() {
		var table = $('#ip_exceptions').DataTable( {
			"data": ip_exceptions,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['ignoreAnomalies'] == "Yes" )
				  $('td', row).eq(4).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(4).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['neverLearnRequests'] == "Yes" )
				  $('td', row).eq(5).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(5).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['neverLogRequests'] == "Yes" )
				  $('td', row).eq(6).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(6).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['ignoreIpReputation'] == "Yes" )
				  $('td', row).eq(7).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(7).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['trustedByPolicyBuilder'] == "Yes" )
				  $('td', row).eq(8).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(8).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
			  },
			  "columns": [
				{ "className": 'bold',"data": "ipAddress" },
				{  "className": 'attacks',"data": "ipMask"},
				{  "className": 'attacks',"data": "description"},
				{  "className": 'attacks',"data": "blockRequests"},
				{  "className": 'attacks',"data": "ignoreAnomalies"},
				{  "className": 'attacks',"data": "neverLearnRequests"},
				{  "className": 'attacks',"data": "neverLogRequests"},
				{  "className": 'attacks',"data": "ignoreIpReputation"},
				{  "className": 'attacks',"data": "trustedByPolicyBuilder"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<script>

	<?php echo $sensitive_param; ?>

	$(document).ready(function() {
		var table = $('#sensitive_param').DataTable( {
			"data": sensitive_param,
			"columns": [
				{ "className": 'bold',"data": "name" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>


<script>

	<?php echo $disallowed_geolocations; ?>

	$(document).ready(function() {
		var table = $('#geolocation').DataTable( {
			"data": disallowed_geolocations,
			"columns": [
				{ "className": 'bold',"data": "countryName" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<script>

	<?php echo $response_pages; ?>

	$(document).ready(function() {
		var table = $('#response_pages').DataTable( {
			"data": response_pages,
			"columns": [
				{ "className": 'bold',"data": "responsePageType" },
				{ "className": 'bold',"data": "responseActionType" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>


  </body>
</html>
