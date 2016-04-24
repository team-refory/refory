<?php 

//to check url strictly

function isUrl($url) {
  if (preg_match('/javascript:/', $url)){
    return false;
  }
  return true;
}

//htmlspecialcharsのショートカット
function h($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>