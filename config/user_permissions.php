<?php
/*available user roles and permissions*/
return [

    'system admin' => [
                        'bypass subscription check',
                        'add roles and permissions','view roles and permissions','update roles and permissions','delete roles and permissions',
                        'add template','view template','update template','delete template','restore template','permanently delete template',
                        'view users','update user roles and permissions',
                        'add subscription','view subscriptions','view subscription','update subscription','delete subscription','restore subscription','permanently delete subscription',
                        ],
    'user' => [
                'view documents','save document','duplicate document','view document','update document','delete document','restore document','permanently delete document',
              ]
];
