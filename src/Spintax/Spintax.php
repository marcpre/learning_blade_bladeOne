<?php 

class Spintax
{
    /*
    public function process($text)
    {
        return preg_replace_callback(
            '/\[(((?>[^\[\]]+)|(?R))*)\]/x',
            array($this, 'replace'),
            $text
        );
    }
    public function replace($text)
    {
        $text = $this->process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
    */
    /*
    public function process($text) {
        return preg_replace_callback('/{([^}]*)}/', 'replace', $text);
    }
      
    public function replace($m) {
        $parts = explode('|', $m[1]);
        shuffle($parts);
        return $parts[0];
    }
    */
    public function process($text) {
        return preg_replace_callback('/{([^}]*)}/', function ($m) {
            $parts = explode('|', $m[1]);
            shuffle($parts);
            return $parts[0];
        }, $text);
    }
      
}
