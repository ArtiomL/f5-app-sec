<?php
sleep(1);
if(!isset($_GET["policy"]) || !isset($_GET["asm_cookie"]) || !isset($_GET["file_uploads"]) || !isset($_GET["icap"]) || !isset($_GET["blacklisted"]) || !isset($_GET["convert"]) || !isset($_GET["redirection_attempt"]) || !isset($_GET["same_site"]) || !isset($_GET["cookie_length"]) || !isset($_GET["header_length"]) || !isset($_GET["secure"]) || !isset($_GET["domain_cookie"]) || !isset($_GET["response_code"]) || !isset($_GET["http_only"]) || !isset($_GET["ipi"]) || !isset($_GET["method"]) || !isset($_GET["cookie_compliance"]) || !isset($_GET["brute_force"]) || !isset($_GET["geolocation"]) || !isset($_GET["file_type"]) || !isset($_GET["file_length"]))
{
	echo "Variables not set correctly";
	exit();
}	

$analyze_cookie_enforcement = "no";
$analyze_file_uploads ="no";
$analyze_icap = "no";
$analyze_domains = "no";
$analyze_cookie_length = "no";
$analyze_header_length = "no";
$analyze_cookie_httponly = "no";
$analyze_cookie_secure = "no";
$analyze_cookie_same_site = "no";
$analyze_ipi = "no";
$analyze_brute = "no";
$analyze_file_types = "no";
$analyze_file_type_lengths = "no";
$analyze_methods = "no";
$analyze_response_codes = "no";
$analyze_geolocation = "no";

$string = file_get_contents("violation_list.json");
$viol_list = json_decode($string, true);

$config_files = "config_files/" . $_GET["policy"];
$suggestions = [];

$string = file_get_contents($config_files."/overview.txt");
$overview = json_decode($string, true);

$string = file_get_contents($config_files."/policy_builder.txt");
$policy_builder = json_decode($string, true);

$string = file_get_contents($config_files."/blocking_settings.txt");
$blocking_settings = json_decode($string, true);

$string = file_get_contents($config_files."/disallowed_geolocations.txt");
$disallowed_geolocations = json_decode($string, true);

$string = file_get_contents($config_files."/allowed_responses.txt");
$response_codes = json_decode($string, true);

$string = file_get_contents($config_files."/headers.txt");
$headers = json_decode($string, true);

$string = file_get_contents($config_files."/cookies.txt");
$cookies = json_decode($string, true);

$string = file_get_contents($config_files."/methods.txt");
$methods = json_decode($string, true);

$string = file_get_contents($config_files."/domains.txt");
$domains = json_decode($string, true);

$ipi_enabled = file_get_contents($config_files."/ipi.txt");

$string = file_get_contents($config_files."/ipi_categories.txt");
$ipi_categories = json_decode($string, true);

$string = file_get_contents($config_files."/evasions.txt");
$evasions = json_decode($string, true);

$string = file_get_contents($config_files."/compliance.txt");
$compliance = json_decode($string, true);

$string = file_get_contents($config_files."/sensitive_param.txt");
$sensitive_param = json_decode($string, true);

$string = file_get_contents($config_files."/file_types_disallowed.txt");
$file_types_disallowed = json_decode($string, true);

$string = file_get_contents($config_files."/file_types_allowed.txt");
$file_types_allowed = json_decode($string, true);

$string = file_get_contents($config_files."/parameters.txt");
$parameters = json_decode($string, true);

$string = file_get_contents($config_files."/urls.txt");
$urls = json_decode($string, true);

$string = file_get_contents($config_files."/signature_sets.txt");
$signature_sets = json_decode($string, true);

$string = file_get_contents($config_files."/signatures_overview.txt");
$signatures_overview = json_decode($string, true);

$string = file_get_contents("config_files/raw_virus_detection_server.json");
$icap = json_decode($string, true);


$string = file_get_contents($config_files."/whitelist.txt");
$whitelist = json_decode($string, true);


$disabled_violations = [];

foreach ($blocking_settings as $var)
{
	if ($var['block']!="Yes" && $var['alarm']!="Yes" && $var['learn']!="Yes")
	{
		array_push($disabled_violations, $var['name']);
	}
}

$violations_high = array("HTTP protocol compliance failed","Evasion technique detected");
$violations_low = array("Malformed XML data","Malformed JSON data");


if ($_GET["asm_cookie"] == "enabled")
{
	$violations_low[] ="Modified ASM cookie";
}

