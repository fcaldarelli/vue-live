<?php

namespace fabriziocaldarelli\vuelive\demo\otherComponent;

use fabriziocaldarelli\vuelive\VueLoaderAbstract;

class VueLoader extends VueLoaderAbstract
{
    protected function componentName() : string
    {
        return "other-component";
    }

    protected function vueType() : string
    {
        return self::VUETYPE_COMPONENT;
    }

    protected function dependencies() : array
    {
        return [

        ];
    }
}

?>