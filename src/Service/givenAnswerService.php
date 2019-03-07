<?php

namespace CheckList\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use CheckList\Entity\AnswerGiven;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class givenAnswerService implements givenAnswerServiceInterface {

    protected $em;
    protected $checkListFieldService;

    public function __construct($em, $checkListFieldService) {
        $this->em = $em;
        $this->checkListFieldService = $checkListFieldService;
    }


    /**
     *
     * Get object of an answerGiven by id
     *
     * @param       id $id of the answerGiven
     * @return      object
     *
     */
    public function getGivenAnswerById($id) {
        $answerGiven = $this->em->getRepository(AnswerGiven::class)
                ->findOneBy(['id' => $id], []);

        return $answerGiven;
    }

    public function getAnswersByChecklistId($checklistId) {
        $qb = $this->em->getRepository(AnswerGiven::class)->createQueryBuilder('ag');
        $qb->select('ag.id answerGivenId, ag.answer answer, cli.id checkListItemId, clf.id checkListFieldId');
        $qb->join('ag.checklist', 'c');
        $qb->join('ag.checklistItem' , 'cli');
        $qb->join('ag.checklistField', 'clf');
        $qb->where('c.id = ' . $checklistId);
        $query = $qb->getQuery();
        $array = $query->getArrayResult();

        return $array;
    }


    public function saveAnswers($data, $checkListItem, $checklist){
        foreach ($data AS $value) {

            $checkListFieldId = $value[0];
            $checklistField = $this->checkListFieldService->getCheckListFieldById($checkListFieldId);
            $answerGiven =  $this->createAnswerGiven();
            $answerGiven->setChecklist($checklist);
            $answerGiven->setChecklistItem($checkListItem);
            $answerGiven->setChecklistField($checklistField);
            $answerGiven->setAnswer($value[1]);
            $this->setNewAnswerGiven($answerGiven, null);

        }
    }


    /**
     *
     * Create a new answerGiven object
     *
     * @return      object
     *
     */
    public function createAnswerGiven() {
        return new AnswerGiven();
    }

    /**
     *
     * Set data to new answerGiven
     *
     * @param       answerGiven $answerGiven object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setNewAnswerGiven($answerGiven, $currentUser) {
        $answerGiven->setDateCreated(new \DateTime());
        $answerGiven->setCreatedBy($currentUser);

        $this->storeAnswerGiven($answerGiven);
    }

    /**
     *
     * Archive answerGiven
     *
     * @param       answerGiven $answerGiven object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function archiveAnswerGiven($answerGiven, $currentUser) {
        $answerGiven->setDateDeleted(new \DateTime());
        $answerGiven->setDeleted(1);
        $answerGiven->setDeletedBy($currentUser);

        $this->storeanswerGiven($answerGiven);
    }

    /**
     *
     * UnArchive answerGiven
     *
     * @param       answerGiven $answerGiven object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function unArchiveAnswerGiven($answerGiven, $currentUser) {
        $answerGiven->setDeletedBy(NULL);
        $answerGiven->setChangedBy($currentUser);
        $answerGiven->setDeleted(0);
        $answerGiven->setDateDeleted(NULL);
        $answerGiven->setDateChanged(new \DateTime());

        $this->storeanswerGiven($answerGiven);
    }

    /**
     *
     * Save event to database
     *
     * @param       answerGiven object
     * @return      void
     *
     */
    public function storeAnswerGiven($answerGiven) {
        $this->em->persist($answerGiven);
        $this->em->flush();
    }

    /**
     *
     * Delete a answerGiven object from database
     * @param       answerGiven $answerGiven object
     * @return      void
     *
     */
    public function deleteAnswerGiven($answerGiven) {
        $this->em->remove($answerGiven);
        $this->em->flush();
    }

}
