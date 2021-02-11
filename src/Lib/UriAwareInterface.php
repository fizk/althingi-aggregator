<?php
namespace App\Lib;

use Laminas\Diactoros\Uri;

interface UriAwareInterface
{
    public function setUri(Uri $uri);
}
