@echo off
if not exist ..\excelsior-jet-maven-plugin.wiki%1 (
    echo Error: ..\excelsior-jet-maven-plugin.wiki%1 does not exist && exit /b 1
)
if not exist ..\excelsior-jet-gradle-plugin.wiki%1 (
    echo Error: ..\excelsior-jet-maven-plugin.wiki%1 does not exist && exit /b 1
)
echo Building...
if exist error.log del error.log
if not exist ..\excelsior-jet-maven-plugin.wiki%1\Home.md (
    echo Error: Maven plugin wiki not found or Home.md is absent && exit /b 1
)
if not exist ..\excelsior-jet-gradle-plugin.wiki%1\Home.md (
    echo Error: Gradle plugin wiki not found or Home.md is absent && exit /b 1
)
for %%s in (src\wiki\*.md.php) do (
    php -d error_log=error.log -f src\wiki\wiki.php maven %%s | sed -r -e "s/(\s+)$//" > ..\excelsior-jet-maven-plugin.wiki%1\%%~ns ^
        || (echo Error building Maven %%s page && exit /b 1)
)

for %%s in (src\wiki\*.md.php) do (
    php -d error_log=error.log -f src\wiki\wiki.php gradle %%s | sed -r -e "s/(\s+)$//" > ..\excelsior-jet-gradle-plugin.wiki%1\%%~ns ^
        || (echo Error building Gradle %%s page && exit /b 1)
)
