<?php

namespace CheckList\Form;

use CheckList\Entity\Answer;
use CheckList\Entity\CheckList;
use CheckList\Entity\CheckListField;
use CheckList\Entity\CheckListFieldType;
use CheckList\Form\Element\ParentSelect;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Form\Element\ObjectSelect;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Collection;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\NotEmpty;
use Symfony\Component\VarDumper\VarDumper;

class ChecklistFieldFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('checklist-field');

        $this->setHydrator(new DoctrineHydrator($objectManager))
            ->setObject(new CheckListField());

        $this->add([
            'type' => Text::class,
            'name' => 'order',
            'options' => [
                'label' => 'Volgorde',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Checkbox::class,
            'name' => 'showInOverview',
            'options' => [
                'label' => 'Toon in het overzicht',
            ],
            'attributes' => [
                'class' => 'form-check-label',
            ],
        ]);

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
            'type' => Textarea::class,
            'name' => 'introText',
            'options' => [
                'label' => 'Intro tekst',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Checkbox::class,
            'name' => 'required',
            'options' => [
                'label' => 'Verplicht',
            ],
            'attributes' => [
                'class' => 'form-check-label',
                'id' => 'isRequired'
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'requiredMessage',
            'options' => [
                'label' => 'Validatie tekst',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'requiredMessage',
            ],
        ]);

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
                'class' => 'form-control',
                'id' => 'checklistFieldType'
            ],
        ]);



        $this->add([
            'type' => ObjectMultiCheckbox::class,
            'name' => 'answers',
            'required' => false,
            'options' => [
                'object_manager' => $objectManager,
                'target_class'   => Answer::class,
                'label' => 'Antwoorden',
                'label_generator' => function ($targetEntity) {
                    return $targetEntity->getLabel();
                },
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
            ],
            'answers' => [
                'required' => false,
            ]
        ];
    }

}
