@echo off
echo Building...
if exist error.log del error.log
php -d error_log=error.log -f README.md.php maven | sed -r -e "s/(\s+)$//" > README-maven.md ^
    || (echo Error building README-maven.md && exit /b 1)
php -d error_log=error.log -f README.md.php gradle | sed -r -e "s/(\s+)$//" > README-gradle.md ^
    || (echo Error building README-gradle.md && exit /b 1)
