## VS code 调试PHP配置

1.  用VS Code 打开php项目后，提示 "Cannot validate since no PHP executable is set. Use the setting 'php.validate.executablePath' to configure the PHP executable."

   解决方法如下：

   在“文件”-“首选项”-“设置”下添加：路径为php.exe所在路径

   ```
    "php.validate.executablePath": "D:\\php-7.3.4\\php.exe",
   ```

   <img src="https://images2015.cnblogs.com/blog/805124/201706/805124-20170607115032122-1055311383.png"/>

2. 在VSCode上安装：PHP Debug插件：

   <img src="https://images2015.cnblogs.com/blog/805124/201706/805124-20170607115313309-180211255.png"/>

3. 安装 XDebug

   - 新建phpInfo.php文件，如图

     <img src="https://images2015.cnblogs.com/blog/805124/201706/805124-20170607115819559-268899920.png"/>

   - 放到服务器运行目录下，用浏览器访问，CTRL+A全选，并粘贴到 <https://xdebug.org/wizard.php>

     注意实际操作与图片不同，按照实际Instructions操作

   <img src="https://images2015.cnblogs.com/blog/805124/201706/805124-20170607115938997-1297930984.png"/>

   

   <img src="https://images2015.cnblogs.com/blog/805124/201706/805124-20170607120343997-1066877598.png"/>

   <img src="https://images2015.cnblogs.com/blog/805124/201706/805124-20170607120508418-607046777.png"/>

   ​	本次配置运行结果如下：![1555833319424](D:\Documents\北大\研一下\互联网软件开发\学习总结报告及代码\img\1555833319424.png)

   - 打开php目录下的php.ini ，添加几行配置，包括上图配置，xdebug的路径按实际情况配置

     ```
     [XDebug]
     zend_extension = d:\php-7.3.4\ext\php_xdebug-2.7.1-7.3-vc15-x86_64.dll
     xdebug.remote_enable = 1
     xdebug.remote_autostart = 1
     ```

4. 开始调试，在"C:\wamp\www\index.php"内设置断点，浏览器访问

