<h2>C'est quoi ?</h2>
<div class="text">
<p>
    <b>DSD</b>, pour <b>Dual Side Definition</b> est une bibliothèque <b>PHP</b> de gestion de formulaires basée sur le concept suivant&nbsp;: vous écrivez votre formulaire entièrement en <b>HTML</b>, en y ajoutant des informations permettant de vérifier des contraintes, et pouvez le manipuler avec <b>PHP</b>. Les avantages sont :
</p>
<ul>
    <li>Une gestion simplifiée du traitement des requêtes <b>POST</b></li>
    <li>Une gestion automatique de la validation des contraintes et des erreurs coté serveur</li>
    <li>Une injection automatique de jeton <b>CSRF</b></li>
    <li>Une détéction automatique de la présence de champs de type <b>file</b> pour changer l'<b>enctype</b> du formulaire</li>
    <li>L'utilisation simplifié de types avancés tels que des codes visuels</li>
    <li>La possibilité de donner vie facilement à des formulaires <b>HTML</b> créés pour des maquettes par des graphistes par exemple</li>
</ul>
</div>
<h2>Un exemple</h2>
<div class="text">
<p>
Un exemple sera plus parlant qu'un long discours, imaginez le formulaire suivant :
</p>
<?php echo highlight('presentation/form.html', 'html5'); ?>
<p>
Une fois passée dans <b>DSD</b> à l'aide d'un code tel que :
</p>
<?php echo highlight('presentation/exemple.php'); ?>
<p>
Le code <b>HTML</b> produit est :
</p>
<?php echo highlight('presentation/out.html', 'html5'); ?>
<p>
On remarque dans ce cas que :
</p>
<ul>
    <li>L'<b>enctype</b> a été automatiquement modifié</li>
    <li>Un champs <b>CSRF</b> a été injecté pour la protection du formulaire</li>
    <li>Les attributs <b>HTML5</b> «<b>required</b>» ont été définis partout ou <b>optional</b> n'était pas présent</li>
</ul>
<p>
Mais ce n'est pas tout ! Maintenant, vous avez accès à votre formulaire en <b>PHP</b> et pouvez le manipuler de plein de façons différentes, pour en découvrir d'avantages, poursuivez votre lecture aux sections suivantes.
</p>
</div>
