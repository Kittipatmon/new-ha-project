@echo off
set "zipfile=%~2"
set "destdir=%~4"
powershell -NoProfile -Command "Expand-Archive -Force -Path '%zipfile%' -DestinationPath '%destdir%'"
