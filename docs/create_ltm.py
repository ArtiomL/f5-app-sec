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

from documention_ltm import *		

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

	
	
create_excel_ltm("reports/LTM Audit.xlsx", partitions, route_domain, device_details, provisioned_modules, self_ips, virtual_servers, pools, persistence, monitor, profile, ssl_cert, trunk, routes, vlans, rules)


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

print("ok");