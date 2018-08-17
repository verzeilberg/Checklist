<?php

namespace CheckList\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * CheckList
 *
 * @ORM\Entity
 * @ORM\Table(name="checklistfieldtypes")
 */
class CheckListFieldType extends UnityOfWork {

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
     * @ORM\Column(name="form_type", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Form type",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"kolommen"})
     */
    protected $formType;

    /**
     * One ChecklistFieldType has Many CheckListFields.
     * @ORM\OneToMany(targetEntity="CheckListField", mappedBy="checklistFieldType")
     * @ORM\OrderBy({"number" = "ASC"})
     */
    private $checkListFields;

    public function __construct() {
        $this->checkListFields = new ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getFormType() {
        return $this->formType;
    }

    function getCheckListFields() {
        return $this->checkListFields;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setFormType($formType) {
        $this->formType = $formType;
    }

    function setCheckListFields($checkListFields) {
        $this->checkListFields = $checkListFields;
    }

    public function addCheckListFields($checklistsFields) {
        foreach ($checklistsFields as $checklistField) {
            $this->checkListFields->add($checklistField);
        }
    }

    public function removeCheckListFields($checklists) {
        foreach ($checklistsFields as $checklistField) {
            $this->checkListFields->removeElement($checklistField);
        }
    }

}
