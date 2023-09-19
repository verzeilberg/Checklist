<?php

namespace CheckList\Form;

use CheckList\Entity\Answer;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\NotEmpty;

class ChecklistAnswerFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('checklist-answer');

        $this->setHydrator(new DoctrineHydrator($objectManager))
            ->setObject(new Answer());

        $this->add([
            'type' => Text::class,
            'name' => 'label',
            'options' => [
                'label' => 'Label',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Label',
                'id' => 'labelAnswer'
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'value',
            'options' => [
                'label' => 'Value',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'Waarde',
                'id' => 'valueAnswer'
            ],
        ]);

    }

    public function getInputFilterSpecification()
    {
        return [
            'label' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Label is verplicht!'
                            ]
                        ]
                    ]
                ]
            ],
            'value' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Waarde is verplicht!'
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}