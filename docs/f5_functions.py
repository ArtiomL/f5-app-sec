#!/usr/bin/env python3
import os
import datetime
import requests
from requests.auth import HTTPDigestAuth
import json
import getpass

requests.packages.urllib3.disable_warnings() 

		
########################					########################
########################	LTM Functions	########################
########################					########################

def get_device_details(my_bigip, my_user, my_pass):
	device_details = {}
	
	###### GET Version of Appliance   #####
	url = "https://" + my_bigip + "/mgmt/tm/cm/device"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_device = jData
		devices=len(jData['items'])

		for key in jData['items']:

			if ('mirrorIp' in key and key['mirrorIp'] != "any6"):
				mirrorIP = key['mirrorIp']
			else:
				mirrorIP = "none"

			if ('mirrorSecondaryIp' in key and key['mirrorSecondaryIp'] != "any6"):
				mirrorSecondaryIp = key['mirrorSecondaryIp']
			else:
				mirrorSecondaryIp = "none"

			failoverState = key['failoverState']
			managementIp = key['managementIp']
			configsyncIp = key['configsyncIp']
			chassisId = key['chassisId']
			activeModules = key['activeModules']
			hostname = key['hostname']
			marketingName = key['marketingName']		
			timeZone = key['timeZone']
			version = key['version']
			optionalModules = key['optionalModules']
			
			if ('unicastAddress' in key ):
				if(len(key['unicastAddress'])==1):	
					unicastAddress = key['unicastAddress'][0]['ip']
				else:
					unicastAddress = key['unicastAddress'][0]['ip'] + ", " + key['unicastAddress'][1]['ip']
			else:
				unicastAddress = "none"
			
			if (managementIp == my_bigip):
				break    #exit the loop when we read the stats from the intended device

	else:
	  # If response code is not ok (200), print the resulting http error code with description
		myResponse.raise_for_status()
		

		
	###   		GET DNS Configuration  			###
	url = "https://" + my_bigip + "/mgmt/tm/sys/dns"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_dns = jData	
		if ('nameServers' in jData ):
			if(len(jData['nameServers'])==1):	
				nameServers = jData['nameServers'][0]
			else:
				nameServers = jData['nameServers'][0] + ", " + jData['nameServers'][1]
		else:
			nameServers = "none"
			
	else:
	  # If response code is not ok (200), print the resulting http error code with description
		myResponse.raise_for_status()
		


	###   		GET NTP Configuration  			###
	url = "https://" + my_bigip + "/mgmt/tm/sys/ntp"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_ntp = jData	
		if ('servers' in jData ):
			if(len(jData['servers'])==1):	
				ntpServers = jData['servers'][0]
			else:
				ntpServers = jData['servers'][0] + ", " + jData['servers'][1]
		else:
			ntpServers = "none"
			
	else:
	  # If response code is not ok (200), print the resulting http error code with description
		myResponse.raise_for_status()
		

		
	###   		GET SYSLOG Configuration  			###
	url = "https://" + my_bigip + "/mgmt/tm/sys/syslog"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_syslog = jData	
		if ('remoteServers' in jData ):
			if(len(jData['remoteServers'])==1):	
				remoteServers = jData['remoteServers'][0]['host'] + ":" + str(jData['remoteServers'][0]['remotePort'])
			else:
				remoteServers = jData['remoteServers'][0]['host'] + ":" + str(jData['remoteServers'][0]['remotePort']) + ", " + jData['remoteServers'][1]['host'] + ":" + str(jData['remoteServers'][1]['remotePort'])
		else:
			remoteServers = "none"
			
	else:
		myResponse.raise_for_status()
		exit()
		
	device_details = {'mirrorIP':mirrorIP, 'unicastAddress': unicastAddress, 'mirrorSecondaryIp':mirrorSecondaryIp, 'failoverState': failoverState,'managementIp':managementIp,'configsyncIp':configsyncIp,'chassisId':chassisId,'activeModules':activeModules,'hostname':hostname,'marketingName':marketingName,'timeZone':timeZone,'version':version,'optionalModules':optionalModules, 'nameServers':nameServers, 'ntpServers':ntpServers, 'remoteServers':remoteServers}


	return device_details, raw_device, raw_dns, raw_ntp, raw_syslog

def get_provisioned_modules(my_bigip, my_user, my_pass):
	provisioned_modules = {}
	url = "https://" + my_bigip + "/mgmt/tm/sys/provision"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_provision = jData
		ltm = "none"
		asm = "none"
		apm = "none"
		gtm = "none"
		afm = "none"
		avr = "none"		
		for key in jData['items']:

			if (key['name'] == "ltm"):
				ltm = key['level']
			if (key['name'] == "asm"):
				asm = key['level']
			if (key['name'] == "apm"):
				apm = key['level']
			if (key['name'] == "gtm"):
				gtm = key['level']
			if (key['name'] == "afm"):
				afm = key['level']
			if (key['name'] == "avr"):
				avr = key['level']

			
	else:
		myResponse.raise_for_status()
		exit()

		
	provisioned_modules = {'ltm':ltm, 'asm':asm, 'apm':apm, 'gtm':gtm, 'afm':afm, 'avr':avr} 
		
	return provisioned_modules, raw_provision
		
def get_self_ips(my_bigip, my_user, my_pass):
	self_ips = {}
	self_ips = []		
		
	url = "https://" + my_bigip + "/mgmt/tm/net/self"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_self = 	jData
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			address = key['address']
			vlan = key['vlan']
			floating = key['floating']
			if ('allowService' in key ):
				allowService = key['allowService']
			else:
				allowService = "none"
			self_ips.append({'name':name, 'partition':partition, 'address':address, 'vlan':vlan, 'floating':floating, 'allowService':allowService}) 
			
	else:
		myResponse.raise_for_status()

	return self_ips, raw_self

def get_virtual_servers(my_bigip, my_user, my_pass):
	virtual_servers = {}
	virtual_servers = []
	url = "https://" + my_bigip + "/mgmt/tm/ltm/virtual"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_virtual = jData
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			if ('subPath' in key ):
				subPath = key['subPath']
				path = "~"+partition+"~"+subPath+"~"+name
			else:
				subPath = ""	
				path = "~"+partition+"~"+name

			destination = key['destination']
			if ('enabled' in key):
				enabled = "True"
			else:
				enabled = "False"

			mask = key['mask']
			if ('pool' in key):
				pool = key['pool']
			else:
				pool = "none"
			source = key['source']
			synCookieStatus = key['synCookieStatus']
			
			if ('vlan' in key):
				vlan = key['vlan']
				if ('vlansEnabled' in key):
					vlanStatus = "Disabled for all VLANs"
				if ('vlansDisabled' in key):
					vlanStatus = "Enabled for all VLANs"
			else:
				vlan = "-"
				if ('vlansEnabled' in key):
					vlanStatus = "Disabled for all VLANs"
				if ('vlansDisabled' in key):
					vlanStatus = "Enabled for all VLANs"
			
			if ('persist' in key):
				persistence = key['persist'][0]['name']
			else:
				persistence = "none"


			if ('rules' in key):
				rules = key['rules']
			else:
				rules = ["none"]

			rateLimit = key['rateLimit']
			ipProtocol = key['ipProtocol']
			if ('throughputCapacity' in key):
				throughputCapacity = key['throughputCapacity']
			else:
				throughputCapacity = "0"
			serviceDownImmediateAction = key['serviceDownImmediateAction']
			sourcePort = key['sourcePort']
			translateAddress = key['translateAddress']
			translatePort = key['translatePort']
			autoLasthop = key['autoLasthop']
		
			if ('securityLogProfiles' in key):
				securityLogProfiles = key['securityLogProfiles']
			else:
				securityLogProfiles = ["none"]
				
			if ('flowEvictionPolicy' in key):
				flowEvictionPolicy = key['flowEvictionPolicy']
			else:
				flowEvictionPolicy = "none"
				
			url = "https://" + my_bigip + "/mgmt/tm/ltm/virtual/"+path+"/profiles"
			response_profiles = requests.get(url, auth=(my_user, my_pass) , verify=False)
			if(response_profiles.ok):
				
				profileName= []
				jData_profiles = json.loads(response_profiles.content.decode('utf-8'))
				for key in jData_profiles['items']:
					profileName.append(key['name'])
				
			else:
				response_profiles.raise_for_status()	
				
			url = "https://" + my_bigip + "/mgmt/tm/ltm/virtual/"+path+"/policies"
			response_policies = requests.get(url, auth=(my_user, my_pass) , verify=False)
			if(response_policies.ok):
				
				policyName = []
				jData_policies = json.loads(response_policies.content.decode('utf-8'))
				if (len(jData_policies['items'])>0):
					for key in jData_policies['items']:
						policyName.append(key['name'])
				else:
					policyName = ["none"]
					
			else:
				response_policies.raise_for_status()				
			
			virtual_servers.append({'name':name, 'partition':partition, 'subPath':subPath, 'destination':destination, 'mask':mask,'pool':pool,'source':source,'enabled':enabled, 'policyName':policyName,'synCookieStatus':synCookieStatus,'vlan':vlan,'vlanStatus':vlanStatus,'persistence':persistence,'profileName':profileName, 'rules':rules, 	'flowEvictionPolicy':flowEvictionPolicy, 'serviceDownImmediateAction':serviceDownImmediateAction, 'sourcePort':sourcePort, 'securityLogProfiles':securityLogProfiles, 'autoLasthop':autoLasthop, 'translateAddress':translateAddress, 'translatePort':translatePort, 'throughputCapacity':throughputCapacity, 'ipProtocol':ipProtocol, 'rateLimit':rateLimit}) 

				
	else:
	  # If response code is not ok (200), print the resulting http error code with description
		myResponse.raise_for_status()

	return virtual_servers, raw_virtual

