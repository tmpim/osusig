<?php
/**
 * Created by Lemmmy.
 */
$mc = new Memcached();
$mc->addServer("localhost", 11211);

$uname = strtolower($_GET['uname']);

for ($i = 0; $i < 4; $i++) {
    $mc->delete("stats_" . $i . "_" . $uname);
}

echo $mc->delete("profilepicture_" . $uname);
