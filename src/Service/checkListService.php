<?php

namespace CheckList\Service;

use Laminas\ServiceManager\ServiceLocatorInterface;
use CheckList\Entity\CheckList;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class checkListService implements checkListServiceInterface {

    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    /**
     *
     * get checklists
     *
     * @return      array
     *
     */
    public function getAllChecklists() {
        $checkLists = $this->em->getRepository(CheckList::class)->findAll();
        if (!empty($checkLists)) {
            return $checkLists;
        } else {
            return null;
        }
    }

    /**
     *
     * Get array of checklist
     *
     * @return      array
     *
     */
    public function getChecklists() {
        $checkLists = $this->em->getRepository(CheckList::class)
                ->findBy(['deleted' => 0], ['dateCreated' => 'DESC']);

        return $checkLists;
    }
    
        /**
     *
     * Get array of checklist
     *
     * @param       $user object
     * @return      array
     *
     */
    public function getChecklistsByUser($user) {
        $checkLists = $this->em->getRepository(CheckList::class)
                ->findBy(['deleted' => 0, 'createdBy' => $user->getId()], ['dateCreated' => 'DESC']);

        return $checkLists;
    }
    
            /**
     *
     * Get array of checklist
     *
     * @param       $user object
     * @param       $public int
     * @return      array
     *
     */
    public function getChecklistsByUserAndStatus($user, $public = 2) {
        $checkLists = $this->em->getRepository(CheckList::class)
                ->findBy(['deleted' => 0, 'public' => $public, 'createdBy' => $user->getId()], ['dateCreated' => 'DESC']);

        return $checkLists;
    }

    /**
     *
     * Get array of checklist
     *
     * @return      array
     *
     */
    public function getArchivedChecklists() {
        $checkLists = $this->em->getRepository(CheckList::class)
                ->findBy(['deleted' => 1], ['id' => 'ASC']);
        return $checkLists;
    }

    /**
     *
     * Get object of an checklist by id
     *
     * @param       id $id of the checklist
     * @return      object
     *
     */
    public function getCheckListById($id) {
        $checkList = $this->em->getRepository(CheckList::class)
                ->findOneBy(['id' => $id], []);

        return $checkList;
    }

    /**
     *
     * Set data to existing checklist
     *
     * @param       checklist $checkList object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setExistingCheckList($checkList, $currentUser) {
        $checkList->setDateChanged(new \DateTime());
        $checkList->setChangedBy($currentUser);
        $this->storeCheckList($checkList);
    }

    /**
     *
     * Create a new checklist object
     *
     * @return      object
     *
     */
    public function createChecklist() {
        return new CheckList();
    }

    /**
     *
     * Create form of an object
     *
     * @param       checklist $checklist object
     * @return      form
     *
     */
    public function createCheckListForm($checklist) {
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($checklist);
        $form->setHydrator(new DoctrineHydrator($this->em, 'CheckList\Entity\CheckList'));
        $form->bind($checklist);

        return $form;
    }

    /**
     *
     * Set data to new checklist
     *
     * @param       checklist $checklist object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setNewCheckList($checklist, $currentUser) {
        $checklist->setDateCreated(new \DateTime());
        $checklist->setCreatedBy($currentUser);

        $this->storeCheckList($checklist);
    }

    /**
     *
     * Archive checklist
     *
     * @param       checklist $checklist object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function archiveChecklist($checklist, $currentUser) {
        $checklist->setDateDeleted(new \DateTime());
        $checklist->setDeleted(1);
        $checklist->setDeletedBy($currentUser);

        $this->storeCheckList($checklist);
    }

    /**
     *
     * UnArchive checklist
     *
     * @param       checklist $checklist object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function unArchiveChecklist($checklist, $currentUser) {
        $checklist->setDeletedBy(NULL);
        $checklist->setChangedBy($currentUser);
        $checklist->setDeleted(0);
        $checklist->setDateDeleted(NULL);
        $checklist->setDateChanged(new \DateTime());

        $this->storeChecklist($checklist);
    }

    /**
     *
     * Save event to database
     *
     * @param       checklist object
     * @return      void
     *
     */
    public function storeCheckList($checklist) {
        $this->em->persist($checklist);
        $this->em->flush();
    }

    /**
     *
     * Delete a Checklist object from database
     * @param       checklist $checklist object
     * @return      void
     *
     */
    public function deleteChecklist($checklist) {
        $this->em->remove($checklist);
        $this->em->flush();
    }

}
