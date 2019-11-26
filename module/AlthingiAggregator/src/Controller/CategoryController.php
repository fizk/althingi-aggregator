<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class CategoryController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findCategoriesAction()
    {
        $superCategoriesList = $this->queryForNoteList(
            'http://www.althingi.is/altext/xml/efnisflokkar/',
            '//efnisflokkar/yfirflokkur'
        );
        foreach ($superCategoriesList as $superCategory) {
            $this->saveDomElement(
                $superCategory,
                'thingmal/efnisflokkar',
                new Extractor\SuperCategory()
            );

            $superCategoryId = (int) $superCategory->getAttribute('id');

            $categoryList = $superCategory->getElementsByTagName('efnisflokkur');
            foreach ($categoryList as $category) {
                $this->saveDomElement(
                    $category,
                    "thingmal/efnisflokkar/{$superCategoryId}/undirflokkar",
                    new Extractor\Category()
                );
            }
        }
    }
}
