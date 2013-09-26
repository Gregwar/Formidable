<h2>Le mapping</h2>
<div class="text">
<p>
    Une fois les données d'un formulaire récupérées, il est assez pratique de pouvoir les exploiter à travers des objets et des tableaux de données dans <b>PHP</b>. <b>Formidable</b> vous permet d'accomplir l'intéraction entre votre formulaire et vos objets à l'aide de son <b>mapping</b>.
</p>
<p>
    Le <b>mapping</b> consiste simplement à renseigner les attributs <code>mapping</code> de votre formulaire&nbsp;:
</p>
<?php echo highlight('mapping/mapping.html', 'html5'); ?>
<p>
    L'avantage de cet attribut est de pouvoir utiliser des noms différents pour les champs du formulaire et de l'objet mappé, mais aussi de pouvoir choisir de mapper ou non certains champs (par exemple, il n'est pas très intéréssant de mapper un <b>captcha</b> ou une case à cocher «acceptez vous les CGU?»)
</p>
<p>
    Il est alors possible d'échanger des données avec le formulaire en utilisant les clés des champs mappés à l'aide d'<b>objets</b> ou de <b>tableaux</b> (<code>array</code>)&nbsp;:
</p>
<?php echo highlight('mapping/mapping.php'); ?>
<p>
    Vous pourrez ainsi persister les données dans une base de données facilement.
</p>

</div>
