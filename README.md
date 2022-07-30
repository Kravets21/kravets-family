# Kravets-Family
Our family website. Just for fun

-cp .env.dist .env
-make build
-make up
-sudo echo $(docker network inspect bridge | grep Gateway | grep -o -E '([0-9]{1,3}\.){3}[0-9]{1,3}') "symfony.local" >> /etc/hosts


