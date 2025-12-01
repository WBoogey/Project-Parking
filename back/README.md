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

## Tests API (Bruno)

```bash
cd docs/api

# Test unique
bru run auth/signin.bru --env local

# Chaîne de tests
bru run auth/signin.bru users/me.bru --env local

# Tous les tests
bru run --env local
```

## Structure du projet

```
back/
├── public/index.php
├── src/
│   ├── Domain/
│   │   ├── User/
│   │   ├── Parking/
│   │   ├── Reservation/
│   │   ├── Subscription/
│   │   ├── Stationing/
│   │   ├── Rate/
│   │   ├── Schedule/
│   │   └── TimeInterval/
│   ├── HTTP/
│   ├── Services/
│   ├── Helper/
│   ├── Infrastructure/
│   │   ├── Core/Config/
│   │   ├── Core/Domain/
│   │   ├── Middleware/
│   │   ├── Repository/
│   │   └── adaptaters/
│   └── routes/
├── database/
├── docs/api/
└── tests/
```

## Auth

JWT stocké dans cookie HttpOnly `auth_token`.
