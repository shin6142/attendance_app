# attendance_app

環境構築
```
$ git clone git@github.com:shin6142/attendance_app.git
```
.envを作成。中身は山家に聞いてください
```
$ cd ~/attendance_app
$ vi .env
```

phpコンテナとmysqlコンテナを起動します
```
$ cd ~/attendance_app
$ docker compose up -d --build
```
コンテナが起動していることを確認します
```
$ docker compose ps 

NAME                IMAGE                COMMAND                  SERVICE             CREATED             STATUS              PORTS
mysql               mysql:5.7            "docker-entrypoint.s…"   db                  16 hours ago        Up 16 hours         33060/tcp, 0.0.0.0:4306->3306/tcp
php                 attendance_app-php   "docker-php-entrypoi…"   php                 16 hours ago        Up 16 hours         0.0.0.0:8000->80/tcp
```

依存ライブラリをコンテナ内にインストールします
```
docker compose exec codmon bash
composer install
```


phpunitを実行します
```
vendor/bin/phpunit src/api/v1/stamp/MainTest.php
```
