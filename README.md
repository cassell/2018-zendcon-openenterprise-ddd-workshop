# 2018 Zendcon & Open Enterprise Domain-Driven Design Workshop

If you want to follow along on your own computer and IDE I would suggest doing the following before the presentation:
* Install a recent version of Docker and Docker Compose [https://www.docker.com/products/docker](https://www.docker.com/products/docker)
* Clone this repo. The folder name must be "2018-zendcon-openenterprise-ddd-workshop" for Docker to use the correct default network name. Otherwise, you may have to modify the Makefile to fit your needs.
````
git clone git@github.com:cassell/2018-zendcon-openenterprise-ddd-workshop.git
````
* If you are on Linux or Mac run "make beer" and that will build the project, otherwise you can use docker compose (Windows users may have the change the slashes in the folder path):
* I will likely not have time to help troubleshoot your setup during the presentation.
* I would suggest pulling changes again right before the workshop as I might have changed something last minute
* BYOIDE - Use the code editor that you are most comfortable with. 
* No web server required. Code will be all backend code.

However, you will be fine without any preparation for this workshop.
I will have printouts of most of the code so you can follow along with a pen and paper.
I will make the slides available at the end of the workshop.


````
make beer
````
or
````
docker-compose build
docker-compose up -d
docker run --rm --interactive --tty --network 2018-zendcon-openenterprise-ddd-workshop_default mariadb:10.1 mysql --host=mariadb --user=root --password=vegas --batch -e "drop database if exists beeriously; create database beeriously;"
docker run --rm --interactive --tty --network 2018-zendcon-openenterprise-ddd-workshop_default --volume `pwd`:/app --user $(id -u):$(id -g) --workdir /app 2018-zendcon-openenterprise-ddd-workshop_php-fpm composer install
docker run --rm --interactive --tty --network 2018-zendcon-openenterprise-ddd-workshop_default --volume `pwd`:/app --user $(id -u):$(id -g) --workdir /app 2018-zendcon-openenterprise-ddd-workshop_php-fpm /app/bin/console doctrine:migrations:migrate --no-interaction -v
````

If you can then run and only see that tests are failing then you are good to go:
````
make unit
make integration
````
or
````
docker run --rm --interactive --tty --network 2018-zendcon-openenterprise-ddd-workshop_default --volume `pwd`:/app --user $(id -u):$(id -g) --workdir /app 2018-zendcon-openenterprise-ddd-workshop_php-fpm /app/vendor/bin/phpunit --configuration /app/src/Tests/Unit/phpunit.xml.dist
docker run --rm --interactive --tty --network 2018-zendcon-openenterprise-ddd-workshop_default --volume `pwd`:/app --user $(id -u):$(id -g) --workdir /app 2018-zendcon-openenterprise-ddd-workshop_php-fpm /app/vendor/bin/phpunit --configuration /app/src/Tests/Integration/phpunit.xml.dist
````


