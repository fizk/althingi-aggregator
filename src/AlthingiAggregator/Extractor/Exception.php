<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/06/15
 * Time: 7:15 PM
 */

namespace AlthingiAggregator\Extractor;

use DOMElement;

class Exception extends \Exception
{
    /**
     * @param string $message The Exception message to throw.
     * @param DOMElement|null $element
     * @param \Exception|null $previous The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct($message = "", DOMElement $element = null, \Exception $previous = null)
    {
        $message = ($element)
            ? $message . PHP_EOL . $element->ownerDocument->saveXML($element)
            : $message;
        parent::__construct($message, 0, $previous);
    }
}
