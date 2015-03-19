<?php

namespace Craft;

class DuplicateUserDashboardVariable
{
    /**
     * Pass through users to plugin settings
     *
     * @return array
     */
    public function getUsersIdAndName()
    {
        $users = craft()->elements->getCriteria(ElementType::User);
        $usersAndIdArr = [];
        foreach($users as $user){
            $usersAndIdArr[$user->id] = $user->name;
        }

        return $usersAndIdArr;
    }
}