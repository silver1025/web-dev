## 安装php

1. 将php压缩包解压到你想要的目录，根据自己的习惯可以重命名，比如我的是：D:\soft\php-7.2.8

2. 打开php目录，找到php.ini-development，重命名为php.ini

3. 打开php.ini，搜索extension_dir，将 ; extension_dir = “ext” 前面的分号（;）去掉，修改为extension_dir = “D:/soft/php-7.2.8/ext” 注：路径根据自己的php存放路径来写。

4. 根据需要开启扩展，将需要的扩展前面分号（;）去掉即可。

5. 配置session存放路径，找到;session.save_path = “/tmp”，去掉前面的分号（;）,修改为session.save_path = “D:/soft/php-7.2.8/temp”。注：php目录中没有temp目录，这个是我自己创建的，根据自己习惯来设定。

6. 配置文件上传路径，找到upload_tmp_dir，将前面的分号（;）去掉，修改为session.save_path = “D:/soft/php-7.2.8/temp”

7. 配置时区，找到;date.timezone =，将前面的分号（;）去掉，修改为date.timezone = date.timezone =Asia/Shanghai

8. Apache安装好之后，在下载PHP开发软件之前，先向`httpd.conf`文件中写入PHP支持模块。
   进入Apache下的conf目录，打开httpd.conf文件， `Crtl+F`

   ```
   DirectoryIndex index.html
   ```

   将其修改为

   ```
   #修改首页面文件类型支持
   DirectoryIndex index.html index.htm index.php
   ```

   然后，在文件尾部添加下面的内容：

   ```
   #让Apache支持PHP
   LoadModule php7_module "D:/phpenvir/php7.1.1/php7apache2_4.dll" 
   #告诉Apache php.ini的位置
   PHPIniDir  "D:/phpenvir/php7.1.1"   
   AddType application/x-httpd-php .php .html .htm
   ```

   注：路径根据自己实际路径来填写。

9. 保存并关闭文件，重启Apache，

10. 在Apache/htdos下新建index.php

11. 保存，测试，在浏览器输入localhost/index.php

12. 配置成功