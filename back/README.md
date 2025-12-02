# Project Parking – Backend

## Structure du projet

- **Domain/** : Entités métier, interfaces des repositories
- **Infrastructure/Repository/** : Implémentations concrètes des repositories (SQL)
- **database/** : Scripts SQL de création de la base
- **tests/** : Tests unitaires (PHPUnit)

## Fonctionnement

- Les entités principales : `User`, `Customer`, `Owner`, `Parking`, `Reservation`, `Subscription`, `Stationing`, `Schedule`, `Rate`
- Chaque entité a un repository interface dans `src/Domain/` et une implémentation SQL dans `Infrastructure/Repository/`

## Base de données

La structure complète des tables SQL dans `database/create_tables.sql`


## Suite du projet

- Implémenter la couche Application (Use Cases) dans `Application/UseCase/`
- Ajouter le système d’authentification (tokens, sécurité)
- Compléter les tests (use cases, intégration)

## Conseils

- Toujours passer par les interfaces pour accéder aux repositories
- Respecter les principes de la clean architecture
- Ne pas versionner le dossier `vendor/` (voir `.gitignore`)
