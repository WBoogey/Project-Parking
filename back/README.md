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

- Use Case
- Auth système, Middleware, token
- Compléter les tests (use cases, intégration)

## Conseils

- Toujours passer par les interfaces pour accéder aux repositories
- Respecter les principes de la clean architecture
- Ne pas versionner le dossier `vendor/` (voir `.gitignore`)
# Project Parking API

## Prérequis

- PHP 8.1+
- MySQL
- Composer
- Bruno CLI (`npm i -g @usebruno/cli`) ou l'extension VSCode Bruno

## Installation

```bash
composer install
cp .env.example .env  # Configurer DB_* et JWT_SECRET
```

## Lancer le serveur

```bash
php -S localhost:8000 -t public
```

## Endpoints API

### Auth (publics)

| Méthode | Endpoint          | Description             |
| ------- | ----------------- | ----------------------- |
| POST    | /api/auth/signup  | Inscription utilisateur |
| POST    | /api/auth/signin  | Connexion               |
| POST    | /api/auth/signout | Déconnexion             |

### User (authentifié)

| Méthode | Endpoint      | Description         |
| ------- | ------------- | ------------------- |
| GET     | /api/users/me | Profil utilisateur  |
| PUT     | /api/users/me | Modifier le profil  |
| DELETE  | /api/users/me | Supprimer le compte |

### Owner (authentifié + rôle owner)

| Méthode | Endpoint             | Description          |
| ------- | -------------------- | -------------------- |
| GET     | /api/owner/dashboard | Dashboard owner      |
| GET     | /api/owner/parkings  | Liste des parkings   |
| POST    | /api/owner/parkings  | Ajouter un parking   |
| DELETE  | /api/owner/parkings  | Supprimer un parking |

### Customer (authentifié + rôle customer)

| Méthode | Endpoint                    | Description              |
| ------- | --------------------------- | ------------------------ |
| GET     | /api/customer/dashboard     | Dashboard customer       |
| GET     | /api/customer/reservations  | Liste des réservations   |
| GET     | /api/customer/subscriptions | Liste des abonnements    |
| GET     | /api/customer/stationings   | Liste des stationnements |

### Subscriptions (authentifié)

| Méthode | Endpoint               | Description            |
| ------- | ---------------------- | ---------------------- |
| GET     | /api/subscriptions     | Liste mes abonnements  |
| GET     | /api/subscriptions/:id | Détail d'un abonnement |
| POST    | /api/subscriptions     | Créer un abonnement    |
| PUT     | /api/subscriptions/:id | Modifier un abonnement |
| DELETE  | /api/subscriptions/:id | Annuler un abonnement  |

## Tests API (Bruno)

```bash
cd docs/api
```

### Tests unitaires

```bash
# Auth
bru run auth/signup.bru --env local
bru run auth/signin.bru --env local
bru run auth/signout.bru --env local
bru run auth/signup-owner.bru --env local

# User
bru run users/me.bru --env local
bru run users/update-profile.bru --env local
bru run users/delete-profile.bru --env local

# Customer
bru run customer/dashboard.bru --env local
bru run customer/reservations.bru --env local
bru run customer/subscriptions.bru --env local
bru run customer/stationings.bru --env local

# Owner
bru run owner/dashboard.bru --env local
bru run owner/get-parkings.bru --env local
bru run owner/add-parking.bru --env local
bru run owner/remove-parking.bru --env local

# Subscriptions
bru run subscriptions/list.bru --env local
bru run subscriptions/create.bru --env local
bru run subscriptions/show.bru --env local
bru run subscriptions/update.bru --env local
bru run subscriptions/cancel.bru --env local
```

### Chaînes de tests (requiert auth)

```bash
# Flow Customer complet
bru run auth/signup.bru users/me.bru users/update-profile.bru customer/dashboard.bru customer/reservations.bru customer/subscriptions.bru customer/stationings.bru --env local

# Flow Owner complet
bru run auth/signup-owner.bru users/me.bru owner/dashboard.bru owner/get-parkings.bru owner/add-parking.bru owner/remove-parking.bru --env local

# Flow Subscription complet (nécessite parkingId et rateId existants)
bru run auth/signup.bru subscriptions/list.bru subscriptions/create.bru subscriptions/show.bru subscriptions/update.bru subscriptions/cancel.bru --env local

# Signup → Signin → Me → Update → Signout
bru run auth/signup.bru auth/signout.bru auth/signin.bru users/me.bru users/update-profile.bru auth/signout.bru --env local

# Signup → Delete account
bru run auth/signup.bru users/me.bru users/delete-profile.bru --env local

# Tous les tests
bru run --env local
```

### Notes sur les tests chaînés

- Les tests chaînés partagent les cookies entre les requêtes
- `signup` ou `signin` doit être exécuté en premier pour obtenir le cookie `auth_token`
- Les endpoints `/api/owner/*` nécessitent un utilisateur avec `role: "owner"`
- Les endpoints `/api/customer/*` nécessitent un utilisateur avec `role: "customer"`
- Les tests de subscription nécessitent des `parkingId` et `rateId` valides en base

## Structure du projet

```
back/
├── public/index.php
├── src/
│   ├── Domain/
│   │   ├── User/
│   │   │   └── Application/     # Use-cases (Signup, Signin, GetProfile, etc.)
│   │   ├── Owner/
│   │   │   └── Application/     # Use-cases (GetParkings, AddParking, etc.)
│   │   ├── Customer/
│   │   │   └── Application/     # Use-cases (GetReservations, etc.)
│   │   ├── Subscription/
│   │   │   └── Application/     # Use-cases (Create, Update, Cancel, etc.)
│   │   ├── Parking/
│   │   ├── Reservation/
│   │   ├── Stationing/
│   │   ├── Rate/
│   │   ├── Payment/             # DTOs pour Stripe (PaymentRequest, etc.)
│   │   ├── Port/                # Interfaces (JwtService, PaymentGateway)
│   │   ├── Schedule/
│   │   └── TimeInterval/
│   ├── HTTP/
│   │   ├── UserController.php
│   │   ├── OwnerController.php
│   │   ├── CustomerController.php
│   │   └── SubscriptionController.php
│   ├── Services/
│   │   ├── UserService.php
│   │   ├── OwnerService.php
│   │   ├── CustomerService.php
│   │   └── SubscriptionService.php
│   ├── Helper/
│   ├── Infrastructure/
│   │   ├── Core/Config/
│   │   ├── Core/Domain/
│   │   ├── Middleware/
│   │   ├── Repository/
│   │   └── adaptaters/          # JWT, Stripe adapters
│   └── routes/
│       ├── user.php
│       ├── owner.php
│       ├── customer.php
│       ├── subscription.php
│       └── app.php
├── database/
├── docs/api/
│   ├── auth/
│   ├── users/
│   ├── owner/
│   ├── customer/
│   ├── subscriptions/
│   └── environments/
└── tests/
```

## Auth

JWT stocké dans cookie HttpOnly `auth_token`.

### Rôles disponibles

- `customer` (défaut) - Accès aux endpoints customer
- `owner` - Accès aux endpoints owner
- `admin` - Accès administrateur

## Paiement (Stripe)

Configuration dans `.env` :

```
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
```

L'intégration Stripe utilise le pattern Port/Adapter :

- `Domain/Port/PaymentGatewayInterface.php` - Interface abstraite
- `Infrastructure/adaptaters/StripePaymentAdapter.php` - Implémentation Stripe
