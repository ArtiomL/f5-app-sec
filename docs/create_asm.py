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

from documention_asm import *		

document = Document("ASM_Template.docx")	

document.add_heading("ASM Configuration", level=1)
document.save("reports/F5 ASM - Config Review.docx")
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
