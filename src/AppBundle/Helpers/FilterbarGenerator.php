<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class FilterbarGenerator extends ElementsGenerator
{

    private $fg;// FiltersGenerator
    
    protected $genType='filterbar';
    protected $type='index';

    public function __construct( EntityHelper $entityHelper, FiltersGenerator $filtersGenerator){
        $this->eh=$entityHelper;
        $this->fg=$filtersGenerator;
    }
  
    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $filterbar=$this->generateElement($options);
        $this->setId($filterbar);
        $filters=$this->fg->generate($type, $this->ecn, Utils::deep_array_value('filters', $options, [
            'parentId' => $filterbar['attr']['id']
        ]));
        $filterbar['filters']= $filters['visible'];
        $filterbar['d']['options']['hiddenFilters'] = Utils::deep_array_value('hidden', $filters, []);
        return $filterbar;
    }

}