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
import sys

from documention_asm import *		

document = Document("ASM_Summary.docx")	

document.add_heading("Summary", level=1)

names = sys.argv[1]
document.save("reports/"+names+"_summary.docx")

########################					########################
########################	ASM Report		########################
########################					########################

########################  GET Geolocation  ########################
with open(os.getcwd()+"/config_files/"+names+"/suggestions.txt", "r") as infile:
	suggestions = json.load(infile)		
		
			
word_file_summary (document, suggestions, names)


print("ok");
