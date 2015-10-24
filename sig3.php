<?php
// Thanks to Cygnix
// Created by Lemmmy

require_once("p/.priv.php");

function __autoload($class_name) {
    $directory = 'class/';

    if (file_exists($directory . $class_name . '.php')) {
        require_once ($directory . $class_name . '.php');
        return;
    }
}

$api = new OsuAPI(constant("AKEY"));

$sig = new OsuSignature("Lemmmy", new TemplateNormal());
$sig->generate();