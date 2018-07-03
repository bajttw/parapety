<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class ModalsGenerator extends ElementsGenerator
{

    protected $tmplh;
    
    protected $genType='modal';
    protected $type='my';


    public function __construct( EntityHelper $entityHelper, SettingsHelper $settingsHelper, TransHelper $transHelper, TemplateHelper $templateHelper){
        $this->eh=$entityHelper;
        $this->sh=$settingsHelper;
        $this->th=$transHelper;
        $this->tmplh=$templateHelper;
    }


    protected function getContent(array $panelOptions):?string
    {
        $contentName = Utils::deep_array_value('contentName', $panelOptions);
        if($contentName){
            return $this->tmplh->getPath(
                $contentName,
                $this->ecn,
                Utils::deep_array_value('contentGeneric', $panelOptions, false),
                'm'
            );
        }
        return null;
    }
    
    protected function getData(array $modalOptions):?array
    {
        $data=[];
        $source=Utils::deep_array_value('dataSource', $modalOptions);
        if(is_array($source)){
            switch($source['type']){
                case 'settings':
                    $data=$this->sh->getSettingValue($source['query']);
                break;
                case 'entity':
                    $fn=Utils::deep_array_value('fnGet', $source, 'getList');
                    $ecn= Utils::deep_array_value('ecn', $source, $this->ecn);
                    $data = $this->eh->getRepository($ecn)->$fn(Utils::deep_array_value('options', $source, [])) ;
                break;
            }
        }
        return $data;
    }

    protected function getSettings(array $modalOptions):?array
    {
        $data=[];
        $source=Utils::deep_array_value('settingsSource', $modalOptions);
        if(is_array($source)){
            switch($source['type']){
                case 'settings':
                    $data=$this->sh->getSettingValue($source['query']);
                break;
                case 'entity':
                    $data = $this->eh->getRepository($source['query'])->getList(Utils::deep_array_value('options', $source, [])) ;
                break;
            }
        }
        return $data;
    }

    protected function generateElement(?array $modalOptions=null):array
    {
        $modalOptions= ($modalOptions) ?: $this->options;
        $modal=parent::generateElement($modalOptions);
        $title=$this->getTitle($modal['name']);
        if(!array_key_exists('title', $modal)){
            $modal['title'] = $this->th->trans($title);
        }
        $this->addAttributes($modal, [
            'title' => $title,
            'id' => $this->getId($modal['name'])
        ]);
        $this->setElementProperty('dialog', $modal, $modalOptions);
        return $modal;
    }

    public function generate(?string $type=null,  ?string $entityClassName='', array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $modal=$this->generateElement();
        $this->setData($modal);
        $this->setSettings($modal);
        $this->setContent($modal);
        return $modal;
    }

    public function generateEntityModal(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $modal=$this->generateElement();
        $modal['en'] = $this->en;
        $modal['ecn'] = $this->ecn;
        $this->addClass($modal, 'ajax');
        $this->setData($modal);
        $this->setSettings($modal);
        return $modal;
    }

    public function generateFieldModal(string $fieldName, string $entityClassName='', array $options=[]):array
    {       
        $options=array_replace_recursive([
                'name' => $fieldName,
                'contentName' => 'field',
                'contentGeneric' => true,
                'd' => [
                    'set-focus' => '.field'
                ]
            ],
            $options
        );
        if(array_key_exists('dataSource', $options)){
            Utils::deep_array_value_set('dataSource-fnGet', $options, 'getDic');
        }
        $modal=$this->generate('field', $entityClassName, $options);
        $this->addClass($modal, 'modal-field');
        $this->setData($modal);
        $modal['field'] = array_replace([
                'type' => 'select'
            ],
            Utils::deep_array_value('field', $options, [])
        );
        Utils::deep_array_value_set('d.modal-widget', $modal, 'field', false, '.');
        $imgOptions=$this->eh->getSettingsValue('image', $this->ecn );
        if (is_array($imgOptions)){
            $imgWidth = $imgOptions['width'];
            $imgColumns = Utils::deep_array_value('columns', $imgOptions, intval(600 / $imgWidth));
            $modal['settings']['image']=array_replace([
                    'columns' => $imgColumns
                ], 
                $imgOptions
            );
            $modal['dialog']['attr']['style'] = Utils::deep_array_value('dialog=attr-style', $modal, '') . 'max-width:' . ($imgColumns * $imgWidth + 100) . 'px;';
        }
        return $modal;
    }


    public function generateExpModal(?string $entityClassName=null, array $options=[]):array
    {       
        $this->init('exp', $entityClassName, array_replace_recursive([
                'contentName' => 'field',
                'contentGeneric' => true,
                'namePrefix' => 'exp',
                'd' => [
                    'set-focus' => '.field'
                ]
            ],
            $options
        ));
        $modal=$this->generateElement();     
        $modal['en'] = $this->en;
        $modal['ecn'] = $this->ecn;
        $this->setId($modal);
        $this->setSettings($modal);
        $this->setContent($modal);
        $this->addClass($modal, 'modal-field');
        $modal['field'] = array_replace([
            'type' => 'textarea'
        ],
        Utils::deep_array_value('field', $options, [])
    );
    // $this->setElementProperty('fieldType', $modal);
    //     $this->setElementProperty('fieldAttr', $modal);
        $modal['showSave']=false;   
        return $modal;
    }

    public function generateFieldModals(array $fields , array $options=[]):array
    {
        $modals = [];
        // $this->init($type, $entityClassName, $options);
        foreach($fields as $name => $fmo){
            $modals[$name]=$this->generateFieldModal(
                $name,
                Utils::deep_array_value('ecn', $fmo, ''),
                array_replace_recursive(
                    $options,
                    $fmo
                )
            );
        }
        return $modals;
    }


}