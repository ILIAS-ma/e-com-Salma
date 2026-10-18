# ATLAS — Boutique e-commerce prêt-à-porter (Maroc)

MVP de production pour une marque de prêt-à-porter marocaine : catalogue avec variantes taille/couleur,
livraison par zones/villes marocaines avec calcul en direct, checkout en une page, paiement CMI/Stripe/PayPal/COD,
facturation PDF et back-office d'administration.

## Stack

- **Backend** : Laravel 13 / PHP 8.3+
- **Frontend** : Blade + Tailwind CSS (esthétique éditoriale façon Zara, palette terracotta/olive) + Alpine.js
- **Base de données** : SQLite par défaut en local (compatible MySQL/PostgreSQL sans changement de code)
- **PDF** : barryvdh/laravel-dompdf
- **Paiement** : Stripe Checkout, PayPal Orders API v2, CMI (hosted page 3D Secure), Cash on Delivery

## Architecture

- `app/Models` — Category, Product, ProductVariant, ProductImage, City, ShippingZone, Order, OrderItem, Payment
- `app/Services` — `CartService` (panier en session), `ShippingCalculator`, `CheckoutService` (transaction complète : verrouillage stock, calcul frais de port, création commande, appel de la passerelle de paiement)
- `app/Services/Payments` — `PaymentGatewayInterface` + implémentations `StripeGateway`, `PayPalGateway`, `CmiGateway`, `CashOnDeliveryGateway`, sélectionnées via `PaymentGatewayFactory`
- `app/Http/Requests` — `CheckoutRequest`, `Admin/ProductRequest`
- `app/Http/Controllers` — contrôleurs fins délégant aux services ; `Admin/*` pour le back-office (protégé par le middleware `admin`)

## Installation

```bash
# 1. Dépendances PHP et JS
composer install
npm install

# 2. Environnement
cp .env.example .env
php artisan key:generate

# 3. Base de données (SQLite par défaut)
touch database/database.sqlite
php artisan migrate --seed

# 4. Assets front-end
npm run build   # ou `npm run dev` pendant le développement

# 5. Lancer le serveur
php artisan serve
```

Ouvrez `http://localhost:8000`.

- Compte admin de démo : **admin@atlas.ma / password** → `/admin`
- Les seeders créent 3 zones de livraison, ~25 villes marocaines, un catalogue arborescent (Femme/Homme/Enfant/Collections/Accessoires) et 48 produits avec variantes taille/couleur.

### Utiliser MySQL ou PostgreSQL

Dans `.env`, remplacez :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atlas
DB_USERNAME=root
DB_PASSWORD=
```

puis relancez `php artisan migrate --seed`. Aucune migration n'est spécifique à SQLite.

## Paiement — configuration

Renseignez dans `.env` (voir `.env.example`) :

```env
STRIPE_KEY=
STRIPE_SECRET=

PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
PAYPAL_MAD_TO_USD_RATE=0.1   # PayPal ne règle pas en MAD ; taux de conversion fixe utilisé pour le sandbox

CMI_GATEWAY_URL=https://payment.cmi.co.ma/fim/est3Dgate
CMI_MERCHANT_ID=
CMI_STORE_KEY=
```

- **CMI** est le processeur bancaire dominant au Maroc. Il n'existe pas de SDK Composer officiel : l'intégration
  se fait via une page hébergée avec un formulaire auto-soumis et une signature HMAC-SHA512
  (voir `app/Services/Payments/CmiGateway.php`). Demandez `merchant_id` et `store_key` à votre banque partenaire.
- **Stripe** utilise Stripe Checkout (redirection hébergée).
- **PayPal** appelle directement l'API REST Orders v2 (les SDK officiels PayPal étant peu maintenus).
- **Paiement à la livraison (COD)** ne nécessite aucune configuration : la commande est créée avec un statut de
  paiement `pending`, confirmé lors du passage au statut "Livrée" côté admin.

## Livraison — zones et villes

Les tarifs de livraison sont gérés dans `/admin/shipping` (tarif standard, surtaxe COD, seuil de livraison
gratuite, délai estimé). Chaque ville (`cities`) est rattachée à une `shipping_zone`. Le coût est recalculé en
direct au checkout via l'endpoint `GET /api/shipping/quote` (appelé par Alpine.js).

Zones seedées par défaut :

| Zone | Tarif | Gratuit dès | Délai |
|---|---|---|---|
| Casablanca - Rabat | 25 MAD | 500 MAD | 1–2 jours |
| Grandes villes (Marrakech, Tanger, Fès...) | 35 MAD (+10 MAD COD) | 500 MAD | 2–4 jours |
| Villes périphériques | 45 MAD (+15 MAD COD) | 700 MAD | 3–6 jours |

## Fonctionnalités principales

- Catalogue multi-catégories hiérarchiques avec filtres (genre, taille, couleur, prix, tri)
- Fiche produit : galerie, sélecteur taille/couleur en Alpine.js, guide des tailles, barre sticky mobile
- Panier en session (invité ou connecté), aucune inscription requise pour commander
- Checkout en une page avec récapitulatif live et calcul de livraison en direct
- Suivi de commande public par référence + email (`/suivi-commande`)
- Facture PDF téléchargeable (`/commande/{reference}/facture`)
- Back-office (`/admin`, accès réservé aux comptes `role = admin`) :
  - Tableau de bord (revenu du mois, commandes en attente, stock faible)
  - Gestion produits + variantes (taille/couleur/stock) en formulaire dynamique Alpine.js
  - Gestion des commandes avec changement de statut (En attente → Confirmée → En préparation → En livraison → Livrée / Annulée)
  - Gestion des zones de livraison et tarifs

## Commandes utiles

```bash
php artisan migrate:fresh --seed   # réinitialiser la base avec des données de démo
php artisan route:list             # lister toutes les routes
npm run dev                        # Vite en mode watch
```
