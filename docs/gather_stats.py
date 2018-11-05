#!/usr/bin/env python3
import os
import datetime
import requests
from requests.auth import HTTPDigestAuth
import json
import getpass

requests.packages.urllib3.disable_warnings() 


bigip = input('Please enter the management address of the standby BIG-IP: ')
customer_name = input('Please enter Customer name: ')
username = input('Please enter BIG-IP username: ')
password = getpass.getpass(prompt='Please enter BIG-IP password:')
ltm_stats = input('Collect LTM stats (Yes/No): ')
asm_stats = input('Collect ASM stats (Yes/No): ')

from f5_functions import *		


########################					########################
########################	LTM Report		########################
########################					########################	
if not (os.path.exists(customer_name)):
	os.makedirs(customer_name)

if ltm_stats !="no" and ltm_stats !="No" and ltm_stats !="n" and ltm_stats !="N":
	########################	GET device details	########################
	device_details, raw_device, raw_dns, raw_ntp, raw_syslog = get_device_details(bigip, username, password)
	with open(customer_name+"/device_details.txt", "w") as outfile:
		json.dump(device_details, outfile)
	with open(customer_name+"/raw_device.json", "w") as outfile:
		json.dump(raw_device, outfile)
	with open(customer_name+"/raw_dns.json", "w") as outfile:
		json.dump(raw_dns, outfile)
	with open(customer_name+"/raw_ntp.json", "w") as outfile:
		json.dump(raw_ntp, outfile)
	with open(customer_name+"/raw_syslog.json", "w") as outfile:
		json.dump(raw_syslog, outfile)		
	########################	GET provisioned modules	########################
	provisioned_modules, raw_provision = get_provisioned_modules(bigip, username, password)
	with open(customer_name+"/provisioned_modules.txt", "w") as outfile:
		json.dump(provisioned_modules, outfile)
	with open(customer_name+"/raw_provision.json", "w") as outfile:
		json.dump(raw_provision, outfile)		
	########################	GET Self IPs 	########################
	self_ips, raw_self = get_self_ips(bigip, username, password)
	with open(customer_name+"/self_ips.txt", "w") as outfile:
		json.dump(self_ips, outfile)		
	with open(customer_name+"/raw_self.json", "w") as outfile:
		json.dump(raw_self, outfile)
	########################	GET Virtuals	 	#######################
	virtual_servers, raw_virtual = get_virtual_servers(bigip, username, password)
	with open(customer_name+"/virtual_servers.txt", "w") as outfile:
		json.dump(virtual_servers, outfile)	
	with open(customer_name+"/raw_virtual.json", "w") as outfile:
		json.dump(raw_virtual, outfile)
	########################	GET Pools		########################
	pools, raw_pool = get_pools(bigip, username, password) 
	with open(customer_name+"/pools.txt", "w") as outfile:
		json.dump(pools, outfile)
	with open(customer_name+"/raw_pool.json", "w") as outfile:
		json.dump(raw_pool, outfile)
	######################## 	GET  Peristence		########################
	persistence, raw_persistence_cookie, raw_persistence_source_addr = get_persistence(bigip, username, password)
	with open(customer_name+"/persistence.txt", "w") as outfile:
		json.dump(persistence, outfile)
	with open(customer_name+"/raw_persistence_cookie.json", "w") as outfile:
		json.dump(raw_persistence_cookie, outfile)
	with open(customer_name+"/raw_persistence_source_addr.json", "w") as outfile:
		json.dump(raw_persistence_source_addr, outfile)
	########################	GET Monitors		########################
	monitor, raw_monitor_http, raw_monitor_https, raw_monitor_tcp, raw_monitor_icmp, raw_monitor_tcp_half_open, raw_monitor_gateway_icmp = get_monitor(bigip, username, password)
	with open(customer_name+"/monitor.txt", "w") as outfile:
		json.dump(monitor, outfile)
	with open(customer_name+"/raw_monitor_http.json", "w") as outfile:
		json.dump(raw_monitor_http, outfile)
	with open(customer_name+"/raw_monitor_https.json", "w") as outfile:
		json.dump(raw_monitor_https, outfile)
	with open(customer_name+"/raw_monitor_tcp.json", "w") as outfile:
		json.dump(raw_monitor_tcp, outfile)
	with open(customer_name+"/raw_monitor_icmp.json", "w") as outfile:
		json.dump(raw_monitor_icmp, outfile)
	with open(customer_name+"/raw_monitor_tcp_half_open.json", "w") as outfile:
		json.dump(raw_monitor_tcp_half_open, outfile)
	with open(customer_name+"/raw_monitor_gateway_icmp.json", "w") as outfile:
		json.dump(raw_monitor_gateway_icmp, outfile)
	with open(customer_name+"/monitor.txt", "w") as outfile:
		json.dump(monitor, outfile)
	########################	GET Profiles		########################
	profile, raw_profile_http, raw_profile_tcp, raw_profile_client_ssl = get_profile(bigip, username, password)
	with open(customer_name+"/profile.txt", "w") as outfile:
		json.dump(profile, outfile)
	with open(customer_name+"/raw_profile_http.json", "w") as outfile:
		json.dump(raw_profile_http, outfile)
	with open(customer_name+"/raw_profile_tcp.json", "w") as outfile:
		json.dump(raw_profile_tcp, outfile)
	with open(customer_name+"/raw_profile_client_ssl.json", "w") as outfile:
		json.dump(raw_profile_client_ssl, outfile)
	########################	GET Certificates		########################
	ssl_cert, raw_ssl_cert = get_ssl_cert(bigip, username, password)
	with open(customer_name+"/ssl_cert.txt", "w") as outfile:
		json.dump(ssl_cert, outfile)
	with open(customer_name+"/raw_ssl_cert.json", "w") as outfile:
		json.dump(raw_ssl_cert, outfile)
	########################	GET Partitions		########################
	partitions, raw_partition = get_partitions(bigip, username, password)
	with open(customer_name+"/partitions.txt", "w") as outfile:
		json.dump(partitions, outfile)
	with open(customer_name+"/raw_partition.json", "w") as outfile:
		json.dump(raw_partition, outfile)
	########################	GET Route Domains  ########################
	route_domain, raw_route_domain = get_route_domains(bigip, username, password)
	with open(customer_name+"/route_domain.txt", "w") as outfile:
		json.dump(route_domain, outfile)
	with open(customer_name+"/raw_route_domain.json", "w") as outfile:
		json.dump(raw_route_domain, outfile)
	########################	GET VLANs 	########################
	vlans, raw_vlan = get_vlans(bigip, username, password)
	with open(customer_name+"/vlans.txt", "w") as outfile:
		json.dump(vlans, outfile)
	with open(customer_name+"/raw_vlan.json", "w") as outfile:
		json.dump(raw_vlan, outfile)
	########################	GET Routes 	########################
	routes, raw_route = get_routes(bigip, username, password)
	with open(customer_name+"/routes.txt", "w") as outfile:
		json.dump(routes, outfile)		
	with open(customer_name+"/raw_route.json", "w") as outfile:
		json.dump(raw_route, outfile)	
	########################	GET Trunk 	########################
	trunk, raw_trunk = get_trunk(bigip, username, password)
	with open(customer_name+"/trunk.txt", "w") as outfile:
		json.dump(trunk, outfile)		
	with open(customer_name+"/raw_trunk.json", "w") as outfile:
		json.dump(raw_trunk, outfile)	
	########################	GET iRules 	########################
	rules = get_irules(bigip, username, password)
	with open(customer_name+"/rules.txt", "w") as outfile:
		json.dump(rules, outfile)		
	

	
	

	
