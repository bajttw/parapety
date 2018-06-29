<?php

namespace AppBundle\Helpers;

use Symfony\Component\Translation\TranslatorInterface;
// use Symfony\Component\DependencyInjection\ContainerInterface ;


class MessageHelper
{

    private $en='';
    private $translator;  

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator=$translator;
    }




}