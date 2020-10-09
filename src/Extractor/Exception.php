<?php
namespace App\Extractor;

use DOMElement;

class Exception extends \Exception
{
    public function __construct(string $message = "", DOMElement $element = null, \Exception $previous = null)
    {
        $message = ($element)
            ? $message . PHP_EOL . $element->ownerDocument->saveXML($element)
            : $message;
        parent::__construct($message, 0, $previous);
    }
}