asm_policies = get_policies(bigip, username, password)

if len(asm_policies)> 0 and (asm_stats !="no" and asm_stats !="No" and asm_stats !="n" and asm_stats !="N"):
########################  Advanced Settings  ########################
	raw_advanced_settings = get_advanced_settings(bigip, username, password)
	with open(customer_name+"/"+"raw_advanced_settings.json", "w") as outfile:
		json.dump(raw_advanced_settings, outfile)
########################  Signatures  ########################
	raw_overall_signatures = get_overall_signatures(bigip, username, password)
	with open(customer_name+"/"+"raw_overall_signatures.json", "w") as outfile:
		json.dump(raw_overall_signatures, outfile)
########################  Meta Characters  ########################
	raw_overall_metachars = get_overall_metachars(bigip, username, password)
	with open(customer_name+"/"+"raw_overall_metachars.json", "w") as outfile:
		json.dump(raw_overall_metachars, outfile)
########################  ICAP  ########################
	raw_virus_detection_server = get_virus_detection_server(bigip, username, password)
	with open(customer_name+"/"+"raw_virus_detection_server.json", "w") as outfile:
		json.dump(raw_virus_detection_server, outfile)

	for asm_policy in asm_policies:
		##### if Policy is not Parent Pocliy ######
		if asm_policy['type'] == "security":
			
			########################  Overview  ########################
			if not (os.path.exists(customer_name+"/"+asm_policy['name'])):
				os.makedirs(customer_name+"/"+asm_policy['name'])
			overview, allowed_responses, raw_policy, raw_general, raw_antivirus, raw_data_guard, raw_login_pages, raw_brute_force_attack_preventions = get_overview(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"overview.txt", "w") as outfile:
				json.dump(overview, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"allowed_responses.txt", "w") as outfile:
				json.dump(allowed_responses, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_policy.json", "w") as outfile:
				json.dump(raw_policy, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_general.json", "w") as outfile:
				json.dump(raw_general, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_antivirus.json", "w") as outfile:
				json.dump(raw_antivirus, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_data_guard.json", "w") as outfile:
				json.dump(raw_data_guard, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_login_pages.json", "w") as outfile:
				json.dump(raw_login_pages, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_brute_force_attack_preventions.json", "w") as outfile:
				json.dump(raw_brute_force_attack_preventions, outfile)				
			########################  File Types  ########################
			file_types_allowed, file_types_disallowed, raw_filetypes = get_file_types(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"file_types_allowed.txt", "w") as outfile:
				json.dump(file_types_allowed, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"file_types_disallowed.txt", "w") as outfile:
				json.dump(file_types_disallowed, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_filetypes.json", "w") as outfile:
				json.dump(raw_filetypes, outfile)
			########################   URLs  ########################
			urls, raw_url = get_urls(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"urls.txt", "w") as outfile:
				json.dump(urls, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_url.json", "w") as outfile:
				json.dump(raw_url, outfile)
			########################   Parameters  ########################
			parameters, raw_parameters = get_parameters (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"parameters.txt", "w") as outfile:
				json.dump(parameters, outfile)			
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_parameters.json", "w") as outfile:
				json.dump(raw_parameters, outfile)		
			########################   Signatures overview ########################
			signatures_overview, raw_signatures, raw_signature_settings, raw_signature_statuses = get_signatures_overview (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"signatures_overview.txt", "w") as outfile:
				json.dump(signatures_overview, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_signatures.json", "w") as outfile:
				json.dump(raw_signatures, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_signature_settings.json", "w") as outfile:
				json.dump(raw_signature_settings, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_signature_statuses.json", "w") as outfile:
				json.dump(raw_signature_statuses, outfile)
			########################   Signature Sets  ########################
			signature_sets,raw_signature_sets = get_signature_sets (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"signature_sets.txt", "w") as outfile:
				json.dump(signature_sets, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_signature_sets.json", "w") as outfile:
				json.dump(raw_signature_sets, outfile)
			###################		  	 Methods 		########################
			methods, raw_methods = get_methods (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"methods.txt", "w") as outfile:
				json.dump(methods, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_methods.json", "w") as outfile:
				json.dump(raw_methods, outfile)
			########################   Headers  ########################
			headers,raw_headers = get_headers (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"headers.txt", "w") as outfile:
				json.dump(headers, outfile)	
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_headers.json", "w") as outfile:
				json.dump(raw_headers, outfile)	
			########################   Cookies  ########################
			cookies, raw_cookies = get_cookies (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"cookies.txt", "w") as outfile:
				json.dump(cookies, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_cookies.json", "w") as outfile:
				json.dump(raw_cookies, outfile)
			########################   Redirection Domains  ########################
			domains, raw_redirection_protection_domains = get_domains (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"domains.txt", "w") as outfile:
				json.dump(domains, outfile)				
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_redirection_protection_domains.json", "w") as outfile:
				json.dump(raw_redirection_protection_domains, outfile)		
			########################   IP Intelligence  ########################
			ipi, ipi_categories,raw_ip_intelligence = get_ipi (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"ipi.txt", "w") as outfile:
				json.dump(ipi, outfile)			
			with open(customer_name+"/"+asm_policy['name']+"/"+"ipi_categories.txt", "w") as outfile:
				json.dump(ipi_categories, outfile)		
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_ip_intelligence.json", "w") as outfile:
				json.dump(raw_ip_intelligence, outfile)		
			########################   Blocking Settings  ########################
			blocking_settings, raw_blocking_violations = get_blocking_settings (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"blocking_settings.txt", "w") as outfile:
				json.dump(blocking_settings, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_blocking_violations.json", "w") as outfile:
				json.dump(raw_blocking_violations, outfile)
			########################   Compliance Settings  ########################
			compliance, raw_blocking_http = get_compliance (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"compliance.txt", "w") as outfile:
				json.dump(compliance, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_blocking_http.json", "w") as outfile:
				json.dump(raw_blocking_http, outfile)
			########################   Evasions Settings  ########################
			evasions, raw_evasions = get_evasion (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"evasions.txt", "w") as outfile:
				json.dump(evasions, outfile)			
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_evasions.json", "w") as outfile:
				json.dump(raw_evasions, outfile)			
			########################   WhiteList IPs ########################
			whitelist, raw_whitelist = get_whitelist (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"whitelist.txt", "w") as outfile:
				json.dump(whitelist, outfile)		
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_whitelist.json", "w") as outfile:
				json.dump(raw_whitelist, outfile)		
			########################   Policy Builder  ########################
			policy_builder = get_policy_builder (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"policy_builder.txt", "w") as outfile:
				json.dump(policy_builder, outfile)

			########################   CSRF URLs  ########################
			raw_csrf_urls = get_csrf_urls (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_csrf_urls.json", "w") as outfile:
				json.dump(raw_csrf_urls, outfile)
			########################   History_revisions  ########################
			raw_history_revisions = get_history_revisions (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_history_revisions.json", "w") as outfile:
				json.dump(raw_history_revisions, outfile)							
			
			########################   Web Scapring  ########################
			raw_web_scraping = get_web_scraping (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_web_scraping.json", "w") as outfile:
				json.dump(raw_web_scraping, outfile)							
			
			########################   CSRF Protection  ########################
			raw_csrf_protection = get_csrf_protection (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_csrf_protection.json", "w") as outfile:
				json.dump(raw_csrf_protection, outfile)							
			
			########################   SESSION_TRACKING  ########################
			session_tracking, raw_session_tracking = get_session_tracking (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"session_tracking.txt", "w") as outfile:
				json.dump(session_tracking, outfile)				
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_session_tracking.json", "w") as outfile:
				json.dump(raw_session_tracking, outfile)
				
			########################  REDIRECTION PROTECTION   ########################
			raw_redirection_protection = get_redirection_protection (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_redirection_protection.json", "w") as outfile:
				json.dump(raw_redirection_protection, outfile)

			########################   RESPONSE PAGE  ########################
			response_pages, raw_response_pages =  get_response_pages(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"response_pages.txt", "w") as outfile:
				json.dump(response_pages, outfile)												
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_response_pages.json", "w") as outfile:
				json.dump(raw_response_pages, outfile)	
				
			########################   PALIN TEXT  ########################
			raw_plain_text_profiles =  get_plain_text_profiles(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_plain_text_profiles.json", "w") as outfile:
				json.dump(raw_plain_text_profiles, outfile)										
				
			######################## SERVER TECHNOLOGIES    ########################
			raw_server_technologies =  get_server_technologies(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_server_technologies.json", "w") as outfile:
				json.dump(raw_server_technologies, outfile)										
			
			########################   raw_sensitive_parameters  ########################
			sensitive_param, raw_sensitive_parameters =  get_sensitive_parameters(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"sensitive_param.txt", "w") as outfile:
				json.dump(sensitive_param, outfile)	
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_sensitive_parameters.json", "w") as outfile:
				json.dump(raw_sensitive_parameters, outfile)										
			
			#######################   raw_character_sets  ########################
			raw_character_sets =  get_character_sets(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_character_sets.json", "w") as outfile:
				json.dump(raw_character_sets, outfile)										
			
			########################  raw_json_profiles   ########################
			raw_json_profiles = get_json_profiles (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_json_profiles.json", "w") as outfile:
				json.dump(raw_json_profiles, outfile)									
				
			######################## raw_xml_profiles ########################
			raw_xml_profiles = get_xml_profiles (bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"raw_xml_profiles.json", "w") as outfile:
				json.dump(raw_xml_profiles, outfile)										
				
			########################  DISALLOWED LOCATION   ########################
			disallowed_geolocations =  get_disallowed_geolocations(bigip, asm_policy['id'], username, password)
			with open(customer_name+"/"+asm_policy['name']+"/"+"disallowed_geolocations.txt", "w") as outfile:
				json.dump(disallowed_geolocations, outfile)										

			########################  Suggestions ########################
			suggestions, results = analyze_policy (overview, allowed_responses, file_types_allowed, urls, parameters, signatures_overview, signature_sets, methods, headers, cookies, domains, ipi, ipi_categories, blocking_settings, compliance, evasions, whitelist, policy_builder, sensitive_param)	
			#with open(customer_name+"/"+asm_policy['name']+"/"+"suggestions.txt", "w") as outfile:
			#	json.dump(suggestions, outfile)
			with open(customer_name+"/"+asm_policy['name']+"/"+"results.txt", "w") as outfile:
				json.dump(results, outfile)				