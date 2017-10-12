<?php

return [

    // Settings
    'customerpoints/settings' => ['action' => 'customerPoints/settings/index'],

    //Customer
    'customerpoints/customers' => ['action' => 'customerPoints/customers/index'],
    'customerpoints/customers/edit/(?P<customerPointsUserId>\d+)' => ['action' => 'customerPoints/customers/editCustomerPointsInfo'],

    //Points
    'customerpoints/points' => ['action' => 'customerPoints/points/index'],
    'customerpoints/points/edit/(?P<customerPointsEventId>\d+)' => ['action' => 'customerPoints/editPointsEvent'],

    //referrals
    'customerpoints/referrals' => ['action' => 'customerPoints/referrals/index'],
    'customerpoints/referrals/edit/(?P<customerPointsReferralId>\d+)' => ['action' => 'customerPoints/referrals/editReferral'],
    //reviews
    'customerpoints/reviews' => ['action' => 'customerPoints/reviews/index'],
    'customerpoints/reviews/edit/(?P<customerPointsReviewId>\d+)' => ['action' => 'customerPoints/reviews/editReviews'],

    ];
