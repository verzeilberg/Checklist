<?php

namespace CheckList\Form;


use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class CreateChecklistFieldForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('create-checklist-field-form');

        // The form will hydrate an object of type "Checklist"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the Blog fieldset, and set it as the base fieldset
        $checklistFieldFieldset = new ChecklistFieldFieldset($objectManager);
        $checklistFieldFieldset->setUseAsBaseFieldset(true);
        $this->add($checklistFieldFieldset);

        // Add the Submit button
        $this->add([
            'type'  => Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Opslaan',
                'id' => 'submit',
                'class' => 'btn btn-primary',
            ],
        ]);

        // Add the CSRF field
        $this->add([
            'type' => Csrf::class,
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

    }
}