FROM postgres:13.3

ARG TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#ADD init.sql /docker-entrypoint-initdb.d/