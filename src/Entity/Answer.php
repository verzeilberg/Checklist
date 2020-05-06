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
 * @ORM\Table(name="answer")
 */
class Answer extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Label",
     * "label_attributes": {"class": ""}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Label"})
     */
    protected $label;

    /**
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Value",
     * "label_attributes": {"class": ""}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Value"})
     */
    protected $value;

    /**
     * One answer has many answerGiven. This is the inverse side.
     * @ORM\OneToMany(targetEntity="AnswerGiven", mappedBy="answerValue")
     */
    private $answersGiven;

    public function __construct() {
        $this->answersGiven = new ArrayCollection();
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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getAnswersGiven()
    {
        return $this->answersGiven;
    }

    /**
     * @param mixed $answersGiven
     */
    public function setAnswersGiven($answersGiven)
    {
        $this->answersGiven = $answersGiven;
    }



}
