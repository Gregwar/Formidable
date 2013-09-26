<h2>Utilisation</h2>
<h3>Création de formulaires</h3>
<div class="text">
<p>
    Afin d'utiliser <b>Formidable</b>, vous devez tout d'abord créer un formulaire simple en <b>HTML</b>. Si vous travaillez avec des graphistes ou intégrateurs à partir d'une page déjà découpée, vous pouvez couper/colelr votre formulaire dans un sous-fichier que vous placerez par exemple dans un dossier <code>forms/</code> de votre site web.
</p>
<p>
    Prenons par exemple le formulaire suivant&nbsp;:
</p>
<?php echo highlight('utilisation/form1.html', 'html5'); ?>
<p>
    Vous pouvez alors obtenir une instance de votre formulaire en <b>PHP</b> en instanciant un formulaire <b>Formidable</b>&nbsp;:
</p>
<?php echo highlight('utilisation/form1.php'); ?>
</div>

<h3>Intéragir avec les valeurs</h3>
<div class="text">
<p>
    Vous pouvez maintenant utiliser l'instance <code>$form</code> pour intéragir avec vos formulaires&nbsp;:
</p>
<?php echo highlight('utilisation/interaction.php'); ?>
<p>
    <b>Formidable</b> retrouve tous ses petits, ces interactions marcheront avec les champs de type <code>text</code>, mais aussi avec les <code>radio</code> en cochant la bonne case, <code>select</code> en séléctionnant la bonne option ou encore <code>checkbox</code> en cochant la case si nécéssaire.
</p>
<p>
    Notez également que les valeurs seront automatiquement échappées à l'aide de <code>htmlspecialchars</code> pour éviter les injections de code <b>XSS</b>.
</p>
</div>

<h3>Gérer les valeurs postées</h3>
<div class="text">
<p>
    Venons en au coeur du sujet, le traitement des valeurs envoyées par l'utilisateur. Pour tester si le formulaire a été soumis, utilisez la fonction <code>posted()</code>&nbsp;:
</p>
<?php echo highlight('utilisation/posted.php'); ?>
<p>
    <b>Formidable</b> testera également si le jeton <b>CSRF</b> est valide. Votre formulaire est donc sécurisé sans avoir rien à faire !
</p>
<p>
    <em>Note&nbsp;: la génération du jeton <b>CSRF</b> utilise également le nom du formulaire si il existe (l'attribut <code>name</code> de la balise <code>form</code>)</em>
</p>
<p>
    En apellant cette fonction, <b>Formidable</b> définira automatiquement la valeur des champs avec les données postées, et remplira ainsi directement et automatiquement le formulaire, si des erreurs de validations surviennent par la suite, l'utilisateur n'aura alors pas à re-saisir toutes les données.
</p>
</div>

<h3>Vérifier les contraintes</h3>
<div class="text">
<p>
    Un des avantages d'utiliser <b>Formidable</b> est également de pouvoir vérifier facilement les contraintes imposées au formulaire coté serveur.
</p>
<p>
    Pour ce faire, utilisez la fonction <code>check()</code> sur votre formulaire, cette dernière retourne toutes les erreurs de votre formulaire&nbsp;:
</p>
<?php echo highlight('utilisation/check.php'); ?>
<p>
    Notez que <code>check()</code> ne retourne pas un tableau de chaînes de caractères, mais des instances de <code>Gregwar\Formidable\Error</code>, qui vous permettent également de connaître les champs sur lesquels il y a eu des erreurs lors de la validation.
</p>
<p>
    La présence des champs est testée, sauf si ils sont marqués de l'attribut <code>optional</code>. Si le champs est de type <code>int</code>, <code>email</code> ou <code>number</code> son format est vérifié.
</p>
<p>
    Vous pouvez également appliquer les contraintes suivantes&nbsp;:
</p>
<ul>
    <li><code>minlength</code>: longueur minimum d'une chaîne de caractère</li>
    <li><code>maxlength</code>: longueur maximale d'une chaîne de caractère, se traduira également par la présence de l'attribut <code>maxlength</code></li>
    <li><code>min</code>: valeur numérique minimum du champ</li>
    <li><code>max</code>: valeur numérique maximum du champ</li>
    <li><code>regex</code>: expression régulière (format) que doit respecter le champ</li>
    <li><code>filetype="image"</code>: vérifier qu'un champ de type <code>file</code> a bien reçu une image</li>
    <li><code>maxsize</code>: limiter la taille du fichier (en octet) envoyé par un champ <code>file</code></li>
</ul>
</div>

<h3>Notation raccourcie</h3>
<div class="text">
<p>
    Il est possible de simplifier la notation pour l'usage courant à l'aide de la méthode <code>handle</code>&nbsp;:
</p>
<?php echo highlight('utilisation/handle.php'); ?>
</div>

<h3>Gérer l'upload de fichiers</h3>
<div class="text">
<p>
    Les champs de type <code>file</code> sont traités spécialement par <b>Formidable</b>. La valeur retournée par l'accesseur sur un fichier n'est pas une valeur mais l'instance d'un objet sur lequel vous pouvez apeller la fonction <code>save()</code>&nbsp;:
</p>
<?php echo highlight('utilisation/file.php'); ?>
</div>

<h3>Mettre un code visuel (CAPTCHA)</h3>
<div class="text">
<p>
    <b>Formidable</b> fournit un champs de type <code>captcha</code> nativement qui vous permettra de sécuriser un formulaire très facilement !
</p>
</div>

<h3>Utilisation des sources</h3>
<div class="text">
<p>
    Parfois, vous voudrez utiliser une liste déroulante contenant des éléments provenant de votre base de données, ou une série de cases à cocher. Pour ce faire, vous pouvez utiliser les <b>sources</b>. Imaginez par exemple que vous souhaitez demander à un utilisateur ses séries préférées avec des <code>checkbox</code>, vous pouvez alors utiliser le type spécial <code>multicheckbox</code>&nbsp;:
</p>
<?php echo highlight('utilisation/series.html', 'html5'); ?>
<p>
    Vous pouvez également utiliser le type <code>multiradio</code>, et la balise spéciale <code>options</code>. Il est alors possible de définir les choix depuis <b>PHP</b>&nbsp;:
</p>
<?php echo highlight('utilisation/source.php'); ?>
<p>
    Les valeurs soumises seront dans ce cas un tableau (<code>array</code>) contenant les clés du tableau sourcé.
</p>
</div>

<h3>Contraintes personnalisées</h3>
<div class="text">
<p>
    Il peut s'avérer utile d'ajouter des contraintes un peu plus évoluées que celles proposées par défaut, pour ce faire, vous pouvez utilisez la méthode <code>addConstraint</code> de cette manière&nbsp;:
</p>
<?php echo highlight('utilisation/constraint.php'); ?>
<p>
    Votre méthode sera alors apellée au moment du <code>check</code>
</p>
</div>

</div>
