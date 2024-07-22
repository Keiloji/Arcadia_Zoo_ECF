                                                                                     Arcadia Zoo API


Bienvenue dans le projet Arcadia Zoo API ! Ce projet est une API pour gérer les données d'un zoo, utilisant Symfony. Ce README fournit des informations sur la configuration, les mécanismes de sécurité, et les résolutions de problèmes rencontrés durant le développement.

Table des Matières
Introduction
Installation
Configuration
Mécanismes de Sécurité
Problèmes Résolus
Veille Technologique
Contributions
Introduction


Arcadia Zoo API est une application Symfony conçue pour gérer les informations des animaux et des installations d'un zoo. Ce projet utilise des pratiques modernes de développement web et suit les meilleures pratiques de sécurité pour protéger les données.

Installation
Clonez le dépôt depuis GitHub et accédez au répertoire du projet.

Installez les dépendances via Composer.


Configurez votre base de données en modifiant les paramètres de connexion.

Créez et migrez la base de données.

Démarrez le serveur Symfony pour tester l'application.

Configuration

Assurez-vous que les fichiers de configuration sont correctement définis :

NelmioApiDocBundle : Vérifiez que le fichier de configuration est correctement formaté pour éviter les erreurs de chargement YAML.

NelmioCorsBundle : Utilisez des espaces pour l'indentation dans le fichier de configuration afin d'éviter les erreurs YAML.

Mécanismes de Sécurité
Voici les mécanismes de sécurité que nous avons mis en place dans le projet :

Formulaires :

Validation des données des utilisateurs pour éviter les soumissions incorrectes.

Protection contre les attaques CSRF (Cross-Site Request Forgery).

Composants Front-End :

Validation côté client pour garantir des données correctes avant soumission.

Composants Back-End :

Authentification via tokens JWT pour sécuriser les points de terminaison de l'API.

Règles d'accès pour protéger les routes sensibles.

Problèmes Résolus
Configuration YAML : Erreurs liées aux caractères inattendus ou à l'indentation ont été résolues en ajustant le formatage des fichiers YAML.

Problèmes d'Indentation : Les erreurs dues aux tabulations dans les fichiers YAML ont été corrigées en utilisant des espaces.

Extension de Configuration : Les erreurs concernant les extensions non trouvées ont été réglées en vérifiant les bundles installés et leur configuration.


Veille Technologique
J'ai effectué une veille technologique pour rester informé des vulnérabilités de sécurité et des meilleures pratiques en consultant des sources comme SymfonyCasts. Cette veille a permis d'améliorer la sécurité de l'API en suivant les recommandations actuelles.


Les contributions sont les bienvenues ! Pour proposer des améliorations ou signaler des bugs, veuillez ouvrir une issue ou soumettre une pull request sur GitHub.

