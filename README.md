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
Aprés dans votre projet symfony mettez les paramétres de votre base de donnée dans le fichier .env.
Puiscr céer la base de donnée:

```bash
  symfony console doctrine:database:create
```

Enfin démarrer symfony avec:

```bash
  symfony serve -d
```

Si tout se passe bien vous serez dirrigé vers la page d'acceuil.
Dés fois composer ne détecte pas les dépendences de certaines bundle de symfony donc il faudra les installer manuellement. 
Contacter nous si vous avez des soucis concernant l'installation.


Repo of the project CrewEvent
Pour avoir le projet en local
Cloner le projet en local et
composer install 
installer aussi quelques dépendances comme avec composer require:
Vich Uploader
Mercure
Turbo Ux
turbo ux/Mercure
symfony serve -d

/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////

CHAT INSTANTANNEE

installation du hub mercure:

aller dans le site https://github.com/dunglas/mercure/releases
pour télécharger le package mercure selon votre systéme d'exploitation
Si vous étes Mac télécharger la version Darwin

maintenant pour démarrer le hub:
*Si vous étes windows: ouvrez powershell va dans le répertoire ou t'a installé le téléchargé le package et colle la commande
$env:MERCURE_PUBLISHER_JWT_KEY='!ChangeMe!'; $env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeMe!';$env:SERVER_NAME='localhost:3014'; .\mercure.exe run -config Caddyfile.dev

*Si vous étes linux ou mac aller dans le site https://mercure.rocks/docs/hub/install

-Pour les variables d'environnement dans le fichier .env.local:
MERCURE_URL=https://localhost:3014/.well-known/mercure
MERCURE_PUBLIC_URL=https://localhost:3014/.well-known/mercure
MERCURE_JWT_SECRET=!ChangeMe!

Si tout marche bien maintenant ouvrez le hub à l'adresse: https://localhost:3014
dans la partie setting en bas à gauche dans la partie JWT coller cette token ci-dessous: eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiKiJdfX0.Ws4gtnaPtM-R2-z9DnH-laFu5lDZrMnmyTpfU8uKyQo

Je passe un peut la génération cet token JWT mais c'est trés simple à faire.

/////Cette partie n'est pas importante: c'est juste pour la génération du token JWT //////
//Donc en bas du token JWT il y'a un bouton qui s'appelle create token:
//ça t'emméne dans le site JWT.io
//Là à droite il y'a 3 champs à renseigner:
Header: qu'on laisse comme ça
Payload: on met:
{
  "mercure": {
    "publish": ["*"],
    "subscribe": ["*"]
  }
}
Verify Signature: !ChangeMe!

A droite ça te génére la clé que tu colle dans la partie JWT du hub

