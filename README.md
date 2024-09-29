                                                                                     Arcadia Zoo API


README - Back-End
Version: 0.1 (2024-09-27)
Ce fichier README a été généré le 2024-09-29 par M'barka Toure.
Dernière mise-à-jour le : 2024-09-27.

INFORMATIONS GENERALES
Titre du projet :
API du Zoo Arcadia



Adresse de contact :
M'barka Toure
Email : [MbarkaToure04@gmail.com]

INFORMATIONS METHODOLOGIQUES
Conditions environnementales / expérimentales :
Le développement back-end a été réalisé dans un environnement local utilisant XAMPP pour le serveur et la base de données.

Description des sources et méthodes utilisées pour collecter et générer les données :
L'application utilise un système de gestion de bases de données relationnelles avec MariaDB. Les fonctionnalités ont été définies en collaboration avec les formateurs de Studi.

Méthodes de traitement des données :
Le back-end a été développé en PHP avec le framework Symfony. Les opérations CRUD sont gérées via des entités Doctrine, facilitant l'interaction avec la base de données. La sécurité est assurée par JWT pour l'authentification des utilisateurs.

Procédures d’assurance-qualité appliquées sur les données :
Des tests unitaires et fonctionnels ont été réalisés pour s'assurer que chaque fonctionnalité répond aux exigences spécifiées dans le cahier des charges.

Autres informations contextuelles :
La sécurité des données est une priorité, avec des mécanismes de protection intégrés pour gérer les informations sensibles des utilisateurs et des animaux.

APERCU DES DONNEES ET FICHIERS
Convention de nommage des fichiers :
Les fichiers de code sont nommés de manière fonctionnelle, par exemple, ZooController.php, JwtService.php, et ApiTokenAuthenticator.php.

Arborescence/plan de classement des fichiers :
/projet-zoo-backend
│
├── src
│   ├── Controller
│   │   └── ZooController.php
│   ├── Service
│   │   └── JwtService.php
│   └── Security
│       └── ApiTokenAuthenticator.php
└── config
    └── packages
        └── security.yaml
INFORMATIONS SPECIFIQUES AUX DONNEES POUR : ZooController.php
Liste des variables/entêtes de colonne :
Nom de la variable : animal
Description : Détails de l'animal dans la base de données
Unité de mesure : N/A
Valeurs autorisées : Liste d'animaux
Nom de la variable : habitat
Description : Habitat de l'animal
Unité de mesure : N/A
Valeurs autorisées : Liste d'habitats
Code des valeurs manquantes :
Les valeurs manquantes sont représentées par "NULL" dans la base de données.

Informations additionnelles :
Ce fichier gère les routes et les opérations CRUD associées aux animaux et habitats du zoo.

