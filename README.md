
# Entercorp project

Entercorp est un site web, dédier à l'entreprise, il agit sous forme de forum pour les employés et les même les externes à l'entreprise.


## Installation

Après avoir récupérer le projet, dans une console powershell :

```composer install```

Après avoir ouvert mamp, xamp ou wamp, puis toujours dans le shell : 

```php bin/console doctrine:database:create``` \
```php bin/console make:migration```

Permet de créer la base de données, une fois ceci fait :

```symfony server:start```

Permet de lancer le serveur d'accès au site.

    
## Ajout role administrateur

Pour ajouter un role d'administrateur à un utilisateur, il faut aller dans la base de données, dans la table user et ajouter le role "ROLE_ADMIN" à l'utilisateur, permettant d'accéder à l'espace administrateur.
