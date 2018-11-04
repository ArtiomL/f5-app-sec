#!/usr/bin/env python3
from docx import Document
from docx.shared import Inches
from docx.shared import Cm
from docx.shared import RGBColor
import datetime
import xlsxwriter
import requests
import json
import getpass

from documention_ltm_asm import *		

document = Document("ASM_Template.docx")	
########################					########################
########################	LTM Report		########################
########################					########################	

########################	GET device details	########################
with open("config_files/device_details.txt", "r") as infile:
	device_details = json.load(infile)
########################	GET provisioned modules	########################
with open("config_files/provisioned_modules.txt", "r") as infile:
	provisioned_modules = json.load(infile)
########################	GET Self IPs 	########################
with open("config_files/self_ips.txt", "r") as infile:
	self_ips = json.load(infile)		
########################	GET Virtuals	 	#######################
with open("config_files/virtual_servers.txt", "r") as infile:
	virtual_servers = json.load(infile)	
########################	GET Pools		########################
with open("config_files/pools.txt", "r") as infile:
	pools = json.load(infile)
######################## 	GET  Peristence		########################
with open("config_files/monitor.txt", "r") as infile:
	monitor = json.load(infile)
########################	GET Monitors		########################
with open("config_files/persistence.txt", "r") as infile:
	persistence = json.load(infile)
########################	GET Profiles		########################
with open("config_files/profile.txt", "r") as infile:
	profile = json.load(infile)
########################	GET Certificates		########################
with open("config_files/ssl_cert.txt", "r") as infile:
	ssl_cert = json.load(infile)
########################	GET Partitions		########################
with open("config_files/partitions.txt", "r") as infile:
	partitions = json.load(infile)
########################	GET Route Domains ########################
with open("config_files/route_domain.txt", "r") as infile:
	route_domain = json.load(infile)
########################	GET VLANs	########################
with open("config_files/vlans.txt", "r") as infile:
	vlans = json.load(infile)
########################	GET Routes ########################
with open("config_files/routes.txt", "r") as infile:
	routes = json.load(infile)
########################	GET Trunk	########################
with open("config_files/trunk.txt", "r") as infile:
	trunk = json.load(infile)
########################	GET iRules	########################
with open("config_files/rules.txt", "r") as infile:
	rules = json.load(infile)

########################	GET Suggestions	########################
if os.path.exists("config_files/suggestions.txt"):
	with open("config_files/suggestions.txt", "r") as infile:
		suggestions = json.load(infile)
else:
	suggestions = []
	
	
	
create_excel_ltm("reports/LTM-Audit.xlsx", partitions, route_domain, device_details, provisioned_modules, self_ips, virtual_servers, pools, persistence, monitor, profile, ssl_cert, trunk, routes, vlans, rules)


word_ltm_overview(document, device_details, suggestions)
word_ltm_modules(document, provisioned_modules)
word_ltm_ha(document, device_details, suggestions)
word_ltm_self_ips(document, self_ips, suggestions)
word_ltm_virtual_servers(document, virtual_servers, suggestions)
word_ltm_pools(document, pools, suggestions)
word_ltm_persistence(document, persistence, suggestions)
word_ltm_monitors(document, monitor, suggestions)
word_ltm_profiles(document, profile, suggestions)
word_ltm_certs(document, ssl_cert, suggestions)
word_ltm_partitions(document, partitions, suggestions)
word_ltm_route_domains(document, route_domain, suggestions)
word_ltm_vlans(document, vlans, suggestions)
word_ltm_routes(document, routes, suggestions)
word_ltm_trunk(document, trunk, suggestions)
word_ltm_irules(document, rules, suggestions)



document.add_heading("ASM Configuration", level=1)
document.save("reports/F5 LTM-ASM Config Review.docx")

