# sj4web_domtomaddresscheck

## Description
**sj4web_domtomaddresscheck** est un module PrestaShop permettant de **valider les adresses des DOM-TOM** lors de la saisie dans le formulaire d'adresse. Il empêche les clients de sélectionner "France" lorsque le code postal correspond à un territoire d'outre-mer et affiche un message d'erreur.

## Fonctionnalités
✅ Vérification dynamique des correspondances **Code postal / Pays**  
✅ Ajout d'un **message d'erreur sous le champ "Pays"** si une incohérence est détectée  
✅ Gestion complète des **territoires DOM-TOM** :
- Guadeloupe
- Martinique
- Guyane française
- La Réunion
- Mayotte
- Saint-Pierre-et-Miquelon
- Nouvelle-Calédonie
- Polynésie française
- Wallis-et-Futuna
- Terres australes et antarctiques françaises
- Saint-Barthélemy
- Saint-Martin  

✅ **Compatibilité avec AJAX** : le module réapplique automatiquement la validation après un rafraîchissement des champs  
✅ **Supporte PrestaShop 8.x** et **PHP 7.0 → 8.x**

## Installation
1. **Téléchargez** ou **clonez** ce repository dans le dossier `/modules/` de votre PrestaShop :
   ```sh
   git clone https://github.com/jorgeRumilly/sj4web_domtomaddresscheck.git modules/sj4web_domtomaddresscheck
   ```
2. **Allez dans le back-office** de PrestaShop :
    - **Modules** > **Gestion des modules**
    - Recherchez **"sj4web_domtomaddresscheck"**
    - Cliquez sur **"Installer"**
3. **Configurez le module** dans **Modules > Paramètres du module**
    - Activez ou désactivez la validation selon vos besoins

## Configuration
- Le module est activé par défaut après installation.
- Il peut être désactivé depuis l'interface d'administration.
- Ajoute une **validation front-end (JS) + back-end (PHP)** pour empêcher les erreurs.

## Comment ça fonctionne ?
1. Lorsqu'un client saisit un **code postal DOM-TOM**, le module vérifie **si le pays est correct**.
2. Si le pays sélectionné n'est **pas le bon** → le champ "Pays" devient rouge et un message d'erreur s'affiche sous le champ.
3. Si le client **corrige** le pays ou le code postal → l'erreur disparaît immédiatement.
4. La validation fonctionne **même si PrestaShop recharge dynamiquement le formulaire**.

## Technologies utilisées
- **PHP 7.0 → 8.x** (validation back-end)
- **JavaScript (ES6)** pour la validation dynamique
- **MutationObserver + `prestashop.on('updatedAddressForm')`** pour re-appliquer la validation après un rechargement AJAX

## Auteurs
👨‍💻 **Développé par SJ4WEB**

## Licence
Ce module est sous licence **MIT**. Vous êtes libre de l'utiliser et de le modifier selon vos besoins.