def get_pools(my_bigip, my_user, my_pass):
	pools = {}
	pools = []
	url = "https://" + my_bigip + "/mgmt/tm/ltm/pool"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_pool = 	jData
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			if ('subPath' in key ):
				subPath = key['subPath']
				path = "~"+partition+"~"+subPath+"~"+name
			else:
				subPath = ""	
				path = "~"+partition+"~"+name

			if ('monitor' in key ):
				pool_monitor = key['monitor']
			else: 
				pool_monitor = "none"
				
			loadBalancingMode = key['loadBalancingMode']

			
			
			url = "https://" + my_bigip + "/mgmt/tm/ltm/pool/"+path+"/members"
			response_members = requests.get(url, auth=(my_user, my_pass) , verify=False)
			if(response_members.ok):
				jData_members = json.loads(response_members.content.decode('utf-8'))
				
				members = []
				if (len(jData_members['items'])>0):
					for key in jData_members['items']:
						members.append(key['name'])
				else:
					members = "none"
				
			else:
				response_members.raise_for_status()	
			pools.append({'name':name, 'partition':partition, 'subPath':subPath, 'pool_monitor':pool_monitor, 'loadBalancingMode':loadBalancingMode, 'members':members}) 

				

				
	else:
		myResponse.raise_for_status()

	return pools, raw_pool

def get_ssl_cert(my_bigip, my_user, my_pass):
	ssl_cert = {}
	ssl_cert = []
	url = "https://" + my_bigip + "/mgmt/tm/sys/file/ssl-cert"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_ssl_cert = jData
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			createTime = key['createTime']
			createdBy = key['createdBy']
			expirationString = key['expirationString']
				
			ssl_cert.append({'name':name, 'partition':partition, 'createTime':createTime, 'createdBy':createdBy, 'expirationString':expirationString}) 

	else:
		myResponse.raise_for_status()

	return ssl_cert, raw_ssl_cert

def get_monitor(my_bigip, my_user, my_pass):
	monitor = {}
	monitor['http'] = []
	monitor['https'] = []
	monitor['other'] = []

	url = "https://" + my_bigip + "/mgmt/tm/ltm/monitor/http"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_monitor_http = jData
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			interval = key['interval']
			timeout = key['timeout']
			if ('send' in key):
				send = key['send']
			else:
				send = "none"
			if ('recv' in key):
				recv = key['recv']
			else:
				recv = "none"
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
				
			monitor['http'].append({'name':name, 'partition':partition, 'interval':interval, 'defaultsFrom':defaultsFrom, 'recv':recv, 'send':send, 'timeout':timeout}) 
		
		
	else:
		myResponse.raise_for_status()

	url = "https://" + my_bigip + "/mgmt/tm/ltm/monitor/https"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)
	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_monitor_https = jData
		
		for key in jData['items']:
			
			name = key['name']
			partition = key['partition']
			interval = key['interval']
			timeout = key['timeout']
			send = key['send']
			if ('send' in key):
				send = key['send']
			else:
				send = "none"
			if ('recv' in key):
				recv = key['recv']
			else:
				recv = "none"
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
				
			monitor['https'].append({'name':name, 'partition':partition, 'interval':interval, 'defaultsFrom':defaultsFrom, 'recv':recv, 'send':send, 'timeout':timeout}) 
			

	else:
		myResponse.raise_for_status()

		
	url = "https://" + my_bigip + "/mgmt/tm/ltm/monitor/icmp"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_monitor_icmp = jData
			
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			interval = key['interval']
			timeout = key['timeout']
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
				
			monitor['other'].append({'name':name, 'partition':partition, 'interval':interval, 'defaultsFrom':defaultsFrom,'timeout':timeout, 'proto': 'ICMP'}) 
			

	else:
		myResponse.raise_for_status()
	


	url = "https://" + my_bigip + "/mgmt/tm/ltm/monitor/gateway-icmp"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_monitor_gateway_icmp = jData	
			
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			interval = key['interval']
			timeout = key['timeout']
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
				
			monitor['other'].append({'name':name, 'partition':partition, 'interval':interval, 'defaultsFrom':defaultsFrom,'timeout':timeout, 'proto': 'Gateway ICMP'}) 
			

	else:
		myResponse.raise_for_status()



	url = "https://" + my_bigip + "/mgmt/tm/ltm/monitor/tcp"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_monitor_tcp = jData	
			
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			interval = key['interval']
			timeout = key['timeout']
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
				
			monitor['other'].append({'name':name, 'partition':partition, 'interval':interval, 'defaultsFrom':defaultsFrom,'timeout':timeout, 'proto': 'TCP'}) 
			

	else:
		myResponse.raise_for_status()


			
	url = "https://" + my_bigip + "/mgmt/tm/ltm/monitor/tcp-half-open"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_monitor_tcp_half_open = jData	
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			interval = key['interval']
			timeout = key['timeout']
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
				
			monitor['other'].append({'name':name, 'partition':partition, 'interval':interval, 'defaultsFrom':defaultsFrom,'timeout':timeout, 'proto': 'TCP Half Open'}) 
			

	else:
		myResponse.raise_for_status()
		
		
	return monitor, raw_monitor_http, raw_monitor_https, raw_monitor_tcp, raw_monitor_icmp, raw_monitor_tcp_half_open, raw_monitor_gateway_icmp
	
def get_persistence(my_bigip, my_user, my_pass):
	persistence = {}
	persistence['cookie'] = []
	persistence['source'] = []
	url = "https://" + my_bigip + "/mgmt/tm/ltm/persistence/cookie"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_persistence_cookie = jData	
			
		for key in jData['items']:
			name = key['name']
			partition = key['partition']
			cookieEncryption = key['cookieEncryption']
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
			if ('cookieName' in key):
				cookieName = key['cookieName']
			else:
				cookieName = "none"			
			if ('encryptCookiePoolname' in key):
				encryptCookiePoolname = key['encryptCookiePoolname']
			else:
				encryptCookiePoolname = "none"	
			persistence['cookie'].append({'name':name, 'partition':partition, 'cookieEncryption':cookieEncryption, 'defaultsFrom':defaultsFrom, 'cookieName':cookieName, 'encryptCookiePoolname':encryptCookiePoolname}) 
		
		
	else:
		myResponse.raise_for_status()

		
	url = "https://" + my_bigip + "/mgmt/tm/ltm/persistence/source-addr"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_persistence_source_addr = jData	
		
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			mask = key['mask']
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"		
			mirror = key['mirror']
			timeout = key['timeout']
			matchAcrossPools = key['matchAcrossPools']
			matchAcrossVirtuals = key['matchAcrossVirtuals']
			
			persistence['source'].append({'name':name, 'partition':partition, 'mask':mask, 'defaultsFrom':defaultsFrom, 'mirror':mirror, 'matchAcrossPools':matchAcrossPools, 'matchAcrossVirtuals':matchAcrossVirtuals, 'timeout':timeout}) 
		
		
	else:
		myResponse.raise_for_status()

	return persistence, raw_persistence_cookie, raw_persistence_source_addr
	
def get_profile(my_bigip, my_user, my_pass):

	profile = {}
	profile['http'] = []
	profile['tcp'] = []
	profile['ssl'] = []


	url = "https://" + my_bigip + "/mgmt/tm/ltm/profile/http"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_profile_http = jData	
			
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			if ('encryptCookies' in key):
				encryptCookies = key['encryptCookies']
			else:
				encryptCookies = []

			if ('fallbackHost' in key):
				fallbackHost = key['fallbackHost']
			else:
				fallbackHost = "none"

			if ('insertXforwardedFor' in key):
				xff = key['insertXforwardedFor']
			else:
				xff = "none"
			hsts = key['hsts']['mode']
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"

			profile['http'].append({'name':name, 'partition':partition, 'encryptCookies':encryptCookies, 'fallbackHost':fallbackHost, 'defaultsFrom':defaultsFrom,'xff':xff, 'hsts': hsts}) 
			

	else:
		myResponse.raise_for_status()

						
	url = "https://" + my_bigip + "/mgmt/tm/ltm/profile/tcp"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_profile_tcp = jData	
			
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			idleTimeout = key['idleTimeout']
			keepAliveInterval = key['keepAliveInterval']
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
				
			profile['tcp'].append({'name':name, 'partition':partition, 'idleTimeout':idleTimeout, 'keepAliveInterval':keepAliveInterval, 'defaultsFrom':defaultsFrom}) 
			

	else:
		myResponse.raise_for_status()

						
						
	url = "https://" + my_bigip + "/mgmt/tm/ltm/profile/client-ssl"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_profile_client_ssl = jData	
			
		for key in jData['items']:

			name = key['name']
			partition = key['partition']
			ciphers = key['ciphers']
			cert_key = key['key']
			cert  = key['cert']
			if ('cipherGroup' in key):
				cipherGroup = key['cipherGroup']
			else:
				cipherGroup = "none"
			if ('defaultsFrom' in key):
				defaultsFrom = key['defaultsFrom']
			else:
				defaultsFrom = "none"
				
			profile['ssl'].append({'name':name, 'partition':partition, 'cert':cert, 'key':cert_key, 'ciphers':ciphers, 'cipherGroup':cipherGroup, 'defaultsFrom':defaultsFrom}) 
			

	else:
		myResponse.raise_for_status()

	return profile, raw_profile_http, raw_profile_tcp, raw_profile_client_ssl
	
def get_partitions(my_bigip, my_user, my_pass):
	partitions = {}
	partitions = []
	url = "https://" + my_bigip + "/mgmt/tm/auth/partition"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_partition = jData	

		for key in jData['items']:

			name = key['name']
			defaultRouteDomain = str(key['defaultRouteDomain'])
			
			partitions.append({'name':name, 'defaultRouteDomain':defaultRouteDomain}) 

	else:
		myResponse.raise_for_status()

	return partitions, raw_partition
	
def get_route_domains(my_bigip, my_user, my_pass):
	route_domains = {}
	route_domains = []
	url = "https://" + my_bigip + "/mgmt/tm/net/route-domain"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_route_domain = jData	
			
		for key in jData['items']:
			name = key['name']
			partition = key['partition']
			connectionLimit = key['connectionLimit']
			strict = key['strict']
			if ('throughputCapacity' in key):
				throughputCapacity = key['throughputCapacity']
			else:
				throughputCapacity = "0"
			id = key['id']
			if ('flowEvictionPolicy' in key):
				flowEvictionPolicy = key['flowEvictionPolicy']
			else:
				flowEvictionPolicy = "none"
			if ('vlans' in key):
				vlans = key['vlans']
			else:
				vlans = "none"
			if ('parent' in key):
				parent = key['parent']
			else:
				parent = "none"				
			route_domains.append({'name':name, 'partition':partition,'connectionLimit':connectionLimit, 'strict':strict, 'throughputCapacity':throughputCapacity, 'vlans':vlans, 'id':id, 'parent':parent, 'flowEvictionPolicy':flowEvictionPolicy}) 

	else:
		myResponse.raise_for_status()

	return route_domains, raw_route_domain
		
