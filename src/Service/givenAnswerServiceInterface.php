<?php

namespace CheckList\Service;

interface givenAnswerServiceInterface {

    /**
     *
     * Get object of an answerGiven by id
     *
     * @param       id $id of the answerGiven
     * @return      object
     *
     */
    public function getGivenAnswerById($id);
}
