<?php

namespace Craft;

class DuplicateUserDashboardPlugin extends BasePlugin
{
    function getName()
    {
        return Craft::t('duplicateUserDashboard');
    }

    function getVersion()
    {
        return '0.1.3';
    }

    function getDeveloper()
    {
        return 'JB React';
    }

    function getDeveloperUrl()
    {
        return 'http://www.reactor15.com/';
    }
    protected function defineSettings()
    {
        return array(
            'userIdToDuplicate' => ''
        );
    }
    public function getSettingsHtml()
    {
        return craft()->templates->render('duplicateuserdashboard/settings', array(
            'settings' => $this->getSettings()
        ));
    }

    public function getUserWidgetRecords($userID)
    {
        return WidgetRecord::model()->ordered()->findAllByAttributes(array(
            'userId' => $userID,
            'enabled' => 1
        ));
    }

    public function init(){
        
        if(craft()->isConsole())
            return;

        // ToDo - Refactor this
        if(isset(craft()->userSession->getUser()->id)) {

            if(craft()->request->getSegment(1) == 'dashboard'){

                $userIdToDuplicate = $this->getSettings()->userIdToDuplicate;
                $userId = craft()->userSession->getUser()->id;

                //If user is not the master remove the edit dashboard button
                if($userId != $userIdToDuplicate) {

                    craft()->templates->includeCss('a.settings { display:none !important }');

                }

                if($userId == $userIdToDuplicate) {

                    craft()->userSession->setFlash('error','Your dashboard is mirrored to other users, edit with caution.');

                }

            }

        }

        // Pass scope for php 5.3 users
        $controller = $this;


        craft()->on('userSession.onLogin', function(Event $event) use ($controller) {

            if(isset(craft()->userSession->getUser()->id)) {

                $userIdToDuplicate = $controller->getSettings()->userIdToDuplicate;


                if(craft()->userSession->id != $userIdToDuplicate) {

                    //remove what ever the user has (mirror on every login).
                    craft()->db->createCommand()
                        ->select('*')
                        ->delete('widgets', array('userId' => craft()->userSession->getUser()->id));

                    $widgetRecords = $controller->getUserWidgetRecords($userIdToDuplicate);
                    $widgetModels = WidgetModel::populateModels($widgetRecords);

                    foreach($widgetModels as $widgetModel) {
                        // Check widget is valid
                        if( $widgetModel && $widgetType = craft()->dashboard->populateWidgetType($widgetModel) ){
                            $widgetModel->id = NULL;
                            craft()->dashboard->saveUserWidget($widgetModel);
                            craft()->dashboard->changeWidgetColspan($widgetModel->id,$widgetModel->colspan);
                        }
                    }

                }
            }

        });

    }

}
