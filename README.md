# <img align="center" src="img/awaf.svg" height="80">&nbsp;&nbsp;f5-app-sec
[![Build Status](https://img.shields.io/travis/com/ArtiomL/f5-app-sec/develop.svg)](https://travis-ci.com/ArtiomL/f5-app-sec)
[![Releases](https://img.shields.io/github/release/ArtiomL/f5-app-sec.svg)](https://github.com/ArtiomL/f5-app-sec/releases)
[![Commits](https://img.shields.io/github/commits-since/ArtiomL/f5-app-sec/v0.0.1.svg?label=commits%20since)](https://github.com/ArtiomL/f5-app-sec/commits/master)
[![Maintenance](https://img.shields.io/maintenance/yes/2018.svg)](https://github.com/ArtiomL/f5-app-sec/graphs/code-frequency)
[![Issues](https://img.shields.io/github/issues/ArtiomL/f5-app-sec.svg)](https://github.com/ArtiomL/f5-app-sec/issues)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)
[![Slack Status](https://f5cloudsolutions.herokuapp.com/badge.svg)](https://f5cloudsolutions.herokuapp.com)

&nbsp;&nbsp;

## Table of Contents
- [Description](#description)
- [Installation](#installation)
	- [Stats](#stats)
	- [Run](#run)
- [License](LICENSE)

&nbsp;&nbsp;

## Description

The [container](https://hub.docker.com/r/artioml/f5-app-sec/) in this repository is a collection of policies, guides, scripts and audit tools to help you succeed with application security.

Based on the following article:
https://support.f5.com/csp/article/K07359270

<p align="center"><img src="img/diagram.png"></p>


&nbsp;&nbsp;

## Installation

### Stats
To gather the configuration and stats from an F5 BIG-IP (which you have management access to), run:

```shell
docker run -it --rm -v /path/to/local/folder:/home/user/ artioml/f5-app-sec gather_stats
```

This will create a file named BIG-Stats.zip in the local directory you mounted into the container (`/path/to/local/folder`).


### Run
To start the actual web app, run:

```shell
docker run -dit --rm -p 443:8443 artioml/f5-app-sec
```





Good WAF Security, Getting started with ASM:  
https://clouddocs.f5.com/training/community/waf/html/class3/class3.html

Elevating ASM Protection:  
https://clouddocs.f5.com/training/community/waf/html/class4/class4.html

High and Maximum Security:  
https://clouddocs.f5.com/training/community/waf/html/class5/class5.html

WAF Programmability:  
https://clouddocs.f5.com/training/community/waf/html/class6/class6.html

- [ ] F5 Hardening script

- [ ] ASM Policies Audit Tool  
  
- [ ] ASM YouTube Videos  
  
- [ ] ASM Word Doc to RtD
  
- [ ] Upload actual ASM policies for each level  

- [ ] WAF Questionnaire

- [ ] ASM Operations Guide

- [ ] 2018 Application Protection Report

- [ ] F5 University ASM training (for Partners)

- [ ] Super-NetOps (Class3?)

&nbsp;&nbsp;
