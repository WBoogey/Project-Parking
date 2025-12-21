# ParkShare

Système de parking partagé permettant aux propriétaires de louer leurs places de stationnement et aux clients de s'abonner pour y accéder facilement.

---

## Collaborateurs

- **JENCK Arthur**
- **OUARDI Ahmed-Amine**
- **JACQUET Oscar**
- **SAMAKE Amadou Aliou**
- **EHOURA Wuia Christ-Yvann Akora**

---

## Technologies utilisées

### Frontend

| Technologie | Version | Description |
|-------------|---------|-------------|
| React | 19 | Bibliothèque UI |
| React Router | 7 | Routage et SSR |
| TanStack Query | 5 | Gestion d'état serveur |
| Tailwind CSS | 4 | Framework CSS utilitaire |
| TypeScript | 5.9 | Typage statique |
| Vite | 7 | Bundler et dev server |
| Vitest | 4 | Framework de tests |

### Backend

| Technologie | Version | Description |
|-------------|---------|-------------|
| PHP | 8.3 | Langage serveur |
| MySQL | 8.0 | Base de données |
| Firebase JWT | 6.11 | Authentification JWT |
| Stripe PHP | 19 | Paiements en ligne |
| PHPUnit | 11 | Tests unitaires |

### Infrastructure

| Technologie | Description |
|-------------|-------------|
| Docker | Conteneurisation |
| Docker Compose | Orchestration des services |

---

## Prérequis

### Avec Docker (recommandé)

- Docker Desktop (ou Docker Engine + Docker Compose)

### Sans Docker (installation manuelle)

- Node.js 20+
- PHP 8.3+
- Composer 2+
- MySQL 8.0

---

## Installation et lancement

### Avec Docker Compose (recommandé)

```bash
# Cloner le repository
git clone <url-du-repo>
cd Project-Parking

# Créer le fichier d'environnement
cp .env.example .env
# Éditer .env avec vos valeurs (voir section Variables d'environnement)

# Lancer tous les services
docker compose up --build
```

Les services seront accessibles sur :

- Frontend : <http://localhost:5173>
- Backend API : <http://localhost:8000>
- MySQL : localhost:3306

### Sans Docker (installation manuelle)

#### 1. Base de données

Créer une base de données MySQL et exécuter le script de création des tables :

```bash
mysql -u root -p < back/database/create_tables.sql
```

#### 2. Backend

```bash
cd back

# Installer les dépendances PHP
composer install

# Créer le fichier .env et le configurer
cp .env.example .env

# Lancer le serveur PHP
php -S localhost:8000 -t public
```

#### 3. Frontend

```bash
cd front

# Installer les dépendances Node
npm install

# Lancer le serveur de développement
npm run dev
```

---

## Variables d'environnement

Créer un fichier `.env` à la racine du projet avec les variables suivantes :

### Base de données

| Variable | Description | Exemple |
|----------|-------------|---------|
| `DB_HOST` | Hôte MySQL | `localhost` ou `mysql` (Docker) |
| `DB_PORT` | Port MySQL | `3306` |
| `DB_NAME` | Nom de la base | `parking` |
| `DB_USER` | Utilisateur MySQL | `parking_user` |
| `DB_PASSWORD` | Mot de passe MySQL | `votre_mot_de_passe` |
| `MYSQL_ROOT_PASSWORD` | Mot de passe root (Docker) | `root_password` |

### Authentification

| Variable | Description | Exemple |
|----------|-------------|---------|
| `JWT_SECRET` | Clé secrète JWT | `votre_cle_secrete_256_bits` |
| `JWT_EXPIRATION` | Durée de validité (secondes) | `3600` |

### Stripe (Paiements)

| Variable | Description |
|----------|-------------|
| `STRIPE_PUBLIC_KEY` | Clé publique Stripe |
| `STRIPE_SECRET_KEY` | Clé secrète Stripe |
| `STRIPE_WEBHOOK_SECRET` | Secret du webhook Stripe |
| `STRIPE_SUCCESS_URL` | URL de redirection après paiement réussi |
| `STRIPE_CANCEL_URL` | URL de redirection après annulation |

### Frontend

| Variable | Description | Exemple |
|----------|-------------|---------|
| `VITE_API_URL` | URL de l'API backend | `http://localhost:8000/api` |

---

## Fonctionnalités principales

### Authentification

- Inscription client et propriétaire
- Connexion / Déconnexion
- Gestion des sessions JWT

### Gestion des parkings

- Recherche de parkings disponibles
- Consultation des détails et tarifs
- Ajout de parkings (propriétaires)

### Abonnements

- Abonnements hebdomadaires, mensuels et annuels
- Sélection des créneaux horaires
- Gestion des abonnements actifs

### Paiements

- Paiement sécurisé via Stripe
- Historique des transactions
- Webhooks pour confirmation automatique

### Dashboards

- **Client** : Voir et gérer ses abonnements
- **Propriétaire** : Gérer ses parkings et tarifs

---

## Commandes utiles

### Frontend (à exécuter dans `front/`)

```bash
cd front
```

| Commande | Description |
|----------|-------------|
| `npm run dev` | Lance le serveur de développement |
| `npm run build` | Build de production |
| `npm run start` | Lance le serveur de production |
| `npm run test` | Lance les tests en mode watch |
| `npm run test:run` | Lance les tests une fois |
| `npm run test:coverage` | Lance les tests avec couverture |
| `npm run typecheck` | Vérifie les types TypeScript |

### Backend (à exécuter dans `back/`)

```bash
cd back
```

| Commande | Description |
|----------|-------------|
| `composer install` | Installe les dépendances |
| `composer test` | Lance les tests PHPUnit |
| `php -S localhost:8000 -t public` | Lance le serveur de développement |

### Docker

| Commande | Description |
|----------|-------------|
| `docker compose up` | Lance tous les services |
| `docker compose up --build` | Rebuild et lance les services |
| `docker compose down` | Arrête tous les services |
| `docker compose logs -f` | Affiche les logs en temps réel |

---

## Licence

MIT
