<?php

namespace fabriziocaldarelli\vuelive;

abstract class VueLoaderAbstract
{
    // VueType costants
    public const VUETYPE_APP = 'app';
    public const VUETYPE_COMPONENT = 'component';

    // Template costants
    public const TEMPLATE_INLINE = 'inline';
    public const TEMPLATE_SCRIPT = 'script';

    // Private members
    private $_dirName = null;
    private $_stackDependencies = [];
    private $_allContentsAsArray = null;

    /**
     * @return string the name of tag
     */
    protected abstract function componentName() : string;

    /**
     * @return string the name of tag
     */
    protected abstract function vueType() : string;    

    /**
     * @return array Array of config array with __class key to identify the class or string identify class path
     */
    protected abstract function dependencies() : array;

    /**
     * @var bool Whether that all data is prepared, after created the object
     */
    private $_isPrepared = false;

    /**
     * Property that can be changed
     * @var string File name for vue contents
     */
    protected $fileName = 'vue';

    /**
     * Property that can be changed
     * @var string File name for vue contents
     */
    protected $template = self::TEMPLATE_INLINE;


    public function __construct()
    {
        $reflector = new \ReflectionClass($this);
        $this->_dirName = dirname($reflector->getFileName());
    }

    /**
     * @return array Array of all contents (html, js, css) from all dependency
     */
    public function allContentsAsArray()
    {
        if($this->_isPrepared == false)
        {
            $this->prepare();
        }

        if($this->_allContentsAsArray === null)
        {
            $out = [
                'html' => [],
                'css' => [],
                'js' => []
            ];

            foreach($this->_stackDependencies as $dep)
            {
                $out['css'] = array_merge($out['css'], $this->loadFilesContents($dep, 'css'));
                $out['html'] = array_merge($out['html'], $this->loadFilesContents($dep, 'html'));
                $out['js'] = array_merge($out['js'], $this->loadFilesContents($dep, 'js'));
            }

            $this->_allContentsAsArray = $out;
        }

        return $this->_allContentsAsArray;
    }    

    public function getHtmlContentsAsString()
    {
        $allContentsArray = $this->allContentsAsArray();
        return implode('', $allContentsArray['html']);
    }

    public function getJsContentsAsString()
    {
        $allContentsArray = $this->allContentsAsArray();
        return implode('', $allContentsArray['js']);
    }
    
    public function getCssContentsAsString()
    {
        $allContentsArray = $this->allContentsAsArray();
        return implode('', $allContentsArray['css']);
    }    

    /**
     * @return string|false returns path file if exists, otherwise false
     */
    private function createObjectFiles($ext)
    {
        $arr = [];
        $pathFile = ($this->_dirName.'/'.$this->fileName.'.'.$ext);
        if(file_exists($pathFile))
        {
            $arr[] = [
                'pathFile' => $pathFile,
            ];
        }

        return $arr;
    }

    public function loadFilesContents($dep, $type)
    {
        $out = [];

        $arrFilesData = [];
        if($type == 'js') $arrFilesData = $dep['jsFiles'];
        if($type == 'html') $arrFilesData = $dep['htmlFiles'];
        if($type == 'css') $arrFilesData = $dep['cssFiles'];

        foreach($arrFilesData as $fileData)
        {
            $file = $fileData['pathFile'];
            $vueType = $dep['model']->vueType();
            $componentName = $dep['model']->componentName();

            $content = null;
            if(file_exists($file))
            {
                $content = file_get_contents($file);
                if($type == 'css')
                {
                    $out[] = '<style type="text/css">'.$content.'</style>';
                }
                if($type == 'html')
                {
                    if($vueType == 'app')
                    {
                        $htmlContent = str_replace(['___APPLICATION_ID___'], [ $componentName ], $content );
                        $out[] = $htmlContent;
                    }
                    else if($vueType == 'component')
                    {
                        if($dep['model']->template == self::TEMPLATE_SCRIPT)
                        {
                            $out[] = sprintf('<script type="text/x-template" id="%s">%s</script>', $componentName.'-content-template', $content);
                        }
                    }                    
                    else
                    {
                        $out[] = sprintf('<script type="text/x-template" id="%s">%s</script>', $componentName.'-content-template', $content);
                    }
                }
                if($type == 'js')
                {
                    if($vueType == 'app')
                    {
                        $jsContent = str_replace(['___APPLICATION_ID___'], [ '#'.$componentName], $content);
                        $out[] = sprintf('<script>%s</script>', $jsContent);
                    }
                    else if($vueType == 'component')
                    {
                        if($dep['model']->template == self::TEMPLATE_INLINE)
                        {
                            $htmlContent = file_get_contents($dep['htmlFiles'][0]['pathFile']);
                            $jsContent = str_replace(['\'___TEMPLATE___\'', '___COMPONENT_NAME___'], [ '`'.$htmlContent.'`', $componentName ], $content);
                        }
                        else
                        {
                            $jsContent = str_replace(['___TEMPLATE___', '___COMPONENT_NAME___'], [ sprintf('#%s-content-template', $componentName), $componentName ], $content);
                        }
                        $out[] = sprintf('<script>%s</script>', $jsContent);
                    }
                }
            }
        }
        return $out;
    }

    /**
     * This method prepare all data. This is called manually to have a last chance to make some changes 
     * or automatically when call getCssContentsAsString, getCssContentsAsString or getJsContentsAsString
     */
    public function prepare()  
    {
        $dependencies = $this->dependencies();

        if($dependencies != null)
        {
            foreach($dependencies as $dependency)
            {
                if(is_array($dependency))
                {
                    if(isset($dependency['__class']) == false)
                    {
                        throw new \Exception('Missing __class in dependency');
                    }
                    $objDep = new $dependency['__class']();
                    $objDep->prepare();
                }
                else if(is_string($dependency))
                {
                    $objDep = new $dependency();
                    $objDep->prepare();
                }

                $this->_stackDependencies = $objDep->_stackDependencies;
            }
        }

        $this->_stackDependencies[] = [
            'model' => $this,
            'htmlFiles' => $this->createObjectFiles('html'),
            'cssFiles' => $this->createObjectFiles('css'),
            'jsFiles' => $this->createObjectFiles('js'),
        ];        

        $this->_isPrepared = true;

    }

}

?>