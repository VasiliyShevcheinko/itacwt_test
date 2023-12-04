FROM nginx:stable-alpine

ADD nginx.conf /etc/nginx/
ADD www.conf /etc/nginx/conf.d/
RUN rm /etc/nginx/conf.d/default.conf

ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
