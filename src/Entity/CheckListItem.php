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
     * @ORM\Column(name="item_content", type="text", nullable=false)
     */
    protected $itemContent;

    /**
     * Many checklistitems have One Checklist.
     * @ORM\ManyToOne(targetEntity="CheckList", inversedBy="checkListItems")
     * @ORM\JoinColumn(name="check_list_id", referencedColumnName="id")
     */
    private $checklist;

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

}
