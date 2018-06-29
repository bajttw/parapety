<?php

namespace AppBundle\Helpers;



class TemplateHelper
{

    private $eh;

    public function __construct(EntityHelper $entityHelper)
    {
        $this->eh=$entityHelper;
    }

    protected function templateType( ?string $type = null):string
    {
        $subdir = '';
        switch ($type) {
            case 'm' :
                $subdir = 'Modal';
                break;
            case 'p' :
                $subdir = 'Panel';
                break;
            case 'w' :
                $subdir = 'Window';
                break;
            default :
                break;
        }
        return $subdir;
    }

    public function getPath(string $name, ?string $entityClassName = null, bool $genericTemplate=true, ?string $renderType = null):string
    {
        $renderSufix = $this->templateType($renderType);
        $path = $renderSufix != '' ? $renderSufix . '/' . $name : $name;
        $ecn=$this->eh->getEntityClassName($entityClassName);
        $genericEntityTemplates= $this->eh->getElements('templates-generic', $ecn) ?: [];
        if(!$genericTemplate || in_array($path, $genericEntityTemplates)){
            $path= '@' . str_replace('Bundle', '', $this->eh->getBundleName()) . '/' . $ecn . '/' . $path;
        }
        return $path. '.html.twig';
        
    }
}