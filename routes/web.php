
<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\Ald\PendingTaskAldController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return view('home');
});


$router->group(['prefix' => 'api'], function () use ($router) {

    /*$router->get('/command-artisan', function () {
        //ini_set("memory_limit", "-1");
        //ini_set('max_execution_time', '-1');
        //ob_clean();
        $exitCode = Artisan::call('db:seed', ['--class' => 'RepairApprovedQuestionnaries', '--force' => true]);
        return $exitCode === 0 ? 'successful run camand' : 'error run camand';

    });*/

    $router->post('/auth/signin', 'AuthController@login');
    $router->get('/delivery-note-ald', 'DownloadController@deliveryNoteAld');
    $router->get('/kpi-all', 'KpiController@kpiFull');
    $router->get('/kpi-inpu-out-stock', 'KpiController@kpiInpuOut');
    $router->get('/kpi-sub-states', 'KpiController@subStates');
    $router->get('/kpi-sub-states-month', 'KpiController@subStatesMonth');
    $router->get('/kpi-diff-reception', 'KpiController@diffTimeReception');
    $router->get('/kpi-check-list', 'KpiController@checkList');
    $router->get('/kpi-pending-tasks', 'KpiController@kpiPendingTask');
    $router->get('/stock-pending-tasks', 'KpiController@pendingTask');
    $router->get('/stock-vehicles', 'KpiController@stockVehicle');

    $router->post('broadcasting/auth', ['uses' => 'BroadcastController@authenticate']);
        /**
         * Reset password
         */
        $router->post('/password/send-code','MailController@sendCodePassword');
        $router->post('/password/reset','MailController@passwordReset');

    $router->group(['middleware' => ['auth']], function () use ($router) {
        $router->get('refresh','AuthController@refresh');
        /**
         * Users
         */
        $router->get('/users/getall', 'UserController@getAll');
        $router->get('/users/notifications', 'UserController@notifications');
        $router->get('/users/{id}', 'UserController@getById');
        $router->post('/users', 'UserController@create');
        $router->post('/users/create-without-password', 'UserController@createUserWithoutPassword');
        $router->put('/users/update/{id}', 'UserController@update');
        $router->delete('/users/delete/{id}', 'UserController@delete');
        $router->post('/users/role/{role_id}', 'UserController@getUsersByRole');
        $router->post('/users/assign-campa', 'CampaUserController@create');
        $router->post('/users/delete-campa', 'CampaUserController@delete');

        /**
         * Roles
         */
        $router->get('/roles/getall', 'RoleController@getAll');
        $router->get('/roles/{id}', 'RoleController@getById');
        $router->post('/roles', 'RoleController@create');
        $router->put('/roles/update/{id}', 'RoleController@update');
        $router->delete('/roles/delete/{id}', 'RoleController@delete');

        /**
         * Campas
         */
        $router->get('/campas/getall', 'CampaController@index');
        $router->get('/campas/{id}', 'CampaController@show');
        $router->post('/campas', 'CampaController@create');
        $router->put('/campas/update/{id}', 'CampaController@update');
        $router->delete('/campas/delete/{id}', 'CampaController@delete');

        /**
         * Categories
         */
        $router->get('/categories/getall', 'CategoryController@index');
        $router->get('/categories/{id}', 'CategoryController@show');
        $router->post('/categories', 'CategoryController@create');
        $router->put('/categories/update/{id}', 'CategoryController@update');
        $router->delete('/categories/delete/{id}', 'CategoryController@delete');

        /**
         * Companies
         */
        $router->get('/companies/getall', 'CompanyController@index');
        $router->get('/companies/{id}', 'CompanyController@show');
        $router->post('/companies', 'CompanyController@create');
        $router->put('/companies/update/{id}', 'CompanyController@update');
        $router->delete('/companies/delete/{id}', 'CompanyController@delete');

        /**
         * Customers
         */
        $router->get('/customers/getall', 'CustomerController@getall');
        $router->get('/customers/{id}', 'CustomerController@getById');
        $router->post('/customers', 'CustomerController@create');
        $router->put('/customers/update/{id}', 'CustomerController@update');
        $router->delete('/customers/delete/{id}', 'CustomerController@delete');

        /**
         * Group Tasks
         */
        $router->get('/grouptasks/getall', 'GroupTaskController@getall');
        $router->post('/grouptasks/approved-available', 'GroupTaskController@approvedGroupTaskToAvailable');
        $router->post('/grouptasks/decline','GroupTaskController@declineGroupTask');
        $router->get('/grouptasks/{id}', 'GroupTaskController@getById');
        $router->post('/grouptasks', 'GroupTaskController@create');
        $router->put('/grouptasks/update/{id}', 'GroupTaskController@update');
        $router->delete('/grouptasks/delete/{id}', 'GroupTaskController@delete');


        /**
         * Incidences
         */
        $router->get('/incidences/getall', 'IncidenceController@getAll');
        $router->get('/incidences/{id}', 'IncidenceController@getById');
        $router->post('/incidences', 'IncidenceController@create');
        $router->put('/incidences/update/{id}', 'IncidenceController@update');
        $router->delete('/incidences/delete/{id}', 'IncidenceController@delete');

        /**
         * Incidence-type
         */
        $router->get('/incidence-types/getall', 'IncidenceController@getAllTypes');

        /**
         * Regions
         */
        $router->get('/regions/getall', 'RegionController@getall');
        $router->get('/regions/{id}', 'RegionController@getById');
        $router->post('/regions', 'RegionController@create');
        $router->put('/regions/update/{id}', 'RegionController@update');
        $router->delete('/regions/delete/{id}', 'RegionController@delete');

         /**
         * Provinces
         */
        $router->get('/provinces/getall', 'ProvinceController@getall');
        $router->get('/provinces/{id}', 'ProvinceController@getById');
        $router->post('/provinces', 'ProvinceController@create');
        $router->put('/provinces/update/{id}', 'ProvinceController@update');
        $router->delete('/provinces/delete/{id}', 'ProvinceController@delete');

        /**
         * Pending task
         */
        $router->get('/pending-tasks/getall', 'PendingTaskController@getAll');
        $router->get('/pending-tasks', 'PendingTaskController@getPendingOrNextTask');
        $router->post('/pending-tasks/add-pending-task-finished', 'PendingTaskController@addPendingTaskFinished');
        $router->put('/pending-tasks/update/{id}', 'PendingTaskController@update');
        $router->delete('/pending-tasks/delete/{id}', 'PendingTaskController@delete');
        $router->post('/pending-tasks/start-pending-task', 'PendingTaskController@startPendingTask');
        $router->post('/pending-tasks/cancel-pending-task', 'PendingTaskController@cancelPendingTask');
        $router->post('/pending-tasks/finish-pending-task', 'PendingTaskController@finishPendingTask');
        $router->post('/pending-tasks/by-state/by-campa', 'PendingTaskController@getPendingTaskByStateCampa');
        $router->post('/pending-tasks/by-plate', 'PendingTaskController@getPendingTaskByPlate');
        $router->post('/pending-tasks/by-vehicle', 'PendingTaskController@getPendingTasksByPlate');
        $router->post('/pending-task/order', 'PendingTaskController@orderPendingTask');
        $router->post('/pending-tasks/add', 'PendingTaskController@addPendingTask');
        $router->get('/pending-tasks/filter', 'PendingTaskController@pendingTasksFilter');
        $router->get('/pending-tasks/{id}', 'PendingTaskController@getById');
        $router->post('/pending-tasks/finish-all', 'PendingTaskController@finishAll');
        $router->post('/pending-tasks/transfer', 'PendingTaskController@createTransferTask');

        /**
         * Purchase operations
         */
        $router->get('/purchase-operations/getall', 'PurchaseOperationController@getall');
        $router->get('/purchase-operations/{id}', 'PurchaseOperationController@getById');
        $router->post('/purchase-operations', 'PurchaseOperationController@create');
        $router->put('/purchase-operations/update/{id}', 'PurchaseOperationController@update');
        $router->delete('/purchase-operations/delete/{id}', 'PurchaseOperationController@delete');

        /**
         * Requests
         */
        $router->get('/requests/getall', 'RequestController@getall');
        $router->get('/requests/{id}', 'RequestController@getById');
        $router->delete('/requests/delete/{id}', 'RequestController@delete');

        /**
         * States
         */
        $router->get('/states/getall', 'StateController@getall');
        $router->get('/states/{id}', 'StateController@getById');
        $router->post('/states', 'StateController@create');
        $router->put('/states/update/{id}', 'StateController@update');
        $router->delete('/states/delete/{id}', 'StateController@delete');

        /**
         * States pending tasks
         */
        $router->get('/states-pending-tasks/getall', 'StatePendingTaskController@getall');
        $router->get('/states-pending-tasks/{id}', 'StatePendingTaskController@getById');
        $router->post('/states-pending-tasks', 'StatePendingTaskController@create');
        $router->put('/states-pending-tasks/update/{id}', 'StatePendingTaskController@update');
        $router->delete('/states-pending-tasks/delete/{id}', 'StatePendingTaskController@delete');

        /**
         * States request
         */
        $router->get('/state-requests/getall', 'StateRequestController@getall');
        $router->get('/state-requests/{id}', 'StateRequestController@getById');
        $router->post('/state-requests', 'StateRequestController@create');
        $router->put('/state-requests/update/{id}', 'StateRequestController@update');
        $router->delete('/state-requests/delete/{id}', 'StateRequestController@delete');

        /**
         * Sub states
         */
        $router->get('/sub-states/getall', 'SubStateController@getall');
        $router->get('/sub-states/{id}', 'SubStateController@getById');
        $router->post('/sub-states', 'SubStateController@create');
        $router->put('/sub-states/update/{id}', 'SubStateController@update');
        $router->delete('/sub-states/delete/{id}', 'SubStateController@delete');

        /**
         * Tasks
         */
        $router->get('/tasks/getall', 'TaskController@getall');
        $router->post('/tasks', 'TaskController@create');
        $router->put('/tasks/update/{id}', 'TaskController@update');
        $router->delete('/tasks/delete/{id}', 'TaskController@delete');
        $router->get('/tasks/{id}', 'TaskController@getById');

        /**
         * Transports
         */
        $router->get('/transports/getall', 'TransportController@getall');
        $router->get('/transports/{id}', 'TransportController@getById');
        $router->post('/transports', 'TransportController@create');
        $router->put('/transports/update/{id}', 'TransportController@update');
        $router->delete('/transports/delete/{id}', 'TransportController@delete');

        /**
         * TypeRequest
         */
        $router->get('/types-requests/getall', 'TypeRequestController@getall');
        $router->get('/types-requests/{id}', 'TypeRequestController@getById');
        $router->post('/types-requests', 'TypeRequestController@create');
        $router->put('/types-requests/update/{id}', 'TypeRequestController@update');
        $router->delete('/types-requests/delete/{id}', 'TypeRequestController@delete');

        /**
         * TypeTasks
         */
        $router->get('/types-tasks/getall', 'TypeTaskController@getall');
        $router->get('/types-tasks/{id}', 'TypeTaskController@getById');
        $router->post('/types-tasks', 'TypeTaskController@create');
        $router->put('/types-tasks/update/{id}', 'TypeTaskController@update');
        $router->delete('/types-tasks/delete/{id}', 'TypeTaskController@delete');

        /**
         * Vehicles
         */
        $router->get('/vehicles/download', 'VehicleController@download');
        $router->get('/vehicles', 'VehicleController@getAll');
        $router->post('/vehicles/by-campa', 'VehicleController@getByCampaWithoutReserve');
        $router->post('/vehicles/create-from-excel', 'VehicleController@createFromExcel');
        $router->post('/vehicles', 'VehicleController@create');
        $router->post('/vehicles/verify-plate', 'VehicleController@verifyPlate');
        $router->post('/vehicles/verify-plate-reception', 'VehicleController@verifyPlateReception');
        $router->post('/vehicles/change-sub-state', 'VehicleController@changeSubState');
        $router->put('/vehicles/update/{id}', 'VehicleController@update');
        $router->delete('/vehicles/delete/{id}', 'VehicleController@delete');
        $router->post('/vehicles/return/{id}', 'VehicleController@returnVehicle');
        $router->post('/vehicles/delete-massive', 'VehicleController@deleteMassive');
        $router->post('/vehicles/defleeting/{id}', 'VehicleController@defleeting');
        $router->put('/vehicles/update-sub-state/{id}', 'VehicleController@updateSubStateVehicle');
        $router->get('/vehicle-with-reservation-without-order/campa', 'VehicleController@getVehiclesWithReservationWithoutOrderCampa');
        $router->post('/vehicle-with-reservation-without-contract/campa', 'VehicleController@getVehiclesWithReservationWithoutContractCampa');
        $router->get('/vehicles/filter', 'VehicleController@filterVehicle');
        $router->get('/vehicles/reserved', 'VehicleController@vehicleReserved');
        $router->get('/vehicles/totals/by-state', 'VehicleController@vehicleTotalsState');
        $router->get('/vehicles/{id}', 'VehicleController@getById');
        $router->post('/vehicles/by-state-date','VehicleController@vehicleByState');
        $router->post('/vehicles/set-vehicle-rented', 'VehicleController@setVehicleRented');
        $router->post('/vehicles/comments', 'VehicleController@comments');

        /**
         * Vehicle model
         */
        $router->get('/vehicle-models','VehicleModelController@getAll');
        $router->post('/vehicle-models', 'VehicleModelController@store');
        $router->put('/vehicle-models/{id}', 'VehicleModelController@update');

        /**
         * Vehicle Picture
         */
        $router->post('/vehicle-pictures', 'VehiclePictureController@create');
        $router->delete('/vehicle-pictures/{id}', 'VehiclePictureController@delete');
        $router->post('/vehicle-pictures/delete-pic-by-place', 'VehiclePictureController@deletePictureByPlace');
        $router->post('/vehicle-pictures/by-vehicle', 'VehiclePictureController@getPicturesByVehicle');

        /**
         * Variables defleet
         */
        $router->get('/defleet-variables', 'DefleetVariableController@getVariables');
        $router->get('/defleet-variables/getall', 'DefleetVariableController@getAll');
        $router->put('/defleet-variables', 'DefleetVariableController@updateVariables');
        $router->post('/defleet-variables', 'DefleetVariableController@createVariables');

        /**
         * Questions
         */
        $router->get('/questions/getall', 'QuestionController@getAll');
        $router->post('/questions', 'QuestionController@create');
        $router->delete('/questions/{id}', 'QuestionController@delete');

        /**
         *
         */
        $router->post('/reservation-time', 'ReservationTimeController@getByCompany');
        $router->post('/reservation-time/create', 'ReservationTimeController@create');
        $router->post('/reservation-time/update', 'ReservationTimeController@update');

        /**
         * Questions answer
         */
        $router->post('/question-answers', 'QuestionAnswerController@create');
        $router->post('/question-answers/checklist', 'QuestionAnswerController@createChecklist');
        $router->put('/question-answers/update/{id}', 'QuestionAnswerController@update');
        $router->put('/question-answers/update-response/{id}', 'QuestionAnswerController@updateResponse');

        /**
         * Manual Questionnaire
         */
        $router->post('/manual-questionnaire', 'ManualQuestionnaireController@create');

        /**
         * Reservation
         */
        $router->post('/get-reservations', 'ReservationController@getReservationActive');
        $router->post('/get-reservations/by-campa', 'ReservationController@getReservationActiveByCampa');
        $router->post('/reservations/update', 'ReservationController@update');
        $router->post('/get-reservation/by-vehicle', 'ReservationController@getReservationsByVehicle');
        $router->post('/reservation/without-order', 'ReservationController@vehicleWithoutOrder');
        $router->post('/reservation/without-contract', 'ReservationController@vehicleWithoutContract');

        /**
         * Chat
         */
        $router->post('/chat', 'ChatController@createMessage');
        $router->post('/chat-app', 'ChatController@createMessageApp');
        $router->post('/get-message', 'ChatController@getMessage');
        $router->get('/get-message-app', 'ChatController@getMessageApp');
        $router->post('/read-messages', 'ChatController@readMessages');

        /**
         * Brands
         */
        $router->get('/brands','BrandController@index');
        $router->post('/brands', 'BrandController@store');
        $router->put('/brands/{id}', 'BrandController@update');
        $router->delete('/brands/{id}','BrandController@delete');

        /**
         * TradeState
         */
        $router->get('/trade-states', 'TradeStateController@getAll');

        /**
         * Type Reservations
         */
        $router->get('/type-reservations', 'TypeReservationController@getAll');

        /**
         * Receptions
         */
        $router->get('/reception','ReceptionController@index');
        $router->post('/reception', 'ReceptionController@create');
        $router->get('/reception/{id}','ReceptionController@getById');
        $router->put('reception/{id}', 'ReceptionController@updateReception');

        /**
         * Type User App
         */
        $router->get('/type-user-app', 'TypeUserAppController@index');

        /**
         * Vehicle exits
         */
        $router->get('/vehicle-exits', 'VehicleExitController@getAll');
        $router->get('/vehicle-exits/{id}', 'VehicleExitController@getById');
        $router->post('/vehicle-exits', 'VehicleExitController@create');
        $router->put('/vehicle-exits/{id}', 'VehicleExitController@update');

        /**
         * Operations
         */
        $router->get('/operations', 'OperationController@getAll');
        $router->get('/operations/{id}', 'OperationController@getById');
        $router->post('/operations', 'OperationController@create');
        $router->put('/operations/{id}', 'OperationController@update');

        /**
         * Type budget lines
         */
        $router->get('/type-budget-lines', 'TypeBudgetLineController@index');

        /**
         * Taxes
         */
        $router->get('/taxes', 'TaxController@index');

        /**
         * Type model order
         */
        $router->get('/type-model-order', 'TypeModelOrderController@getAll');

        /**
         * Budget pending task
         */
        $router->post('/budget-pending-task', 'BudgetPendingTaskController@store');
        $router->put('/budget-pending-task/{id}', 'BudgetPendingTaskController@update');
        $router->get('/budget-pending-task', 'BudgetPendingTaskController@index');

        /**
         * Questionnaire
         */
        $router->get('/questionnaire','QuestionnaireController@index');
        $router->get('/questionnaire/{id}', 'QuestionnaireController@getById');
        $router->post('/questionnaire/approved', 'QuestionnaireController@approved');


        /**
         * Login logs
         */
        $router->get('/login-logs', 'LoginLogController@getAll');
        $router->post('/login-logs', 'LoginLogController@create');
        $router->post('/login-logs/by-user', 'LoginLogController@getUser');

        /**
         * Budget
         */
        $router->get('/budgets', 'BudgetController@index');

        /**
         * Budget lines
         */
        $router->get('/budget-lines', 'BudgetLineController@index');

        /**
         * Password reset code
         */
        $router->get('/password-reset-code', 'PasswordResetCodeController@getAll');

        /**
         * People for report
         */
        $router->get('/people-for-report', 'PeopleForReportController@getAll');

        /**
         * Comments
         */
        $router->get('/comments', 'CommentController@index');
        $router->post('/comments', 'CommentController@store');

        /**
         * Incidence image
         */
        $router->get('/incidence-images', 'IncidenceImageController@index');
        $router->post('/incidence-images', 'IncidenceImageController@store');
        $router->put('/incidence-images/{id}', 'IncidenceImageController@update');

        /**
         * Zones
         */
        $router->get('/zones', 'ZoneController@index');
        $router->post('/zones', 'ZoneController@store');
        $router->put('/zones/{id}', 'ZoneController@update');

        /**
         * Streets
         */
        $router->get('/streets','StreetController@index');
        $router->post('/streets','StreetController@store');
        $router->put('/streets/{id}','StreetController@update');

        /**
         * Squares
         */
        $router->get('/squares','SquareController@index');
        $router->post('/squares','SquareController@store');
        $router->put('/squares/{id}','SquareController@update');
        $router->delete('/squares/{id}', 'SquareController@delete');


        /**
         * Colors
         */
        $router->get('/colors','ColorController@index');
        $router->post('/colors','ColorController@store');
        $router->put('/colors/{id}','ColorController@update');

        /**
         * Damages
         */
        $router->get('/damages', 'DamageController@index');
        $router->post('/damages', 'DamageController@store');
        $router->put('/damages/{id}', 'DamageController@update');

        /**
         * Accessory
         */
        $router->get('/accessories', 'AccessoryController@index');
        $router->post('/accessories', 'AccessoryController@store');
        $router->get('/accessories/{id}', 'AccessoryController@show');
        $router->put('/accessories/{id}', 'AccessoryController@update');
        $router->delete('/accessories/{id}', 'AccessoryController@destroy');

        /**
         * Accessory type
         */
        $router->get('/accessory-types','AccessoryTypeController@index');
        $router->post('/accessory-types','AccessoryTypeController@store');
        $router->get('/accessory-types/{id}', 'AccessoryTypeController@show');
        $router->put('/accessory-types/{id}', 'AccessoryTypeController@update');
        $router->delete('/accessory-types/{id}', 'AccessoryTypeController@destroy');

        /**
         * Accessory vehicle
         */
        $router->get('/accessory-vehicle', 'AccessoryVehicleController@index');
        $router->post('/accessory-vehicle', 'AccessoryVehicleController@store');
        $router->post('/accessory-vehicle/delete', 'AccessoryVehicleController@destroy');
        $router->get('/accessory-vehicle/export','AccessoryVehicleController@export');

        /**
         * Damage images
         */
        $router->get('/damage-images', 'DamageImageController@index');
        $router->post('/damage-images', 'DamageImageController@store');
        $router->put('/damage-images/{id}', 'DamageImageController@destroy');

        /**
         * Status damage
         */
        $router->get('/status-damages', 'StatusDamageController@index');
        $router->post('/status-damages', 'StatusDamageController@store');
        $router->put('/status-damages/{id}', 'StatusDamageController@destroy');

        /**
         * Severity damage
         */
        $router->get('/severity-damages', 'SeverityDamageController@index');
        $router->post('/severity-damages', 'SeverityDamageController@store');
        $router->put('/severity-damages/{id}', 'SeverityDamageController@destroy');

        /**
         * History Location
         */
        $router->get('/history-locations', 'HistoryLocationController@index');

        /**
         * Delivery vehicles
         */
        $router->get('/delivery-vehicles','DeliveryVehicleController@index');
        $router->get('/delivery-vehicles/export','DeliveryVehicleController@export');
        $router->delete('/delivery-vehicles/{id}', 'DeliveryVehicleController@destroy');

        /**
         * Type delivery note
         */
        $router->get('/type-delivery-note', 'TypeDeliveryNoteController@index');

        /**
         * Damage Type
         */
        $router->get('/damage-type', 'DamageTypeController@index');

        /**
         * Pending Authorization
         */
        $router->get('/pending-authorization','PendingAuthorizationController@index');

        /** Estimated dates */
        $router->post('/estimated-dates', 'EstimatedDateController@store');
        $router->put('/estimated-dates/{id}', 'EstimatedDateController@update');

        /**
         * Statistics
         */
        $router->get('/statistics/stock-by-state','StatisticsController@getStockByState');
        $router->get('/statistics/stock-by-month','StatisticsController@getStockByMonth');
        $router->get('/statistics/average-by-substate', 'StatisticsController@getAverageSubState');
        $router->get('/statistics/stock-by-channel','StatisticsController@getAverageTypeModelOrder');
        $router->get('/statistics/average-by-task','StatisticsController@getAveragePendingTask');
        $router->get('/statistics/half-task-start','StatisticsController@halfTaskStart');
        $router->get('/statistics/execution-time','StatisticsController@executionTime');
        $router->get('/statistics/average-time-sub-state', 'StatisticsController@averageTimeInSubState');
        $router->get('/statistics/time-approval', 'StatisticsController@timeApproval');

        /**
         * HTTP Requests
         */
        $router->get('/http-requests', 'MonitorController@index');

        /**
         * kpis
         */
        $router->get('/kpis', 'KpiController@index');
        $router->get('/kpis/inpu', 'KpiController@inpu');
        $router->get('/kpis/out', 'KpiController@out');
    });
});

