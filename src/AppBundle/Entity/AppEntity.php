<?php

namespace AppBundle\Entity;

use AppBundle\Utils\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class AppEntity
{
    const ns = 'AppBundle\\Entity\\';

    // const numerate=false;


//  <editor-fold defaultstate="collapsed" desc="Utils">
    protected function _cloneElements($original){
        $copy = new ArrayCollection();
        foreach ($original as $o) {
            $copy->add($o);
        }
        return $copy;        
    }

    protected function _checkElements($name, $em)
    {
        $fn = 'get' . ucfirst($name);
        if (method_exists($this, $fn) && array_key_exists($name, $this->oldValues)){
            $elements=$this->$fn();
            foreach ($this->oldValues[$name] as $old) {
                if (false === $elements->contains($old)) {
                    $em->remove($old);
                }
            }

        }
    } 
 // </editor-fold>       


//  <editor-fold defaultstate="collapsed" desc="Global funtions">

    protected $oldValues=[];

    protected $removedChilds;

    protected $old;

    private function initOld():void
    {
        if(is_null($this->old)){
            $this->old=new \stdClass();
        }
    }
    
    protected function removeChild(string $name, $child):void
    {
        if(is_null($this->removedChilds)){
            $this->removedChilds=new \stdClass();
        }
        if( !is_array($this->removedChilds->$name) ){
            $this->removedChilds->$name=[];
        }
        $this->removedChilds->$name[]=$child;
    }


    public function getOld()
    {
        $this->initOld();
        return $this->old;
    }

    public function getSavedField(string $name)
    {
        return $this->old ? $this->old->$name ?? null : null;
    } 

    public function getOldValues():array
    {
        return $this->$oldValues;
    }

    public function saveFieldsValues(array $names):void
    {
        foreach($names as $name){
            $this->saveFieldValue($name);
        }
    }

    protected function saveFieldValue(string $name):void
    {
        $fn = 'get' . ucfirst($name);
        if (method_exists($this, $fn)) {
            $this->initOld();
            $old=$this->$fn();
            if($old instanceof Collection){
                $this->oldValues[$name] = $this->_cloneElements($old);
                $this->old->$name=$this->oldValues[$name];
            }else{
                $this->oldValues[$name] = gettype($name) == 'object' ? clone $old : $old;    
                $this->old->$name=$this->oldValues[$name];
            }
        }
    }
// </editor-fold>       

//  <editor-fold defaultstate="collapsed" desc="Global funtions">

    public function __construct(array $options = [])
    {
        $defaults = $options['defaults'] ?? [];
        foreach ($defaults as $name => $value) {
            $fnInit = 'init' . ucfirst($name);
            if (method_exists($this, $fnInit)) {
                $this->$fnInit($value);
            }
        }
    }

    public function __toString():string
    {
        return (string) $this->getId();
    }

    public static function getChildEntityClass(string $name):?string
    {
        $dd = static::$shortNames;
        if ($childClass = Utils::deep_array_value(['childs', $name], static::$shortNames)) {
            return static::ns . $childClass;
        }
        return null;
    }

    public function diffColletions(ArrayCollection $collection1, ArrayCollection $collection2):ArrayCollection
    {
        $diff = new ArrayCollection();
        foreach ($collection2 as $c2) {
            if (false === $collection1->contains($c2)) {
                $diff->add($c2);
            }
        }
        return $diff;
    }

// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Fields functions">
    public static function getFields(?string $type = null):array
    {
        return array_diff(array_keys(static::$shortNames), ['childs']);
    }

    public static function getChildShortNames(string $name):?array
    {
        if ($ec = self::getChildEntityClass($name)) {
            return $ec::$shortNames;
        }
        return null;
    }

    public function getSuccessFields(?string $type=null):array
    {
        return [];
    }

    public function getDeleteFields(?string $type=null):array
    {
        return [ 'id' ];
    }

    public function getMessageDataFields(?string $type=null):array
    {
        return [];
    }

    public static function getShortName(string $name):string
    {
        $names = static::$shortNames;
        $keys = explode('.', $name);
        foreach ($keys as $k) {
            $short = array_key_exists($k, $names) ? $names[$k] : null;
            if ($childClass = Utils::deep_array_value(['childs', $k], $names)) {
                $ec = static::ns . $childClass;
                $names = $ec::$shortNames;
            }
        }
        return $short ? $short : $name;
    }

    public static function getDicName(string $name):string
    {
        $names = isset(static::$dicNames) ? static::$dicNames : [];
        $keys = explode('.', $name);
        foreach ($keys as $k) {
            $short = array_key_exists($k, $names) ? $names[$k] : null;
            if ($childClass = Utils::deep_array_value(['childs', $k], $names)) {
                $ec = static::ns . $childClass;
                $names = isset($ec::$dicNames) ? $ec::$dicNames : [];
            }
        }
        return $short ? $short : $name;
    }

// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Data functions">
    protected function toStrData($field):string
    {
        if (is_string($field)) {
            return $field;
        }
        $val = '';
        if (Utils::is_sequential_array($field) || $field instanceof \Traversable) {
            $val = json_encode($field);
        } elseif (gettype($field) == 'object') {
            if ($field instanceof \DateTime) {
                $val = $this->getTimeField($field);
            } else {
                if (method_exists($field, '__toString')) {
                    $val = $field->__toString();
                } elseif (method_exists($field, 'getId')) {
                    $val = $field->getId();
                }
            }
        } else {
            $val = (string) $field;
        }
        return $val;
    }

    private function toShowData($field, array $options = [])
    {
        $val = null;
        Utils::deep_array_value_set('shortNames', $options, true);
        if (Utils::is_sequential_array($field) || $field instanceof \Traversable) {
            $val = [];
            foreach ($field as $f) {
                array_push($val, $this->toShowData($f, $options));
            }
        } elseif (gettype($field) == 'object') {
            if ($field instanceof \DateTime) {
                $val = $this->getTimeField($field, $options);
            } else {
                if (method_exists($field, 'getShowData')) {
                    $val = $field->getShowData($options);
                } elseif (method_exists($field, 'getId')) {
                    $val = $field->getId();
                }
            }
        } else {
            $val = $field;
        }
        return $val;
    }

    protected function getTimeField($time, array $options = []):string
    {
        return Utils::dateTimeStr($time, Utils::deep_array_value('strDate', $options, 'date') == 'time');
    }

    protected function getFieldData($entity, string $fieldName, string $prefix = 'get')
    {
        $fn = $prefix . ucfirst($fieldName);
        if (gettype($entity) == 'object' && method_exists($entity, $fn)) {
            return $entity->$fn();
        }
        return null;
    }

    public function getStrField(string $fieldName):string
    {
        $fields = explode('.', $fieldName);
        $fieldName = array_pop($fields);
        $entity = $this;
        for ($i = 0, $count = count($fields); $i < $count; $i++) {
            $entity = $this->getFieldData($entity, $fields[$i]);
        }
        $field = $this->getFieldData($entity, $fieldName, 'getStr') ?? $this->getFieldData($entity, $fieldName);
        return $this->toStrData($field);
    }

    public function getFieldsStr(array $fields):array
    {
        $fieldsStr = [];
        foreach ($fields as $name) {
            $fieldsStr[$name] = $this->getStrField($name);
        }
        return $fieldsStr;
    }

    public function getDataId():array
    {
        $result = [
            'id' => $this->getId(),
        ];
        return $result;
    }

    public function getShowData(array $options = []):array
    {
        $data = [];
        if ($this->getId()) {
            $en = Utils::deep_array_value('en', $options);
            if (is_string($en)) {
                $en = [$en];
                $options['en'] = $en;
            }
            if (is_array($en)) {
                if (in_array(static::en, $en)) {
                    return $this->getId();
                } else {
                    array_push($options['en'], static::en);
                }
            } else {
                $options['en'] = [static::en];
            }
            $type = Utils::deep_array_value('type', $options, '');
            $typePrefix = join('_', array_diff($options['en'], [static::en]));
            if ($typePrefix != '') {
                $type = $type == '' ? $typePrefix : $typePrefix . '_' . $type;
            }
            $short = Utils::deep_array_value('shortNames', $options);
            if ($short) {
                $fnShortName = is_string($short) ? 'get' . ucfirst($short) . 'Name' : 'getShortName';
            } else {
                $fnShortName = false;
            }
            $fields = static::getFields($type);
            foreach ($fields as $f) {
                if (is_array($f)) {
                    $name = $f['name'];
                } else {
                    $name = $f;
                }
                $key = $fnShortName ? static::$fnShortName($name) : $name;
                $fnShow = 'getShow' . ucfirst($name);
                $fn = 'get' . ucfirst($name);
                if (method_exists($this, $fnShow)) {
                    $field = $this->$fnShow($options);
                } elseif (method_exists($this, $fn)) {
                    $field = $this->$fn($options);
                } else {
                    $field = null;
                }
                $data[$key] = $this->toShowData($field, $options);
            }
        }
        return $data;
    }

    public function getShowDataJSON(array $options = []):string
    {
        return json_encode($this->getShowData($options));
    }

    public function getData(array $options=[]):array
    {
        return $this->getShowData(array_replace(
            [
                'shortNames' => true,
                'type' => 'data',
            ],
            $options
        ));
    }

    public function getDataJSON(array $options=[]):string
    {
        return json_encode($this->getData($options));
    }

    public function getMessageData(?string $type=null, array $dataReturn=[]):array{
        $data=[];
        foreach($this->getMessageDataFields($type) as $f){
            $data[]=$this->getStrField($f);
        }
        return $data;
    }

    public function getSuccessData(?string $type=null):array
    {
        $data = [
            "fields" => [],
        ];
        switch ($type) {
            case 'create':
            case 'update':
                $data['entity_data'] = $this->getShowData();
            case 'remove':
            default:
        }
        $data['fields'] = $this->getFieldsStr($this->getSuccessFields($type));
        return $data;
    }

    public function getDataDelete():array
    {
        $data=[];
        foreach($this->getDeleteFields() as $f){
            $data[$f]=$this->getStrField($f);
        }
        return $data;
    }

    protected function setDateField(string $name, $value = null, bool $overwrite = false)
    {
        if ($value = 'now') {
            $value = new \DateTime();
        }
        $fnGet = 'get' . ucfirst($name);
        if ($overwrite || is_null($this->$fnGet())) {
            $fnSet = 'set' . ucfirst($name);
            $this->$fnSet($value);
        }
        return $this;
    }

    public function setData(?array $data=null)
    {
        return $this;
    }


// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Numbering">

    protected $intNr;

    public function setIntNr(int $nr=0)
    {
        $nr = intval($nr);
        $this->intNr = $nr > 0 ? $nr : 1;
        return $this;
    }

    public function getIntNr():int
    {
        return $this->intNr;
    }

    private function generateNumber(?array $numberGenerator, int $nr):string
    {
        $generator = [];
        if (is_array($numberGenerator)) {
            foreach ($numberGenerator as $gen) {
                if (is_array($gen)) {
                    $type = strtolower(Utils::deep_array_value('type', $gen, ''));
                    $code = Utils::deep_array_value('code', $gen, '');
                    switch ($type) {
                        case 'field':
                            $generator[] = $this->getStrField($code);
                            break;
                        case 'date':
                            $now = new \DateTime();
                            $generator[] = $now->format($code);
                            break;
                        default:
                            switch ($code) {
                                case 'nr':
                                    $generator[] = $nr;
                                    break;
                                default:
                                    $generator[] = $code;
                            }
                    }
                } else {
                    $generator[] = $gen;
                }
            }
        }
        return count($generator) > 0 ? implode('', $generator) : $nr;
    }
    
    public function genNumber(array $numerate):string
    {
        $this->setIntNr($numerate['nr']);
        $this->setNumber($this->generateNumber($numerate['numberGenerator'], $this->getIntNr()));
        return $this->getNumber();
    }

// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Validate">
    public $validationStatuses = ['ok', 'info', 'warning', 'error'];

    protected function setValidationStatus(string $status = 'ok', string $actualStatus = 'ok')
    {
        return (array_search($status, $this->validationStatuses) > array_search($actualStatus, $this->validationStatuses)) ? $status : $actualStatus;
    }

    protected function validate(array $options = []):array
    {
        $validate = [
            'status' => $this->setValidationStatus(),
            'messages' => [],
        ];
        $limits = Utils::deep_array_value([static::ec, 'limits'], $options, []);
        foreach ($limits as $fieldName => $field_limits) {
            if (\property_exists($this, $fieldName) && \is_array($field_limits)) {
                $getfn = 'get' . ucfirst($fieldName);
                $statusIdx = count($this->validationStatuses) - 1;
                while ($statusIdx >= 0) {
                    $limit_type = $this->validationStatuses[$statusIdx];
                    if (\array_key_exists($limit_type, $field_limits)) {
                        $val = $this->$getfn();
                        $limit = Utils::check_limits($field_limits[$limit_type], $val);
                        if (is_array($limit)) {
                            $validate['status'] = $this->setValidationStatus($limit_type, $validate['status']);
                            $validate['messages'] = $this->genMessage('import_' . $fieldName . '_' . $validate['status'], null, [$val]) . ' ' . $limit['msg'];
                            break;
                        }
                    }
                    $statusIdx--;
                }
            }
        }
        return $validate;
    }
// </editor-fold>

    public function genMessage($msg, $entityClassName = null, $include = [])
    {
        if ($this->controller) {
            $msg = $this->controller->trans($this->controller->messageText($msg, $entityClassName ? $entityClassName : static::ec));
            if ($count = count($include)) {
                $search = [];
                for ($i = 1; $i <= $count; $i++) {
                    $search[] = '%' . $i;
                }
                $msg = str_replace($search, $include, $msg);
            }
        }
        return $msg;
    }

    public function preUpdate(){
        return $this;
    }

}



