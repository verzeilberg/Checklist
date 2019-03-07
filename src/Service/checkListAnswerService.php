<?php

namespace CheckList\Service;

use CheckList\Entity\Answer;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class checkListAnswerService implements checkListAnswerServiceInterface
{

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     *
     * Get object of an $answer by id
     *
     * @param       id $id of the answer
     * @return      object
     *
     */
    public function getAnswerById($id)
    {
        $answer = $this->em->getRepository(Answer::class)
            ->findOneBy(['id' => $id], []);

        return $answer;
    }

    /**
     *
     * Get object of an $answer by id
     *
     * @param       id $id of the answer
     * @return      boolean
     *
     */
    public function getAnswerByLabel($label)
    {
        $qb = $this->em->getRepository(Answer::class)->createQueryBuilder('a');
        $qb->where($qb->expr()->like('a.label', ":label"));
        $qb->setParameter('label', $label);
        $query = $qb->getQuery();
        $result = $query->getResult();

        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * Get object of an $answer by id
     *
     * @param       id $id of the answer
     * @return      object
     *
     */
    public function getAnswersByLabel($label)
    {
        $qb = $this->em->getRepository(Answer::class)->createQueryBuilder('a');
        $qb->where($qb->expr()->like('a.label', ":label"));
        $qb->setParameter('label', '%'.$label.'%');
        $query = $qb->getQuery();
        $result = $query->getArrayResult();

        return $result;
    }

    /**
     *
     * Get array of answers
     *
     * @param       index $index (a-z)
     * @return      array
     *
     */
    public function getAnswersByIndex($index)
    {
        $qb = $this->em->getRepository(Answer::class)->createQueryBuilder('a');
        $qb->where($qb->expr()->like('a.label', ":index"));
        $qb->setParameter('index', $index.'%');
        $query = $qb->getQuery();
        $result = $query->getArrayResult();

        return $result;
    }

    /**
     *
     * Get array of answers
     *
     * @return      array
     *
     */
    public function getAnswersByIntegerIndex()
    {
        $totalResult = [];
        foreach(range(0,9) AS $number) {
            $qb = $this->em->getRepository(Answer::class)->createQueryBuilder('a');
            $qb->where($qb->expr()->like('a.label', ":index"));
            $qb->setParameter('index', $number . '%');
            $query = $qb->getQuery();
            $result = $query->getArrayResult();
            $totalResult = array_merge($totalResult, $result);
        }

        return $totalResult;

    }


    /**
     *
     * Get array of answers
     *
     * @return      array
     *
     */
    public function getAnswers()
    {
        $answers = $this->em->getRepository(Answer::class)
            ->findAll();

        return $answers;
    }

    /**
     *
     * Get array of links
     *
     * @return      array
     *
     */
    public function getSearchLinks()
    {
        $alphabethArray = range('A', 'Z');
        array_push($alphabethArray, '0-9');
        return $alphabethArray;


    }

    /**
     *
     * Create a new checklistitem object
     *
     * @return      object
     *
     */
    public function createAnswer()
    {
        return new Answer();
    }

    /**
     *
     * Create form of an object
     *
     * @param       checklistitem $answer object
     * @return      form
     *
     */
    public function createAnswerForm($answer)
    {
        $builder = new AnnotationBuilder($this->em);
        $form = $builder->createForm($answer);
        $form->setHydrator(new DoctrineHydrator($this->em, 'CheckList\Entity\Answer'));
        $form->bind($answer);

        return $form;
    }

    /**
     *
     * Set data to new answer
     *
     * @param       answer $answer object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setNewAnswer($answer, $currentUser)
    {
        $answer->setDateCreated(new \DateTime());
        $answer->setCreatedBy($currentUser);

        $this->storeAnswer($answer);
    }

    /**
     *
     * Save answer to database
     *
     * @param       answer object
     * @return      void
     *
     */
    public function storeAnswer($answer)
    {
        $this->em->persist($answer);
        $this->em->flush();
    }

    /**
     *
     * Remove answer from database
     *
     * @param       answer object
     * @return      void
     *
     */
    public function removeAnswer($answer)
    {
        $this->em->remove($answer);
        $this->em->flush();
    }

}
