<?php
namespace Tooma\Api;

class Reader{
  
  public static function read($file){
    if(is_file($file) && is_readable($file)){
      return file_get_contents($file);
    }
    return null;
  }
  
}