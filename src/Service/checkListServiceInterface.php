<?php

namespace CheckList\Service;

interface checkListServiceInterface {

    /**
     *
     * Get checklists
     *
     * @return      array
     *
     */
    public function getChecklists();

    /**
     *
     * get checklists
     *
     * @return      array
     *
     */
    public function getAllChecklists();

    /**
     *
     * Get array of checklist
     *
     * @return      array
     *
     */
    public function getArchivedChecklists();

    /**
     *
     * Create a new checklist object
     *
     * @return      object
     *
     */
    public function createChecklist();

    /**
     *
     * Create form of an object
     *
     * @param       checklist $checklist object
     * @return      form
     *
     */
    public function createCheckListForm($checklist);

    /**
     *
     * Set data to new event
     *
     * @param       checklist $checklist object
     * @param       currentUser $currentUser who is logged on
     * @return      void
     *
     */
    public function setNewCheckList($checklist, $currentUser);

    /**
     *
     * Get object of an checklist by id
     *
     * @param       id $id of the checklist
     * @return      object
     *
     */
    public function getCheckListById($id);

    /**
     *
     * Set data to existing checklist
     *
     * @param       checklist $checkList object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setExistingCheckList($checkList, $currentUser);

    /**
     *
     * Archive checklist
     *
     * @param       checklist $checklist object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function archiveChecklist($checklist, $currentUser);

    /**
     *
     * Save checklist to database
     *
     * @param       event object
     * @return      void
     *
     */
    public function storeCheckList($event);

    /**
     *
     * Delete a Checklist object from database
     * @param       checklist $checklist object
     * @return      void
     *
     */
    public function deleteChecklist($checklist);
}
