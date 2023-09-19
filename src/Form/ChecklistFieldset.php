<?php

namespace CheckList\Form;

use CheckList\Entity\CheckList;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\NotEmpty;

class ChecklistFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('checklist');

        $this->setHydrator(new DoctrineHydrator($objectManager))
            ->setObject(new CheckList());

        $this->add([
            'type' => Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Naam',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Radio::class,
            'name' => 'public',
            'options' => [
                'label' => 'Type lijst',
                'label_attributes' => [
                    'class' => 'form-check-label',
                ],
                'value_options' => [
                    '1' => 'Publiek',
                    '2' => 'Persoonlijk',
                ],
            ],
            'attributes' => [
                'class' => 'form-check-input',
            ],
        ]);

        $this->add([
            'type' => Radio::class,
            'name' => 'autocomplete',
            'options' => [
                'label' => 'Autocomplete',
                'label_attributes' => [
                    'class' => 'form-check-label',
                ],
                'value_options' => [
                    '0' => 'Nee',
                    '1' => 'Ja',
                ],
            ],
            'attributes' => [
                'class' => 'form-check-input',
            ],
        ]);

    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Naam is verplicht!'
                            ]
                        ]
                    ]
                ]
            ],
            'public' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Type lijst is verplicht!'
                            ]
                        ]
                    ]
                ]
            ],
            'autocomplete' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Autocomplete is verplicht!'
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}