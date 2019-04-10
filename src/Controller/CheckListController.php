<?php

namespace CheckList\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Uri\Uri;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class CheckListController extends AbstractActionController {

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;
    private $viewhelpermanager;
    private $checkListService;
    private $checkListItemService;
    private $checkListFieldService;
    private $checkListAnswerService;
    private $givenAnswerService;

    /**
     * Constructor.
     */
    public function __construct(
        $entityManager,
        $viewhelpermanager,
        $checkListService,
        $checkListItemService,
        $checkListFieldService,
        $checkListAnswerService,
        $givenAnswerService
    ) {
        $this->entityManager = $entityManager;
        $this->viewhelpermanager = $viewhelpermanager;
        $this->checkListService = $checkListService;
        $this->checkListItemService = $checkListItemService;
        $this->checkListFieldService = $checkListFieldService;
        $this->checkListAnswerService = $checkListAnswerService;
        $this->givenAnswerService = $givenAnswerService;
    }

    /**
     * Index action to show checklistst
     * 
     * @return Array()    
     */
    public function indexAction() {

        $this->layout('layout/beheer');
        $checkLists = $this->checkListService->getChecklists();
        return new ViewModel([
            'checkLists' => $checkLists
        ]);
    }

    /**
     * 
     * Action to show all deleted (archived) checklists
     */
    public function archiveAction() {
        $this->layout('layout/beheer');
        $checklists = $this->checkListService->getArchivedChecklists();

        return new ViewModel([
            'checklists' => $checklists
        ]);
    }

    /**
     * Add action to add a new cheklist     
     */
    public function addAction() {
        $this->layout('layout/beheer');
        $checkList = $this->checkListService->createCheckList();
        $form = $this->checkListService->createCheckListForm($checkList);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                //Save Checklist
                $this->checkListService->setNewCheckList($checkList, $this->currentUser());
                $this->flashMessenger()->addSuccessMessage('CheckList opgeslagen');

                return $this->redirect()->toRoute('beheer/checklist');
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editAction() {
        $this->layout('layout/beheer');

        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $form = $this->checkListService->createCheckListForm($checklist);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                //Save Checklist
                $this->checkListService->setExistingCheckList($checklist, $this->currentUser());
                $this->flashMessenger()->addSuccessMessage('Checklist opgeslagen');
                return $this->redirect()->toRoute('beheer/checklist');
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function showAction() {
        $this->layout('layout/beheer');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/underscore-1.9.1.js');

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
        $givenAnswers = $this->givenAnswerService->getGivenAnswersByChecklistId($checklist->getId());

        return new ViewModel([
            'checklist' => $checklist,
            'checkListFields' => $checkListFields,
            'form' => $form,
            'givenAnswers' => $givenAnswers

        ]);
    }

    public function archiefAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checkList');
        }

        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }

        $this->checkListService->archiveChecklist($checklist, $this->currentUser());
        $this->flashMessenger()->addSuccessMessage('Checklist gearchiveerd');

        $this->redirect()->toRoute('beheer/checklist', array('action' => 'index'));
    }

    public function unArchiefAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        //Unarchive checklist
        $this->checkListService->unArchiveChecklist($checklist, $this->currentUser());
        $this->flashMessenger()->addSuccessMessage('Checklist terug gezet');
        return $this->redirect()->toRoute('beheer/checklist');
    }

    /**
     * 
     * Action to set delete a checklist
     */
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        // Remove checklist
        $this->checkListService->deleteChecklist($checklist);
        $this->flashMessenger()->addSuccessMessage('Checklist verwijderd');
        $this->redirect()->toRoute('beheer/checklist', array('action' => 'archive'));
    }

    public function addFieldAction() {
        $this->layout('layout/beheer');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/bootbox-4.4.0.min.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/jquery-ui.min.js');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/checklist.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/jquery-ui.min.css');

        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }

        $checkListField = $this->checkListFieldService->createChecklistField();
        $form = $this->checkListFieldService->createCheckListFieldForm($checkListField);
        $answer = $this->checkListAnswerService->createAnswer();
        $formAnswer = $this->checkListAnswerService->createAnswerForm($answer);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                //Save Checklist
                $this->checkListFieldService->setNewCheckListField($checkListField, $checklist, $this->currentUser());
                $this->flashMessenger()->addSuccessMessage('CheckListField opgeslagen');

                return $this->redirect()->toRoute('beheer/checklist', array('action' => 'add-field', 'id' => $checklist->getId()));
            }
        }

        $answers = $this->checkListAnswerService->getAnswers();
        $searchLinks = $this->checkListAnswerService->getSearchLinks();

        return new ViewModel([
            'checklist' => $checklist,
            'form' => $form,
            'formAnswer' => $formAnswer,
            'answers' => $answers,
            'searchLinks' => $searchLinks
        ]);
    }

    public function editFieldAction() {
        $this->layout('layout/beheer');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/bootbox-4.4.0.min.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/jquery-ui.min.js');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/checklist.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/jquery-ui.min.css');

        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checkListField = $this->checkListFieldService->getCheckListFieldById($id);
        if (empty($checkListField)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checkList = $checkListField->getChecklist();
        $form = $this->checkListFieldService->createCheckListFieldForm($checkListField);
        $answer = $this->checkListAnswerService->createAnswer();
        $formAnswer = $this->checkListAnswerService->createAnswerForm($answer);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                //Save Checklist
                $this->checkListFieldService->setNewCheckListField($checkListField, $checkList, $this->currentUser());
                $this->flashMessenger()->addSuccessMessage('CheckListField opgeslagen');

                return $this->redirect()->toRoute('beheer/checklist', array('action' => 'add-field', 'id' => $checkList->getId()));
            }
        }

        $answers = $this->checkListAnswerService->getAnswers();
        $searchLinks = $this->checkListAnswerService->getSearchLinks();

        return new ViewModel([
            'checklist' => $checkList,
            'form' => $form,
            'formAnswer' => $formAnswer,
            'answers' => $answers,
            'checkListField' => $checkListField,
            'searchLinks'=> $searchLinks
        ]);
    }

    public function deleteFieldAction() {
        $this->layout('layout/beheer');

        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checkListField = $this->checkListFieldService->getCheckListFieldById($id);
        if (empty($checkListField)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checkList = $checkListField->getChecklist();

        $this->checkListFieldService->removeCheckListField($checkListField);

        return $this->redirect()->toRoute('beheer/checklist', array('action' => 'add-field', 'id' => $checkList->getId()));
    }

}
