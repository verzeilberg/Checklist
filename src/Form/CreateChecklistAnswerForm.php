<?php

namespace CheckList\Form;


use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class CreateChecklistAnswerForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('create-checklist-answer-form');

        // The form will hydrate an object of type "Checklist"
        $this->setHydrator(new DoctrineHydrator($objectManager));

        // Add the Blog fieldset, and set it as the base fieldset
        $checklistAnswerFieldset = new ChecklistAnswerFieldset($objectManager);
        $checklistAnswerFieldset->setUseAsBaseFieldset(true);
        $this->add($checklistAnswerFieldset);

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