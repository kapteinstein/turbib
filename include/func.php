<?php

function bson_array_to_php_array($bson) {
    $array = [];
    if (is_array($bson) || is_object($bson)) {
      foreach($bson as $element) {
        array_push($array, $element);
      }
    }
    return $array;
  }

  function bson_array_to_str_list($array) {
    return implode(", ", bson_array_to_php_array($array));
  }

function create_html_string($str) {
  return str_replace("\r\n", "<br />", htmlspecialchars($str));
}