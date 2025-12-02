# Guide de Démarrage - Ranked Crew Battle (Next.js Version)

Voici comment installer et lancer la nouvelle version du projet sur votre machine locale.

## 1. Pré-requis

*   **Node.js** (Version 18 ou supérieure recommandée). Télécharger sur [nodejs.org](https://nodejs.org/).

## 2. Installation

Ouvrez un terminal dans ce dossier (`ranked-crew-battle-next`) et lancez :

```bash
npm install
```

Cela va télécharger toutes les librairies nécessaires (Next.js, React, Prisma, etc.).

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

## Structure du Projet

*   `app/` : Le code de l'application (Pages, API).
*   `lib/` : Les fonctions utilitaires (Connexion BDD, Calcul Elo).
*   `prisma/` : Configuration de la base de données (`schema.prisma`) et fichier de base de données (`dev.db`).
*   `public/` : Images et fichiers statiques.
