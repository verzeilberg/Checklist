<?php

namespace CheckList\Service;

interface checkListFieldServiceInterface {

    /**
     *
     * Get object of an checklistitem by id
     *
     * @param       id $id of the checklistitem
     * @return      object
     *
     */
    public function getCheckListFieldById($id);

    /**
     *
     * Create a new checklistitem object
     *
     * @return      object
     *
     */
    public function createChecklistField();

    /**
     *
     * Create form of an object
     *
     * @param       checklistitem $checklistfield object
     * @return      form
     *
     */
    public function createCheckListFieldForm($checklistfield);

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
    public function setNewCheckListField($checklistfield, $checklist, $currentUser);

    /**
     *
     * Save checklistitem to database
     *
     * @param       checklistitem object
     * @return      void
     *
     */
    public function storeCheckListField($checklistfield);

    /**
     *
     * Remove checklistfield from database
     *
     * @param       checklistfield object
     * @return      void
     *
     */
    public function removeCheckListField($checklistfield);
}
