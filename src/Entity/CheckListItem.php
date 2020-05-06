<?php

namespace CheckList\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * CheckList
 *
 * @ORM\Entity
 * @ORM\Table(name="checklistitems")
 */
class CheckListItem extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="item_content", type="text", nullable=true)
     */
    protected $itemContent;

    /**
     * Many checklistitems have One Checklist.
     * @ORM\ManyToOne(targetEntity="CheckList", inversedBy="checkListItems")
     * @ORM\JoinColumn(name="check_list_id", referencedColumnName="id")
     */
    private $checklist;

    /**
     * One checklistItem has many answerGiven. This is the inverse side.
     * @ORM\OneToMany(targetEntity="AnswerGiven", mappedBy="checklistItem", cascade={"persist", "remove"})
     */
    private $answersGiven;


    public function __construct() {
        $this->answersGiven = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getItemContent() {
        return json_decode($this->itemContent, 1);
    }

    function setItemContent($itemContent) {
        $this->itemContent = json_encode($itemContent, 1);
    }

    function getChecklist() {
        return $this->checklist;
    }

    function setChecklist($checklist) {
        $this->checklist = $checklist;
    }

    /**
     * @return mixed
     */
    public function getAnswersGiven()
    {

        $answersGiven = [];
        foreach ($this->answersGiven AS $answerGiven) {

            $formType = $answerGiven->getChecklistField()->getChecklistFieldType()->getFormType();
            if($formType == 'text') {
                $answersGiven[$answerGiven->getChecklistItem()->getId()][$answerGiven->getChecklistField()->getId()][] = $answerGiven->getAnswer();
            } else if($formType == 'checkbox') {
                $answersGiven[$answerGiven->getChecklistItem()->getId()][$answerGiven->getChecklistField()->getId()][] = $answerGiven->getAnswerValue()->getId();
            }
        }

        return $answersGiven;
    }

    /**
     * @param mixed $answersGiven
     */
    public function setAnswersGiven($answersGiven)
    {
        $this->answersGiven = $answersGiven;
    }


    /**
     * @return mixed
     */
    function getAnswersGivenForDeleting() {
        return $this->answersGiven;
    }


}
