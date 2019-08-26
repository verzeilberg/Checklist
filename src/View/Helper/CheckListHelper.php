<?php

namespace CheckList\View\Helper;

use Zend\View\Helper\AbstractHelper;

// This view helper class translate text
class CheckListHelper extends AbstractHelper
{
    public function generateCheckListFields($checklistFields, $urlHelper, $childeren = 0)
    {
        if ($childeren == 0) {
            echo '<ol class="sortable">';
        } else {
            echo '<ol>';
        }
        foreach ($checklistFields AS $field) {
            echo '<li id="menuItem_' . $field->getId() . '">';
            echo '<div class="row bg-dark">';
            echo '<span class="col">' . $field->getName() . '</span>';
            echo '<span class="col-md-auto text-center">';
            echo '<a class="btn btn-sm btn-secondary text-white" ';
            echo 'href="' . $urlHelper->url('beheer/checklist', ['action' => 'edit-field', 'id' => $field->getId()]) . '">';
            echo '<i class="fas fa-edit"></i>';
            echo '</a>';
            echo '&nbsp;';
            echo '<a class="btn btn-sm btn-danger text-white" href="';
            echo $urlHelper->url('beheer/checklist', ['action' => 'delete-field', 'id' => $field->getId()]) . '">';
            echo '<i class="fas fa-trash-alt"></i>';
            echo '</a>';
            echo '</div>';

            if (count($field->getChildren() > 0)) {
                $this->generateCheckListFields($field->getChildren(), $urlHelper, 1);
            }
            echo '</li>';
        }
        echo '</ol>';


    }

    public function showCheckListFields($checkListFields, $urlHelper)
    {
        foreach ($checkListFields AS $checkListField) {
            echo '<div class="row">';
            echo '<div class="col">';
            echo '<div class="form-group">';
            if ($checkListField->getChecklistFieldType()->getFormType() != 'fieldset') {
                echo '<label>' . $checkListField->getName() . ($checkListField->getRequired() == 1 ? ' *' : '') . '</label>';
            }
            $formTypeURL = 'check-list/check-list-item/partials/input-' . $checkListField->getChecklistFieldType()->getFormType() . '.phtml';
            echo $urlHelper->partial($formTypeURL, array('checkListField' => $checkListField, 'urlHelper' => $urlHelper));
            echo '</div>';
            echo '</div>';
            echo '</div>';

            if (count($checkListField->getChildren()) > 0 && $checkListField->getChecklistFieldType()->getFormType() != 'fieldset') {
                $this->showCheckListFields($checkListField->getChildren(), $urlHelper);
            }

        }
    }


    public function createFormRadioInputs($formInput)
    {
        $values = $formInput->getOptions()['value_options'];
        echo '<div class="form-group">';
        echo '<label>'.$formInput->getLabel().'</label>';
        foreach ($values as $index => $value) {
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="radio" name="'.$formInput->getName().'" value="'.$index.'" '.($formInput->getValue() == $index? 'checked':'').'>';
            echo '    <label class="form-check-label" for="exampleRadios1">';
            echo        $value;
            echo '    </label>';
            echo '</div>';
        }
        echo '</div>';
    }


}