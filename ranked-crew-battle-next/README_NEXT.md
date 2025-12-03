# Guide de Démarrage - Ranked Crew Battle (Next.js Version)

Voici comment installer et lancer la nouvelle version du projet sur votre machine locale.

## 1. Pré-requis

*   **Node.js** (Version 18 ou supérieure recommandée). Télécharger sur [nodejs.org](https://nodejs.org/).

## 2. Installation et Configuration

1.  **Installation des dépendances :**
    Ouvrez un terminal dans ce dossier (`ranked-crew-battle-next`) et lancez :
    ```bash
    npm install
    ```

2.  **Configuration des variables d'environnement :**
    Renommez le fichier `.env.example` en `.env`.
    *   Sous Windows : `ren .env.example .env`
    *   Sous Mac/Linux : `mv .env.example .env`

    *Ce fichier contient la clé API Steam (pour les tests) et le chemin vers la base de données locale.*

## 3. Base de Données (SQLite)

Le projet utilise une base de données locale (fichier `prisma/dev.db`). Pas besoin d'installer MySQL ou WampServer.

1.  **Créer la base de données :**
    ```bash
    npx prisma migrate dev --name init
    ```

2.  **Remplir avec des données de test (Seed) :**
    ```bash
    npx ts-node --compiler-options '{"module":"CommonJS"}' prisma/seed.ts
    ```
    *Cela va créer les clans fictifs (Les Vainqueurs, Les Challengers, etc.) pour que le site ne soit pas vide.*

## 4. Lancer le Projet

Pour démarrer le serveur de développement :

```bash
npm run dev
```

Ouvrez ensuite votre navigateur sur **[http://localhost:3000](http://localhost:3000)**.

## 5. Outils Utiles

### Voir et Modifier la Base de Données (Prisma Studio)
Pour inspecter vos données sans écrire de SQL, utilisez l'interface visuelle incluse :

```bash
npx prisma studio
```
Cela ouvrira une page web où vous pourrez voir vos tables (`Clan`, `User`, `Tournament`) et modifier les données directement.

### Commandes en cas de problème

*   Si la base de données semble cassée ou si vous voulez repartir de zéro :
    ```bash
    npx prisma migrate reset
    ```
    *(Attention, cela efface toutes les données !)*

## Dépannage Windows

### Erreur "L'exécution de scripts est désactivée" (PowerShell)
Si vous voyez une erreur rouge mentionnant `PSSecurityException` ou `UnauthorizedAccess` dans PowerShell :

**Solution 1 (Recommandée) : Utiliser l'Invite de Commandes (CMD)**
Fermez PowerShell et ouvrez "Invite de commandes" (tapez `cmd` dans la barre de recherche Windows). Les commandes `npm` fonctionneront sans problème.

**Solution 2 : Autoriser les scripts dans PowerShell**
1. Ouvrez PowerShell en tant qu'**Administrateur** (Clic droit > Exécuter en tant qu'administrateur).
2. Tapez : `Set-ExecutionPolicy RemoteSigned`
3. Tapez `O` (ou `Y`) pour confirmer.
4. Relancez votre commande `npm install`.

## Structure du Projet

*   `app/` : Le code de l'application (Pages, API).
*   `lib/` : Les fonctions utilitaires (Connexion BDD, Calcul Elo).
*   `prisma/` : Configuration de la base de données (`schema.prisma`) et fichier de base de données (`dev.db`).
*   `public/` : Images et fichiers statiques.
