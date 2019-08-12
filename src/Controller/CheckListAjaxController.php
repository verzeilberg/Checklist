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
    private $checkListAnswerService;
    private $checkListFieldService;
    private $givenAnswerService;

    /**
     * Constructor.
     */
    public function __construct(
        $entityManager,
        $checkListService,
        $checkListItemService,
        $checkListAnswerService,
        $checkListFieldService,
        $givenAnswerService
    )
    {
        $this->entityManager = $entityManager;
        $this->checkListService = $checkListService;
        $this->checkListItemService = $checkListItemService;
        $this->checkListAnswerService = $checkListAnswerService;
        $this->checkListFieldService = $checkListFieldService;
        $this->givenAnswerService = $givenAnswerService;
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
                        $row .= '<td id="' . $checkListItem->getId() . '_' . $index . '">' . $ontentItem . '</td>';
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
        if (empty($checkListItem)) {
            $success = false;
            $errorMessage = 'ChecklistItem not found';
        }

        $checkList = $checkListItem->getChecklist();
        $checkListFields = $checkList->getCheckListFields();

        $answers = $checkListItem->getAnswersGiven();
        $answers = $this->givenAnswerService->getGivenAnswersByChecklisItemtId($checkListItem->getId());

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'itemContent' => $answers,
            'checkListItemId' => $checkListItem->getId()
        ]);

    }

    public function addChecklistItemAction()
    {
        //Set variables
        $success = true;
        $errorMessage = null;
        $answerData = null;
        $checkListId = null;
        $action = null;

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

        $checklistItemId = array_shift($data)[1];

        //Check if data is valid
        $valid = $this->givenAnswerService->validateAnswers($data);
        if($valid) {

            if (empty($checklistItemId)) {
                $checkListItem = $this->checkListItemService->createCheckListItem();
                $checkListItem = $this->checkListItemService->setNewCheckListItem($checkListItem, $checklist, $this->currentUser());
                $answerData = $this->givenAnswerService->saveAnswers($data, $checkListItem, $checklist);
                $action = 'add';
            } else {
                $checkListItem = $this->checkListItemService->getCheckListItemById($checklistItemId);
                $checkListItem = $this->checkListItemService->updateCheckListItem($checkListItem, $checklist, $this->currentUser());
                $this->givenAnswerService->deleteAnswersGiven($checkListItem);
                $answerData = $this->givenAnswerService->saveAnswers($data, $checkListItem, $checklist);
                $action = 'update';
            }

            $checkListId = $checkListItem->getId();

        } else {
            $success = false;
            $errorMessage = 'Form is not valid!';
        }

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'item' => $answerData,
            'checkListItemId' => $checkListId,
            'action' => $action
        ]);
    }

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


    public function addAnswerAction()
    {
        $success = true;
        $errorMessage = '';
        $data = [];
        $label = trim($this->params()->fromPost('label', ''));
        $value = trim($this->params()->fromPost('value', ''));

        if (!empty($label) || !empty($value)) {

            //Check if answer already excists
            if (!is_object($this->checkListAnswerService->getAnswerByLabel($label))) {
                $answer = $this->checkListAnswerService->createAnswer();
                $answer->setLabel($label);
                $answer->setValue($value);
                $this->checkListAnswerService->setNewAnswer($answer, $this->currentUser());
                //Set data in array
                $data['id'] = $answer->getId();
                $data['label'] = $answer->getLabel();
                $data['value'] = $answer->getValue();

            } else {
                $errorMessage = 'Bestaat al een vraag met dat label!';
                $success = false;
            }

        } else {
            $errorMessage = 'Geen label of waarde ingevuld!';
            $success = false;
        }

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'data' => $data
        ]);
    }

    public function checkIfAnswerExcistAction()
    {
        $success = true;
        $answerExcists = false;
        $errorMessage = '';
        $label = trim($this->params()->fromPost('label', ''));
        if (!empty($label)) {

            //Check if answer already excists
            if ($this->checkListAnswerService->getAnswerByLabel($label)) {
                $answerExcists = true;
            }

        } else {
            $errorMessage = 'Geen label of waarde ingevuld!';
            $success = false;
        }

        return new JsonModel([
            'success' => $success,
            'answerExcists' => $answerExcists,
            'errorMessage' => $errorMessage,
        ]);
    }

    public function searchAnswerAction()
    {
        $success = true;
        $errorMessage = '';
        $searchPhrase = trim($this->params()->fromPost('searchPhrase', ''));
        if (!empty($searchPhrase)) {

            //Check if answer already excists
            $result = $this->checkListAnswerService->getAnswersByLabel($searchPhrase);

        } else {
            $errorMessage = 'Geen label of waarde ingevuld!';
            $success = false;
        }

        return new JsonModel([
            'success' => $success,
            'result' => $result,
            'errorMessage' => $errorMessage,
        ]);
    }

    public function searchAnswerOnIndexAction()
    {
        $success = true;
        $errorMessage = '';
        $index = trim($this->params()->fromPost('index', ''));
        if (!empty($index)) {

            if (is_numeric($index[0])) {
                $result = $this->checkListAnswerService->getAnswersByIntegerIndex();
            } else {
                //Check if answer already excists
                $result = $this->checkListAnswerService->getAnswersByIndex($index);
            }
        } else {
            $errorMessage = 'Geen label of waarde ingevuld!';
            $success = false;
        }

        return new JsonModel([
            'success' => $success,
            'result' => $result,
            'errorMessage' => $errorMessage,
        ]);
    }

    public function addAnswerToQuestionAction()
    {
        $success = true;
        $errorMessage = '';
        $answerId = $this->params()->fromPost('answerId', 0);

        $answer = $this->checkListAnswerService->getAnswerById($answerId);
        if (empty($answer)) {
            $success = false;
            $errorMessage = 'Antwoord niet gevonden!';
        } else {
            //Set data in array
            $data['id'] = $answer->getId();
            $data['label'] = $answer->getLabel();
            $data['value'] = $answer->getValue();
        }

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage,
            'data' => $data
        ]);
    }

    public function orderCheckListFieldsAction()
    {
        $success = true;
        $errorMessage = '';
        $checkListFields = $this->params()->fromPost('list', '');

        $this->saveChecklists($checkListFields);

        return new JsonModel([
            'success' => $success,
            'errorMessage' => $errorMessage
        ]);

    }

    private function saveChecklists($checkListFields, $parentCheckListField = null) {
        foreach($checkListFields AS $index => $checkListFieldId) {

            $sortOrder = $index + 1;
            $checkListField = $this->checkListFieldService->getCheckListFieldById($checkListFieldId['id']);
            $checkListField->setOrder($sortOrder);
            if(empty($parentCheckListField)) {
                $checkListField->setParent(null);
            } else {
                $checkListField->setParent($parentCheckListField);
            }

            $this->checkListFieldService->storeCheckListField($checkListField);

            if (count($checkListFieldId["children"]) > 0) {
                $this->saveChecklists($checkListFieldId["children"], $checkListField);
            }



        }
    }

}
