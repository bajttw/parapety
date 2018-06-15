<?php

namespace AppBundle\Helpers;

class TransGenerator
{

    private $en='';
    private $translator='';

    function __constructor(TranslatorInterface $translator, string $entityName ){
        $this->translator=$translator;
        $this->$en=$entityName;
    }

    public static function gen_trans_text(string $str, string $type = '', string $entityName = null) :string
    {
        if ($str == '') {
            return 'error';
        }
        $text = '';
        if ( $entityName != '') {
            $text = $entityName . '.';
            if (strpos($str, $text) === 0) {
                return $str;
            }
        }
        if ($type != '') {
            $text .= $type . '.';
        }
        return $text . $str;
    }

    // public static function gen_trans_text($str, $type, $entityName = null)
    // {
    //     return $str ?
    //         Utils::gen_trans_text($str, $type, $entityName === '' ? '' : self::getEntityName($entityName) )
    //         : '';
    // }

    public function genTranslateText(string $str, string $type, string $entityName = null): string
    {
        return self::gen_trans_text($str, $type, is_null($entityName) ? $this->en : '' );
    }

    public function errorText($str, $entityName = null)
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

    public function trans($str, $include=[])
    {
        if(is_null($str) || $str == ''){
            return '';
        }
        $trans=function($s, $include){
            $s = $this->get('translator')->trans($s);
            if($count=count($include)){
                $search=[];
                for($i=1; $i<=$count; $i++){
                    $search[]='%'.$i;
                }
                $s=str_replace($search, $include, $s);
            }
            return $s;
        };
        if (is_array($str)) {
            $t = [];
            foreach ($str as $s) {
                $t[]=$trans($s, $include);
            }
            return implode(' ', $t);
        }
        else {
            return $trans($str, $include);
        }
    }

}