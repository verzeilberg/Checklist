<?php

namespace CheckList\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Uri\Uri;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class CheckListItemController extends AbstractActionController {

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;
    private $checkListService;
    private $checkListItemService;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $checkListService, $checkListItemService) {
        $this->entityManager = $entityManager;
        $this->checkListService = $checkListService;
        $this->checkListItemService = $checkListItemService;
    }

    /**
     * Index action to show checklistst
     * 
     * @return Array()    
     */
    public function indexAction() {
        $this->layout('layout/beheer');
        $checkListsItems = $this->checkListItemService->getChecklistItems();
        return new ViewModel([
            'checkListsItems' => $checkListsItems
        ]);
    }

    /**
     * Add action to add a new cheklistitem     
     */
    public function addAction() {
        $this->layout('layout/beheer');
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checkListFields = $checklist->getCheckListFields();

        $checkListItem = $this->checkListItemService->createCheckListItem();
        $form = $this->checkListItemService->createCheckListItemForm($checkListItem);

        if ($this->getRequest()->isPost()) {
            $item = [];
            foreach ($this->getRequest()->getPost() AS $index => $value) {
                $item[$index] = $value;
            }

            $checkListItem->setItemContent($item);
            $this->checkListItemService->setNewCheckListItem($checkListItem, $checklist, $this->currentUser());
            return $this->redirect()->toRoute('beheer/checklist', array('action' => 'show', 'id' => $checklist->getId()));
        }


        return new ViewModel([
            'checkListFields' => $checkListFields,
            'checklist' => $checklist,
            'form' => $form,
        ]);
    }

    public function editAction() {
        $this->layout('layout/beheer');

        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklistitem = $this->checkListItemService->getCheckListItemById($id);
        if (empty($checklistitem)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }

        $form = $this->checkListItemService->createCheckListItemForm($checklistitem);
        $checklist = $checklistitem->getChecklist();
        $checkListFields = $checklist->getCheckListFields();

        if ($this->getRequest()->isPost()) {
            $item = [];
            foreach ($this->getRequest()->getPost() AS $index => $value) {
                $item[$index] = $value;
            }

            $checklistitem->setItemContent($item);
            $this->checkListItemService->setNewCheckListItem($checklistitem, $checklist, $this->currentUser());
            return $this->redirect()->toRoute('beheer/checklist', array('action' => 'show', 'id' => $checklist->getId()));
        }


        return new ViewModel([
            'checkListFields' => $checkListFields,
            'checklistitem' => $checklistitem,
            'form' => $form,
        ]);
    }

    public function showAction() {
        $this->layout('layout/beheer');

        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checkListItem = $this->checkListItemService->createCheckListItem();
        $form = $this->checkListItemService->createCheckListItemForm($checkListItem);

        return new ViewModel([
            'checklist' => $checklist,
            'form' => $form
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checkList');
        }

        $checklistitem = $this->checkListItemService->getCheckListItemById($id);
        $checklist = $checklistitem->getChecklist();
        if ($this->checkListItemService->deleteCheckListItem($id)) {
            $this->flashMessenger()->addSuccessMessage('Item verwijderd');
        } else {
            $this->flashMessenger()->addErrorMessage('Item niet verwijderd');
        }
        return $this->redirect()->toRoute('beheer/checklist', array('action' => 'show', 'id' => $checklist->getId()));
    }

    /**
     * Index action to show checklistst
     *
     * @return Array()
     */
    public function exportListAction() {
        $this->layout('layout/beheer');

        $id = (int) $this->params()->fromPost('id', 0);
        $filters = (int) $this->params()->fromPost('filters', 0);
        $freezeRow = (int) $this->params()->fromPost('freezeRow', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }

        $checkListFields = $checklist->getCheckListFields();

        if (count($checkListFields) > 0) {

            /** Create a new Spreadsheet Object * */
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            $worksheet = $spreadsheet->getActiveSheet();

            $alphabeth = range('A', 'Z');
            foreach ($checkListFields AS $index => $checkListField) {
                $worksheet->getCell($alphabeth[$index] . '1')->setValue($checkListField->getFormFieldName());
                $worksheet->getStyle( $alphabeth[$index] . '1')->getFont()->setBold( true );
            }

            //If filters is set than apply filters
            if($filters == 1) {
                $worksheet->setAutoFilter('A1:' . $alphabeth[$index] . '1');
            }

            //If freeze row is set than apply freeze pane for first row
            if($freezeRow == 1) {
                $worksheet->freezePane('A2');
            }

            $rowStart = 2;
            foreach ($checklist->getCheckListItems() AS $index => $item) {
                foreach ($checklist->getCheckListFields() as $fieldIndex => $fields) {

                    $worksheet->getCell($alphabeth[$fieldIndex] . $rowStart)->setValue($item->getItemContent()[$fields->getFormFieldName()]);


                }
                $rowStart++;
            }


            // redirect output to client browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$checklist->getName().'.xls"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        } else {
            $this->flashMessenger()->addSuccessMessage('Geen velden om .xls bestand te maken');
            return $this->redirect()->toRoute('beheer/checklist');
        }
    }

}
