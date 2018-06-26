<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class FilterbarGenerator extends ElementsGenerator
{

    private $th;// TransHelper
    private $fg;// FiltersGenerator
    
    protected $genType='filterbar';
    protected $type='index';

    private $filters;

    public function __construct( EntityHelper $entityHelper, FiltersGenerator $filtersGenerator){
        $this->eh=$entityHelper;
        $this->fg=$filtersGenerator;
    }
  
    protected function getD(array $elementOptions):array
    {
        return array_replace_recursive(
            Utils::deep_array_value('d', $elementOptions, []),
            [
                'options' => [
                    'hiddenFilters' => Utils::deep_array_value('hidden', $this->filters, [])
                ]
            ]
        );
    }

    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        parent::generate($type, $entityClassName, $options);
        $this->filters=$this->fg->generate($type, $this->ecn, Utils::deep_array_value('filters', $options, []));
        $filterbar=$this->generateElement($options);
        $filterbar['filters']= $this->filters['visible'];
        return $filterbar;
    }

}