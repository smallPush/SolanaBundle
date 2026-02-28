<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/solana/contract' => [[['_route' => 'app_solana_contract_index', '_controller' => 'App\\Controller\\SolanaContractController::index'], null, ['GET' => 0], null, true, false, null]],
        '/solana/contract/new' => [[['_route' => 'app_solana_contract_new', '_controller' => 'App\\Controller\\SolanaContractController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/solana/contract/([^/]++)(?'
                    .'|(*:70)'
                    .'|/validate(*:86)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        70 => [[['_route' => 'app_solana_contract_show', '_controller' => 'App\\Controller\\SolanaContractController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        86 => [
            [['_route' => 'app_solana_contract_validate', '_controller' => 'App\\Controller\\SolanaContractController::validate'], ['id'], ['POST' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
