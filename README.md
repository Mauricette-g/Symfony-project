# Site e-commerce avec Symfony  -  Stubborn
## Boutique en ligne


## Prérequis
- PHP 8.1+
- Composer
- Symfony CLI (optionnel)
- MySQL
- Stipe


## Installation rapide
1. `composer install`
2.  Adapter `DATABASE_URL`, `MAILER_DSN`,  et `STRIPE_SECRET_KEY`.
3. `php bin/console doctrine:database:create`
4. `php bin/console make:migration`
4. `php bin/console doctrine:migrations:migrate`
5. `php bin/console doctrine:fixtures:load`
6. `php bin/console doctrine:schema:update --dump-sql` si la mise à jour de la migration ne fonctionne pas
7. `php bin/console doctrine:schema:update --force ` 
6. `symfony server:start`


## Tests
`php bin/phpunit`


## Entités
1. `php bin/console make:entity User` ajouter les propriétés de l'entité
2. `php bin/console make:entity Produit` ajouter les propriétés de l'entité


## Repository
UserRepository et ProduitRepository sont créés.


## Controllers
1. `php bin/console make:controller SecurityController` pour se connecter
2. `php bin/console make:controller RegistrationController` pour se s'inscrire 
3. `php bin/console make:controller HomeController` pour la page d'accueil
4. `php bin/console make:controller ProduitController` pour la page de Produit
5. `php bin/console make:controller PanierController` pour le panier
6. `php bin/console make:controller AdminProductController` pour la gestion de la boutique en backend.


## Fixtures
`php bin/console make:fixture ProduitFixtures`


## Form
FormType pour produit et pour s'inscrire


## Templates
1- `security` pour se connecter
2- `registration` pour se s'inscrire
3- `home`
4- `produit`
6- `admin`


## Routes

/  -> home

/produits  -> liste des produits (boutique)

/produit/id/  -> affiche produit sélectionné

/panier  -> panier

/admin/produits/  -> interface admin de gestion du backend

Ajouter le rôle ‘ ROLE_ADMIN ‘ dans la base de données pour un utilisateur admin.
Ajouter le rôle ‘ ROLE_ADMIN ‘ dans la base de données pour un utilisateur admin.

