# Kravets-Family
Our family website. Just for fun

	cp .env.dist .env
	make build
	make up
    doctrine:database:create
    doctrine:schema:update --force

    local kibana: http://172.17.0.1:81/
    local website: http://172.17.0.1:80/


    create entity:
	php bin/console make:entity

    create controller:
    php bin/console make:controller