<?php

namespace CheckList\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;
use Symfony\Component\VarDumper\VarDumper;
use function is_object;

/**
 * CheckList
 *
 * @ORM\Entity
 * @ORM\Table(name="checklistfields")
 */
class CheckListField extends UnityOfWork
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="sort_order", type="integer", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Volgorde",
     * "label_attributes": {"class": ""}
     * })
     * @Annotation\Attributes({"class":"form-control"})
     */
    protected $order = 1;

    /**
     * @ORM\Column(name="show_in_overview", type="integer", length=1, nullable=false)
     * @Annotation\Type("Laminas\Form\Element\Checkbox")
     * @Annotation\Options({
     * "label": "Toon in het overzicht",
     * "label_attributes": {"class": "form-check-label col-md-3"}
     * })
     * @Annotation\Attributes({"class":""})
     */
    protected $showInOverview = 0;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Naam",
     * "label_attributes": {"class": ""}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Naam"})
     */
    protected $name;

    /**
     * @ORM\Column(name="intro_text", type="text", nullable=true)
     * @Annotation\Options({
     * "label": "Intro tekst",
     * "label_attributes": {"class": ""}
     * })
     * @Annotation\Attributes({"class":"form-control"})
     */
    protected $introText;

    /**
     * @ORM\Column(name="form_field_name", type="string", length=255, nullable=false)
     * @Annotation\Exclude()
     */
    protected $formFieldName;

    /**
     * @ORM\Column(name="required", type="integer", length=1, nullable=true)
     * @Annotation\Type("Laminas\Form\Element\Checkbox")
     * @Annotation\Options({
     * "label": "Verplicht",
     * "label_attributes": {"class": "form-check-label col-md-3"}
     * })
     * @Annotation\Attributes({"class":""})
     */
    protected $required;

    /**
     * @ORM\Column(name="options", type="string", length=2555, nullable=true)
     */
    protected $options;

    /**
     * @ORM\Column(name="required_message", type="string", length=255, nullable=true)
     *  @Annotation\Options({
     * "label": "Validatie tekst",
     * "label_attributes": {"class": ""}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Validatie tekst"})
     */
    protected $requiredMessage;

    /**
     * Many checklistfields have One Checklist.
     * @ORM\ManyToOne(targetEntity="CheckList", inversedBy="checkListFields")
     * @ORM\JoinColumn(name="check_list_id", referencedColumnName="id")
     */
    private $checklist;

    /**
     * Many checklistfields have One ChecklistFieldType.
     * @ORM\ManyToOne(targetEntity="CheckListFieldType", inversedBy="checkListFields", cascade={"persist"}))
     * @ORM\JoinColumn(name="check_list_field_type_id", referencedColumnName="id")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "empty_option": "---",
     * "target_class":"CheckList\Entity\CheckListFieldType",
     * "property": "name",
     * "label": "Veld type",
     * "label_attributes": {"class": ""},
     * })
     * @Annotation\Attributes({"class": "form-control"})
     */
    private $checklistFieldType;

    /**
     * One checkListField has many answerGiven. This is the inverse side.
     * @ORM\OneToMany(targetEntity="AnswerGiven", mappedBy="checklistField", cascade={"persist", "remove"})
     */
    private $answersGiven;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Answer", cascade={"persist"})
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectMultiCheckbox")
     * @ORM\JoinTable(name="checklistfield_answer",
     *      joinColumns={@ORM\JoinColumn(name="checklistfield_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="answer_id", referencedColumnName="id")}
     *      )
     */
    private $answers;


    /**
     * One CheckListField has Many CheckListFields.
     * @ORM\OneToMany(targetEntity="CheckListField", mappedBy="parent", cascade={"persist"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $children;

    /**
     * Many CheckListFields have One CheckListField.
     * @ORM\ManyToOne(targetEntity="CheckListField", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;



    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->answersGiven = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function addAnswers($answers)
    {
        foreach ($answers as $answer) {
            $this->answers->add($answer);
        }
    }

    public function removeAnswers($answers)
    {
        foreach ($answers as $answer) {
            $this->answers->removeElement($answer);
        }
    }

    function getId()
    {
        return $this->id;
    }

    function getName()
    {
        return $this->name;
    }

    function getChecklist()
    {
        return $this->checklist;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setChecklist($checklist)
    {
        $this->checklist = $checklist;
    }

    function getOrder()
    {
        return $this->order;
    }

    function getChecklistFieldType()
    {
        return $this->checklistFieldType;
    }

    function setOrder($order)
    {
        $this->order = $order;
    }

    function setChecklistFieldType($checklistFieldType)
    {
        $this->checklistFieldType = $checklistFieldType;
    }

    function getRequired()
    {
        return $this->required;
    }

    function setRequired($required)
    {
        $this->required = $required;
    }

    function getFormFieldName()
    {
        return $this->formFieldName;
    }

    function setFormFieldName($formFieldName)
    {

        $formFieldName = trim($formFieldName);
        $this->formFieldName = $this->clean($formFieldName);
    }

    private function clean($string)
    {
        $string = strtolower(str_replace(' ', '_', $string)); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param mixed $answers
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }


    public function checkIfAnswerIsLinked($id)
    {

        $result = false;
        foreach ($this->answers as $answer) {
            if ($answer->getId() == $id) {
                $result = true;
                break;
            }
        }

        return $result;
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

    /**
     * @return mixed
     */
    public function getRequiredMessage()
    {
        return $this->requiredMessage;
    }

    /**
     * @param mixed $requiredMessage
     */
    public function setRequiredMessage($requiredMessage)
    {
        $this->requiredMessage = $requiredMessage;
    }

    /**
     * @return mixed
     */
    public function getIntroText()
    {
        return $this->introText;
    }

    /**
     * @param mixed $introText
     */
    public function setIntroText($introText)
    {
        $this->introText = $introText;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        if (is_object($parent) && $parent->getName() !== null) {
            $this->parent = $parent;
        }
    }

    /**
     * @return mixed
     */
    public function getShowInOverview()
    {
        return $this->showInOverview;
    }

    /**
     * @param mixed $showInOverview
     */
    public function setShowInOverview($showInOverview)
    {
        $this->showInOverview = $showInOverview;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function getOptionsAsArray()
    {
        return json_decode(stripslashes($this->options??''), 1);
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }



}
