<?php

namespace CheckList\Service;

use CheckList\Entity\CheckListItem;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class checkListItemService implements checkListItemServiceInterface {

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
    public function getCheckListItemById($id) {
        $checkListItem = $this->em->getRepository(CheckListItem::class)
                ->findOneBy(['id' => $id], []);
        return $checkListItem;
    }

    /**
     *
     * Create a new checklistitem object
     *
     * @return      object
     *
     */
    public function createChecklistItem() {
        return new CheckListItem();
    }

    /**
     *
     * Create form of an object
     *
     * @param       checklistitem $checklistitem object
     * @return      form
     *
     */
    public function createCheckListItemForm($checklistitem) {
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($checklistitem);
        $form->setHydrator(new DoctrineHydrator($this->em, 'CheckList\Entity\CheckListItem'));
        $form->bind($checklistitem);

        return $form;
    }

    /**
     *
     * Set data to new checklistitem
     *
     * @param       checklistitem $checklistitem object
     * @param       checklist $checklist object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setNewCheckListItem($checklistitem, $checklist, $currentUser) {
        $checklistitem->setDateCreated(new \DateTime());
        $checklistitem->setCreatedBy($currentUser);
        $checklistitem->setChecklist($checklist);
        
        $this->storeCheckListItem($checklistitem);

        return $checklistitem;
    }

    /**
     *
     * Set data to new checklistitem
     *
     * @param       checklistitem $checklistitem object
     * @param       checklist $checklist object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function updateCheckListItem($checklistitem, $checklist, $currentUser) {
        $checklistitem->setDateChanged(new \DateTime());
        $checklistitem->setChangedBy($currentUser);
        $checklistitem->setChecklist($checklist);

        $this->storeCheckListItem($checklistitem);

        return $checklistitem;
    }

    /**
     *
     * Save checklistitem to database
     *
     * @param       checklistitem object
     * @return      void
     *
     */
    public function storeCheckListItem($checklistitem) {
        $this->em->persist($checklistitem);
        $this->em->flush();
    }
    
        /**
     *
     * Delete checklist item from database
     *
     * @param       id  $id
     * @return      boolean
     *
     */
    public function deleteCheckListItem($id = NULL) {
        if ($id > 0) {
            $checklistitem = $this->em->getRepository(CheckListItem::class)
                    ->findOneBy(['id' => $id], []);
            if (empty($checklistitem)) {
                return false;
            }
            $this->em->remove($checklistitem);
            $this->em->flush();

            return true;
        } else {
            return false;
        }
    }

}
