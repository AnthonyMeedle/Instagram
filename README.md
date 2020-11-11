# Instagram

This module display your photo from your Instagram account. You need to know your Access token from your Instagram account.


## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Instagram.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/instagram:~1.0
```

## Usage

In the configuration panel of this module, you can record your Access token, the username and the number of photo to display

## How to retrieve my Access Token

1. You have to connect to https://instagram.com/developer/ to create your application token.
2. Ajouter un produit Instagram
3. Remplissez la partie basic-display
4. Dans le champ "Paramètres OAuth client" - "URI de redirection OAuth valides" rentrez une url spécifique fourni par le module
5. Récupérez le client id et le client secret à sauvegarder dans ce module
6. Si vous laissez votre application en mode test ajoutez le compte instagram voulu en compte test de l'application
7. Cliquez sur le lien "Générer un nouveau token d'accès" qui est dans le module. 

8. à suivre mettre en place un cron sur une url tous les mois pour rafraichir le token (qui est normalement valide 60 jours)

## Loop
{loop type="instagram" name="instagram" limit=8 width="578" height="578" resize_mode="crop"}
<a href="{$URL}" title="" >
	<img src="{$IMAGE_URL}" alt="{$ALT}" >
</a>
{/loop}
