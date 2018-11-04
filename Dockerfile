# f5-app-sec - Dockerfile
# https://github.com/ArtiomL/f5-app-sec
# Artiom Lichtenstein
# v1.0.3, 04/11/2018

FROM debian:stable-slim

LABEL maintainer="Artiom Lichtenstein" version="1.0.3"

# Core dependencies
RUN apt-get update && \
	apt-get install -y apache2 curl php7.0 php7.0-cli php7.0-xml php7.0-zip && \
	apt-get install -y python3 python3-pip && \
	pip3 install python-docx requests xlsxwriter && \
	apt-get remove -y python3-dev python3-pip && \
	apt-get autoclean -y && \
	apt-get autoremove -y && \
	apt-get clean -y && \
	rm -rf /var/lib/apt/lists/*

# f5-app-sec
COPY /artifacts /var/www/app-sec/
COPY /docs /var/www/app-sec/
WORKDIR /var/www/app-sec/

# apache2
COPY /etc/app-sec.conf /etc/apache2/sites-available/
COPY /etc/apache2.conf /etc/apache2/apache2.conf.append
COPY /etc/ssl /etc/apache2/ssl/
COPY /scripts/start.sh /usr/local/bin/
RUN cat /etc/apache2/apache2.conf.append | tee -a /etc/apache2/apache2.conf
RUN a2dissite 000-default.conf
RUN a2disconf other-vhosts-access-log
RUN a2enmod ssl headers
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf
RUN sed -i 's/Listen 443/Listen 8443/g' /etc/apache2/ports.conf
RUN a2ensite app-sec.conf

# System account and environment variables
RUN useradd -r -u 1001 user
RUN chown -RL user: /etc/apache2/ssl/ /var/log/apache2/ /var/run/apache2/ /var/www/app-sec/
ENV PATH="/var/www/app-sec:${PATH}"

# Expose ports
EXPOSE 8443

# UID to use when running the image and for CMD
USER 1001

# Run
CMD ["/usr/local/bin/start.sh"]
