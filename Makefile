# npm init
init:
	docker-compose run php-npm npm i

# Build package
build:
	docker-compose run php-npm npm run build

# Watch js changes when developing
watch:
	docker-compose run php-npm npm run watch

# Just open console the container
sh:
	docker-compose run php-npm sh
