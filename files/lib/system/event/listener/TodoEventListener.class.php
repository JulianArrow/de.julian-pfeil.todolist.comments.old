<?php
namespace todolist\system\event\listener;

use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;
use todolist\page\TodoPage;
use wcf\data\comment\StructuredCommentList;
use todolist\data\todo\Todo;
use wcf\system\comment\CommentHandler;

class TodoEventListener implements IParameterizedEventListener {
    /**
     * list of comments
     * @var StructuredCommentList
     */
    protected $commentList;

    /**
     * todo comment manager object
     * @var TodoCommentManager
     */
    protected $commentManager;

    /**
     * id of the todo comment object type
     * @var int
     */
    protected $commentObjectTypeID = 0;

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters) {
        $this->$eventName($eventObj);
    }

   /**
     * @inheritDoc
     */
    protected function assignVariables(TodoPage $eventObj)
    {
        WCF::getTPL()->assign([
            'commentCanAdd' => WCF::getSession()->getPermission('user.todolist.canAddComments'),
            'commentList' => $this->commentList,
            'commentObjectTypeID' => $this->commentObjectTypeID,
            'lastCommentTime' => $this->commentList ? $this->commentList->getMinCommentTime() : 0,
            'likeData' => MODULE_LIKE && $this->commentList ? $this->commentList->getLikeData() : [],
            'todo' => $eventObj->todo,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function readData(TodoPage $eventObj)
    {
        if ($eventObj->todo->enableComments) {
            $this->commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID(
                'de.julian-pfeil.todolist.comments.todoComment'
            );
            $this->commentManager = CommentHandler::getInstance()->getObjectType(
                $this->commentObjectTypeID
            )->getProcessor();
            $this->commentList = CommentHandler::getInstance()->getCommentList(
                $this->commentManager,
                $this->commentObjectTypeID,
                $eventObj->todo->todoID
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function readParameters(TodoPage $eventObj)
    {
        if (isset($_REQUEST['id'])) {
            $eventObj->todoID = \intval($_REQUEST['id']);
        }
        $eventObj->todo = new Todo($eventObj->todoID);
        if (!$eventObj->todo->todoID) {
            throw new IllegalLinkException();
        }
    }
}
