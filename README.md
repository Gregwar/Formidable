
DSD - Dual Side Definition
==========================

DSD est une bibliothèque PHP utilisant du code pseudo HTML 
contenant des métadonnées pour simplifier la gestion des 
formulaires.

Par exemple, l'input suivant :

```html
<input type="mail" name="email" optional />
```

Sera parsé et affiché comme cela :

```html
<input type="text" name="email" />
```

Le code PHP ressemblera à :

```php
<?php
$form = new Gregwar\DSD\Form('forms/example.html');

if ($form->posted()) {
    echo 'Vous avez tappés : '.htmlspecialchars($f->email);
}

echo $f->getHTML();
```
