## apache搭建

#### 下载

2.4.39 版本64位： <https://home.apache.org/~steffenal/VC15/binaries/httpd-2.4.39-win64-VC15.zip>

#### 解压文件

解压到自定义的文件夹如"d:/"，Read me.txt会告诉我们如何使用，根据里面的说明配置，其中Apache文件夹就是我们要用到的服务器程序

#### 修改配置文件

打开Apache24\conf\httpd.conf

如果下载的是 **2.4.39** 版本，只需要修改一个位置即可：

修改第37行，Define SRVROOT "**c:/Apache24**"改为 Define SRVROOT "**d:/Apache24**" 即可

#### 运行服务器

进入**Apache24\bin\ ** 目录下

方法一：双击httpd.exe程序
此时会弹出一个窗口，当窗口打开时，服务器就是开启了
当将窗口关闭时，服务器也就关闭了

方法二：双击ApacheMonitor.exe
在任务栏会出现图标，右击小图标会显示“Open Apatch Monitor” ，点击打开Apache监视器
打开后，点击Start即可启动服务器，如需停止服务器，点击Stop.

方法三：CMD命令行启动

>httpd.exe

方法四：开机自动运行
>httpd.exe -k install

如需卸载开机自启动
>httpd.exe -k uninstall

#### 放入我们自己的文件并尝试下载
将Apache24\htdocs文件夹下面的 index.html 文件删除，我们刚才看到的 It works! 页面就是这个文件的作用，然后将我们的文件及文件夹放到 Apache24\htdocs 下面，浏览器就可以下载了。

