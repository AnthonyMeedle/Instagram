<?php

return array(
    'An error occured' => 'Une erreur est survenue',
    'Edit your Instagram configuration.' => 'Modifier votre configuration Instagram',
    'Save' => 'Sauvegarder',
    '## How to retrieve my Access Token ##' => '## Comment trouver le token d\'accès de votre compte Instagram ##
<ol>
<li>Connectez-vous sur <a href="https://developers.facebook.com/" target="_blank">https://developers.facebook.com/</a> pour enregistrer votre application.</li>
<li>Ajouter un produit Instagram</li>
<li>Remplissez la partie basic-display</li>
<li>Dans le champ "Paramètres OAuth client" - "URI de redirection OAuth valides" rentrez cette url :  %urloauth</li>
<li>Récupérez le client id et le client secret à sauvegarder dans ce module</li>
<li>Si vous laissez votre application en mode test ajoutez le compte instagram voulu en compte test de l\'application</li>
<li>Cliquez sur le lien "Générer un nouveau token d\'accès" qui est au bas de cette page et suivez les instruction</li>
</ol>',
    '## How to refresh my Access Token ##' => '## Comment rafraichir le token d\'accès de votre compte Instagram ##<br>
	Une fois le token d\'accès généré, il va avoir une certaine durée de vie (60 jours je crois), la date est sous le bouton "Générer un nouveau token d\'accès". <br> Pour éviter de générer un token et donc de devoir accéder au compte instagram, on peut le renouveller, pour ce faire il existe url suivante qui peut être appelé par un cron (ou directement dans le navigateur) : %urlrefresh',
);
