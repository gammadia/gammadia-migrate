<?php
/**
 * @return string
 */
function migrate() {
    $messages = [];

    // perform some complex actions that you can't do with SQL
    $messages[] = "Everything's ok !";

    // perform changes in your application directory structure (move uploaded files elsewhere, reorganize user managed contents, etc.)
    $messages[] = "34 images were moved.";

    return implode("\n", $messages);
}