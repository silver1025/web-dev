### Nginx 证书部署

#### 证书安装

1. 使用 “WinSCP” 工具，登录 Nginx 服务器。

2. 将已获取到的

   ```
   1_www.domain.com_bundle.crt
   ```

    

   证书文件和

    

   ```
   2_www.domain.com.key
   ```

    

   私钥文件拷贝到 Nginx 服务器的

    

   ```
   /usr/local/nginx/conf
   ```

    

   目录下。

   > 说明：
   >
   > 若无 `/usr/local/nginx/conf` 目录，可新建。

3. 关闭 WinSCP 界面。

4. 使用 “PuTTY” 工具，登录 Nginx 服务器。

5. 更新 Nginx 根目录下的 conf/nginx.conf 文件。修改内容如下：

   - 

   ```
   server {
        listen 443;
        server_name www.domain.com; #填写绑定证书的域名
        ssl on;
        ssl_certificate 1_www.domain.com_bundle.crt;
        ssl_certificate_key 2_www.domain.com.key;
        ssl_session_timeout 5m;
        ssl_protocols TLSv1 TLSv1.1 TLSv1.2; #按照这个协议配置
        ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:HIGH:!aNULL:!MD5:!RC4:!DHE;#按照这个套件配置
        ssl_prefer_server_ciphers on;
        location / {
            root   html; #站点目录
            index  index.html index.htm;
        }
    }
   ```

   配置文件的主要参数说明如下：

   - listen 443：SSL 访问端口号为 443
   - ssl on：启用 SSL 功能
   - ssl_certificate：证书文件
   - ssl_certificate_key：私钥文件
   - ssl_protocols：使用的协议
   - ssl_ciphers：配置加密套件，写法遵循 openssl 标准

6. 执行以下命令，检验配置是否有误。

   - 

   ```
   bin/nginx –t
   ```

   - 是，请重新配置。
   - 否，重启 Nginx。即可使用 `https://www.domain.com` 进行访问。

#### 使用全站加密，HTTP 自动跳转 HTTPS（可选）

对于用户不知道网站可以通过 HTTPS 方式访问的情况，我们可以通过配置服务器，让其自动将 HTTP 的请求重定向到 HTTPS。
您可以在页面中添加 JS 脚本，也可以在后端程序中添加重定向，还可以通过 Web 服务器实现跳转。
若您在编译时没有去掉 pcre，Nginx 支持 rewrite 功能。您可在 HTTP 的 server 中增加 `rewrite ^(.*) https://$host$1 permanent;`，即可将80端口的请求重定向为 HTTPS。