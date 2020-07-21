##环境安装
### 测试&开发环境
```
composer install --no-dev --ignore-platform-reqs -o
```
### 生产环境
```
composer install --no-dev -o
``` 
###compose提升性能优化
composer dumpautoload --no-dev -o

###composer安装
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer