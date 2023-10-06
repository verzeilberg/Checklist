<?php

namespace CheckList\Controller;

use Blog\Entity\Blog;
use Blog\Form\CreateBlogForm;
use CheckList\Entity\CheckList;
use CheckList\Form\CreateChecklistAnswerForm;
use CheckList\Form\CreateChecklistFieldForm;
use CheckList\Form\CreateChecklistForm;
use CheckList\Form\CreateChecklistItemForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\Result;
use Laminas\Uri\Uri;
use Symfony\Component\VarDumper\VarDumper;

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
        $page = $this->params()->fromQuery('page', 1);
        $query = $this->checkListService->getChecklists();

        $checkLists = $this->checkListService->getItemsForPagination($query, $page, 10);

        return new ViewModel([
            'checkLists' => $checkLists
        ]);
    }

    /**
     * Action to show all deleted (archived) checklists
     */
    public function archiveAction() {
        $this->layout('layout/beheer');
        $page = $this->params()->fromQuery('page', 1);
        $query = $this->checkListService->getChecklists();
        $checkLists = $this->checkListService->getItemsForPagination($query, $page, 10);

        return new ViewModel([
            'checklists' => $checkLists
        ]);
    }

    /**
     * Add action to add a new cheklist     
     */
    public function addAction() {
        $this->layout('layout/beheer');
        $checkList = $this->checkListService->createCheckList();
        // Create the form and inject the EntityManager
        $form = new CreateChecklistForm($this->entityManager);
        // Create a new, empty entity and bind it to the form
        $form->bind($checkList);

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
        // Create the form and inject the EntityManager
        $form = new CreateChecklistForm($this->entityManager);
        // Create a new, empty entity and bind it to the form
        $form->bind($checklist);

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
        $this->viewhelpermanager->get('headScript')->appendFile('/js/show-checklist.js');

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
        // Create the form and inject the EntityManager
        $form = new CreateChecklistItemForm($this->entityManager);
        // Create a new, empty entity and bind it to the form
        $form->bind($checkListItem);

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

        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/checklist.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');

        $this->viewhelpermanager->get('headScript')->appendFile('https://code.jquery.com/ui/1.10.4/jquery-ui.min.js');

        $this->viewhelpermanager->get('headScript')->appendFile('/js/jquery.ui.nestedSortable.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/bootbox-4.4.0.min.js');

        $this->viewhelpermanager->get('headScript')->appendFile('/js/options.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/add-answer.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/answers-library.js');


        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checklist = $this->checkListService->getCheckListById($id);
        if (empty($checklist)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }

        $checkListField = $this->checkListFieldService->createChecklistField();
        $form = new CreateChecklistFieldForm($this->entityManager);
        $form->bind($checkListField);

        $answer = $this->checkListAnswerService->createAnswer();
        $formAnswer = new CreateChecklistAnswerForm($this->entityManager);
        $formAnswer->bind($answer);

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
        $checkListFields = $this->checkListFieldService->getCheckListFieldsByCheckListId($checklist);

        return new ViewModel([
            'checklist' => $checklist,
            'form' => $form,
            'formAnswer' => $formAnswer,
            'answers' => $answers,
            'searchLinks' => $searchLinks,
            'checkListFields'=> $checkListFields
        ]);
    }

    public function editFieldAction() {
        $this->layout('layout/beheer');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/checklist.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');

        $this->viewhelpermanager->get('headScript')->appendFile('https://code.jquery.com/ui/1.10.4/jquery-ui.min.js');

        $this->viewhelpermanager->get('headScript')->appendFile('/js/jquery.ui.nestedSortable.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/bootbox-4.4.0.min.js');

        $this->viewhelpermanager->get('headScript')->appendFile('/js/options.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/add-answer.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/answers-library.js');

        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }
        $checkListField = $this->checkListFieldService->getCheckListFieldById($id);
        if (empty($checkListField)) {
            return $this->redirect()->toRoute('beheer/checklist');
        }

        $checkList = $checkListField->getChecklist();

        $form = new CreateChecklistFieldForm($this->entityManager);
        $form->bind($checkListField);

        $answer = $this->checkListAnswerService->createAnswer();
        $formAnswer = new CreateChecklistAnswerForm($this->entityManager);
        $formAnswer->bind($answer);

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
        $checkListFields = $this->checkListFieldService->getCheckListFieldsByCheckListId($checkList, $id);

        return new ViewModel([
            'checklist' => $checkList,
            'form' => $form,
            'formAnswer' => $formAnswer,
            'answers' => $answers,
            'checkListField' => $checkListField,
            'searchLinks'=> $searchLinks,
            'checkListFields'=> $checkListFields
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
