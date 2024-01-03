<?php

return [
    'HUBSPOT_API_KEY' => env('HUBSPOT_API_KEY', ''),
    'objectTypeIds' => [
        'hubspot_user_uuid' => env('HUBSPOT_USER_ID', ''),
        'wishpod_uuid' => env('HUBSPOT_WISHPOD_UUID', ''),
        'userpod_uuid' => env('HUBSPOT_USER_UUID', ''),
        'booking_uuid' => env('HUBSPOT_BOOKING_UUID', '')
    ],
    'primaryKeys' => [        
        'hubspot_user_uuid',
        'wishpod_uuid',
        'hubspotpod_uuid',
        'booking_uuid'
    ],
    'property_map' => [
        'User' => [
            'hubspot_user_uuid' => 'hubspot_user_uuid',	
            'email_address' => 'email',
            'firstName' => 'firstname',
            'lastName' => 'lastname',
            'bio' => 'user_bio',
            'identity_risk_score' => 'risk_score',
            'city' => 'city',
            'country_code' => 'country',
            'postal_code' => 'zip',
            'phoneNumber' => 'phone',
            'account_signup_time' => 'signup_date',
            'publicData' => [
                'fieldOfStudy' => 'user_field_of_study',
                'gender' => 'user_gender',
                'languages' => 'user_languages',
                // 'occupation' => '', MISSING IN HUBSPOT
                'relationship' => 'user_relationship',
                'religion' => 'user_religion',
                'interests' => 'parent_interest',
                'personalityType' => 'kind_of_personality',
                'politicalBeliefs' => 'political_beliefs',
                'personalityTraits' => 'personality_traits',
                'sub_interests' => 'sub_interests',
                'pronoun' => 'pronouns',
            ],
            'protectedData' => [],
            'privateData' => []
        ],
        'WishPod' => [],
        'UserPod' => [],
        'Booking' => [],
        'Listing' => []
    ]
];
