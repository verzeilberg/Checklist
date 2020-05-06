<?php

namespace CheckList\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;
use CheckList\Entity\CheckListField;
use Doctrine\Common\Collections\Criteria;

/**
 * CheckList
 *
 * @ORM\Entity
 * @ORM\Table(name="checklists")
 */
class CheckList extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @ORM\Column(name="public", type="integer", length=1, nullable=false)
     * @Annotation\Type("Laminas\Form\Element\Radio")
     * @Annotation\Options({
     * "label": "Type lijst",
     * "value_options":{
     * "1":"Publiek",
     * "2":"Persoonlijk",
     * },
     * "label_attributes": {"class": "form-check-label"}
     * })
     * @Annotation\Attributes({"class":"form-check-input"})
     */
    protected $public;

    /**
     * @ORM\Column(name="autocomplete", type="integer", length=1, nullable=false)
     * @Annotation\Type("Laminas\Form\Element\Radio")
     * @Annotation\Options({
     * "label": "Autocomplete",
     * "empty_option": "---",
     * "value_options":{
     * "1":"Nee",
     * "2":"Ja",
     * },
     * "label_attributes": {"class": "form-check-label"}
     * })
     * @Annotation\Attributes({"class":"form-check-input"})
     */
    protected $autocomplete = 1;

    /**
     * One Checklist has Many CheckList items.
     * @ORM\OneToMany(targetEntity="CheckListItem", mappedBy="checklist", cascade={"remove"}))
     */
    private $checkListItems;

    /**
     * One Checklist has Many CheckList items.
     * @ORM\OneToMany(targetEntity="CheckListField", mappedBy="checklist", cascade={"remove"})
     */
    private $checkListFields;

    /**
     * One checklist has many answerGiven. This is the inverse side.
     * @ORM\OneToMany(targetEntity="AnswerGiven", mappedBy="checklist")
     */
    private $answersGiven;

    public function __construct() {
        $this->checkListItems = new ArrayCollection();
        $this->checkListFields = new ArrayCollection();
        $this->answersGiven = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function getColumns() {
        return $this->columns;
    }

    function setColumns($columns) {
        $this->columns = $columns;
    }

    function getCheckListItems() {
        return $this->checkListItems;
    }

    function setCheckListItems($checkListItems) {
        $this->checkListItems = $checkListItems;
    }

    public function addCheckListItems($checklists) {
        foreach ($checklists as $checklist) {
            $this->checkListItems->add($checklist);
        }
    }

    public function removeCheckListItems($checklists) {
        foreach ($checklists as $checklist) {
            $this->checkListItems->removeElement($checklist);
        }
    }

    function getCheckListFields() {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("parent", null))
            ->orderBy(["order" => Criteria::ASC]);

        $checkListFields = $this->checkListFields->matching($criteria);
        return $checkListFields;
    }

    function getCheckListFieldsForOverview() {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("showInOverview", 1))
            ->orderBy(["order" => Criteria::ASC]);

        $checkListFields = $this->checkListFields->matching($criteria);
        return $checkListFields;
    }

    function setCheckListFields($checkListFields) {
        $this->checkListFields = $checkListFields;
    }

    public function addCheckListFields($checklists) {
        foreach ($checklists as $checklist) {
            $this->checkListFields->add($checklist);
        }
    }

    public function removeCheckListFields($checklists) {
        foreach ($checklists as $checklist) {
            $this->checkListFields->removeElement($checklist);
        }
    }
    
    function getPublic() {
        return $this->public;
    }

    function setPublic($public) {
        $this->public = $public;
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
    public function getAutocomplete()
    {
        return $this->autocomplete;
    }

    /**
     * @param mixed $autocomplete
     */
    public function setAutocomplete($autocomplete)
    {
        $this->autocomplete = $autocomplete;
    }


}
