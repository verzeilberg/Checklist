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
                    $row .= '<td><input class="delete-item" type="checkbox" name="checked-items[]" value="377" /></td>';
                    foreach ($checkListItem->getItemContent() AS $ontentItem) {
                        $row .= '<td>' . $ontentItem . '</td>';
                    }
                    $row .= '<td class="text-center">';
                    $row .= '<a class="btn btn-sm btn-primary" href="/beheer/checklistitem/edit/' . $checkListItem->getId() . '">';
                    $row .= '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit';
                    $row .= '</a>';
                    $row .= '&nbsp;';
                    $row .= '<a class="btn btn-sm btn-primary" href="/beheer/checklistitem/delete/' . $checkListItem->getId() . '">';
                    $row .= '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Verwijder';
                    $row .= '</a>';
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
        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'itemContent' => $checkListItem->getItemContent(),
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

        $checkListItem = $this->checkListItemService->createCheckListItem();
        $data = [];
        foreach ($formDataArray AS $formField) {
            $data[] = explode('=', $formField);

        }

        $item = [];
        foreach ($data AS $value) {
            $item[$value[0]] = urldecode($value[1]);
        }

        $checkListItem->setItemContent($item);
        $checkListItem = $this->checkListItemService->setNewCheckListItem($checkListItem, $checklist, $this->currentUser());

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'item' => $item,
            'checkListItemId' => $checkListItem->getId()
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

}
