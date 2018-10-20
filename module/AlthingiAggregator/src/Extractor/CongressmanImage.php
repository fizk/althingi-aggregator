<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class CongressmanImage implements ExtractionInterface, MediaInterface
{
    /** @var  string */
    private $slug;

    /** @var  string */
    private $fileName;

    /** @var  string */
    private $contentType;

    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function extract(DOMElement $object)
    {
        if (! $object->hasAttribute('id')) {
            throw new ModelException('Missing [id] value', $object);
        }

        $this->slug = "thingmenn-{$object->getAttribute('id')}.jpg";
        $this->fileName = "http://www.althingi.is/myndir/mynd/thingmenn/{$object->getAttribute('id')}/org/mynd.jpg";
        $this->contentType = 'image/jpg';

        return [
            'id' => (int)$object->getAttribute('id'),
            'slug' => "thingmenn-{$object->getAttribute('id')}.jpg",
            'fileName' => "http://www.althingi.is/myndir/mynd/thingmenn/{$object->getAttribute('id')}/org/mynd.jpg",
            'contentType' => 'image/jpg',
        ];
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getContentType()
    {
        return $this->contentType;
    }
}
