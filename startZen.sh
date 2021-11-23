#!/bin/bash
cd ~/workspace/zenity/webService/
bin/console server:start localhost:8002

cd ~/workspace/zenity/appli/
bin/console server:start localhost:8001
