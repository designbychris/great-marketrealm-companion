<?php

declare(strict_types=1);
?>

Old Name: <?= $old['name'] ?? '' ?>

Errors:
<?php
if (is_array($errors)) {
    echo implode(', ', array_keys($errors));
}
?>

Success: <?= $flash['success'] ?? '' ?>

Error: <?= $flash['error'] ?? '' ?>
