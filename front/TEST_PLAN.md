# Plan de Test Manuel - Project Parking

Ce document décrit les étapes pour valider manuellement le bon fonctionnement de l'application front-end.

## 1. Prérequis
- Le backend doit être lancé et accessible à l'adresse configurée (ex: `http://localhost:8000`).
- Le frontend doit être lancé avec `npm run dev` et accessible (ex: `http://localhost:5173`).

## 2. Authentification

### Scénario 2.1 : Inscription Propriétaire (Pro)
1.  Aller sur la page d'accueil.
2.  Cliquer sur "S'inscrire".
3.  Cliquer sur "Créer un compte professionnel".
4.  Remplir le formulaire avec des données valides (Prénom, Nom, Email, Mot de passe, Société, Adresse, Ville).
5.  Soumettre le formulaire.
6.  **Résultat attendu :** Redirection vers le tableau de bord propriétaire (`/owner/dashboard`).

### Scénario 2.2 : Inscription Particulier (Client)
1.  Aller sur la page d'inscription (`/register`).
2.  Remplir le formulaire (Prénom, Nom, Email, Mot de passe).
3.  Soumettre.
4.  **Résultat attendu :** Redirection vers la page d'accueil (`/`) ou connexion réussie.

### Scénario 2.3 : Connexion
1.  Aller sur la page de connexion (`/login`).
2.  Entrer les identifiants d'un compte existant.
3.  Soumettre.
4.  **Résultat attendu :** Redirection vers la page d'accueil ou dashboard selon le rôle (implémentation actuelle redirige vers `/`).

## 3. Espace Propriétaire

### Scénario 3.1 : Affichage du Dashboard
1.  Se connecter en tant que propriétaire.
2.  Aller sur `/owner`.
3.  **Résultat attendu :**
    - Affichage de la liste des parkings existants.
    - Si aucun parking, affichage du message "Vous n'avez pas encore ajouté de parking".

### Scénario 3.2 : Ajout d'un Parking
1.  Depuis le dashboard, cliquer sur "Ajouter un parking".
2.  Remplir l'adresse et le nombre de places.
3.  Soumettre.
4.  **Résultat attendu :**
    - Redirection vers le dashboard.
    - Le nouveau parking apparaît dans la liste.

### Scénario 3.3 : Suppression d'un Parking
1.  Sur le dashboard, identifier un parking à supprimer.
2.  Cliquer sur le bouton "Supprimer".
3.  Confirmer la boîte de dialogue.
4.  **Résultat attendu :** Le parking disparaît de la liste.

## 4. Espace Client (Fonctionnalités Mockées)

### Scénario 4.1 : Recherche de Parking
1.  Aller sur "Trouver un parking" (depuis la barre de navigation).
2.  Saisir "Paris" dans la barre de recherche.
3.  **Résultat attendu :** La liste des parkings filtrée s'affiche (données mockées).

### Scénario 4.2 : Détails d'un Parking
1.  Cliquer sur une carte de parking dans les résultats de recherche.
2.  **Résultat attendu :** Affichage de la page de détails avec description, services et tarifs.

### Scénario 4.3 : Simulation de Paiement
1.  Sur la page de détails, cliquer sur "Réserver maintenant".
2.  Remplir le formulaire de paiement (données fictives).
3.  Cliquer sur "Payer".
4.  **Résultat attendu :** Affichage de l'écran de confirmation "Réservation confirmée !".
