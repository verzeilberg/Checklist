<?php

namespace CheckList\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;
use CheckList\Entity\CheckListField;

/**
 * CheckList
 *
 * @ORM\Entity
 * @ORM\Table(name="answer_given")
 */
class AnswerGiven extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Many AnswerGiven have one checklist. This is the owning side.
     * @ORM\ManyToOne(targetEntity="CheckList\Entity\CheckList", inversedBy="answersGiven")
     * @ORM\JoinColumn(name="checklist_id", referencedColumnName="id")
     */
    private $checklist;

    /**
     * Many AnswerGiven have one checkListItem. This is the owning side.
     * @ORM\ManyToOne(targetEntity="CheckListItem", inversedBy="answersGiven")
     * @ORM\JoinColumn(name="checklist_item_id", referencedColumnName="id")
     */
    private $checklistItem;

    /**
     * Many AnswerGiven have one checkListField. This is the owning side.
     * @ORM\ManyToOne(targetEntity="CheckListField", inversedBy="answersGiven")
     * @ORM\JoinColumn(name="checklist_field_id", referencedColumnName="id")
     */
    private $checklistField;

    /**
     * Many AnswerGiven have one answer. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Answer", inversedBy="answersGiven")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id")
     */
    private $answerValue;

    /**
     * @ORM\Column(name="answer", type="text", nullable=true)
     */
    protected $answer;

    public function __construct()
    {
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getChecklist()
    {
        return $this->checklist;
    }

    /**
     * @param mixed $checklist
     */
    public function setChecklist($checklist)
    {
        $this->checklist = $checklist;
    }

    /**
     * @return mixed
     */
    public function getChecklistItem()
    {
        return $this->checklistItem;
    }

    /**
     * @param mixed $checklistItem
     */
    public function setChecklistItem($checklistItem)
    {
        $this->checklistItem = $checklistItem;
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getChecklistField()
    {
        return $this->checklistField;
    }

    /**
     * @param mixed $checklistField
     */
    public function setChecklistField($checklistField)
    {
        $this->checklistField = $checklistField;
    }

    /**
     * @return mixed
     */
    public function getAnswerValue()
    {
        return $this->answerValue;
    }

    /**
     * @param mixed $answerValue
     */
    public function setAnswerValue($answerValue)
    {
        $this->answerValue = $answerValue;
    }
}
