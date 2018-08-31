<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/06/15
 * Time: 6:59 AM
 */

namespace AlthingiAggregator\Lib;

interface IdentityInterface
{
    public function setIdentity($id);
    public function getIdentity();
}
