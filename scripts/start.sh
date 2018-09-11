#!/bin/sh
# f5-app-sec - Docker Wrapper Script
# https://github.com/ArtiomL/f5-app-sec
# Artiom Lichtenstein
# v1.0.1, 12/09/2018

openssl req -new -x509 -days 365 -nodes -out /etc/apache2/ssl/self.pem -keyout /etc/apache2/ssl/self.key -subj "/C=US/ST=Washington/L=Seattle/O=F5 Networks Inc/CN=f5-app-sec" > /dev/null 2>&1

exec /usr/sbin/apachectl -DFOREGROUND
