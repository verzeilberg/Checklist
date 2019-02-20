<?php

namespace CheckList\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Result;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class CheckListAjaxController extends AbstractActionController
{

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
    public function __construct($entityManager, $checkListService, $checkListItemService)
    {
        $this->entityManager = $entityManager;
        $this->checkListService = $checkListService;
        $this->checkListItemService = $checkListItemService;
    }

    /**
     * Index action to show checklistst
     *
     * @return Array()
     */
    public function uploadFileAction()
    {
        $error = false;
        $checklistId = $_POST["checklistid"];
        $header = (int)$_POST["header"];

        $checklist = $this->checkListService->getCheckListById($checklistId);

        if (0 < $_FILES['file']['error']) {
            echo 'Error: ' . $_FILES['file']['error'] . '<br>';
        } else {
            $items = [];
            $upload_path = $_SERVER['DOCUMENT_ROOT'] . '/files/userFiles/importFiles/' . $_FILES['file']['name'];

            $upload_result = move_uploaded_file($_FILES['file']['tmp_name'], $upload_path);
            if ($upload_result) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

                /**  Create a new Reader of the type defined in $inputFileType  * */
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(ucfirst($ext));
                /**  Advise the Reader that we only want to load cell data  * */
                $reader->setReadDataOnly(true);
                /**  Load $inputFileName to a Spreadsheet Object  * */
                $spreadsheet = $reader->load($upload_path);

                $worksheet = $spreadsheet->getActiveSheet();

                $alphabeth = range('A', 'Z');

                foreach ($worksheet->getRowIterator() as $rowNumber => $row) {

                    if ($header == 1 && $rowNumber == 1) {
                        continue;
                    }

                    $checkListItem = $this->checkListItemService->createChecklistItem();
                    $checkListItem->setChecklist($checklist);
                    $checkListItem->setDateCreated(new \DateTime());
                    $checkListItem->setCreatedBy($this->currentUser());
                    $item = [];
                    $cellIterator = $row->getCellIterator();
                    foreach ($cellIterator as $index => $cell) {
                        foreach ($checklist->getCheckListFields() AS $indexField => $field) {
                            if ($alphabeth[$indexField] == $index) {
                                $item[$field->getFormFieldName()] = $cell->getValue();
                            }
                        }
                    }

                    $checkListItem->setItemContent($item);
                    $this->checkListItemService->setNewCheckListItem($checkListItem, $checklist, $this->currentUser());


                    $row = '<tr id="item-' . $checkListItem->getId() . '">';
                    $row .= '<td class="text-center"><input class="delete-item" type="checkbox" name="checked-items[]" value="377" /></td>';
                    foreach ($checkListItem->getItemContent() AS $index => $ontentItem) {
                        $row .= '<td id="' . $checkListItem->getId() . '_'.$index.'">' . $ontentItem . '</td>';
                    }
                    $row .= '<td class="text-center">';
                    $row .= '<button data-checklistitemid="' . $checkListItem->getId() . '" class="btn btn-sm btn-secondary editChecklistItemOpen">';
                    $row .= '<i class="fas fa-edit"></i>';
                    $row .= '</button>&nbsp;';
                    $row .= '<button data-checklistitemid="' . $checkListItem->getId() . '" class="btn btn-sm btn-danger removeChecklistItemOpen">';
                    $row .= '<i class="fas fa-trash-alt"></i>';
                    $row .= '</button>';


                    $row .= '</td>';
                    $row .= '</tr>';

                    $items[] = $row;

                }
            } else {
                $error = true;
            }
        }

        return new JsonModel([
            'error' => $error,
            'items' => $items
        ]);
    }


    public function getChecklistItemAction()
    {
        $success = true;
        $errorMessage = '';
        $id = $this->params()->fromPost('id', 0);
        if (empty($id)) {
            $success = false;
            $errorMessage = 'No id given';
        }
        $checkListItem = $this->checkListItemService->getCheckListItemById($id);
        if (empty($checklist)) {
            $success = false;
            $errorMessage = 'ChecklistItem not found';
        }

        $checkList = $checkListItem->getChecklist();
        $checkListFields = $checkList->getCheckListFields();

        $itemContent = $checkListItem->getItemContent();

        $returnArray = [];
        foreach ($checkListFields AS $checkListField) {
            $returnArray[$checkListField->getFormFieldName()]['fieldType'] = $checkListField->getChecklistFieldType()->getFormType();
            $returnArray[$checkListField->getFormFieldName()]['fieldvalue'] = $itemContent[$checkListField->getFormFieldName()];
        }

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'itemContent' => $returnArray,
            'checkListItemId' => $checkListItem->getId()
        ]);

    }

    public function addChecklistItemAction()
    {
        $success = true;
        $errorMessage = '';
        $id = $this->params()->fromPost('id', 0);
        $formData = $this->params()->fromPost('formData', 0);
        $formDataArray = explode('&', $formData);

        if (empty($id)) {
            $success = false;
            $errorMessage = 'No id given';
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            $success = false;
            $errorMessage = 'Checklist not found';
        }

        $data = [];
        foreach ($formDataArray AS $formField) {
            $data[] = explode('=', $formField);

        }

        $item = [];
        foreach ($data AS $value) {

            if ($value[0] == 'id') {
                $id = $value[1];
            }

            $item[$value[0]] = urldecode($value[1]);
        }

        if (empty($id)) {
            $checkListItem = $this->checkListItemService->createCheckListItem();
            $checkListItem->setItemContent($item);
            $checkListItem = $this->checkListItemService->setNewCheckListItem($checkListItem, $checklist, $this->currentUser());
            $action = 'add';
        } else {
            $checkListItem = $this->checkListItemService->getCheckListItemById($id);
            $checkListItem->setItemContent($item);
            $checkListItem = $this->checkListItemService->updateCheckListItem($checkListItem, $checklist, $this->currentUser());
            $action = 'update';
        }

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'item' => $item,
            'checkListItemId' => $checkListItem->getId(),
            'action' => $action
        ]);
    }

    /**
     * Index action to show checklistst
     *
     * @return Array()
     */
    public function deleteItemsAction()
    {
        $error = false;
        $checklistIds = $_POST["itemsToDelete"];
        $checklistIds = explode(',', $checklistIds);

        if (count($checklistIds) == 0) {
            $error = false;
            $error_message = 'Geen items geselecteerd';
        } else {
            $deletedItems = [];
            $notDeletedItems = [];
            foreach ($checklistIds AS $checklistItemId) {
                $result = $this->checkListItemService->deleteCheckListItem($checklistItemId);
                if (!$result) {
                    $notDeletedItems[] = $checklistItemId;
                } else {
                    $deletedItems[] = $checklistItemId;
                }
            }
        }

        return new JsonModel([
            'error' => $error,
            'error_message' => $error_message,
            'deletedItems' => $deletedItems,
            'notDeletedItems' => $notDeletedItems
        ]);
    }

    public function deleteItemAction()
    {
        $success = true;
        $errorMessage = '';
        $checklistItemId = $this->params()->fromPost('id', 0);

        $result = $this->checkListItemService->deleteCheckListItem($checklistItemId);
        if (!$result) {
            $success = false;
            $errorMessage = 'Item niet verwijderd!';
        } else {
            $errorMessage = 'Item verwijderd!';
        }

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'checklistItemId' => $checklistItemId
        ]);
    }

}