def get_vlans(my_bigip, my_user, my_pass):
	vlans = {}
	vlans = []
	url = "https://" + my_bigip + "/mgmt/tm/net/vlan"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_vlan = jData	
			
		for key in jData['items']:
			name = key['name']
			partition = key['partition']
			autoLasthop = key['autoLasthop']
			failsafe = key['failsafe']
			failsafeAction = key['failsafeAction']
			failsafeTimeout = key['failsafeTimeout']
			mtu = key['mtu']
			temp_url = key['interfacesReference']['link'].replace("localhost", my_bigip)
			tempResponse = requests.get(temp_url, auth=(my_user, my_pass) , verify=False)
			print (temp_url)
			print (tempResponse.status_code)							
			if(tempResponse.ok):
				temp_jData = json.loads(tempResponse.content.decode('utf-8'))
				interfaces = "";
				if ('items' in temp_jData):
					for temp_key in temp_jData['items']:
						interfaces += temp_key['name'] + " "
				else:
					interfaces ='None'
			else:
				tempResponse.raise_for_status()
			
			vlans.append({'name':name, 'partition':partition,'autoLasthop':autoLasthop, 'failsafe':failsafe, 'failsafeAction':failsafeAction, 'failsafeTimeout':failsafeTimeout, 'mtu':mtu, 'interfaces':interfaces}) 

	else:
		myResponse.raise_for_status()

	return vlans, raw_vlan

def get_routes(my_bigip, my_user, my_pass):
	routes = {}
	routes = []
	url = "https://" + my_bigip + "/mgmt/tm/net/route"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_route = jData
		if 'items' in jData:	
			for key in jData['items']:
				type = "-"
				name = key['name']
				partition = key['partition']
				mtu = key['mtu']
				network = key['network']
				if 'gw' in key:
					resource = key['gw']
					type = "Gateway"
				
				if 'blackhole' in key:
					type = "Reject"
					resource = "None"

				if 'pool' in key:
					type = "Pool"
					resource = key['Pool']

				if 'pool' in key:
					type = "VLAN"
					resource = key['tmInterface']
					
				routes.append({'name':name, 'partition':partition, 'type':type, 'resource':resource, 'mtu':mtu, 'network':network}) 

	else:
		myResponse.raise_for_status()

	return routes, raw_route

def get_trunk(my_bigip, my_user, my_pass):
	trunk = {}
	trunk = []
	url = "https://" + my_bigip + "/mgmt/tm/net/trunk"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_trunk = jData
		if 'items' in jData:
			for key in jData['items']:
				name = key['name']
				distributionHash = key['distributionHash']
				lacp = key['lacp']
				media = key['media']
				interfaces = key['interfaces']
				
				trunk.append({'name':name, 'distributionHash':distributionHash, 'lacp':lacp, 'media':media, 'interfaces':interfaces}) 


	else:
		myResponse.raise_for_status()

	return trunk, raw_trunk

def get_irules(my_bigip, my_user, my_pass):
	rules = {}
	rules = []
	url = "https://" + my_bigip + "/mgmt/tm/ltm/rule"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)	
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		if 'items' in jData:
			for key in jData['items']:
				name = key['name']
				partition = key['partition']
				apiAnonymous = key['apiAnonymous']
				
				rules.append({'name':name, 'partition':partition, 'apiAnonymous':apiAnonymous}) 


	else:
		myResponse.raise_for_status()

	return rules


	
########################					########################
########################	ASM Functions	########################
########################					########################

def get_overview(my_bigip, my_id, my_user, my_pass):
	overview = {}

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_policy = jData
		name = jData['name']
		applicationLanguage = jData['applicationLanguage']
		if (len(jData['virtualServers'])>0):
			virtualServers = jData['virtualServers']
		else:
			virtualServers = ['None']			
		partition = jData['partition']
		if jData['caseInsensitive'] :
			caseInsensitive= "Yes"
		else:
			caseInsensitive= "No"
		enforcementMode = jData['enforcementMode']
		createdDatetime = jData['createdDatetime']
		id = jData['id']
		isModified = jData['isModified']
		if 	jData['lastUpdateMicros'] == 0:
			lastUpdateMicros = "Never"
		else:
			time_temp = datetime.datetime.fromtimestamp(jData['lastUpdateMicros']/1000000)
			lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')
		creatorName = jData['creatorName']
	else:
		myResponse.raise_for_status()
		
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/general"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_general = jData
		if jData['trustXff']:
			trustXff  = "Yes"
		else:
			trustXff  = "No"
		if jData['maskCreditCardNumbersInRequest']:
			maskCreditCardNumbersInRequest  = "Yes"
		else:
			maskCreditCardNumbersInRequest  = "No"	
		triggerAsmIruleEvent  = jData['triggerAsmIruleEvent']

		if (len(jData['allowedResponseCodes'])>0):
			allowed_responses = jData['allowedResponseCodes']
		else:
			allowed_responses = ['None']		
		if (len(jData['customXffHeaders'])>0):
			customXffHeaders = jData['customXffHeaders']
		else:
			customXffHeaders = ['None']	

	else:
		myResponse.raise_for_status()
		

	#--------------   Antivirus (ICAP)	------------- 
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/antivirus"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_antivirus = jData
		if jData['inspectHttpUploads']:
			inspectHttpUploads = "Yes"
		else:
			inspectHttpUploads = "No"		
	else:
		myResponse.raise_for_status()	

		
	#--------------   DataGuard	------------- 
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/data-guard"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_data_guard = jData
		if jData['enabled']:
			data_guard_enabled = "Yes"
		else:
			data_guard_enabled = "No"
	else:
		myResponse.raise_for_status()	

			
	#--------------   Login Pages	------------- 
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/login-pages"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_login_pages = jData
		Login_pages_totalItems = jData['totalItems']

	else:
		myResponse.raise_for_status()	

			
	#--------------   Brute Force 	------------- 
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/brute-force-attack-preventions"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_brute_force_attack_preventions = jData
		Brute_force_totalItems = jData['totalItems']
		if Brute_force_totalItems>1:
			brute_enabled = "Yes"
		else:
			brute_enabled = "No"
		for key in jData['items']:
			if 'bruteForceProtectionForAllLoginPages' in key:
				if key['bruteForceProtectionForAllLoginPages']:
					default_brute_enabled = "Yes"
					brute_enabled = "Yes"
				else:
					default_brute_enabled = "No"
				break
	else:
		myResponse.raise_for_status()	

	#--------------   HTTP Header Length 	------------- 
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/header-settings"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		maximumHttpHeaderLength = jData['maximumHttpHeaderLength']
	else:
		myResponse.raise_for_status()	


	#--------------   HTTP Cookie Length 	------------- 
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/cookie-settings"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		maximumCookieHeaderLength = jData['maximumCookieHeaderLength']
	else:
		myResponse.raise_for_status()	

		
		
	#--------------   Redirection Domain 	------------- 

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/redirection-protection"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		if jData['redirectionProtectionEnabled']:
			redirectionProtectionEnabled = "Yes"
		else:
			redirectionProtectionEnabled = "No"

	else:
		myResponse.raise_for_status()			
		
		
		
		
		
	overview = {'name':name, 'partition':partition, 'creatorName':creatorName, 'applicationLanguage':applicationLanguage, 'virtualServers':virtualServers,'caseInsensitive':caseInsensitive,'enforcementMode':enforcementMode,'createdDatetime':createdDatetime, 'lastUpdateMicros':lastUpdateMicros,'isModified':isModified,'id':id,'customXffHeaders':customXffHeaders, 'trustXff':trustXff,'triggerAsmIruleEvent':triggerAsmIruleEvent,'maskCreditCardNumbersInRequest':maskCreditCardNumbersInRequest, 'brute_enabled':brute_enabled,'default_brute_enabled':default_brute_enabled, 'Login_pages_totalItems':Login_pages_totalItems, 'data_guard_enabled':data_guard_enabled, 'inspectHttpUploads':inspectHttpUploads, 'maximumHttpHeaderLength':maximumHttpHeaderLength, 'maximumCookieHeaderLength':maximumCookieHeaderLength, 'redirectionProtectionEnabled': redirectionProtectionEnabled}

		
	return overview, allowed_responses, raw_policy, raw_general, raw_antivirus, raw_data_guard, raw_login_pages, raw_brute_force_attack_preventions
			
def get_urls(my_bigip, my_id, my_user, my_pass):
	urls = {}
	urls = []
	num_of_sign_overides = 0
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/urls"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_url = jData
		for key in jData['items']:
			if key['isAllowed']:

				type = key['type']
				name = key['name']
				time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
				lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')	
				if key['performStaging']:
					performStaging = "Yes"
				else: 
					performStaging = "No"
				protocol = key['protocol']

				if ('metacharsOnUrlCheck' in key and key['metacharsOnUrlCheck']) :
					metacharsOnUrlCheck =  "Yes"
					if (len(key['metacharOverrides'])>=1):
						metacharOverrides = str(len(key['metacharOverrides']))
					else:
						metacharOverrides ="None"
				else:
					metacharsOnUrlCheck =  "No"
					metacharOverrides ="None"
				if key['attackSignaturesCheck'] :
					attackSignaturesCheck =  "Yes"
					num_of_sign_overides = 0
					signatureOverrides = []
					if (len(key['signatureOverrides'])>=1):
						for temp_key in key['signatureOverrides']:
							num_of_sign_overides += 1
							temp_url = temp_key['signatureReference']['link'].replace("localhost", my_bigip)
							tempResponse = requests.get(temp_url, auth=(my_user, my_pass) , verify=False)
							print (temp_url)
							print (tempResponse.status_code)							
							if(tempResponse.ok):
								temp_jData = json.loads(tempResponse.content.decode('utf-8'))
								signatureOverrides.append((str(temp_jData['signatureId']) + " - " + temp_jData['name']))
							else:
								tempResponse.raise_for_status()
					else:
						signatureOverrides = ["None"]
				else:
					attackSignaturesCheck =  "No"
				urlContentProfiles = key['urlContentProfiles']
				urls.append({'name':name, 'type':type, 'protocol':protocol, 'num_of_sign_overides':num_of_sign_overides, 'signatureOverrides':signatureOverrides, 'attackSignaturesCheck':attackSignaturesCheck, 'metacharsOnUrlCheck':metacharsOnUrlCheck, 'metacharOverrides':metacharOverrides, 'lastUpdateMicros':lastUpdateMicros,'performStaging':performStaging,'urlContentProfiles':urlContentProfiles}) 


	else:
		myResponse.raise_for_status()

	return urls, raw_url

