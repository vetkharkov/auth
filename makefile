
include .env

container_php       = $(DOCKER_PREFIX)-app
container_db        = $(DOCKER_PREFIX)-db
container_server    = $(DOCKER_PREFIX)-webserver
container_node      = $(DOCKER_PREFIX)-node

#############################################
###                                       ###
###         MakeFile for Laravel          ###
###                                       ###
#############################################

composer_install: #install composer
	docker exec $(container_php) composer global require hirak/prestissimo
	docker exec $(container_php) composer install

laravel_install: #Create new Laravel project
	docker exec $(container_php) composer create-project --prefer-dist laravel/laravel:^7.0 .
	docker exec $(container_php) cp .env.example .env

key: #generate APP key
	docker exec $(container_php) php artisan key:generate

migrate: #php artisan migrate
	docker exec $(container_php) php artisan migrate

seed: #php artisan migrate
	docker exec $(container_php) php artisan migrate:fresh --seed

pm: #Set ownership
	@sudo chown $(USER):$(USER) . -R

#####################################
###                               ###
###       Work in containers      ###
###                               ###
#####################################

up: #start docker containers @docker-compose up -d
	@docker-compose up -d

down: #stop docker containers
	@docker-compose down

build: #build docker containers @docker-compose up -d --build
	docker-compose up -d --build

show: #show docker's containers
	docker ps

connect_app: #Connect to APP container
	docker exec -it $(container_php) /bin/bash

connect_db: #Connect to DB container
	docker exec -it $(container_db) /bin/bash

connect_server: #Connect to container_server container
	docker exec -it $(container_server) /bin/sh

