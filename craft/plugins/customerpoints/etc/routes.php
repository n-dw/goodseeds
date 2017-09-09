<?php

return [

    // Settings
    'customerpoints/settings' => ['action' => 'customerPoints/settings/index'],

    //Points
    'customerPoints/edit/(?P<customerPointsEventId>\d+)' => ['action' => 'customerPoints/editPointsEvent'],

    'customerreferralprogram/edit/(?P<referralId>\d+)' => array('action' => 'customerReferralProgram/editReferral'),

    ];
