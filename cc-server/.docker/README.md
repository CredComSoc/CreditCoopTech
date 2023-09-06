# `cc-server` running in a container

1. Make sure you have run `composer install --no-dev` in root folder
2. run `docker compose up` in this folder, this should make the `cc-server` available at `localhost:8000` and bootstrap a MariaDB database named `cc-server`, accessible at `localhost:3306` with user `root` and password `secret`
3. you can change the `node.ini` file to your choosing, this may need to run `docker compose down` and `docker compose up` again.
