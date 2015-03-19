<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jbreact
 * Date: 19/03/15
 * Time: 14:47
 * To change this template use File | Settings | File Templates.
 */

namespace Craft;

class DuplicateUserDashboardVariable
{
    /**
     * Pass through optgroup of all local sources
     *
     * @return array
     */
    public function getUsersIdAndName()
    {
        //return craft()->flurryGraph->getSourcesOptGroup();
        $users = craft()->elements->getCriteria(ElementType::User);

       // var_dump($users); exit;
        $usersAndIdArr = [];
        foreach($users as $user){
            $usersAndIdArr[$user->id] = $user->name;
        }

        return $usersAndIdArr;
    }
}