<?php

namespace CheckList\Service;

interface checkListAnswerServiceInterface {

    /**
     *
     * Get object of an checklistitem by id
     *
     * @param       id $id of the checklistitem
     * @return      object
     *
     */
    public function getAnswerById($id);

    /**
     *
     * Create a new checklistitem object
     *
     * @return      object
     *
     */
    public function createAnswer();

    /**
     *
     * Create form of an object
     *
     * @param       checklistitem $checklistfield object
     * @return      form
     *
     */
    public function createAnswerForm($checklistfield);

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
    public function setNewAnswer($checklistfield, $currentUser);

    /**
     *
     * Save checklistitem to database
     *
     * @param       checklistitem object
     * @return      void
     *
     */
    public function storeAnswer($checklistfield);

    /**
     *
     * Remove checklistfield from database
     *
     * @param       checklistfield object
     * @return      void
     *
     */
    public function removeAnswer($checklistfield);
}
