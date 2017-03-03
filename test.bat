@echo off
call build.bat || (echo "Build error, tests not run" && exit /b 1)
echo Testing...
pandoc -o README-maven.html -s -f markdown_github-hard_line_breaks -t html5 README-maven.md ^
    || (echo Error building README-maven.html && exit /b 1)
pandoc -o README-gradle.html -s -f markdown_github-hard_line_breaks -t html5 README-gradle.md ^
    || (echo Error building README-gradle.html && exit /b 1)