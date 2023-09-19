<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'welcome/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['dashboard']                                                     = 'upload/dashboard';
$route['getroutes']                                               = 'api/getroutes';
$route['insertuser']                                               = 'api/insertuser';
$route['edituser']                                                   = 'api/edituser';
$route['deleteuser']                                               = 'api/deleteuser';
$route['insertroute']                                               = 'api/insertroute';
$route['editroute']                                               = 'api/editroute';
$route['deleteroute']                                               = 'api/deleteroute';
$route['getsalesperson']                                           = 'api/getsalesperson';
$route['insertsalesperson']                                       = 'api/insertsalesperson';
$route['editsalesperson']                                           = 'api/editsalesperson';
$route['deletesalesperson']                                       = 'api/deletesalesperson';
//city
$route['v1/city']                                                         = 'city';
$route['v1/city/(:any)']                                           = 'city/$1';
//route
$route['v1/route']                                                          = 'route';
$route['v1/route/(:any)']                                           = 'route/$1';
//stop
$route['v1/stop']                                                         = 'stop';
$route['v1/stop/(:any)']                                           = 'stop/$1';
//salesrepresentative 
$route['v1/salesrepresentative']                                = 'salesrepresentative';
$route['v1/salesrepresentative/(:any)']                        = 'salesrepresentative/$1';
//category 
$route['v1/category']                                              = 'category';
$route['v1/category/(:any)']                                       = 'category/$1';
//retailer
$route['v1/retailer']                                                  = 'retailer';
$route['v1/retailer/(:any)']                                           = 'retailer/$1';
//distributor
$route['v1/distributor']                                                  = 'distributor';
$route['v1/distributor/(:any)']                                                     = 'distributor/$1';
//product
$route['v1/product']                                                  = 'product';
$route['v1/product/(:any)']                                        = 'product/$1';
//stock
$route['v1/stock']                                                          = 'stock';
$route['v1/stock/(:any)']                                                  = 'stock/$1';
$route['v1/stock-summary/(:any)']                                                  = 'stock/stock_summary/$1';
$route['v1/stock/(:any)/(:any)']                                                  = 'stock/$1/$2';
//variant
$route['v1/variant']                                                      = 'variant';
$route['v1/variant/(:any)']                                       = 'variant/$1';
//units
$route['v1/units']                                                          = 'admin/units';
//status
$route['v1/status']                                                      = 'admin/status';

$route['v1/forgot-password']                                        = 'admin/forget_password';
$route['v1/distributor-reset-password']                                        = 'admin/reset_password_distributor';
$route['v1/rep-reset-password']                                                      = 'admin/reset_password_rep';
$route['v1/admin-reset-password']                                                      = 'admin/reset_password_admin';

//login
$route['v1/login/admin']                                                  = 'auth/loginAdmin';
$route['v1/login/distributor']                                              = 'auth/loginDistributor';
$route['v1/login/salesrepresentative']                                = 'auth/loginSalesrepresentative';


$route['v1/schedules']                                                          = 'schedule';
$route['v1/day-schedule']                                                          = 'schedule/today_schedule';
$route['v1/schedules/(:any)']                                                  = 'schedule/$1';

//teams 
$route['v1/teams']                                              = 'teams';
$route['v1/teams/(:any)']                                       = 'teams/$1';

//orders
$route['v1/orders']                                                  = 'orders';
$route['v1/orders/(:any)']                                            = 'orders/$1';
$route['v1/order-approve']                                                  = 'orders/order_approve';
$route['v1/order-cancel']                                                  = 'orders/order_cancel';

//analyitcs
$route['v1/analytics/orders/(:any)/(:any)']                                             = 'orders/my_analytics/$1/$2';


//leaderBoardTeam
$route['v1/leaderboardteam']                                                          = 'admin/leaderboardteam';
//leaderBoardRep
$route['v1/leaderboardrep']                                                          = 'admin/leaderboardrep';
$route['v1/myreward/(:any)']                                             = 'admin/myreward/$1';


$route['v1/image']                                                          = 'image';
$route['v1/image/(:any)']                                               = 'image/$1';





$route['v1/orders/authenticate']                                       = 'orders/authenticate';
//orderItem
$route['getorderitems']                                           = 'orderItem/getOrderItems';
$route['insertorderitem']                                              = 'orderItem/insertOrderItem';
$route['updateorderitem']                                           = 'orderItem/updateOrderItem';
$route['deleteorderitem']                                              = 'orderItem/deleteOrderItem';
//routeShedule
$route['getrouteshedules']                                           = 'routeShedule/getRouteShedules';
$route['insertrouteshedule']                                       = 'routeShedule/insertRouteShedule';
$route['updaterouteshedule']                                       = 'routeShedule/updateRouteShedule';
$route['deleterouteshedule']                                       = 'routeShedule/deleteRouteShedule';
//salesrepresentative 
$route['getsalesrepresentatives']                                           = 'salesrepresentative/getSalesrepresentative';
$route['insertsalesrepresentative']                                       = 'salesrepresentative/insertSalesrepresentative';
$route['updatesalesrepresentative']                                       = 'salesrepresentative/updateSalesrepresentative';
$route['deletesalesrepresentative']                                       = 'salesrepresentative/deleteSalesrepresentative';
