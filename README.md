# CrewEvent

  ![alt text](https://github.com/CrewEvent/CrewEvent/blob/master/public/images/crewevent.png?raw=true)

## Description

Ce projet vise à créer un réseau social centré sur une activité événementielle. Les internautes qui auront accès à notre site web pourront consulter les activités disponibles, y participer et interagir avec d'autres membres de la communauté. Pour les organisateurs d'activités, ils pourront promouvoir leurs événements, établir des statistiques sur leurs participants afin de faire des bilans, et établir un répertoire de contacts, etc.


![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/symfony-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/bootstrap-%23563D7C.svg?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E)

## Installation

D'abord, assurez-vous d'avoir une version de PHP supérieure à 8.1 et une version de Symfony supérieure à 5.4.

Tout d'abord, clonez le projet localement :

```bash
  git clone https://github.com/CrewEvent/CrewEvent.git
  cd CrewEvent
```

Installez les dépendances avec :

```bash
  composer install
```

Installez npm avec :

```bash
  npm install
```

Installez bootstrap :

```bash
  npm -i bootstrap -D
```


Pour la base de données locale, vous avez besoin de MySQL sur votre machine. Vous pouvez utiliser XAMPP (https://www.apachefriends.org/fr/index.html) ou WAMP (https://www.wampserver.com/) selon votre préférence.


Ensuite, démarrez votre base de données en local. 
Dans votre projet Symfony, mettez les paramètres de votre base de données(connexion à MYSQL) dans la section "DATABASE_URL",  dans le fichier .env . 

Ensuite, créez la base de données :

```bash
  symfony console doctrine:database:create
```

Configurez également le paramètre " MAILER_DSN " pour l'envoi de courriels dans le fichier .env. Si vous n'avez pas de solution de test d'envoi de courriels, copiez et collez cette ligne :

```
MAILER_DSN=smtp://16cf114b74c58f:774cd13a1710fc@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login
```

Enfin, démarrez Symfony avec :

```bash
  symfony serve -d
```

Si tout se passe bien, vous serez redirigé vers la page d'accueil. Parfois, Composer ne détecte pas les dépendances de certains bundles de Symfony, il faudra donc les installer manuellement. 
Contactez-nous si vous rencontrez des problèmes lors de l'installation.

## Setup locale de Mercure

Notre application dispose d'un système de chat instantané en utilisant Mercure, que l'on peut facilement tester en local.

Dans votre fichier .env, mettez ces paramètres :

```
MERCURE_URL=https://localhost:3014/.well-known/mercure
MERCURE_PUBLIC_URL=https://localhost:3014/.well-known/mercure
MERCURE_JWT_SECRET=!ChangeMe!

```
Démarrez maintenant le hub Mercure avec :

```bash
   cd mercure
   $env:MERCURE_PUBLISHER_JWT_KEY='!ChangeMe!'; $env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeMe!';$env:SERVER_NAME='localhost:3014'; .\mercure.exe run -config Caddyfile.dev
```

Si tout fonctionne bien jusque-là, ouvrez le hub à l'adresse suivante : https://localhost:3014.
Sur certains systèmes d'exploitation différents, comme Mac et Linux, il peut être ouvert à l'adresse suivante : https://localhost


## Fonctionnalités 

Voici la liste des fonctionnalités de l'application CrewEvent :

- Système de connexion et d'inscription
- Gestionnaire de contacts
- Gestionnaire de profil
- Gestionnaire d'événements
- Gestionnaire de notifications
- Chat instantané
- Publications, commentaires et likes
- Partage d'événements
- Suggestions d'événements
- Recherche de contacts et d'événements

## Generer les fixtures
- Installation du bundle
```
composer require --dev orm-fixtures
```

- Chargement des fixtures dans la db
```
php bin/console doctrine:fixtures:load
```


## Tech Stack

**Client:** Bootstrap, Twig, Html and css

**Server:** PHP, Symfony

## Support

Si vous avez besoin de support, rejoignez notre groupe Discord : https://discord.gg/yCdvSf2Y. 
Nous sommes disponibles et ouverts à toutes vos questions.



## Les leçons

Pendant un temps limité, nous avons pris en main Symfony et, pour beaucoup d'entre nous, cela a été notre premier projet. Nous avons appris beaucoup de choses, notamment en matière de gestion de projet avec l'application GitHub, de travail en équipe et de recherche d'informations parfois difficiles à trouver. 
