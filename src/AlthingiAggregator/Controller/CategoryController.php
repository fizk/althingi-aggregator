<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:48 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Extractor\Category;
use AlthingiAggregator\Extractor\SuperCategory;
use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;

class CategoryController extends AbstractActionController implements
    ConsumerAwareInterface,
    ProviderAwareInterface
{
    use ConsoleHelper;

    public function findCategoriesAction()
    {
        $superCategoriesList = $this->queryForNoteList('http://www.althingi.is/altext/xml/efnisflokkar/', '//efnisflokkar/yfirflokkur');
        foreach ($superCategoriesList as $superCategory) {
            $this->saveDomElement($superCategory, 'thingmal/efnisflokkar', new SuperCategory());

            $superCategoryId = (int) $superCategory->getAttribute('id');

            $categoryList = $superCategory->getElementsByTagName('efnisflokkur');
            foreach ($categoryList as $category) {
                $this->saveDomElement($category, "thingmal/efnisflokkar/{$superCategoryId}/undirflokkar", new Category());
            }
        }
    }
}
