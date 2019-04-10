<?php

namespace CheckList\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use CheckList\Entity\AnswerGiven;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class givenAnswerService implements givenAnswerServiceInterface
{

    protected $em;
    protected $checkListFieldService;
    protected $checkListAnswerService;

    public function __construct($em, $checkListFieldService, $checkListAnswerService)
    {
        $this->em = $em;
        $this->checkListFieldService = $checkListFieldService;
        $this->checkListAnswerService = $checkListAnswerService;
    }


    /**
     *
     * Get object of an answerGiven by id
     *
     * @param       id $id of the answerGiven
     * @return      object
     *
     */
    public function getGivenAnswerById($id)
    {
        $answerGiven = $this->em->getRepository(AnswerGiven::class)
            ->findOneBy(['id' => $id], []);

        return $answerGiven;
    }

    public function getGivenAnswersByChecklistId($checklistId)
    {
        $qb = $this->em->getRepository(AnswerGiven::class)->createQueryBuilder('ag');
        $qb->select('ag.id answerGivenId, ag.answer answer, cli.id checkListItemId, clf.id checkListFieldId, a.label answerGiven');
        $qb->leftJoin('ag.checklist', 'c');
        $qb->leftJoin('ag.checklistItem', 'cli');
        $qb->leftJoin('ag.checklistField', 'clf');
        $qb->leftJoin('ag.answerValue', 'a');
        $qb->where('c.id = ' . $checklistId);
        $query = $qb->getQuery();
        $result = $query->getResult();


        $data = [];
        foreach ($result AS $item) {
            $givenAnswer = $item["answer"];
            if ($givenAnswer == null) {
                $givenAnswer = $item["answerGiven"];
            }

            $data[$item["checkListItemId"]][$item["checkListFieldId"]][$item["answerGivenId"]] = $givenAnswer;
        }

        return $data;
    }

    public function getGivenAnswersByChecklisItemtId($checklistItemId)
    {
        $qb = $this->em->getRepository(AnswerGiven::class)->createQueryBuilder('ag');
        $qb->leftJoin('ag.answerValue', 'av');
        $qb->leftJoin('ag.checklistItem', 'cli');
        $qb->leftJoin('ag.checklistField', 'clf');
        $qb->where('cli.id = ' . $checklistItemId);
        $query = $qb->getQuery();
        $result = $query->getResult();

        $data = [];
        foreach ($result AS $item) {


            if (!empty($item->getAnswer())) {
                $data[$checklistItemId][$item->getChecklistField()->getId()][] = $item->getAnswer();
            } else {
                $data[$checklistItemId][$item->getChecklistField()->getId()][] = $item->getAnswerValue()->getId();
            }

        }

        return $data;
    }


    public function saveAnswers($data, $checkListItem, $checklist)
    {

        $answerData = [];
        foreach ($data AS $index => $value) {
            $answerGiven = $this->createAnswerGiven();

            $checkListFieldId = $value[0];
            $checklistField = $this->checkListFieldService->getCheckListFieldById($checkListFieldId);
            $givenAnswer = null;
            $formType = $checklistField->getChecklistFieldType()->getFormType();
            if ($formType == 'checkbox') {
                $answer = $this->checkListAnswerService->getAnswerById($value[1]);
                $answerGiven->setAnswerValue($answer);
                $answerData[$checklistField->getId()]['type'][$formType][] = $answer->getLabel();
            } else {
                $givenAnswer = $value[1];
                $answerData[$checklistField->getId()]['type'][$formType] = $givenAnswer;
            }

            $answerGiven->setChecklist($checklist);
            $answerGiven->setChecklistItem($checkListItem);
            $answerGiven->setChecklistField($checklistField);
            $answerGiven->setAnswer($givenAnswer);
            $this->setNewAnswerGiven($answerGiven, null);

        }

        return (array)$answerData;

    }

    public function deleteAnswersGiven($checklistItem = null) {
        foreach($checklistItem->getAnswersGivenForDeleting() AS $answerGiven) {
            $this->deleteAnswerGiven($answerGiven);
        }
    }


    /**
     *
     * Create a new answerGiven object
     *
     * @return      object
     *
     */
    public function createAnswerGiven()
    {
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
    public function setNewAnswerGiven($answerGiven, $currentUser)
    {
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
    public function archiveAnswerGiven($answerGiven, $currentUser)
    {
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
    public function unArchiveAnswerGiven($answerGiven, $currentUser)
    {
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
    public function storeAnswerGiven($answerGiven)
    {
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
    public function deleteAnswerGiven($answerGiven)
    {
        $this->em->remove($answerGiven);
        $this->em->flush();
    }

}
