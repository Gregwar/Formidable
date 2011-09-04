
DSD - Dual Side Definition
==========================

DSD Is a PHP Library using pseudo-HTML metadatas to allow
easy form processing.

For instance an input written:

```html
<input type="mail" name="email" />
```

Will be parsed by DSD and render as:

```html
<input type="text" name="email" />
```

The PHP code will look like:

```
<?php
$f = new DSDForm("forms/example.html");
if ($f->posted) {
echo "You entered: ".htmlspecialchars($f->email);
}
echo $f->getHTML();
```
