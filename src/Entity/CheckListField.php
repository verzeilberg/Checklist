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
 * @ORM\Table(name="checklistfields")
 */
class CheckListField extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="sort_order", type="integer", length=11, nullable=false)
     * @Annotation\Options({
     * "label": "Volgorde",
     * "label_attributes": {"class": ""}
     * })
     * @Annotation\Attributes({"class":"form-control"})
     */
    protected $order = 1;

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
     * @ORM\Column(name="form_field_name", type="string", length=255, nullable=false)
     * @Annotation\Exclude()
     */
    protected $formFieldName;

    /**
     * @ORM\Column(name="required", type="integer", length=1, nullable=false)
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({
     * "label": "Verplicht",
     * "label_attributes": {"class": "form-check-label"}
     * })
     * @Annotation\Attributes({"class":""})
     */
    protected $required = 0;

    /**
     * Many checklistfields have One Checklist.
     * @ORM\ManyToOne(targetEntity="CheckList", inversedBy="checkListFields")
     * @ORM\JoinColumn(name="check_list_id", referencedColumnName="id")
     */
    private $checklist;

    /**
     * Many checklistfields have One ChecklistFieldType.
     * @ORM\ManyToOne(targetEntity="CheckListFieldType", inversedBy="checkListFields")
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

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getChecklist() {
        return $this->checklist;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setChecklist($checklist) {
        $this->checklist = $checklist;
    }

    function getOrder() {
        return $this->order;
    }

    function getChecklistFieldType() {
        return $this->checklistFieldType;
    }

    function setOrder($order) {
        $this->order = $order;
    }

    function setChecklistFieldType($checklistFieldType) {
        $this->checklistFieldType = $checklistFieldType;
    }

    function getRequired() {
        return $this->required;
    }

    function setRequired($required) {
        $this->required = $required;
    }

    function getFormFieldName() {
        return $this->formFieldName;
    }

    function setFormFieldName($formFieldName) {

        $formFieldName = trim($formFieldName);
        $this->formFieldName = $this->clean($formFieldName);
    }

    private function clean($string) {
        $string = strtolower(str_replace(' ', '_', $string)); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

}
