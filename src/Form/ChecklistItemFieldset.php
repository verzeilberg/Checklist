<?php

namespace CheckList\Form;

use CheckList\Entity\CheckList;
use CheckList\Entity\CheckListItem;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\NotEmpty;

class ChecklistItemFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('checklist-item');

        $this->setHydrator(new DoctrineHydrator($objectManager))
            ->setObject(new CheckListItem());

        $this->add([
            'type' => Textarea::class,
            'name' => 'itemContent',
            'options' => [
                'label' => 'itemContent',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
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