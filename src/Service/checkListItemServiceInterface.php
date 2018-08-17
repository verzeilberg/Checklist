<?php

namespace CheckList\Service;

interface checkListItemServiceInterface {

    /**
     *
     * Get object of an checklistitem by id
     *
     * @param       id $id of the checklistitem
     * @return      object
     *
     */
    public function getCheckListItemById($id);

    /**
     *
     * Create a new checklistitem object
     *
     * @return      object
     *
     */
    public function createChecklistItem();

    /**
     *
     * Create form of an object
     *
     * @param       checklistitem $checklistitem object
     * @return      form
     *
     */
    public function createCheckListItemForm($checklistitem);

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
    public function setNewCheckListItem($checklistitem, $checklist, $currentUser);

    /**
     *
     * Save checklistitem to database
     *
     * @param       checklistitem object
     * @return      void
     *
     */
    public function storeCheckListItem($checklistitem);
}