def get_parameters(my_bigip, my_id, my_user, my_pass):
	parameters = {}
	parameters = []
	num_of_sign_overides = 0
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/parameters"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_parameters = jData
		for key in jData['items']:
			num_of_sign_overides = 0
			type = key['type']
			name = key['name']
			time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
			lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')		
			if key['performStaging'] :
				performStaging =  "Yes"
			else:
				performStaging =  "No"	
			if 'valueType' in key:
				valueType = key['valueType'];
			else: 
				valueType = "N/A"
			if 'dataType' in key:
				dataType =  key['dataType'];
			else: 
				dataType = "N/A"
			if key['sensitiveParameter']:
				sensitiveParameter = "Yes"
			else: 
				sensitiveParameter = "No"
				
			if ('metacharsOnParameterValueCheck' in key and key['metacharsOnParameterValueCheck']) :
				metacharsOnParameterValueCheck =  "Yes"
				if (len(key['valueMetacharOverrides'])>=1):
					valueMetacharOverrides = str(len(key['valueMetacharOverrides']))
				else:
					valueMetacharOverrides ="None"
			else:
				metacharsOnParameterValueCheck =  "No"
				valueMetacharOverrides ="None"
			signatureOverrides = []	
			if ('attackSignaturesCheck' in key and key['attackSignaturesCheck']):
				attackSignaturesCheck =  "Yes"
				num_of_sign_overides = 0
				if (len(key['signatureOverrides'])>=1):
					for temp_key in key['signatureOverrides']:
						num_of_sign_overides += 1
						temp_url = temp_key['signatureReference']['link'].replace("localhost", my_bigip)
						tempResponse = requests.get(temp_url, auth=(my_user, my_pass) , verify=False)
						print (temp_url)
						print (tempResponse.status_code)
						if(tempResponse.ok):
							temp_jData = json.loads(tempResponse.content.decode('utf-8'))
							signatureOverrides.append((str(temp_jData['signatureId']) + " - " + temp_jData['name']))
						else:
							tempResponse.raise_for_status()
				else:
					signatureOverrides = ["None"]
			else:
				attackSignaturesCheck =  "No"
				signatureOverrides = ["None"]


			if 'urlReference' in key:
				temp_url = key['urlReference']['link'].replace("localhost", my_bigip)
				tempResponse = requests.get(temp_url, auth=(my_user, my_pass) , verify=False)
				print (temp_url)
				print (tempResponse.status_code)
				if(myResponse.ok):
					temp_jData = json.loads(tempResponse.content.decode('utf-8'))
					enforcement = temp_jData['name']
				else:
					tempResponse.raise_for_status()	
			else:
				enforcement = "Global"
			parameters.append({'name':name, 'type':type, 'valueMetacharOverrides':valueMetacharOverrides, 'metacharsOnParameterValueCheck':metacharsOnParameterValueCheck, 'sensitiveParameter':sensitiveParameter, 'signatureOverrides':signatureOverrides, 'num_of_sign_overides':num_of_sign_overides, 'attackSignaturesCheck':attackSignaturesCheck,'lastUpdateMicros':lastUpdateMicros,'performStaging':performStaging, 'enforcement': enforcement,'dataType':dataType, 'valueType':valueType}) 


	else:
		myResponse.raise_for_status()

	return parameters, raw_parameters

def get_policies(my_bigip, my_user, my_pass):
	asm_policies = {}
	asm_policies = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies?$select=name,id,type"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		if len(jData['items']) > 0:
			for key in jData['items']:
				asm_policies.append({'name':key['name'], 'id':key['id'], 'type':key['type']})
	else:
		myResponse.raise_for_status()
	
	return asm_policies

def get_file_types (my_bigip, my_id, my_user, my_pass):

	file_types_allowed = {}
	file_types_disallowed = {}
	file_types_allowed = []
	file_types_disallowed = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/filetypes"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):

		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_filetypes = jData
		for key in jData['items']:

			if key['allowed']:
				if key['checkPostDataLength'] :
					postDataLength =  key['postDataLength']
				else:
					postDataLength =  "Any"
				if key['checkUrlLength'] :
					urlLength =  key['urlLength']
				else:
					urlLength =  "Any"
				if key['checkRequestLength'] :
					requestLength =  key['requestLength']
				else:
					requestLength =  "Any"
				if key['checkQueryStringLength'] :
					queryStringLength =  key['queryStringLength']
				else:
					queryStringLength =  "Any"

				if key['performStaging'] :
					performStaging =  "Yes"
				else:
					performStaging =  "No"				

				type = key['type']
				name = key['name']
				time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
				lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')			
				file_types_allowed.append({'name':name, 'type':type, 'postDataLength':postDataLength, 'urlLength':urlLength,'requestLength':requestLength,'queryStringLength':queryStringLength,'lastUpdateMicros':lastUpdateMicros,'performStaging':performStaging}) 
			
			else:
				name = key['name']
				time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
				lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')	
				file_types_disallowed.append({'name':name, 'lastUpdateMicros':lastUpdateMicros}) 
			
			
	else:
		myResponse.raise_for_status()

	return file_types_allowed, file_types_disallowed, raw_filetypes

