<?php

namespace AppBundle\Helpers;

use Symfony\Component\Translation\TranslatorInterface;


class TransGenerator
{

    private $en='';
    private $translator;  
    
    public function __construct(TranslatorInterface $translator)
        // string $entityName = ''){
        // $this->en=$entityName;
    }

    public function genTranslateText(string $str, string $type, string $entityName = null): string
    {
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

    public function errorText($str, $entityName = null):string
    {
        return $this->genTranslateText($str, 'error', $entityName);
    }

    public function labelText(string $str, string $entityName = null):string
    {
        return $this->genTranslateText($str, 'label', $entityName);
    }

    public function messageText($str, $entityName = null):string
    {
        return $this->genTranslateText($str, 'message', $entityName);
    }

    public function titleText(string $str, string $entityName = null):string
    {
        return $this->genTranslateText($str, 'title', $entityName );
    }

    public function filterLabel(string $str, string $entityName=null):string
    {
        return $this->labelText('filter'.$str, $entityName );
    }
    
    public function filterTitle(string $str, string $entityName=null):string
    {
        return $this->titleText('filter'.$str, $entityName );
    }

    public function btnLabel(string $str, string $entityName=null):string
    {
        return $this->labelText('btn'.$str, $entityName );
    }
    
    public function btnTitle( string $str, string $entityName=null):string
    {
        return $this->titleText('btn'.$str, $entityName );
    }
  
    public function modalTitle( string $str, string $entityName=null):string
    {
        return $this->titleText('btn'.$str, $entityName );
    }

}