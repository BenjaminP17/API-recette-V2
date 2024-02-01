# API REST recettes de cuisines :fork_and_knife:
![Symfony](https://img.shields.io/badge/symfony-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)

API qui regroupe des recettes de cuisine du monde.

Projet développé dans le but de mettre en 
pratique mon apprentissage de PHP/Symfony, et d'y être partagé via mon [portfolio](https://www.benjaminpelissier.com).


## Documentation 📖

[Documentation complète](https://linktodocumentation) (en cours d'écriture)

## Pré-requis :white_check_mark:

- PHP 8.1 ou version plus récente 
- MySQL
- Apache

## Installation

_Récupérer le projet_

```sh
git clone git@github.com:BenjaminP17/API-recette-V2.git
```

_Installer les dépendences_ 

```sh
composer install
```

_Configurer le fichier_ `.env`

`DATABASE_URL="mysql://UTILISATEUR:MOTDEPASSE@127.0.0.1:3306/NOMDELABDD?charset=utf8mb4"`

_Créer la base de données_

```sh
php bin/console doctrine:database:create
```

## Fonctionnalités développées

- CRUD
- Recherche par mots clés et catégorie
- Création de compte
- Ajout en favoris de recettes
- Commentaire et notation

## Status du projet

![Static Badge](https://img.shields.io/badge/En%20cours-green)