def get_signatures_overview (my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/signature-settings"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_signature_settings = jData
				
		if (jData['signatureStaging']):
			signatureStaging = "Yes"
		else:
			signatureStaging = "No"

		if (jData['placeSignaturesInStaging']):
			placeSignaturesInStaging = "Yes"
		else:
			placeSignaturesInStaging = "No"

	else:
		myResponse.raise_for_status()

	signatures_overview = {'placeSignaturesInStaging':placeSignaturesInStaging, 'signatureStaging': signatureStaging, 'staging':0, 'enabled':0, 'total':0}

	url = "https://" + my_bigip + "/mgmt/tm/asm/signature-statuses"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_signature_statuses = jData
		Sig_update_totalItems = jData['totalItems']
		Sig_update_totalItems -= 1
		if (Sig_update_totalItems>0):
			latest_sig_update = jData['items'][Sig_update_totalItems]['timestamp']
		else:
			latest_sig_update = "Never"

	else:
		myResponse.raise_for_status()	

	
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/signatures"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_signatures = jData
		staging = 0
		enabled = 0
		total = 0
		
		for key in jData['items']:
						
			if (key['performStaging']):
				staging += 1
			if ( not key['enabled']):
				enabled += 1
			total += 1			
		
		signatures_overview = {'placeSignaturesInStaging':placeSignaturesInStaging, 'signatureStaging': signatureStaging, 'latest_sig_update':latest_sig_update, 'staging':staging, 'enabled':enabled, 'total':total}

	else:
		myResponse.raise_for_status()

	return signatures_overview, raw_signatures, raw_signature_settings, raw_signature_statuses

def get_signature_sets (my_bigip, my_id, my_user, my_pass):
	signature_sets = { }
	signature_sets = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/signature-sets"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_signature_sets = jData
		for key in jData['items']:
						
			temp_url = key['signatureSetReference']['link'].replace("localhost", my_bigip)
			
			if  key['learn']:
				learn = "Yes"
			else:
				learn = "No"
			if  key['alarm']:
				alarm = "Yes"
			else:
				alarm = "No"
			if  key['block']:
				block = "Yes"
			else:
				block = "No"			

			time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
			lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')	
		
			tempResponse = requests.get(temp_url, auth=(my_user, my_pass) , verify=False)
			print (temp_url)
			print (tempResponse.status_code)
			if(tempResponse.ok):
				temp_jData = json.loads(tempResponse.content.decode('utf-8'))
				name = temp_jData['name']
			else:
				tempResponse.raise_for_status()
			signature_sets.append({'learn':learn, 'alarm':alarm, 'block':block,'name':name, 'lastUpdateMicros':lastUpdateMicros })

	else:
		myResponse.raise_for_status()

	return signature_sets, raw_signature_sets

def get_methods (my_bigip, my_id, my_user, my_pass):
	methods = {}
	methods = []
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/methods"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_methods = jData
		for key in jData['items']:
			time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
			lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')			
			methods.append({'name':key['name'], 'actAsMethod':key['actAsMethod'], 'lastUpdateMicros':lastUpdateMicros}) 
	else:
		myResponse.raise_for_status()
	
	return methods, raw_methods

def get_headers (my_bigip, my_id, my_user, my_pass):
	num_of_sign_overides = 0
	headers = {}
	headers = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/headers"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_headers = jData

		for key in jData['items']:
			num_of_sign_overides = 0
			type = key['type']
			name = key['name']
			
			if key['checkSignatures']:
				checkSignatures = "Yes"
			else: 
				checkSignatures = "No"
				
			time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
			lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')		
			
			percentDecoding = "No"
			htmlNormalization = "No"
			urlNormalization = "No"	
			normalizationViolations	= "No"	
			
			if checkSignatures == "Yes" :
				if key['percentDecoding']:
					percentDecoding = "Yes"
				if key['htmlNormalization']:
					htmlNormalization = "Yes"
				if key['urlNormalization']:
					urlNormalization = "Yes"
				if key['normalizationViolations']:
					normalizationViolations = "Yes"
				signatureOverrides = []
				if (len(key['signatureOverrides'])>=1):
					num_of_sign_overides = 0
					for temp_key in key['signatureOverrides']:
						num_of_sign_overides +=1
						temp_url = temp_key['signatureReference']['link'].replace("localhost", my_bigip)
						tempResponse = requests.get(temp_url, auth=(my_user, my_pass) , verify=False)
						print (temp_url)
						print (tempResponse.status_code)
						if(tempResponse.ok):
							temp_jData = json.loads(tempResponse.content.decode('utf-8'))
							signatureOverrides.append((str(temp_jData['signatureId']) + " - " + temp_jData['name']))
						else:
							tempResponse.raise_for_status()
				else:
					signatureOverrides = ["None"]
				
			headers.append({'name':name, 'type':type, 'lastUpdateMicros':lastUpdateMicros, 'checkSignatures':checkSignatures, 'percentDecoding':percentDecoding, 'htmlNormalization':htmlNormalization, 'urlNormalization':urlNormalization, 'normalizationViolations':normalizationViolations,	 'num_of_sign_overides':num_of_sign_overides,'signatureOverrides':signatureOverrides}) 
															
	else:
		myResponse.raise_for_status()	
	
	return headers, raw_headers

def get_cookies (my_bigip, my_id, my_user, my_pass):
	num_of_sign_overides = 0
	cookies = {}
	cookies = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/cookies"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_cookies = jData

		for key in jData['items']:
			type = key['type']
			name = key['name']
			insertSameSiteAttribute = key['insertSameSiteAttribute']
			enforcementType = key['enforcementType']
			time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
			lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')		

			if key['performStaging']:
				performStaging = "Yes"
			else:
				performStaging = "No"
			
			
			if key['securedOverHttpsConnection']:
				securedOverHttpsConnection = "Yes"
			else:
				securedOverHttpsConnection = "No"
			
			if key['accessibleOnlyThroughTheHttpProtocol']:
				accessibleOnlyThroughTheHttpProtocol = "Yes"
			else:
				accessibleOnlyThroughTheHttpProtocol = "No"		
			signatureOverrides = []
			if enforcementType == "allow" :
				
				if key['attackSignaturesCheck']:
					attackSignaturesCheck = "Yes"
				else:
					attackSignaturesCheck = "No"		
				if (attackSignaturesCheck == "Yes" and len(key['signatureOverrides'])>=1):
					num_of_sign_overides = 0
					for temp_key in key['signatureOverrides']:
						num_of_sign_overides += 1
						temp_url = temp_key['signatureReference']['link'].replace("localhost", my_bigip)
						tempResponse = requests.get(temp_url, auth=(my_user, my_pass) , verify=False)
						print (temp_url)
						print (tempResponse.status_code)
						if(tempResponse.ok):
							temp_jData = json.loads(tempResponse.content.decode('utf-8'))
							signatureOverrides.append((str(temp_jData['signatureId']) + " - " + temp_jData['name']))
						else:
							tempResponse.raise_for_status()
				else:
					signatureOverrides = ["None"]
			else:
				signatureOverrides = ["None"]
				attackSignaturesCheck = "No"
				num_of_sign_overides = 0
				
			cookies.append({'name':name, 'type':type, 'lastUpdateMicros':lastUpdateMicros, 'signatureOverrides':signatureOverrides, 'attackSignaturesCheck':attackSignaturesCheck, 'accessibleOnlyThroughTheHttpProtocol':accessibleOnlyThroughTheHttpProtocol, 'securedOverHttpsConnection':securedOverHttpsConnection, 'enforcementType':enforcementType, 'insertSameSiteAttribute':insertSameSiteAttribute, 'num_of_sign_overides':num_of_sign_overides, 'performStaging':performStaging})
																		
	else:
		myResponse.raise_for_status()	
	
	return cookies, raw_cookies

def get_domains (my_bigip, my_id, my_user, my_pass):
	domains = {}
	domains = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/redirection-protection-domains"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_redirection_protection_domains = jData
		for key in jData['items']:
			type = key['type']
			domainName = key['domainName']
			time_temp = datetime.datetime.fromtimestamp(key['lastUpdateMicros']/1000000)
			lastUpdateMicros = time_temp.strftime('%Y-%m-%d %H:%M:%S')		
			includeSubdomains =  "Yes"
			
			if type == "explicit" :
														
				if key['includeSubdomains']:
					includeSubdomains =  "Yes"
				else:
					includeSubdomains =  "No"
					

			domains.append({'domainName':domainName, 'type':type, 'lastUpdateMicros':lastUpdateMicros, 'includeSubdomains':includeSubdomains}) 
															
	else:
		myResponse.raise_for_status()	

	return domains, raw_redirection_protection_domains

def get_ipi (my_bigip, my_id, my_user, my_pass):

	ipi_categories = {}
	ipi_categories = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/ip-intelligence"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_ip_intelligence = jData
		if jData['enabled']:
			ipi = "Yes"
		else:
			ipi = "No"

		if ipi == "Yes":
			for key in jData['ipIntelligenceCategories']:
				name = key['category']
				if key['block']:
					block = "Yes"
				else:
					block = "No"
				if key['alarm']:
					alarm = "Yes"
				else:
					alarm = "No"			
				ipi_categories.append({'name':name, 'block':block, 'alarm':alarm }) 
													
	else:
		myResponse.raise_for_status()	

	return ipi, ipi_categories, raw_ip_intelligence

def get_blocking_settings (my_bigip, my_id, my_user, my_pass):
	blocking_settings = {}
	blocking_settings = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/blocking-settings/violations"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_blocking_violations = jData
		for key in jData['items']:
			name = key['description']
			if 'alarm' in key:
				if key['alarm']:
					alarm = "Yes"
				else:
					alarm = "No"
			else:
				alarm = "-"

			if 'learn' in key:
				if key['learn']:
					learn = "Yes"
				else:
					learn = "No"
			else:
				learn = "-"			

			if 'block' in key:
				if key['block']:
					block = "Yes"
				else:
					block = "No"
			else:
				block = "-"	
				
			blocking_settings.append({'name':name, 'block':block, 'alarm':alarm, 'learn':learn }) 

	else:
		myResponse.raise_for_status()	

	return blocking_settings, raw_blocking_violations

def get_compliance (my_bigip, my_id, my_user, my_pass):
	compliance = {}
	compliance = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/blocking-settings/http-protocols"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_blocking_http = jData
		for key in jData['items']:
			name = key['description']
			if key['enabled']:
				enabled = "Yes"
			else:
				enabled = "No"
			if 'learn' in key:
				if key['learn']:
					learn = "Yes"
				else:
					learn = "No"
			else:
				learn = "-"		
				
			compliance.append({'name':name, 'learn':learn, 'enabled':enabled}) 

	else:
		myResponse.raise_for_status()	

	return compliance, raw_blocking_http

def get_evasion (my_bigip, my_id, my_user, my_pass):
	evasions = {}
	evasions = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/blocking-settings/evasions"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_evasions = jData
		for key in jData['items']:
			name = key['description']
			if key['enabled']:
				enabled = "Yes"
			else:
				enabled = "No"

			if key['learn']:
				learn = "Yes"
			else:
				learn = "No"
				
			evasions.append({'name':name, 'learn':learn, 'enabled':enabled}) 

	else:
		myResponse.raise_for_status()	

	return evasions, raw_evasions

def get_whitelist(my_bigip, my_id, my_user, my_pass):
	whitelist = {}
	whitelist = []

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/whitelist-ips"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_whitelist = jData
		for key in jData['items']:
			ipAddress = key['ipAddress']
			ipMask = key['ipMask']		
			blockRequests = key['blockRequests']
			description = key['description']
			if key['blockRequests'] == "always":
				ignoreIpReputation = "-"
				ignoreAnomalies = "-"
				neverLogRequests = "-"
				if key['neverLearnRequests']:
					neverLearnRequests = "Yes"
				else:
					neverLearnRequests = "No"
				trustedByPolicyBuilder = "-"
			else:
				if key['ignoreIpReputation']:
					ignoreIpReputation = "Yes"
				else:
					ignoreIpReputation = "No"
				if key['ignoreAnomalies']:
					ignoreAnomalies = "Yes"
				else:
					ignoreAnomalies = "No"		
				if key['neverLogRequests']:
					neverLogRequests = "Yes"
				else:
					neverLogRequests = "No"
				if key['neverLearnRequests']:
					neverLearnRequests = "Yes"
				else:
					neverLearnRequests = "No"
				if key['trustedByPolicyBuilder']:
					trustedByPolicyBuilder = "Yes"
				else:
					trustedByPolicyBuilder = "No"
			
			whitelist.append({'ipAddress':ipAddress, 'ipMask':ipMask, 'ignoreIpReputation':ignoreIpReputation, 'ignoreAnomalies':ignoreAnomalies, 'neverLogRequests':neverLogRequests, 'neverLearnRequests':neverLearnRequests, 'trustedByPolicyBuilder':trustedByPolicyBuilder, 'blockRequests':blockRequests, 'description':description}) 

	else:
		myResponse.raise_for_status()	

	return whitelist, raw_whitelist
	
def get_policy_builder (my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/policy-builder-filetype"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		learnExplicitFiletypes = jData['learnExplicitFiletypes']
		maximumFileTypes = jData['maximumFileTypes']
	else:
		myResponse.raise_for_status()	

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/policy-builder-url"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		learnExplicitUrls = jData['learnExplicitUrls']
		maximumUrls = jData['maximumUrls']
	else:
		myResponse.raise_for_status()	

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/policy-builder-cookie"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		learnExplicitCookies = jData['learnExplicitCookies']
		maximumCookies = jData['maximumCookies']
	else:
		myResponse.raise_for_status()	

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/policy-builder-parameter"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		learnExplicitParameters = jData['learnExplicitParameters']
		maximumParameters = jData['maximumParameters']
		parameterLearningLevel = jData['parameterLearningLevel']
		if jData['parametersIntegerValue']:
			parametersIntegerValue = "Yes"
		else:
			parametersIntegerValue = "No"
		if jData['classifyParameters']:
			classifyParameters = "Yes"
		else:
			classifyParameters = "No"	
	else:
		myResponse.raise_for_status()	

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/policy-builder-redirection-protection"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		learnExplicitRedirectionDomains = jData['learnExplicitRedirectionDomains']

	else:
		myResponse.raise_for_status()	


	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/policy-builder"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		learningMode = jData['learningMode']
		print(jData)
		if jData['enableFullPolicyInspection']:
			enableFullPolicyInspection = "Yes"
			if jData['learnInactiveEntities']:
				learnInactiveEntities = "Yes"
			else:
				learnInactiveEntities = "No"
		else:
			enableFullPolicyInspection = "No"
			learnInactiveEntities = "No"

		if jData['trustAllIps']:
			trustAllIps = "All IP addresses"
		else:
			trustAllIps = "Address List"

			
		trusted_loosen_source = jData['trustedTrafficLoosen']['differentSources']
		trusted_loosen_hours = jData['trustedTrafficLoosen']['minHoursBetweenSamples']
		untrusted_loosen_source = jData['untrustedTrafficLoosen']['differentSources']
		untrusted_loosen_hours = jData['untrustedTrafficLoosen']['minHoursBetweenSamples']
		
		
	else:
		myResponse.raise_for_status()	

	policy_builder = {'learnExplicitFiletypes':learnExplicitFiletypes, 'maximumFileTypes':maximumFileTypes, 'learnExplicitUrls':learnExplicitUrls, 'maximumUrls':maximumUrls, 'learnExplicitCookies':learnExplicitCookies, 'maximumCookies':maximumCookies, 'learnExplicitParameters':learnExplicitParameters, 'parameterLearningLevel':parameterLearningLevel, 'parametersIntegerValue':parametersIntegerValue, 'classifyParameters':classifyParameters, 'learnExplicitRedirectionDomains':learnExplicitRedirectionDomains, 'learningMode':learningMode, 'enableFullPolicyInspection':enableFullPolicyInspection, 'learnInactiveEntities':learnInactiveEntities, 'trustAllIps':trustAllIps,'trusted_loosen_source':trusted_loosen_source, 'trusted_loosen_hours':trusted_loosen_hours, 'untrusted_loosen_source':untrusted_loosen_source, 'untrusted_loosen_hours':untrusted_loosen_hours,'maximumParameters':maximumParameters}
				
	return policy_builder

def get_sensitive_parameters(my_bigip, my_id, my_user, my_pass):
	sensitive_param = {}
	sensitive_param = []
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/sensitive-parameters"
	print (url)
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_sensitive_parameters = jData
		for key in jData['items']:
			name = key['name']

			sensitive_param.append({'name':name}) 
	else:
		myResponse.raise_for_status()	

	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/parameters"
	print (url)
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		for key in jData['items']:
			name = key['name']
			if key['sensitiveParameter']:
				sensitive_param.append({'name':name}) 
			
	else:
		myResponse.raise_for_status()			
		
	return sensitive_param,raw_sensitive_parameters

def get_character_sets(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/character-sets"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_character_sets = jData
	else:
		myResponse.raise_for_status()	

	return raw_character_sets
	
def get_json_profiles(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/json-profiles"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_json_profiles = jData
	else:
		myResponse.raise_for_status()	

	return raw_json_profiles

def get_xml_profiles(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/xml-profiles"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_xml_profiles = jData
	else:
		myResponse.raise_for_status()	

	return raw_xml_profiles
	
def get_disallowed_geolocations(my_bigip, my_id, my_user, my_pass):
	disallowed_geolocations = {}
	disallowed_geolocations = []
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/disallowed-geolocations"
	print (url)
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		for key in jData['items']:
			countryName = key['countryName']
			disallowed_geolocations.append({'countryName':countryName}) 
	else:
		myResponse.raise_for_status()	

	return disallowed_geolocations
	
def get_server_technologies(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/server-technologies"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_server_technologies = jData
	else:
		myResponse.raise_for_status()	

	return raw_server_technologies

def get_plain_text_profiles(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/plain-text-profiles"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_plain_text_profiles = jData
	else:
		myResponse.raise_for_status()	

	return raw_plain_text_profiles
	
def get_response_pages(my_bigip, my_id, my_user, my_pass):
	response_pages = {}
	response_pages = []
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/response-pages"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_response_pages = jData
		for key in jData['items']:
			responsePageType = key['responsePageType']
			if 'responseActionType' in key:
				responseActionType = key['responseActionType']
			else:
				responseActionType = "N/A"		
				
			response_pages.append({'responsePageType':responsePageType, 'responseActionType':responseActionType}) 
	else:
		myResponse.raise_for_status()	

	return response_pages, raw_response_pages
	
def get_redirection_protection(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/redirection-protection"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_redirection_protection = jData
	else:
		myResponse.raise_for_status()	

	return raw_redirection_protection
		
def get_session_tracking(my_bigip, my_id, my_user, my_pass):
	session_tracking = {}
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/session-tracking"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)
	delayBlocking_ip = "No"
	delayBlocking_device = "No"
	delayBlocking_session = "No"	
	delayBlocking_user = "No"	
	logAll_user = "No"
	logAll_ip = "No"
	logAll_device = "No"
	logAll_session = "No"
	blockAll_user = "No"
	blockAll_ip = "No"
	blockAll_device = "No"
	blockAll_session = "No"
	enableSessionAwareness = "No"
	enableTrackingSessionHijackingByDeviceId = "No"			
	trackViolationsAndPerformActions = "No"	
	userNameSource = "-"
	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_session_tracking = jData
		
		
		if jData['sessionTrackingConfiguration']['enableSessionAwareness']:
			enableSessionAwareness = "Yes"
		
		if jData['sessionTrackingConfiguration']['enableTrackingSessionHijackingByDeviceId']:
			enableTrackingSessionHijackingByDeviceId = "Yes"
		if enableSessionAwareness == "Yes":
			userNameSource = jData['sessionTrackingConfiguration']['userNameSource']
			
		if (enableSessionAwareness == "Yes" and "violationDetectionActions" in jData):
			if	jData['violationDetectionActions']['trackViolationsAndPerformActions']:
				trackViolationsAndPerformActions = "Yes"
				if jData['delayBlocking']['checkUsernameThreshold']:
					delayBlocking_user = "Yes"
				if jData['delayBlocking']['checkSessionThreshold']:
					delayBlocking_session = "Yes"
				if jData['delayBlocking']['checkIpThreshold']:
					delayBlocking_ip = "Yes"
				if jData['delayBlocking']['checkDeviceIdThreshold']:
					delayBlocking_device = "Yes"
				if jData['logAllRequests']['checkUsernameThreshold']:
					logAll_user = "Yes"
				if jData['logAllRequests']['checkSessionThreshold']:
					logAll_session = "Yes"
				if jData['logAllRequests']['checkIpThreshold']:
					logAll_ip = "Yes"
				if jData['logAllRequests']['checkDeviceIdThreshold']:
					logAll_device = "Yes"
				if jData['blockAll']['checkUsernameThreshold']:
					blockAll_user = "Yes"
				if jData['blockAll']['checkSessionThreshold']:
					blockAll_session = "Yes"
				if jData['blockAll']['checkIpThreshold']:
					blockAll_ip = "Yes"
				if jData['blockAll']['checkDeviceIdThreshold']:
					blockAll_device = "Yes"

		session_tracking = {'enableSessionAwareness':enableSessionAwareness, 'enableTrackingSessionHijackingByDeviceId':enableTrackingSessionHijackingByDeviceId, 'trackViolationsAndPerformActions':trackViolationsAndPerformActions, 'delayBlocking_user':delayBlocking_user, 'delayBlocking_session':delayBlocking_session, 'delayBlocking_ip':delayBlocking_ip, 'delayBlocking_device':delayBlocking_device, 'logAll_user':logAll_user, 'logAll_session':logAll_session, 'logAll_ip':logAll_ip, 'logAll_device':logAll_device, 'blockAll_user':blockAll_user, 'blockAll_session':blockAll_session, 'blockAll_ip':blockAll_ip, 'blockAll_device':blockAll_device, 'userNameSource': userNameSource}
				
	else:
		myResponse.raise_for_status()	

	return session_tracking, raw_session_tracking
	
def get_csrf_protection(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/csrf-protection"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_csrf_protection = jData
	else:
		myResponse.raise_for_status()	

	return raw_csrf_protection
	
def get_web_scraping(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/web-scraping"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_web_scraping = jData
	else:
		myResponse.raise_for_status()	

	return raw_web_scraping

def get_history_revisions(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/history-revisions"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_history_revisions = jData
	else:
		myResponse.raise_for_status()	

	return raw_history_revisions
	
def get_csrf_urls(my_bigip, my_id, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/policies/" + my_id + "/csrf-urls"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_csrf_urls = jData
	else:
		myResponse.raise_for_status()	

	return raw_csrf_urls

def get_overall_signatures(my_bigip, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/signatures"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_overall_signatures = jData
	else:
		myResponse.raise_for_status()	

	return raw_overall_signatures

def get_virus_detection_server(my_bigip, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/virus-detection-server"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_virus_detection_server = jData
	else:
		myResponse.raise_for_status()	

	return raw_virus_detection_server
		
def get_overall_metachars(my_bigip, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/metachars"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_overall_metachars = jData
	else:
		myResponse.raise_for_status()	

	return raw_overall_metachars
	
def get_advanced_settings(my_bigip, my_user, my_pass):
	url = "https://" + my_bigip + "/mgmt/tm/asm/advanced-settings"
	myResponse = requests.get(url, auth=(my_user, my_pass) , verify=False)
	print (url)
	print (myResponse.status_code)

	if(myResponse.ok):
		jData = json.loads(myResponse.content.decode('utf-8'))
		raw_advanced_settings = jData
	else:
		myResponse.raise_for_status()	

	return raw_advanced_settings
	
def analyze_policy (overview, allowed_responses, file_types_allowed, urls, parameters, signatures_overview, signature_sets, methods, headers, cookies, domains, ipi, ipi_categories, blocking_settings, compliance, evasions, whitelist, policy_builder,sensitive_param ):
	suggestions =[]
#### Overview   ######
	if overview['enforcementMode'] != "blocking":
		suggestions.append({'severity':'error', 'section':'Overview', 'score':100, 'txt':'The ASM Policy is in transparent mode and therefore the violations are not being blocked.'})
	if overview['virtualServers'] != "None":
		suggestions.append({'severity':'error', 'section':'Overview', 'score':0, 'txt':'The ASM Policy is not applied to any Virtual Servers. Please review the configuration.'})
	if overview['brute_enabled'] != "Yes":
		suggestions.append({'severity':'info', 'section':'Overview', 'score':0, 'txt':'Brute Force is disabled. If you have Login Pages we recommend that you configure Brute Force Protection'})
	if overview['brute_enabled'] == "Yes" and overview['Login_pages_totalItems']==0:
		suggestions.append({"severity":"warning", "section":"Overview", "score":2, "txt":"You have Brute Force enabled, but you haven't configured any Login Pages. Please review your configuration."})
	if overview['inspectHttpUploads'] != "Yes":
		suggestions.append({'severity':'info', 'section':'Overview', 'score':0, 'txt':'Antivirus inspection is disabled. If you have an ICAP "enabled" antivirus server and your website has file uploads, it is recommended that you enable Antivirus protection'})		
	if overview['caseInsensitive'] == "Yes":
		suggestions.append({'severity':'info', 'score':0, 'section':'Overview', 'txt':'It is recommended that you create your ASM policies as ASM '})			
	if overview['maximumCookieHeaderLength'] == "any":
		suggestions.append({'severity':'warning', 'score':2, 'section':'Cookies', 'txt':' The Length for Cookies is set to "any" and therefore Illegal Cookie Length is will not be applied. Please review the configuration to understand why this is configured'})
	if overview['maximumHttpHeaderLength'] == "any":
		suggestions.append({'severity':'warning', 'score':2, 'section':'Headers', 'txt':' The Length for HTTP Header is set to "any" and therefore Illegal Header Length is will not be applied. Please review the configuration to understand why this is configured'})	
	if overview['redirectionProtectionEnabled'] == "No":
		suggestions.append({'severity':'warning', 'score':2, 'section':'Redirection Domains', 'txt':' Redirection Domains protection is Disabled. Please review the configuration to understand why this is configured'})
		
#### Policy Builder   ######
	
	if policy_builder['learningMode'] != "manual":
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you configure the policy to Manual mode, unless you are confident on running the policy on Automatic mode.'})	

	if policy_builder['learnExplicitFiletypes'] != "always":
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you configure "Learn New File Types" to "Always".'})	

	if policy_builder['learnExplicitUrls'] != "selective":
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you configure "Learn New URLs" to "Selective".'})	

	if policy_builder['learnExplicitParameters'] != "selective":
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you configure "Learn New Parameters" to "Selective".'})	

	if policy_builder['learnExplicitCookies'] != "selective":
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you configure "Learn New Cookies" to "Selective".'})	

	if policy_builder['parameterLearningLevel'] != "global":
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you configure "Parameter Learning Level" to "Global".'})	

	if policy_builder['parametersIntegerValue'] != "No":
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you dont configure "Learn Integer Parameters values" unless you are performing input validation with ASM.'})				
	if policy_builder['learnExplicitRedirectionDomains'] != "always":
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you dont configure "Learn New Redirection Domains" to "Always".'})		
		
	if policy_builder['trusted_loosen_source'] < 100:
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you increase the Untrusted sources to 100.'})	

	if policy_builder['untrusted_loosen_hours'] !=0:
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you configure the untrusted time (hours) to 0.'})	

	if policy_builder['trusted_loosen_hours'] !=0:
		suggestions.append({'severity':'info', 'score':0, 'section':'Policy Builder', 'txt':' We recommend that you configure the trusted time (hours) to 0.'})	
			
#### Blocking Settings   ######

	violations = ["HTTP protocol compliance failed", "Evasion technique detected",  "Illegal file type","Illegal header length", "Illegal cookie length", "Illegal cookie length", "Modified ASM cookie", "Modified domain cookie(s)", "Cookie not RFC-compliant", "Illegal redirection attempt", "Illegal method", "Illegal HTTP status in response", "HTTP protocol compliance failed", "Evasion technique detected","Attack signature detected", "IP is blacklisted", "Access from malicious IP address","Malformed JSON data", "Malformed XML data", "Illegal query string length", "Illegal POST data length", "Illegal URL length", "Illegal request length"]

	for key in blocking_settings:
		if key['name'] in violations:
			if key['learn'] == "No" and (key['name']!="Access from malicious IP address" or key['name']!="IP is blacklisted"):
				suggestions.append({'severity':'info', 'score':0, 'section':'Blocking Settings ', 'txt':'  Learning of "'+ key['name'] + '" is currently disabled and therefore there is no learning suggestions will be created. Please review the configuration to understand why this is disabled'})
			if key['alarm'] == "No":
				suggestions.append({'severity':'info', 'score':0, 'section':'Blocking Settings ', 'txt':' Logging of "'+ key['name'] + '" is currently disabled and therefore there is no logs created during these attacks. Please review the configuration to understand why this is disabled'})
			if key['block'] == "No":
				suggestions.append({'severity':'warning', 'score':3, 'section':'Blocking Settings ',  'txt':' Blocking of "'+ key['name'] + '" is currently disabled and therefore there is no protection against these type of violations. Please review the configuration to understand why this is disabled'})

			
#### Evasion   ######
	evasion_disabled = 0
	evasion_total = 0
	for key in evasions:
		if key['enabled'] == "No":
			evasion_disabled +=1
		evasion_total +=1
			
	if evasion_disabled >= evasion_total/2 and evasion_disabled < evasion_total:
		suggestions.append({'severity':'info', 'score':0, 'section':'Evasion', 'txt':' There are "'+ str(evasion_disabled) + '" Evasion techniques disabled. Please review the configuration to understand why this is disabled'})

	if evasion_disabled == evasion_total:
		suggestions.append({'severity':'warning', 'score':5, 'section':'Evasion',  'txt':' All "'+ str(evasion_disabled) + '" Evasion techniques disabled. Please review the configuration to understand why this is disabled'})
			
#### Compliance   ######			
	compliance_disabled = 0	
	compliance_total = 0			
	for key in compliance:
		if key['enabled'] == "No":
			compliance_disabled +=1
		compliance_total +=1

	if compliance_disabled >= compliance_total/2 and compliance_disabled < compliance_total:
		suggestions.append({'severity':'info', 'score':0, 'section':'HTTP Compliance', 'txt':' There are "'+ str(compliance_disabled) + '" HTTP Compliance violations disabled. Please review the configuration to understand why this is disabled'})

	if compliance_disabled == compliance_total:
		suggestions.append({'severity':'warning', 'score':5, 'section':'HTTP Compliance', 'txt':' All "'+ str(compliance_disabled) + '" HTTP Compliance violations disabled. Please review the configuration to understand why this is disabled'})
	
	
#### Signatures   ######	
	for key in signature_sets:
		if key['learn'] == "No":
			suggestions.append({'severity':'info', 'score':0, 'section':'Signatures', 'txt':'  Learning of Signature Set "'+ key['name'] + '" is currently disabled and therefore there is no learning suggestions will be created. Please review the configuration to understand why this is disabled'})
		if key['alarm'] == "No":
			suggestions.append({'severity':'info', 'score':0, 'section':'Signatures', 'txt':' Logging of Signature Set "'+ key['name'] + '" is currently disabled and therefore there is no logs created during these attacks. Please review the configuration to understand why this is disabled'})
		if key['block'] == "No":
			suggestions.append({'severity':'warning', 'score':5, 'section':'Signatures', 'txt':' Blocking of Signature Set "'+ key['name'] + '" is currently disabled. Please review the configuration as this might have been overriden manually.'})

	if signatures_overview['enabled'] > 30 and signatures_overview['staging']<signatures_overview['total']:
		suggestions.append({'severity':'warning',  'score':10, 'section':'Signatures', 'txt':' More than "'+ str(signatures_overview['enabled']) + '" Signatures are currently disabled. As this is a high number, well above the average, please review the configuration to understand why these signatures are disabled.'})

	if signatures_overview['staging'] > 20 and signatures_overview['staging']<signatures_overview['total']:
		suggestions.append({'severity':'warning', 'score':10, 'section':'Signatures', 'txt':' More than "'+ str(signatures_overview['staging']) + '" Signatures are still in staging. Please review the configuration to understand why these signatures are still in staging.'})

	if signatures_overview['staging']==signatures_overview['total']:
		suggestions.append({'severity':'error',  'score':100, 'section':'Signatures', 'txt':' All Signatures are currently in staging. Please review the configuration to understand why all signatures are in staging.'})
		
	if signatures_overview['enabled']==signatures_overview['total']:
		suggestions.append({'severity':'error',  'score':100, 'section':'Signatures', 'txt':' All Signatures are currently disabled. Please review the configuration to understand why ALL signatures are disabled.'})

	if signatures_overview['enabled']==signatures_overview['signatureStaging']:
		suggestions.append({'severity':'info',  'score':0, 'section':'Signatures', 'txt':' It is recommended that you enable the "global" Signature Staging option, so that you can have control which signatures to put in staging. If not, all signatures will be in "enforced" mode.'})		
	
	sig_total = signatures_overview['total']
	sig_not_enforced = "Staging:" + str(signatures_overview['staging']) + "/" + "Disabled:" + str(signatures_overview['enabled'])
	url_sig_disabled = 0
	url_staging = 0
	url_total = 0
	url_star_disabled = 0
	url_star_staging = 0
	param_staging = 0
	param_total = 0
	param_star_disabled = 0
	param_star_staging = 0
	cookie_staging = 0
	cookies_total = 0
	cookie_star=0
	param_sig_disabled = 0
	header_sig_disabled = 0
	header_star_disabled = 0
	cookie_star_disabled = 0
	cookie_star_staging = 0
	cookie_sig_disabled = 0
	override_suggestion = 0	
	
	for key in urls:
		url_total +=1			
		if len(key['signatureOverrides'])>1  and key['attackSignaturesCheck'] == "Yes":
			override_suggestion += 1
		else:
			if (key['signatureOverrides'][0]!="None")  and key['attackSignaturesCheck'] == "Yes":
				override_suggestion += 1
		if (key['attackSignaturesCheck'] == "No"):
			url_sig_disabled += 1
			if (key['name'] == "*"):
				url_star_disabled = 1	
				url_sig_disabled -= 1
				
		if (key['performStaging'] == "Yes"):
			url_staging +=1				
			if (key['name'] == "*"):
				url_star_staging = 1	
				url_staging -= 1
				
	for key in parameters:
		param_total +=1		
		if len(key['signatureOverrides'])>1 and key['attackSignaturesCheck'] == "Yes":
			override_suggestion += 1
		else:
			if (key['signatureOverrides'][0]!="None") and key['attackSignaturesCheck'] == "Yes":
				override_suggestion += 1
		if (key['attackSignaturesCheck'] == "No" and (key['valueType']!="ignore" and key['valueType']!="xml" and key['valueType']!="xml" and key['valueType']!="dynamic-content" and key['valueType']!="static-content")):
			param_sig_disabled += 1
			if (key['name'] == "*"):
				param_star_disabled = 1
				param_sig_disabled -= 1
		if (key['performStaging'] == "Yes"):
			param_staging +=1
			if (key['name'] == "*"):
				param_star_staging = 1	
				param_staging -= 1

	for key in headers:
		if len(key['signatureOverrides'])>1 and key['checkSignatures'] == "Yes":
			override_suggestion += 1
		else:
			if (key['signatureOverrides'][0]!="None") and key['checkSignatures'] == "Yes":
				override_suggestion += 1
		if (key['checkSignatures'] == "No" and key['name']!="cookie"):
			header_sig_disabled += 1
			if (key['name'] == "*"):
				header_star_disabled = 1	
				header_sig_disabled -= 1

	for key in cookies:
		cookies_total +=1
		if (key['performStaging'] == "Yes"):
			cookie_staging +=1		
			if (key['name'] == "*"):
				cookie_star_staging = 1	
				cookie_staging -= 1
		if len(key['signatureOverrides'])>1 and key['attackSignaturesCheck'] == "Yes":
			override_suggestion += 1
		else:
			if (key['signatureOverrides'][0]!="None") and key['attackSignaturesCheck'] == "Yes":
				override_suggestion += 1
		if (key['attackSignaturesCheck'] == "No"):
			cookie_sig_disabled += 1
			if (key['name'] == "*"):
				cookie_star_disabled = 1	
				cookie_sig_disabled -= 1				
				
				
	if override_suggestion > 0:
		suggestions.append({'severity':'info', 'score':0, 'section':'Signatures', 'txt':' There are "'+ str(override_suggestion) + '" entities with Signature Overrides. Please review the configuration to confirm that the signatures are disabled correctly.'})


	if (url_staging > 0 ):
		suggestions.append({'severity':'warning', 'score':5, 'section':'URLs', 'txt':' There are ' + str(url_staging) + ' URLs still on staging. Please review the configuration.'})

	if (url_sig_disabled> 0 ):
		suggestions.append({'severity':'warning', 'score':5, 'section':'URLs', 'txt':' There are ' + str(url_sig_disabled) + ' URLs with attack signatures disabled. Please review the configuration.'})

		
	if (param_star_disabled > 0 ):
			suggestions.append({'severity':'error', 'score':30, 'section':'Parameters', 'txt':' Wildcard Parameter * has its signatures disabled. Please review the configuration.'})
	if (param_star_staging > 0 ):
			suggestions.append({'severity':'error', 'score':30, 'section':'Parameters', 'txt':' Wildcard Parameter * is still on staging. Please review the configuration.'})
	if (header_star_disabled > 0 ):
			suggestions.append({'severity':'error', 'score':5, 'section':'Headers', 'txt':' Wildcard Header * has its signatures disabled. Please review the configuration.'})
	if (cookie_star_staging > 0 ):
			suggestions.append({'severity':'error', 'score':5, 'section':'Cookies', 'txt':' Wildcard Cookie * is still on staging. Please review the configuration.'})
	if (cookie_star_disabled > 0 ):
			suggestions.append({'severity':'error', 'score':5, 'section':'Headers', 'txt':' Wildcard Cookie * has its signatures disabled. Please review the configuration.'})
	if (url_star_disabled > 0 ):
			suggestions.append({'severity':'error', 'score':15, 'section':'URLs', 'txt':' Wildcard URLs * has its signatures disabled. Please review the configuration.'})
	if (url_star_staging > 0 ):
			suggestions.append({'severity':'error', 'score':15, 'section':'URLs', 'txt':' Wildcard URLs * is still on staging. Please review the configuration.'})
	if (param_staging > 0 ):
			suggestions.append({'severity':'warning', 'score':5, 'section':'Parameters', 'txt':' There are ' + str(param_staging) + ' Parameters still on staging. Please review the configuration.'})
	if (param_sig_disabled > 0 ):
			suggestions.append({'severity':'warning', 'score':5, 'section':'Parameters', 'txt':' There are ' + str(param_sig_disabled) + ' Parameters with attack signature disabled. Please review the configuration.'})			
	if (header_sig_disabled > 0 ):
			suggestions.append({'severity':'warning', 'score':5, 'section':'Headers', 'txt':' There are ' + str(header_sig_disabled) + ' Headers with attack signature disabled. Please review the configuration.'})			
	if (cookie_sig_disabled > 0 ):
			suggestions.append({'severity':'warning', 'score':5, 'section':'Cookies', 'txt':' There are ' + str(header_sig_disabled) + ' Cookies with attack signature disabled. Please review the configuration.'})			
	if (cookie_staging > 0 ):
			suggestions.append({'severity':'warning', 'score':5, 'section':'Cookies', 'txt':' There are ' + str(cookie_staging) + ' Cookies still on staging. Please review the configuration.'})		

			
#########  	File Types
	file_total = 0
	file_staging = 0
	for key in file_types_allowed:
		file_total +=1
		if key['name'] == "*":
			suggestions.append({'severity':'warning', 'score':2, 'section':'File Types', 'txt':' The wildcard entry needs to be removed for the Illegal File Type Extension violation to be enfocred.'})		
		if (key['performStaging'] == "yes"):
			file_staging +=1
			suggestions.append({'severity':'info', 'score':0.5, 'section':'File Types', 'txt':' File Type Extension "' + key['name'] +  '" is still on staging. If you are validating the File Type Lengths, this will prevent them from being applied.'})
				
	
#########  	Redirection Domain
	for key in domains:
		if key['domainName'] == "*":
			suggestions.append({'severity':'warning', 'score':3, 'section':'Redirection Domains', 'txt':' The wildcard entry needs to be removed for the Redirection Protection to be enfoced.'})

			#######	Methods
	num_of_methods =0
	for key in methods:
		if key['name'] == "DELETE":
			suggestions.append({'severity':'warning', 'score':3, 'section':'Methods',  'txt':' The DELETE HTTP Method is allowed. Please review that this method should be allowed as it can cause unwanted behaviour.'})
		num_of_methods += 1
		
	if (num_of_methods>6):
		suggestions.append({'severity':'info', 'score':1, 'section':'Methods', 'txt':' Too many methods configured. Please review the allowed methods for this ASM policy.'})	

#######	Responses
	num_of_response =0

	for key in allowed_responses:
		num_of_response += 1
	
	if (num_of_response>10):
		suggestions.append({'severity':'info', 'score':0, 'section':'Responses', 'txt':' Too many HTTP Response codes allowed. Please review the allowed methods for this ASM policy.'})


		
		
#########  	Sensitive_param
	num_of_sensitive = 0
	for key in sensitive_param:
		num_of_sensitive += 1

	if (num_of_sensitive==1 and key['name']== "password"):
		suggestions.append({'severity':'warning',  'score':3, 'section':'Parameters', 'txt':' There is only the default configuration for sensitive parameters. Please review the configuration.'})

		
		
#########  	Trusted IPs
	num_of_trusted_ips = 0
	for key in whitelist:
		num_of_trusted_ips += 1

	if (num_of_trusted_ips==0):
		suggestions.append({'severity':'info',  'score':0, 'section':'Policy Builder', 'txt':' There are no trusted IPs configured for this ASM policy.'})

		
		
#########  	IPI
	ipi_enabled = 0
	ipi_services_disabled = 0
	ipi_services_total = 0

	if "Yes" in ipi:
		ipi_enabled = 1

	for key in ipi_categories:
		ipi_services_total +=1
		if key['block'] == "No":
			ipi_services_disabled += 1

			
	if ipi_services_disabled > 0 and ipi_enabled==1:
		suggestions.append({'severity':'info', 'score':0, 'section':'IPI', 'txt':' There are "'+ str(evasion_disabled) + '" IP Categories disabled. Please review the configuration to understand why this is disabled'})

	if ipi_services_disabled == ipi_services_total and ipi_enabled==1:
		suggestions.append({'severity':'warning', 'score':10, 'section':'IPI',  'txt':' All "'+ str(evasion_disabled) + '" IP Categories are disabled. Please review the configuration to understand why this is disabled'})

		
				
#########  	Results

	results = {}
	score = 0
	error = 0
	info = 0
	warning = 0
		
	for key in suggestions:
		if key['severity'] == 'info':
			info += 1
		if key['severity'] == 'warning':
			warning += 1
		if key['severity'] == 'error':
			error += 1
		score += key['score']

	results = {'info':info,  'warning':warning, 'error':error, 'score':score, 'file_type_total':file_total, 'file_type_not_enforced':file_staging, 'urls_total':url_total, 'urls_not_enforced':url_staging, 'param_total':param_total, 'param_not_enforced':param_staging, 'cookies_total':cookies_total, 'sig_total':sig_total, 'compliance_total':compliance_total, 'evasion_total':evasion_total, 'cookies_not_enforced':cookie_staging, 'sig_not_enforced':sig_not_enforced,'compliance_not_enforced':compliance_disabled, 'evasion_not_enforced':evasion_disabled}
		
	return suggestions,results

	
	