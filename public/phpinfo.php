<?php
echo "Current working directory: " . getcwd() . "\n";
echo "DocumentRoot: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "PHP Version: " . phpversion() . "\n";
phpinfo();
