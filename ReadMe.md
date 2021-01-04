# Регистрация и авторизация пользователей
# Рабочий проект docker 

    MySQL

create table user
(
user_id  int auto_increment
primary key,
email    varchar(255) not null,
username varchar(255) not null,
password varchar(255) not null
);

    Terminal:

cp .env.example .env

make build

make up

make composer_install

http://localhost:9290/register