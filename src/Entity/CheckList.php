<?php

namespace CheckList\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;
use CheckList\Entity\CheckListField;

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
     * "label_attributes": {"class": "col-sm-2 col-md-2 col-lg-2 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Naam"})
     */
    protected $name;

    /**
     * @ORM\Column(name="public", type="integer", length=1, nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({
     * "label": "Type lijst",
     * "empty_option": "---",
     * "value_options":{
     * "1":"Publiek",
     * "2":"Persoonlijk",
     * },
     * "label_attributes": {"class": "col-sm-2 col-md-2 col-lg-2 control-label"}
     * })
     * @Annotation\Attributes({"class":""})
     */
    protected $public;

    /**
     * One Checklist has Many CheckList items.
     * @ORM\OneToMany(targetEntity="CheckListItem", mappedBy="checklist", cascade={"remove"}))
     */
    private $checkListItems;

    /**
     * One Checklist has Many CheckList items.
     * @ORM\OneToMany(targetEntity="CheckListField", mappedBy="checklist", cascade={"remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $checkListFields;

    public function __construct() {
        $this->checkListItems = new ArrayCollection();
        $this->checkListFields = new ArrayCollection();
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
        return $this->checkListFields;
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

}