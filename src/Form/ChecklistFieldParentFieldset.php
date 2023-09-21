<?php

namespace CheckList\Form;


use CheckList\Form\Element\ParentSelect;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\NotEmpty;

class ChecklistFieldParentFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('checklist-field-parent');

        $this->add([
            'name' => 'parent',
            'type' => ParentSelect::class,
            'options' => [
                'label' => 'Parensssssst',
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => false,
            ],
        ];
    }
}