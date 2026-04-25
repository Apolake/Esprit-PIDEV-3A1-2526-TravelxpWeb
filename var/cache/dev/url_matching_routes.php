<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_wdt/styles' => [[['_route' => '_wdt_stylesheet', '_controller' => 'web_profiler.controller.profiler::toolbarStylesheetAction'], null, null, null, false, false, null]],
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/activities' => [[['_route' => 'activity_index', '_controller' => 'App\\Controller\\ActivityController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/activities' => [[['_route' => 'admin_activity_index', '_controller' => 'App\\Controller\\ActivityController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/activities/new' => [[['_route' => 'admin_activity_new', '_controller' => 'App\\Controller\\ActivityController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/admin/gamification' => [[['_route' => 'app_admin_gamification_index', '_controller' => 'App\\Controller\\AdminGamificationController::index'], null, ['GET' => 0], null, true, false, null]],
        '/admin/gamification/quest/new' => [[['_route' => 'app_admin_gamification_quest_new', '_controller' => 'App\\Controller\\AdminGamificationController::newQuest'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/blogs' => [[['_route' => 'blog_index', '_controller' => 'App\\Controller\\BlogController::index'], null, ['GET' => 0], null, false, false, null]],
        '/blogs/suggest' => [[['_route' => 'blog_suggest', '_controller' => 'App\\Controller\\BlogController::suggest'], null, ['GET' => 0], null, false, false, null]],
        '/blogs/new' => [[['_route' => 'blog_new', '_controller' => 'App\\Controller\\BlogController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/blogs/tools/grammar' => [[['_route' => 'blog_tool_grammar', '_controller' => 'App\\Controller\\BlogController::grammarTool'], null, ['POST' => 0], null, false, false, null]],
        '/admin/bookings' => [[['_route' => 'admin_booking_index', '_controller' => 'App\\Controller\\BookingController::index'], null, ['GET' => 0], null, false, false, null]],
        '/bookings' => [[['_route' => 'booking_index', '_controller' => 'App\\Controller\\BookingController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/bookings/new' => [[['_route' => 'admin_booking_new', '_controller' => 'App\\Controller\\BookingController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/bookings/new' => [[['_route' => 'booking_new', '_controller' => 'App\\Controller\\BookingController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/comments/tools/grammar' => [[['_route' => 'comment_tool_grammar', '_controller' => 'App\\Controller\\CommentController::grammarTool'], null, ['POST' => 0], null, false, false, null]],
        '/' => [[['_route' => 'app_home', '_controller' => 'App\\Controller\\HomeController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin' => [[['_route' => 'admin_portal', '_controller' => 'App\\Controller\\HomeController::adminPortal'], null, ['GET' => 0], null, false, false, null]],
        '/user' => [[['_route' => 'user_portal', '_controller' => 'App\\Controller\\HomeController::userPortal'], null, ['GET' => 0], null, false, false, null]],
        '/offers' => [[['_route' => 'offer_index', '_controller' => 'App\\Controller\\OfferController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/offers' => [[['_route' => 'admin_offer_index', '_controller' => 'App\\Controller\\OfferController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/offers/new' => [[['_route' => 'admin_offer_new', '_controller' => 'App\\Controller\\OfferController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/profile' => [[['_route' => 'app_profile_show', '_controller' => 'App\\Controller\\ProfileController::show'], null, ['GET' => 0], null, false, false, null]],
        '/profile/edit' => [[['_route' => 'app_profile_edit', '_controller' => 'App\\Controller\\ProfileController::edit'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/profile/delete' => [[['_route' => 'app_profile_delete', '_controller' => 'App\\Controller\\ProfileController::delete'], null, ['POST' => 0], null, false, false, null]],
        '/properties' => [[['_route' => 'property_index', '_controller' => 'App\\Controller\\PropertyController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/properties' => [[['_route' => 'admin_property_index', '_controller' => 'App\\Controller\\PropertyController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/properties/new' => [[['_route' => 'admin_property_new', '_controller' => 'App\\Controller\\PropertyController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/register' => [[['_route' => 'app_register', '_controller' => 'App\\Controller\\RegistrationController::register'], null, null, null, false, false, null]],
        '/login' => [[['_route' => 'app_login', '_controller' => 'App\\Controller\\SecurityController::login'], null, null, null, false, false, null]],
        '/logout' => [[['_route' => 'app_logout', '_controller' => 'App\\Controller\\SecurityController::logout'], null, null, null, false, false, null]],
        '/admin/services' => [[['_route' => 'admin_service_index', '_controller' => 'App\\Controller\\ServiceController::index'], null, ['GET' => 0], null, false, false, null]],
        '/services' => [[['_route' => 'service_index', '_controller' => 'App\\Controller\\ServiceController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/services/new' => [[['_route' => 'admin_service_new', '_controller' => 'App\\Controller\\ServiceController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/trips' => [[['_route' => 'trip_index', '_controller' => 'App\\Controller\\TripController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/trips' => [[['_route' => 'admin_trip_index', '_controller' => 'App\\Controller\\TripController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/trips/new' => [[['_route' => 'admin_trip_new', '_controller' => 'App\\Controller\\TripController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/admin/users' => [[['_route' => 'app_user_index', '_controller' => 'App\\Controller\\UserController::index'], null, ['GET' => 0], null, true, false, null]],
        '/admin/users/new' => [[['_route' => 'app_user_new', '_controller' => 'App\\Controller\\UserController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/(?'
                        .'|font/([^/\\.]++)\\.woff2(*:98)'
                        .'|([^/]++)(?'
                            .'|/(?'
                                .'|search/results(*:134)'
                                .'|router(*:148)'
                                .'|exception(?'
                                    .'|(*:168)'
                                    .'|\\.css(*:181)'
                                .')'
                            .')'
                            .'|(*:191)'
                        .')'
                    .')'
                .')'
                .'|/a(?'
                    .'|ctivities/(?'
                        .'|(\\d+)(*:225)'
                        .'|(\\d+)/join(*:243)'
                        .'|(\\d+)/leave(*:262)'
                    .')'
                    .'|dmin/(?'
                        .'|activities/(?'
                            .'|(\\d+)(*:298)'
                            .'|(\\d+)/edit(*:316)'
                            .'|(\\d+)/delete(*:336)'
                        .')'
                        .'|gamification/(?'
                            .'|quest/([^/]++)/(?'
                                .'|edit(*:383)'
                                .'|delete(*:397)'
                            .')'
                            .'|user/([^/]++)/edit(*:424)'
                        .')'
                        .'|bookings/(?'
                            .'|(\\d+)(*:450)'
                            .'|(\\d+)/edit(*:468)'
                            .'|(\\d+)/cancel(*:488)'
                            .'|(\\d+)/delete(*:508)'
                        .')'
                        .'|offers/(?'
                            .'|(\\d+)(*:532)'
                            .'|(\\d+)/edit(*:550)'
                            .'|(\\d+)(*:563)'
                        .')'
                        .'|properties/(?'
                            .'|(\\d+)(*:591)'
                            .'|(\\d+)/edit(*:609)'
                            .'|(\\d+)(*:622)'
                        .')'
                        .'|services/(?'
                            .'|(\\d+)(*:648)'
                            .'|(\\d+)/edit(*:666)'
                            .'|(\\d+)/delete(*:686)'
                        .')'
                        .'|trips/(?'
                            .'|(\\d+)(*:709)'
                            .'|(\\d+)/edit(*:727)'
                            .'|(\\d+)/delete(*:747)'
                        .')'
                        .'|users/([^/]++)(?'
                            .'|(*:773)'
                            .'|/edit(*:786)'
                            .'|(*:794)'
                        .')'
                    .')'
                .')'
                .'|/b(?'
                    .'|logs/(?'
                        .'|(\\d+)(*:823)'
                        .'|(\\d+)/edit(*:841)'
                        .'|(\\d+)/delete(*:861)'
                        .'|(\\d+)/react/(like|dislike)(*:895)'
                        .'|(\\d+)/export/pdf(*:919)'
                        .'|(\\d+)/comments/export/pdf(*:952)'
                        .'|(\\d+)/translate(*:975)'
                        .'|(\\d+)/ai/summarize(*:1001)'
                        .'|(\\d+)/comments/new(*:1028)'
                    .')'
                    .'|ookings/(\\d+)(*:1051)'
                .')'
                .'|/comments/(?'
                    .'|(\\d+)/edit(*:1084)'
                    .'|(\\d+)/delete(*:1105)'
                    .'|(\\d+)/react/(like|dislike)(*:1140)'
                    .'|(\\d+)/translate(*:1164)'
                .')'
                .'|/offers/(\\d+)(*:1187)'
                .'|/properties/(\\d+)(*:1213)'
                .'|/services/(\\d+)(*:1237)'
                .'|/trips/(?'
                    .'|(\\d+)(*:1261)'
                    .'|(\\d+)/join(*:1280)'
                    .'|(\\d+)/leave(*:1300)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        98 => [[['_route' => '_profiler_font', '_controller' => 'web_profiler.controller.profiler::fontAction'], ['fontName'], null, null, false, false, null]],
        134 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        148 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        168 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        181 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        191 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        225 => [[['_route' => 'activity_show', '_controller' => 'App\\Controller\\ActivityController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        243 => [[['_route' => 'activity_join', '_controller' => 'App\\Controller\\ActivityController::join'], ['id'], ['POST' => 0], null, false, false, null]],
        262 => [[['_route' => 'activity_leave', '_controller' => 'App\\Controller\\ActivityController::leave'], ['id'], ['POST' => 0], null, false, false, null]],
        298 => [[['_route' => 'admin_activity_show', '_controller' => 'App\\Controller\\ActivityController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        316 => [[['_route' => 'admin_activity_edit', '_controller' => 'App\\Controller\\ActivityController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        336 => [[['_route' => 'admin_activity_delete', '_controller' => 'App\\Controller\\ActivityController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        383 => [[['_route' => 'app_admin_gamification_quest_edit', '_controller' => 'App\\Controller\\AdminGamificationController::editQuest'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        397 => [[['_route' => 'app_admin_gamification_quest_delete', '_controller' => 'App\\Controller\\AdminGamificationController::deleteQuest'], ['id'], ['POST' => 0], null, false, false, null]],
        424 => [[['_route' => 'app_admin_gamification_user_edit', '_controller' => 'App\\Controller\\AdminGamificationController::editUserGamification'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        450 => [[['_route' => 'admin_booking_show', '_controller' => 'App\\Controller\\BookingController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        468 => [[['_route' => 'admin_booking_edit', '_controller' => 'App\\Controller\\BookingController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        488 => [[['_route' => 'admin_booking_cancel', '_controller' => 'App\\Controller\\BookingController::cancel'], ['id'], ['POST' => 0], null, false, false, null]],
        508 => [[['_route' => 'admin_booking_delete', '_controller' => 'App\\Controller\\BookingController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        532 => [[['_route' => 'admin_offer_show', '_controller' => 'App\\Controller\\OfferController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        550 => [[['_route' => 'admin_offer_edit', '_controller' => 'App\\Controller\\OfferController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        563 => [[['_route' => 'admin_offer_delete', '_controller' => 'App\\Controller\\OfferController::delete'], ['id'], ['POST' => 0], null, false, true, null]],
        591 => [[['_route' => 'admin_property_show', '_controller' => 'App\\Controller\\PropertyController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        609 => [[['_route' => 'admin_property_edit', '_controller' => 'App\\Controller\\PropertyController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        622 => [[['_route' => 'admin_property_delete', '_controller' => 'App\\Controller\\PropertyController::delete'], ['id'], ['POST' => 0], null, false, true, null]],
        648 => [[['_route' => 'admin_service_show', '_controller' => 'App\\Controller\\ServiceController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        666 => [[['_route' => 'admin_service_edit', '_controller' => 'App\\Controller\\ServiceController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        686 => [[['_route' => 'admin_service_delete', '_controller' => 'App\\Controller\\ServiceController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        709 => [[['_route' => 'admin_trip_show', '_controller' => 'App\\Controller\\TripController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        727 => [[['_route' => 'admin_trip_edit', '_controller' => 'App\\Controller\\TripController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        747 => [[['_route' => 'admin_trip_delete', '_controller' => 'App\\Controller\\TripController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        773 => [[['_route' => 'app_user_show', '_controller' => 'App\\Controller\\UserController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        786 => [[['_route' => 'app_user_edit', '_controller' => 'App\\Controller\\UserController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        794 => [[['_route' => 'app_user_delete', '_controller' => 'App\\Controller\\UserController::delete'], ['id'], ['POST' => 0], null, false, true, null]],
        823 => [[['_route' => 'blog_show', '_controller' => 'App\\Controller\\BlogController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        841 => [[['_route' => 'blog_edit', '_controller' => 'App\\Controller\\BlogController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        861 => [[['_route' => 'blog_delete', '_controller' => 'App\\Controller\\BlogController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        895 => [[['_route' => 'blog_react', '_controller' => 'App\\Controller\\BlogController::react'], ['id', 'type'], ['POST' => 0], null, false, true, null]],
        919 => [[['_route' => 'blog_export_pdf', '_controller' => 'App\\Controller\\BlogController::exportBlogPdf'], ['id'], ['GET' => 0], null, false, false, null]],
        952 => [[['_route' => 'blog_comments_export_pdf', '_controller' => 'App\\Controller\\BlogController::exportBlogCommentsPdf'], ['id'], ['GET' => 0], null, false, false, null]],
        975 => [[['_route' => 'blog_translate', '_controller' => 'App\\Controller\\BlogController::translateBlog'], ['id'], ['POST' => 0], null, false, false, null]],
        1001 => [[['_route' => 'blog_ai_summarize', '_controller' => 'App\\Controller\\BlogController::summarize'], ['id'], ['POST' => 0], null, false, false, null]],
        1028 => [[['_route' => 'comment_new', '_controller' => 'App\\Controller\\CommentController::new'], ['id'], ['POST' => 0], null, false, false, null]],
        1051 => [[['_route' => 'booking_show', '_controller' => 'App\\Controller\\BookingController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        1084 => [[['_route' => 'comment_edit', '_controller' => 'App\\Controller\\CommentController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1105 => [[['_route' => 'comment_delete', '_controller' => 'App\\Controller\\CommentController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        1140 => [[['_route' => 'comment_react', '_controller' => 'App\\Controller\\CommentController::react'], ['id', 'type'], ['POST' => 0], null, false, true, null]],
        1164 => [[['_route' => 'comment_translate', '_controller' => 'App\\Controller\\CommentController::translateComment'], ['id'], ['POST' => 0], null, false, false, null]],
        1187 => [[['_route' => 'offer_show', '_controller' => 'App\\Controller\\OfferController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        1213 => [[['_route' => 'property_show', '_controller' => 'App\\Controller\\PropertyController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        1237 => [[['_route' => 'service_show', '_controller' => 'App\\Controller\\ServiceController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        1261 => [[['_route' => 'trip_show', '_controller' => 'App\\Controller\\TripController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        1280 => [[['_route' => 'trip_join', '_controller' => 'App\\Controller\\TripController::join'], ['id'], ['POST' => 0], null, false, false, null]],
        1300 => [
            [['_route' => 'trip_leave', '_controller' => 'App\\Controller\\TripController::leave'], ['id'], ['POST' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
