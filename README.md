# MATTAPHP
![](/public/images/mattaphp.png)
## Installation

- faire un git clone du projet :
```bash
git clone https://github.com/matteobnvl/mattaphp.git
```

- Installer les dépendances :

```shell
composer install
```

- Faire une copie du `.env.local` en `.env` en y ajoutant vos informations.

- Supprimer l'origine du depôt :

```bash
git remote remove origin
```

### Option 1 : utilisation docker

Prérequis : docker d'installer sur le pc

 - Lancer le container :

```shell
docker-composer up -d
```

- Retrouver l'application lancé sur :

http://localhost/

### Option 2 : votre envrionnement

Nécessaire : 

 - PHP qui tourne 
 - Une base de données MySql 
 - Un serveur Apache

Changer dans le `.env` la variable `APP_URL` en fonction de votre environnement
```
APP_URL = http://localhost/[Votre nom de dossier]
```

## Documentation

Pour plus de détails vous pouvez jeter un coups d'oeil à la documentation officielle, vous pouvez y retrouver les routes, les migrations, les fixtures, etc ...

https://mattaphp.matteo-bonneval.fr
