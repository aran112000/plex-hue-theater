<?php

require('vendor/autoload.php');

echo '<ul>';
foreach ((new App\Hue())->getAllLights() as $id => $light) {
    echo '<li>';
    echo "#$id - " . $light['name'];
    echo '</li>';
}
echo '</ul>';
