<?php

namespace fabriziocaldarelli\vuelive\demo\customApp;

use fabriziocaldarelli\vuelive\VueLoaderAbstract;

class VueLoader extends VueLoaderAbstract
{
    protected function componentName() : string
    {
        return "app";
    }

    protected function vueType() : string
    {
        return self::VUETYPE_APP;
    }

    protected function dependencies() : array
    {
        return [
            \fabriziocaldarelli\vuelive\demo\customComponent\VueLoader::class
        ];
    }
}

?>