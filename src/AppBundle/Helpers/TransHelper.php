<?php

namespace AppBundle\Helpers;

use Symfony\Component\Translation\TranslatorInterface;
// use Symfony\Component\DependencyInjection\ContainerInterface ;


class TransHelper
{

    private $en='';
    private $translator;  

    // public function __construct(ContainerInterface $seviceContainer){
    //     $this->translator=$seviceContainer->get('translator');
    // }
 
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator=$translator;
        // string $entityName = ''){
        // $this->en=$entityName;
    }

    public function setEntityName(string $entityName){
        $this->en=$entityName;
    }

    public function genTranslateText(?string $str, string $type, ?string $entityName=null): string
    {
        if(is_null($str)){
            return '';
        }
        $en=is_null($entityName) ? $this->en : strtolower($entityName); 
        if ($str == '') {
            return 'error';
        }
        $text = '';
        if ( $en != '') {
            $text = $en . '.';
            if (strpos($str, $text) === 0) {
                return $str;
            }
        }
        if ($type != '') {
            $text .= $type . '.';
        }
        return $text . $str;
    }

    public function errorText(?string $str, ?string $entityName=null):string
    {
        return $this->genTranslateText($str, 'error', $entityName);
    }

    public function labelText(?string $str, ?string $entityName=null):string
    {
        return $this->genTranslateText($str, 'label', $entityName);
    }

    public function messageText(?string $str, ?string $entityName=null):string
    {
        return $this->genTranslateText($str, 'message', $entityName);
    }

    public function titleText(?string $str, ?string $entityName=null):string
    {
        return $this->genTranslateText($str, 'title', $entityName );
    }

    public function filterLabel(?string $str, ?string $entityName=null):string
    {
        return $this->labelText('filter'.$str, $entityName );
    }
    
    public function filterTitle(?string $str, ?string $entityName=null):string
    {
        return $this->titleText('filter'.$str, $entityName );
    }

    public function btnLabel(?string $str, ?string $entityName=null):string
    {
        return $this->labelText('btn'.$str, $entityName );
    }
    
    public function btnTitle(?string $str, ?string $entityName=null):string
    {
        return $this->titleText('btn'.$str, $entityName );
    }
  
    public function modalTitle(?string $str, ?string $entityName=null):string
    {
        return $this->titleText('btn'.$str, $entityName );
    }

}