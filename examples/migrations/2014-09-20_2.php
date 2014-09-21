<?php

/**
 * Class have to be named after de migration version.
 */
class Migration2 {
    /**
     * @return void|false If false, will be considered as failed migration
     */
    public function go() {
        $messages = [];

        // perform some complex actions that you can't do with SQL
        $something_went_wrong = true;
        if ($something_went_wrong) {
            echo "Something went wrong...";
            return false;
        }
        $messages[] = "Everything's ok !";

        // perform changes in your application directory structure (move uploaded files elsewhere, reorganize user managed contents, etc.)
        $messages[] = "34 images were moved.";

        echo implode("\n", $messages);
    }
}