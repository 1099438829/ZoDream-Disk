<?php
defined('APP_DIR') or exit();
/** @var $this \Zodream\Domain\Response\View */
$this->extend(array(
    'layout' => array(
        'head',
        'navbar'
    )), array(
    )
);
?>



<?php
$this->extend(array(
    'layout' => array(
        'foot'
    )), array(
        'vue',
        'zodream'
    )
);
?>
