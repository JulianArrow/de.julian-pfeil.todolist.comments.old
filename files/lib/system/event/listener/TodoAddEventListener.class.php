<?php
namespace todolist\system\event\listener;

use todolist\form\TodoAddForm;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\form\builder\field\BooleanFormField;

/**
 * Adds to the form to create a new todo.
 *
 * @author  Julian Pfeil <https://julian-pfeil.de>
 * @copyright   2022 Julian Pfeil Websites & Co.
 * @license Creative Commons <by> <https://creativecommons.org/licenses/by/4.0/legalcode>
 */

class TodoAddEventListener implements IParameterizedEventListener {

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters) {
        $this->$eventName($eventObj);
    }

    /**
     * @inheritDoc
     */
    protected function createForm(TodoAddForm $eventObj)
    {
        $container = $eventObj->form->getNodeById('data');
        $container->appendChildren([
            BooleanFormField::create('enableComments')
                ->label('todolist.comment.enable')
                ->description('todolist.comment.enable.description')
                ->value(true),
        ]);
    }
}
