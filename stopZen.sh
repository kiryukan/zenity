#!/bin/bash
cd ~/workspace/zenity/webService/
bin/console server:stop

cd ~/workspace/zenity/appli/
bin/console server:stop localhost:8001
