<?php
defined('APP_DIR') or exit();
$this->extend(array(
    'layout' => array(
        'head'
    )), array(
    'tree.css'
)
);
?>

<?=\Infrastructure\Tree::makeUl($this->get('data'), 0, function (){});?>

<?php
$this->extend(array(
        'layout' => array(
            'foot'
        ))
);
?>