Création de la Database : 

Création d'une entité : php bin/console make:entity
Création d'un controller : php bin/console make:controller

Migration : php bin/console make:migration
            php bin/console doctrine:migrations:migrate


Vider le cache : php bin/console cache:clear

Démarrer le serveur Symfo : symfony server:start

Vérifier l'état de notre BDD par rapport à notre code : php bin/console doctrine:schema:validate