// protected $clientNumbering = false;


    // protected function preUpdateChilds($childs){
    //     foreach($childs as $c){
    //         $c->preUpdate();
    //     }
    //     return $this;
    // }

    // public function postUpdate($em){
    //     // if (property_exists($this, 'uploads')) {
    //     //     $this->_checkElements('uploads', $em);
    //     //     $this->checkUploads();
    //     // }elseif (property_exists($this, 'upload')) {
    //     //     $this->checkUpload($this->oldValues['upload']);
    //     // }
    //     // return $this;
    // }

    // protected function postUpdateChilds($childs, $em){
    //     foreach($childs as $c){
    //         $c->postUpdate($em);
    //     }
    //     return $this;
    // }

    // public function checkFieldChange($name){
    //     $fn = 'get' . ucfirst($name);
    //     if (!method_exists($this, $fn) || !array_key_exists($name, $this->oldValues)) {
    //         return null;
    //     }
    //     return $this->$fn()!=$this->oldValues[$name];
    // }

    // public function remove($options = [])
    // {
    //     return [];
    // }

    // public function genNumber1($client = null):string
    // {
    //     $numberGenerator = null;
    //     $nr = 0;
    //     $es = $this->controller->getEntitySettings(static::ec);
    //     if ($client) {
    //         $cs = $this->controller->getClientSettingsValue($client, static::ec);
    //         $nr = Utils::deep_array_value('number', $cs, 0);
    //         if ($nr > 0) {
    //             $numberGenerator = Utils::deep_array_value('numberGenerator', $cs);
    //         }
    //     }
    //     if ($nr > 0) {
    //         $this->clientNumbering = true;
    //     } else {
    //         $nr = Utils::deep_array_value('number', $es, 1);
    //         $this->clientNumbering = false;
    //     }
    //     $this->setIntNr($nr);
    //     if (!is_array($numberGenerator)) {
    //         $numberGenerator = Utils::deep_array_value('numberGenerator', $es, []);
    //     }
    //     return $this->generateNumber($numberGenerator, $this->getIntNr());
    // }

    // public function nextNr($client = null)
    // {
    //     $sn = static::en . '-number';
    //     $next = $this->getIntNr() + 1;
    //     if ($this->clientNumbering && $client) {
    //         $this->controller->saveClientSetting($client, $sn, $next);
    //     } else {
    //         $this->controller->saveSetting($sn, $next);
    //     }
    //     return $next;
    // }
  //  public function saveOldUploads()
    // {
    //     $this->oldUploads = $this->cloneUploads();
    //     return $this->oldUploads;
    // }

    // public function checkUpload($oldUpload = null)
    // {
    //     $uploadType = array_search(static::en, Uploads::$types);
    //     $upload = $this->getUpload();
    //     if ($upload) {
    //         if ($oldUpload && ($upload->getName() != $oldUpload->getName())) {
    //             $oldUpload->removeUpload();
    //         }
    //         $upload->changeUploadType($uploadType);
    //     }
    //     return $this;
    // }

 //  <editor-fold defaultstate="collapsed" desc="Uploads">
//  private $oldUploads;

  
//  public function checkUploads()
//  {
//      $uploadType = array_search(static::en, Uploads::$types);
//      foreach ($this->getUploads() as $upload) {
//          $upload->changeUploadType($uploadType);
//      }
//      return $this;
//  }

// </editor-fold>
