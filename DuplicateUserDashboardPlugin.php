<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jbreact
 * Date: 10/03/15
 * Time: 11:10
 * To change this template use File | Settings | File Templates.
 */

namespace Craft;

class DuplicateUserDashboardPlugin extends BasePlugin
{
    function getName()
    {
        return Craft::t('duplicateUserDashboard');
    }

    function getVersion()
    {
        return '0.1';
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
            //'accountApiKey' => ''
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
            //'userId' => craft()->userSession->getUser()->id
        ));
    }

    public function init(){

        // ToDo - Refactor this
        if(isset(craft()->userSession->getUser()->id)) {

            if(craft()->request->getSegment(1) == 'dashboard'){

                $userIdToDuplicate = $this->getSettings()->userIdToDuplicate;
                $userId = craft()->userSession->getUser()->id;

                    if($userId != $userIdToDuplicate) {

                        craft()->templates->includeCss('a.settings { display:none !important }');

                    }

                    if($userId == $userIdToDuplicate) {

                        craft()->userSession->setFlash('error','Your dashboard is mirrored to other users, edit with caution.');

                    }

            }
        }


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
                              $widgetModel->id = NULL;
                              craft()->dashboard->saveUserWidget($widgetModel);
                          }

                }
            }

        });



    }
}