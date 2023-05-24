# 도커의 경우 명령어마다 레이어가 만들어지기 때문에 명령어 수를 줄이는게 중요
# \ 로 연장한 명령 사이에 주석 들어가 있을 경우 empty continuation line warning 발생

# 이미지 세팅
FROM ubuntu:20.04
MAINTAINER Gale <cmg4739@gmail.com>

# 환경 변수 세팅
ARG 	DB_NAME \
	DB_HOST \
	DB_USER \
	DB_PW \
	MAIL_PW \
	FCM_TOKEN \
	PHP_ENV_DIR=/var/utils/env.php \
	NGINX_DEFAULT_DIR=/etc/nginx/sites-available/default \
	PHP_INI_DIR=/etc/php/8.0/fpm/php.ini

#1. 기본 세팅 & 구성요소 설치 + 리마인드 알림 전송을 위한 cron 설치
RUN 	mkdir -p /var/utils && \
	apt-get update && \
	apt-get install curl -y && \
	apt-get install cron && \
	apt install software-properties-common -y && \
	add-apt-repository ppa:ondrej/php -y && \
	apt-get install nginx -y && \
	rm $NGINX_DEFAULT_DIR && \
	apt-get install php8.0-common \
			php8.0-cli \
			php8.0-fpm \
			php8.0-cgi \
			php8.0-curl \
			php8.0-mysql \
			php8.0-opcache \
			php8.0-mbstring \
			php8.0-redis -y

#2. 패키지 COPY
COPY . /var/utils

#3. env.php 수정 & 패키지 권한 부여
# sed -i 's(구문자)(바꿀 단어)(구분자)(대체단어)(구분자)g' 파일명
# 로그 폴더 생성 위해 쓰기 권한까지 부여
# 참고 :  WORKDIR에서 ENV 참조 시 ${env} 형식으로 참조
RUN	sed -i 's%DB_NAME_SAMPLE%'$DB_NAME'%g' $PHP_ENV_DIR && \
	sed -i 's%DB_HOST_SAMPLE%'$DB_HOST'%g' $PHP_ENV_DIR && \
	sed -i 's%DB_USER_SAMPLE%'$DB_USER'%g' $PHP_ENV_DIR && \
	sed -i 's%DB_PW_SAMPLE%'$DB_PW'%g' $PHP_ENV_DIR && \
	sed -i 's%MAIL_PW_SAMPLE%'$MAIL_PW'%g' $PHP_ENV_DIR && \
	sed -i 's%FCM_TOKEN_SAMPLE%'$FCM_TOKEN'%g' $PHP_ENV_DIR && \
	chmod -R 777 /var/utils

#4. nginx 수정
COPY ./conf/nginx/nginx.conf $NGINX_DEFAULT_DIR

#5. php 수정
RUN	sed -i 's%;date.timezone =%date.timezone = Asia/Seoul%g' $PHP_INI_DIR && \
	sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' $PHP_INI_DIR && \
	sed -i 's/session.cookie_httponly =/session.cookie_httponly = 1/g' $PHP_INI_DIR && \
	sed -i 's/;session.cookie_secure =/session.cookie_secure = 1/g' $PHP_INI_DIR && \
	sed -i 's/memory_limit = 128M/memory_limit = 256M/g' $PHP_INI_DIR && \
	sed -i 's/post_max_size = 8M/post_max_size = 56M/g' $PHP_INI_DIR && \
	sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 1024M/g' $PHP_INI_DIR && \
	sed -i 's/max_file_uploads = 20/max_file_uploads = 50/g' $PHP_INI_DIR && \
	sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=128/g' $PHP_INI_DIR && \
	sed -i 's/;opcache.interned_strings_buffer=8/opcache.interned_strings_buffer=8/g' $PHP_INI_DIR && \
	sed -i 's/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=50000/g' $PHP_INI_DIR && \
	sed -i 's/;opcache.revalidate_freq=2/opcache.revalidate_freq=60/g' $PHP_INI_DIR && \
	sed -i 's/;opcache.enable_cli=0/opcache.enable_cli=1/g' $PHP_INI_DIR && \
	sed -i 's/;opcache.enable=1/opcache.enable=1 opcache.jit=tracing opcache.jit_buffer_size=100M/g' $PHP_INI_DIR

#6. cron 명령어 파일 추가
RUN crontab -l | { cat; echo "* * * * * curl -X POST http://localhost/alarm/remind\n* * * * * curl -X POST http://localhost/alarm/remind/aos"; } | crontab -

#7. nginx & php 실행
# CMD, ENTRYPOINT의 경우 Dockerfile 내에서 단 한번만 실행
# nginx 서버를 foreground로 돌리지 않으면 컨테이너를 background로 실행해도 컨테이너 안의 서버가 실행이 안된 상태이기 때문에 daemon off로 foreground로 계속 실행 중인 상황으로 만들기
ENTRYPOINT cron && service php8.0-fpm start && nginx -g "daemon off;"

