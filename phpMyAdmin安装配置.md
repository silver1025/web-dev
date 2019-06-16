## phpMyAdmin安装配置

1. 下载phpMyAdmin

2. 解压文件到apache的项目目录（/htdocs或修改后的运行目录）中

3. 进入phpmyadmin文件夹，修改phpmyadmin的简单配置文件config.sample.inc.php为config.sample.php，作为默认配置文件

4. 配置phpmyadmin的主机，修改成你要连的主机

5. 远程连接需要防火墙放行3306端口，注意宝塔的二次防火墙

6. 远程客户端连接需要修改权限，最简单的为改表法。在localhost的那台电脑，登入mysql后，更改 "mysql" 数据库里的 "user" 表里的 "host" 项，从"localhost"改称"%"
```
   mysql -u root -pvmwaremysql>use mysql;
   mysql>update user set host = '%' where user = 'username';
   mysql>select host, user from user;
```