########################					########################
########################	ASM Report		########################
########################					########################
items = os.listdir("config_files")
newlist = []
for names in items:
	if(os.path.isdir(os.getcwd()+"/config_files/"+names)):
		########################  Overview  ########################
		with open(os.getcwd()+"/config_files/"+names+"/overview.txt", "r") as infile:
			overview = json.load(infile)

		########################  Allowed Responses  ########################
		with open(os.getcwd()+"/config_files/"+names+"/allowed_responses.txt", "r") as infile:
			allowed_responses = json.load(infile)			

		########################  File Types  ########################
		with open(os.getcwd()+"/config_files/"+names+"/file_types_allowed.txt", "r") as infile:
			file_types = json.load(infile)			

		########################  GET URLs  ########################
		with open(os.getcwd()+"/config_files/"+names+"/urls.txt", "r") as infile:
			urls = json.load(infile)			

		########################  GET Parameters  ########################
		with open(os.getcwd()+"/config_files/"+names+"/parameters.txt", "r") as infile:
			parameters = json.load(infile)			
			
		########################  GET Signatures overview ########################
		with open(os.getcwd()+"/config_files/"+names+"/signatures_overview.txt", "r") as infile:
			signatures_overview = json.load(infile)			

		########################  GET Signature Sets  ########################
		with open(os.getcwd()+"/config_files/"+names+"/signature_sets.txt", "r") as infile:
			signature_sets = json.load(infile)			

		###################		  	GET Methods 		########################
		with open(os.getcwd()+"/config_files/"+names+"/methods.txt", "r") as infile:
			methods = json.load(infile)			

		########################  GET Headers  ########################
		with open(os.getcwd()+"/config_files/"+names+"/headers.txt", "r") as infile:
			headers = json.load(infile)			
	
		########################  GET Cookies  ########################
		with open(os.getcwd()+"/config_files/"+names+"/cookies.txt", "r") as infile:
			cookies = json.load(infile)			

		########################  GET Redirection Domains  ########################
		with open(os.getcwd()+"/config_files/"+names+"/domains.txt", "r") as infile:
			domains = json.load(infile)			
		
		########################  GET IP Intelligence  ########################
		with open(os.getcwd()+"/config_files/"+names+"/ipi.txt", "r") as infile:
			ipi = json.load(infile)			

		with open(os.getcwd()+"/config_files/"+names+"/ipi_categories.txt", "r") as infile:
			ipi_categories = json.load(infile)			

			
		########################  GET Blocking Settings  ########################
		with open(os.getcwd()+"/config_files/"+names+"/blocking_settings.txt", "r") as infile:
			blocking_settings = json.load(infile)			

		########################  GET Compliance Settings  ########################
		with open(os.getcwd()+"/config_files/"+names+"/compliance.txt", "r") as infile:
			compliance = json.load(infile)			

		########################  GET Evasions Settings  ########################
		with open(os.getcwd()+"/config_files/"+names+"/evasions.txt", "r") as infile:
			evasions = json.load(infile)			
			
		########################  GET WhiteList IPs ########################
		with open(os.getcwd()+"/config_files/"+names+"/whitelist.txt", "r") as infile:
			whitelist = json.load(infile)			
		
		########################  GET Policy Builder  ########################
		with open(os.getcwd()+"/config_files/"+names+"/policy_builder.txt", "r") as infile:
			policy_builder = json.load(infile)			

		########################  GET Results  ########################
		with open(os.getcwd()+"/config_files/"+names+"/results.txt", "r") as infile:
			results = json.load(infile)	
		########################  GET Sensitive Parameters  ########################
		with open(os.getcwd()+"/config_files/"+names+"/sensitive_param.txt", "r") as infile:
			sensitive_param = json.load(infile)	
			
		########################  GET Geolocation  ########################
		with open(os.getcwd()+"/config_files/"+names+"/disallowed_geolocations.txt", "r") as infile:
			disallowed_geolocations = json.load(infile)		

		########################  GET Suggestions  ########################
		if os.path.exists(os.getcwd()+"/config_files/"+names+"/suggestions.txt"):
			with open(os.getcwd()+"/config_files/"+names+"/suggestions.txt", "r") as infile:
				suggestions = json.load(infile)		
		else:
			suggestions = []
				
		########################  Create Excel Files  ########################
		create_asm_excel_file ("reports/"+names,  overview, allowed_responses, file_types, urls, parameters, signatures_overview, signature_sets, methods, headers, cookies, domains, ipi, ipi_categories, blocking_settings, compliance, evasions, whitelist, policy_builder)

		########################  Create Word Files  ########################
		word_file_results (document, results, overview, suggestions)
		
		word_file_overview (document, overview, suggestions)
		
		word_file_learning (document, policy_builder, whitelist, suggestions)
		
		word_file_whitelist (document, blocking_settings, whitelist, suggestions)
		
		word_file_compliance (document, blocking_settings, compliance,suggestions)
		
		word_file_evasion (document, blocking_settings, evasions,suggestions)	
		
		word_file_signatures (document, signatures_overview, signature_sets, urls, headers, parameters, cookies, suggestions)
		
		word_file_types (document, file_types, blocking_settings, policy_builder, suggestions)
		
		word_file_urls (document, overview, urls, blocking_settings, policy_builder, suggestions)
		
		word_file_parameters (document, parameters, blocking_settings, policy_builder, sensitive_param, suggestions)
		
		word_file_headers (document, overview, headers, blocking_settings, suggestions)
		
		word_file_cookies (document, overview, cookies, blocking_settings, policy_builder, suggestions)
		
		word_file_ipi (document, blocking_settings, ipi, ipi_categories, suggestions)
		
		word_file_redirection (document, blocking_settings, domains, suggestions)
		
		word_file_methods (document, blocking_settings, methods, suggestions)

		word_file_response (document, blocking_settings, allowed_responses, suggestions)
		
		word_file_geolocation (document, blocking_settings, disallowed_geolocations, suggestions)
		
print("ok");