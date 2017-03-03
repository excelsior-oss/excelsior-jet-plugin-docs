@echo off
for %%f in (README-gradle.html,README-gradle.md,README-maven.html,README-maven.md) do (
    if exist %%f del %%f
)