<?php

//============================================================================
//função genérica para mostrar dados - Auxiliando na programação
function printData($data, $die = true)
{
  if (is_object($data) || is_array($data)) {
    echo '
<pre>';
    print_r($data);
    echo '</pre>';
  } else {
    echo $data . '<br />';
  }
  if ($die) {
    die(PHP_EOL . 'Fim comando - Print Data - Terminado');
  }
}
