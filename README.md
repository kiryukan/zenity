# zenity
Monitoring and reporting of Oracle databases application.
- Base App framework --> Symfony
- ORM --> Doctrine
- Templates --> twig / mustache

Parts:
- Front client side --> appli
- Back api side     --> webservice

Application has a lot of sequentials mechanisms before to be accessible by web interface:
Here are globals steps:
- 1 - retrieve raw datas (called "snapshots") from clients db queries
- 2 - read and parse raw datas by db parameters (python scripts) process and inject it into database
- 3 - read and render datas to UI
