<?php

namespace CheckList\Form;

use Blog\Entity\Category;
use CheckList\Entity\CheckListFieldType;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Form\Element\ObjectSelect;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class ChecklistFieldTypeFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('checklist-field-type');

        $this->setHydrator(new DoctrineHydrator($objectManager))
            ->setObject(new CheckListFieldType());

        $this->add([
            'name' => 'checklistFieldType',
            'required' => false,
            'type' => ObjectSelect::class,
            'options' => [
                'object_manager' => $objectManager,
                'target_class'   => CheckListFieldType::class,
                'property'       => 'id',
                'is_method'      => true,
                'display_empty_item' => true,
                'label' => 'Veld type',
                'label_generator' => function ($targetEntity) {
                    return $targetEntity->getName();
                },
            ],
            'attributes' => [
                'class' => 'form-check-input',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'categories' => [
                'required' => false,
            ]
        ];
    }
}