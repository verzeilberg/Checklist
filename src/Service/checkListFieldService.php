<?php

namespace CheckList\Service;

use CheckList\Entity\CheckListField;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class checkListFieldService implements checkListFieldServiceInterface {

    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    /**
     *
     * Get object of an checklistitem by id
     *
     * @param       id $id of the checklistitem
     * @return      object
     *
     */
    public function getCheckListFieldById($id) {
        $checkListField = $this->em->getRepository(CheckListField::class)
                ->findOneBy(['id' => $id], []);

        return $checkListField;
    }

    /**
     *
     * Create a new checklistitem object
     *
     * @return      object
     *
     */
    public function createChecklistField() {
        return new CheckListField();
    }

    /**
     *
     * Create form of an object
     *
     * @param       checklistitem $checklistfield object
     * @return      form
     *
     */
    public function createCheckListFieldForm($checklistfield) {
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($checklistfield);
        $form->setHydrator(new DoctrineHydrator($this->em, 'CheckList\Entity\CheckListField'));
        $form->bind($checklistfield);

        return $form;
    }

    /**
     *
     * Set data to new checklistitem
     *
     * @param       checklistitem $checklistfield object
     * @param       checklist $checklist object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setNewCheckListField($checklistfield, $checklist, $currentUser) {
        $checklistfield->setFormFieldName($checklistfield->getName());
        $checklistfield->setDateCreated(new \DateTime());
        $checklistfield->setCreatedBy($currentUser);
        $checklistfield->setChecklist($checklist);



        $this->storeCheckListField($checklistfield);
    }

    /**
     *
     * Save checklistitem to database
     *
     * @param       checklistitem object
     * @return      void
     *
     */
    public function storeCheckListField($checklistfield) {
        $this->em->persist($checklistfield);
        $this->em->flush();
    }

    /**
     *
     * Remove checklistfield from database
     *
     * @param       checklistfield object
     * @return      void
     *
     */
    public function removeCheckListField($checklistfield) {
        $this->em->remove($checklistfield);
        $this->em->flush();
    }

}