if ($_GET["domain_cookie"] == "enabled" && !in_array("Modified domain cookie(s)", $disabled_violations))
{
	$violations_high[] ="Modified domain cookie(s)";
	$analyze_cookie_enforcement = "yes";
}	
if ($_GET["file_uploads"] == "enabled" && !in_array("Disallowed file upload content detected", $disabled_violations))
{
	$violations_high[] ="Disallowed file upload content detected";
	$analyze_file_uploads ="yes";
}	
if ($_GET["icap"] == "enabled" && !in_array("Virus detected", $disabled_violations))
{
	$violations_high[] ="Virus detected";
	$analyze_icap = "yes";
}	

if ($_GET["convert"] == "enabled")
{
	$violations_low[] ="Failed to convert character";
}	
if ($_GET["redirection_attempt"] == "enabled" && !in_array("Illegal redirection attempt", $disabled_violations))
{
	$violations_low[] ="Illegal redirection attempt";
	$analyze_domains = "yes";

}	
if ($_GET["cookie_length"] == "enabled" && !in_array("Illegal cookie length", $disabled_violations))
{
	$violations_low[] ="Illegal cookie length";
	$analyze_cookie_length = "yes";
}	
if ($_GET["header_length"] == "enabled" && !in_array("Illegal header length", $disabled_violations))
{
	$violations_low[] ="Illegal header length";
	$analyze_header_length = "yes";
}
if ($_GET["same_site"] == "enabled")
{
	$analyze_cookie_same_site = "yes";
}	

if ($_GET["http_only"] == "enabled")
{
	$analyze_cookie_httponly = "yes";
}	
if ($_GET["secure"] == "enabled")
{
	$analyze_cookie_secure = "yes";
}	
if ($_GET["ipi"] == "enabled"  && !in_array("Access from malicious IP address", $disabled_violations))
{
	$violations_high[] ="Access from malicious IP address";
	$analyze_ipi = "yes";
}
if ($_GET["brute_force"] == "enabled" && !in_array("Brute Force: Maximum login attempts are exceeded", $disabled_violations))
{
	$violations_high[] ="Brute Force: Maximum login attempts are exceeded";
	$analyze_brute = "yes";
}
if ($_GET["file_type"] == "enabled" && !in_array("Illegal file type", $disabled_violations))
{
	$violations_high[] ="Illegal file type";
	$analyze_file_types = "yes";
}
if ($_GET["file_length"] == "enabled")
{
	$violations_low[] ="Illegal request length";
	$violations_low[] ="Illegal POST data length";
	$violations_low[] ="Illegal URL length";
	$violations_low[] ="Illegal query string length";
	$analyze_file_type_lengths = "yes";
}
if ($_GET["method"] == "enabled" && !in_array("Illegal method", $disabled_violations))
{
	$violations_high[] ="Illegal method";
	$analyze_methods = "yes";
}
if ($_GET["cookie_compliance"] == "enabled" && !in_array("Cookie not RFC-compliant", $disabled_violations))
{
	$violations_low[] ="Cookie not RFC-compliant";
}
if ($_GET["response_code"] == "enabled" && !in_array("Illegal HTTP status in response", $disabled_violations))
{
	$violations_low[] ="Illegal HTTP status in response";
	$analyze_response_codes = "yes";
}
if ($_GET["blacklisted"] == "enabled")
{
	$violations_low[] ="IP is blacklisted";
}
if ($_GET["geolocation"] == "enabled" && !in_array("Access from disallowed Geolocation", $disabled_violations))
{
	$violations_low[] ="Access from disallowed Geolocation";
	$analyze_geolocation = "yes";
}

#########      Overview    #########
include 'analysis/overview.php';

#########      Policy Builder    #########
include 'analysis/policy_builder.php';

########  Blocking Settings #######
include 'analysis/blocking_settings.php';

########    Geolocation   #######
include 'analysis/geolocation.php';

########    HTTP Methods   #######
include 'analysis/methods.php';

########    HTTP Response Codes   #######
include 'analysis/response_codes.php';
	
########   Redirection Domains   #######
include 'analysis/domains.php';
	
########   Headers   #######
include 'analysis/headers.php';

########   Cookies  #######
include 'analysis/cookies.php';

########   IP Intelligence  #######
include 'analysis/ipi.php';

########   Evasion  #######
include 'analysis/evasions.php';

########   HTTP Compliance  #######
include 'analysis/compliance.php';

########  Trusted IPs  #######
include 'analysis/trusted_ips.php';

########  Trusted IPs  #######
include 'analysis/sensitive_parameters.php';

########  File Type Allowed  #######
include 'analysis/file_types.php';

########  Parameters #######
include 'analysis/parameters.php';

########  Parameters #######
include 'analysis/urls.php';

########  Signatures #######
include 'analysis/signatures.php';

########  ICAP #######
include 'analysis/icap.php';


$myfile = fopen($config_files."/suggestions.txt", "w") or die("Unable to open file!");
$txt = json_encode($suggestions);
fwrite($myfile, $txt);
fclose($myfile);
	
print_r(json_encode($suggestions));

?>