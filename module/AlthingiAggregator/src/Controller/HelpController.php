<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/03/2016
 * Time: 5:46 PM
 */

namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class HelpController extends AbstractActionController
{
    use ConsoleHelper;

    public function indexAction()
    {
//        $result = '';
//        $config = $this->getConfig();
//        $routes = $config['console']['router']['routes'];
//        foreach ($routes as $name => $route) {
//            $result .= "{$route['options']['route']}\n";
//        }
//
//        return $result;
        return '';
    }
}
