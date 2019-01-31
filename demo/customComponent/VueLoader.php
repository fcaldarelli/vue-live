<?php

namespace fabriziocaldarelli\vuelive\demo\customComponent;

use fabriziocaldarelli\vuelive\VueLoaderAbstract;

class VueLoader extends VueLoaderAbstract
{
    protected function componentName() : string
    {
        return "custom-component";
    }

    protected function vueType() : string
    {
        return self::VUETYPE_COMPONENT;
    }

    protected function dependencies() : array
    {
        return [
            \fabriziocaldarelli\vuelive\demo\otherComponent\VueLoader::class

        ];
    }
}

?>