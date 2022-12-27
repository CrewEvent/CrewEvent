# CrewEvent

  ![alt text](https://github.com/CrewEvent/CrewEvent/blob/master/public/images/crewevent.png?raw=true)

## Description

Ce projet aura pour objectif principal de créer un réseau social centré autour d’une activité événementielle. Les internautes ayant accès à notre site web pourront donc voir les activités disponibles, y participer et bien sûr interagir avec d’autres membres de cette communauté. Pour les organisateurs d’activité ils pourront promouvoir leurs événements, faire un statistique de leurs participants afin de pouvoir faire des bilans, construire un répertoire de contact etc.


![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/symfony-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/bootstrap-%23563D7C.svg?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E)

## Installation

D'abord ayez sur d'avoir une version de php supérieur à 8.1 et une version de symfony supérieur à 5.4.

Cloner d'abord le projet en locale:

```bash
  git clone https://github.com/CrewEvent/CrewEvent.git
  cd CrewEvent
```

Installer les dépendances avec:

```bash
  composer install
```

Installer npm avec:

```bash
  npm install
```

Installer bootstrap:

```bash
  npm -i bootstrap -D
```


Pour la base de locale il vous faut Mysql sur votre machine vous pouvez donc disposer de xamp: https://www.apachefriends.org/fr/index.html ou de wamp:  https://www.wampserver.com/ selon votre préférence.


Puis démarrer votre base de donnée en locale.
Aprés dans votre projet symfony mettez les paramétres de votre base de donnée dans le paramétre `DATABASE_URL` dans le fichier .env.

Puis céer la base de donnée:

```bash
  symfony console doctrine:database:create
```

settez aussi le paramétre `MAILER_DSN` pour l'envoie des mails dans le fichier .env aussi. Si vous n'avez pas de solutions de teste d'envoie de mail, copier et coller cette ligne:

```
MAILER_DSN=smtp://16cf114b74c58f:774cd13a1710fc@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login
```

Enfin démarrer symfony avec:

```bash
  symfony serve -d
```

Si tout se passe bien vous serez dirrigé vers la page d'acceuil.
Dés fois composer ne détecte pas les dépendences de certaines bundle de symfony donc il faudra les installer manuellement. 
Contacter nous si vous avez des soucis concernant l'installation.

## Setup locale de Mercure

Notre application dispose d'un systéme de chat instantannée utilisant mercure que l'on peut tester facilement en locale.
Dans votre fichier .env mettez ces paramétres:

```
MERCURE_URL=https://localhost:3014/.well-known/mercure
MERCURE_PUBLIC_URL=https://localhost:3014/.well-known/mercure
MERCURE_JWT_SECRET=!ChangeMe!

```
Démarrer maintenant le hub mercure avec:

```bash
   cd mercure
   $env:MERCURE_PUBLISHER_JWT_KEY='!ChangeMe!'; $env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeMe!';$env:SERVER_NAME='localhost:3014'; .\mercure.exe run -config Caddyfile.dev
```

Si tout marche bien jusque là ouvrez le hub à l'adresse: https://localhost:3014


## Fonctionnalités 

Voici la liste des fonctionnalités de l'application CrewEvent.

- Systéme de connection et d'inscription
- Géstionnaire de contacts
- Gestionnaire de profil
- Gestionnaire d'événement
- Gestionnaire de notifications
- Chat instantannée
- Publications, commenataire, et like
- Partage d'événements
- Suggestions d'événement
- Recherche de contacts et d'événements
