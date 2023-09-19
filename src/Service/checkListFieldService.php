<?php

namespace CheckList\Service;

use CheckList\Entity\CheckListField;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class checkListFieldService
{

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
     * Get object of an checklistitem by id
     *
     * @param       id $id of the checklistitem
     * @return      object
     *
     */
    public function getCheckListFieldsByCheckListId($checkList, $checkListFieldId = null) {
        $checkListFields = $this->em->getRepository(CheckListField::class)
            ->findBy(['checklist' => $checkList], []);

        $qb = $this->em->getRepository(CheckListField::class)->createQueryBuilder('clf');
        $qb->join('clf.checklist','cl');
        $qb->where($qb->expr()->like('cl.id', ":checkListId"));
        if(!empty($checkListFieldId)) {
            $qb->andWhere($qb->expr()->neq('clf.id', ":checkListFieldId"));
            $qb->setParameter('checkListFieldId', $checkListFieldId);
        }

        $qb->setParameter('checkListId', $checkList->getId());
        $query = $qb->getQuery();
        $checkListFields = $query->getResult();

        return $checkListFields;
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
        $this->em->flush($checklistfield);
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
