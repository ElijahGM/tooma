<?php
namespace Tooma\Api;

class Writer{
  protected $file;

  public function __construct($path){
     $this->file = $path;
  }
  public function save($content){
    if(is_writable($this->file)){
    	file_put_contents($this->file, $content);
    }
  }
  
}