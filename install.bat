@echo off
call test.bat || (echo "Test error, install aborted" && exit /b 1)
echo Copying...
if not exist ..\excelsior-jet-maven-plugin\README.md (
    echo Error: Maven plugin project not found or README.md is absent && exit /b 1
)
xcopy /q /y README-maven.md ..\excelsior-jet-maven-plugin\README.md
if not exist ..\excelsior-jet-gradle-plugin\README.md (
    echo Error: Gradle plugin project not found or README.md is absent && exit /b 1
)
xcopy /q /y README-gradle.md ..\excelsior-jet-gradle-plugin\README.md
echo.
echo Done!