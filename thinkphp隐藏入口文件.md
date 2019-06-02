# thinkphp隐藏入口文件

原入口：http://localhost/tp5/public/index.php/admin

在ThinkPHP5.0中，出于优化的URL访问原则，还支持通过URL重写隐藏入口文件，下面以`Apache`为例说明隐藏应用入口文件index.php的设置。

下面是Apache的配置过程，可以参考下：

1、`httpd.conf`配置文件中加载了`mod_rewrite.so`模块

2、`AllowOverride None` 将`None`改为 `All`

3、部分程序需要指定二级目录作为运行目录，如ThinkPHP5，DocumentRoot修改至入口文件所在目录，上述2、3点修改后如下

```
#
# DocumentRoot: The directory out of which you will serve your
# documents. By default, all requests are taken from this directory, but
# symbolic links and aliases may be used to point to other locations.
#
DocumentRoot "${SRVROOT}/htdocs/tp5/public"
<Directory "${SRVROOT}/htdocs/tp5/public">
    #
    # Possible values for the Options directive are "None", "All",
    # or any combination of:
    #   Indexes Includes FollowSymLinks SymLinksifOwnerMatch ExecCGI MultiViews
    #
    # Note that "MultiViews" must be named *explicitly* --- "Options All"
    # doesn't give it to you.
    #
    # The Options directive is both complicated and important.  Please see
    # http://httpd.apache.org/docs/2.4/mod/core.html#options
    # for more information.
    #
    Options Indexes FollowSymLinks

    #
    # AllowOverride controls what directives may be placed in .htaccess files.
    # It can be "All", "None", or any combination of the keywords:
    #   AllowOverride FileInfo AuthConfig Limit
    #
    AllowOverride All

    #
    # Controls who can get stuff from this server.
    #
    Require all granted
</Directory>
```

3、在应用入口文件同级目录添加`.htaccess`文件，内容如下：

```
<IfModule mod_rewrite.c>
 RewriteEngine on
 RewriteBase /
 RewriteCond %{REQUEST_FILENAME} !-d
 RewriteCond %{REQUEST_FILENAME} !-f
 RewriteRule ^(.*)$ index.php?s=/$1 [QSA,PT,L]
</IfModule>
```

修改后入口：http://localhost/admin