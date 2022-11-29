# CrewEvent
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

