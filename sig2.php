<?php
// Thanks to Cygnix
// Created by Lemmmy

require_once("p/.priv.php");

include_once("class/PredefinedColours.php");

include_once("class/OsuAPI.php");
include_once("class/Signature.php");
include_once("class/OsuSignature.php");

$api = new OsuAPI(constant("AKEY"));

$sig = new OsuSignature("Lemmmy");
$sig->generate();