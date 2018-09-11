# f5-app-sec - Dockerfile
# https://github.com/ArtiomL/f5-app-sec
# Artiom Lichtenstein
# v1.0.0, 10/09/2018

FROM debian:stable-slim

LABEL maintainer="Artiom Lichtenstein" version="1.0.0"

# Core dependencies
RUN apt-get update && \
	apt-get install -y apache2 curl php7.0 php7.0-xml unzip && \
	apt-get autoclean -y && \
	apt-get autoremove -y && \
	apt-get clean -y && \
	rm -rf /var/lib/apt/lists/*

# f5-app-sec
COPY / /var/www/f5-app-sec/
WORKDIR /var/www/f5-app-sec/
RUN curl -L  -o master.zip && \
	unzip master.zip && \
	rm master.zip

# apache2
RUN cp /var/www/f5-app-sec/etc/f5-app-sec.conf /etc/apache2/sites-available/
#RUN cat /var/www/adct/etc/apache2.conf | tee -a /etc/apache2/apache2.conf
COPY /scripts/start.sh /usr/local/bin/
RUN a2dissite 000-default.conf
RUN a2disconf other-vhosts-access-log
RUN a2enmod ssl headers
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf
RUN sed -i 's/Listen 443/Listen 8443/g' /etc/apache2/ports.conf
RUN a2ensite f5-app-sec.conf

# System account
RUN useradd -r -u 1001 user
RUN chown -RL user: /etc/ssl/private/ /var/log/apache2/ /var/run/apache2/ /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose ports
EXPOSE 8443

# UID to use when running the image and for CMD
USER 1001

# Run
CMD ["/usr/local/bin/start.sh"]
