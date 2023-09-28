<?php

use App\Models\Accessory;
use App\Models\Budget;
use App\Models\Campa;
use App\Models\Category;
use App\Models\Color;
use App\Models\Company;
use App\Models\Damage;
use App\Models\DeliveryNote;
use App\Models\DeliveryVehicle;
use App\Models\GroupTask;
use App\Models\Incidence;
use App\Models\Operation;
use App\Models\Order;
use App\Models\PendingAuthorization;
use App\Models\PendingTask;
use App\Models\Questionnaire;
use App\Models\Reception;
use App\Models\Request;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\Square;
use App\Models\StateChange;
use App\Models\SubState;
use App\Models\SubStateChangeHistory;
use App\Models\TradeState;
use App\Models\TypeModelOrder;
use App\Models\Vehicle;
use App\Models\VehicleExit;
use App\Models\VehicleModel;
use App\Models\VehiclePicture;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class VehicleTest extends TestCase
{

    use DatabaseTransactions;

    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vehicle = Vehicle::factory()->create();
    }

    /** @test */
    public function it_belongs_to_category()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->vehicle->category());
        $this->assertInstanceOf(Category::class, $this->vehicle->category()->getModel());
    }

    /** @test */
    public function it_belongs_to_campa()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->vehicle->campa());
        $this->assertInstanceOf(Campa::class, $this->vehicle->campa()->getModel());
    }

    /** @test */
    public function it_belongs_to_sub_state()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->vehicle->subState());
        $this->assertInstanceOf(SubState::class, $this->vehicle->subState()->getModel());
    }

    /** @test */
    public function it_belongs_to_color()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->vehicle->color());
        $this->assertInstanceOf(Color::class, $this->vehicle->color()->getModel());
    }

    /** @test */
    public function it_has_many_requests()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->requests());
        $this->assertInstanceOf(Request::class, $this->vehicle->requests()->getModel());
    }

    /** @test */
    public function it_belongs_to_company()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->vehicle->company());
        $this->assertInstanceOf(Company::class, $this->vehicle->company()->getModel());
    }

    /** @test */
    public function it_has_many_pending_tasks()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->pendingTasks());
        $this->assertInstanceOf(PendingTask::class, $this->vehicle->pendingTasks()->getModel());
    }

    /** @test */
    public function it_has_many_pending_task_budget()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->pendingTasksBudget());
        $this->assertInstanceOf(PendingTask::class, $this->vehicle->pendingTasks()->getModel());
    }

    /** @test */
    public function it_has_many_group_task()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->groupTasks());
        $this->assertInstanceOf(GroupTask::class, $this->vehicle->groupTasks()->getModel());
    }

    /** @test */
    public function it_has_many_vehicle_pictures()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->vehiclePictures());
        $this->assertInstanceOf(VehiclePicture::class, $this->vehicle->vehiclePictures()->getModel());
    }

     /** @test */
     public function it_has_many_reservations()
     {
         $this->assertInstanceOf(HasMany::class, $this->vehicle->reservations());
         $this->assertInstanceOf(Reservation::class, $this->vehicle->reservations()->getModel());
     }

     /** @test */
     public function it_has_many_receptions()
     {
         $this->assertInstanceOf(HasMany::class, $this->vehicle->receptions());
         $this->assertInstanceOf(Reception::class, $this->vehicle->receptions()->getModel());
     }

     /** @test */
     public function it_has_belongs_to_type_model_order()
     {
         $this->assertInstanceOf(BelongsTo::class, $this->vehicle->typeModelOrder());
         $this->assertInstanceOf(TypeModelOrder::class, $this->vehicle->typeModelOrder()->getModel());
     }

      /** @test */
    public function it_belongs_to_trade_state()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->vehicle->tradeState());
        $this->assertInstanceOf(TradeState::class, $this->vehicle->tradeState()->getModel());
    }

    /** @test */
    public function it_has_many_questionnaire()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->questionnaires());
        $this->assertInstanceOf(Questionnaire::class, $this->vehicle->questionnaires()->getModel());
    }

    /** @test */
    public function it_has_many_vehicle_exit()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->vehicleExits());
        $this->assertInstanceOf(VehicleExit::class, $this->vehicle->vehicleExits()->getModel());
    }

    /** @test */
    public function it_has_many_operations()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->operations());
        $this->assertInstanceOf(Operation::class, $this->vehicle->operations()->getModel());
    }

    /** @test */
    public function it_has_many_incidences()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->incidences());
        $this->assertInstanceOf(Incidence::class, $this->vehicle->incidences()->getModel());
    }

    /** @test */
    public function it_has_many_damages()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->damages());
        $this->assertInstanceOf(Damage::class, $this->vehicle->damages()->getModel());
    }

    /** @test */
    public function it_belongs_to_many_accessories()
    {
        $this->assertInstanceOf(BelongsToMany::class, $this->vehicle->accessories());
        $this->assertInstanceOf(Accessory::class, $this->vehicle->accessories()->getModel());
    }

    /** @test */
    public function it_belongs_to_many_accessories_type_accessory()
    {
        $this->assertInstanceOf(BelongsToMany::class, $this->vehicle->accessoriesTypeAccessory());
        $this->assertInstanceOf(Accessory::class, $this->vehicle->accessoriesTypeAccessory()->getModel());
    }

    /** @test */
    public function it_has_many_pending_authorization()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->pendingAuthorizations());
        $this->assertInstanceOf(PendingAuthorization::class, $this->vehicle->pendingAuthorizations()->getModel());
    }

    /** @test */
    public function should_search_last_questionnaire()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->lastQuestionnaire());
        $this->assertInstanceOf(Questionnaire::class, $this->vehicle->lastQuestionnaire()->getModel());
    }

    /** @test */
    public function shoul_search_last_reception()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->lastReception());
        $this->assertInstanceOf(Reception::class, $this->vehicle->lastReception()->getModel());
    }

    /** @test */
    public function it_has_many_orders()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->orders());
        $this->assertInstanceOf(Order::class, $this->vehicle->orders()->getModel());
    }

    /** @test */
    public function it_has_one_order()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->lastOrder());
        $this->assertInstanceOf(Order::class, $this->vehicle->lastOrder()->getModel());
    }

    /** @test */
    public function it_has_many_budgets()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->budgets());
        $this->assertInstanceOf(Budget::class, $this->vehicle->budgets()->getModel());
    }

    /** @test */
    public function it_has_many_to_delivery_vehicles()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->deliveryVehicles());
        $this->assertInstanceOf(DeliveryVehicle::class, $this->vehicle->deliveryVehicles()->getModel());
    }

    /** @test */
    public function it_has_one_delivery_vehicle()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->lastDeliveryVehicle());
        $this->assertInstanceOf(DeliveryVehicle::class, $this->vehicle->deliveryVehicles()->getModel());
    }

    /** @test */
    public function should_search_by_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byIds([]));
    }

    /** @test */
    public function it_belongs_to_vehicle_model()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->vehicle->vehicleModel());
        $this->assertInstanceOf(VehicleModel::class, $this->vehicle->vehicleModel()->getModel());
    }

    /** @test */
    public function it_has_many_sub_state_change_histories()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->subStateChangeHistories());
        $this->assertInstanceOf(SubStateChangeHistory::class, $this->vehicle->subStateChangeHistories()->getModel());
    }

    /** @test */
    public function it_has_many_state_change()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->stateChange());
        $this->assertInstanceOf(StateChange::class, $this->vehicle->stateChange()->getModel());
    }

    /** @test */
    public function it_has_one_square()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->square());
        $this->assertInstanceOf(Square::class, $this->vehicle->square()->getModel());
    }

    /** @test */
    public function should_return_vehicles_by_role_id()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byRole(Role::MANAGER_MECHANIC));
    }

    /** @test */
    public function should_search_where_has_budget_pending_task()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->whereHasBudgetPendingTask([]));
    }

    /** @test */
    public function search_by_campas_of_user()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byCampasOfUser([]));
    }

    /** @test */
    public function search_by_sub_state_null()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->bySubStateNull());
    }

    /** @test */
    public function should_search_by_campa()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byCampaId(1));
    }

    /** @test */
    public function should_search_by_campas()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->campaIds([]));
    }

    /** @test */
    public function should_search_by_sub_states()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->subStateIds([]));
    }

    /** @test */
    public function should_search_by_states()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->stateIds([]));
    }

    /** @test */
    public function should_return_by_company_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byCompanies([]));
    }

    /** @test */
    public function should_search_by_plate()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byPlate('0000AAA'));
    }

    /** @test */
    public function should_search_by_trade_states()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byTradeStateIds([]));
    }

    /** @test */
    public function should_search_by_brands()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->brandIds([]));
    }

    /** @test */
    public function should_vehicle_by_type_model_order_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byTypeModelOrderIds([]));
    }

    /** @test */
    public function should_return_by_budget_pending_task_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byBudgetPendingTaskIds([]));
    }

    /** @test */
    public function should_search_by_vehicle_models()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->vehicleModelIds([]));
    }

    /** @test */
    public function should_search_by_categories()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->categoriesIds([]));
    }

    /** @test */
    public function should_search_by_ubication()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byUbication('A01'));
    }

    /** @test */
    public function should_search_by_ready_delivery()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byReadyDelivery(true));
    }

    /** @test */
    public function should_search_by_state_pending_task()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byStatePendingTasks([]));
    }

    /** @test */
    public function should_return_by_sub_state_pending_task_ids()
    {   
        $this->assertInstanceOf(Builder::class, $this->vehicle->bySubStatePendingTasks([]));
    }


    /** @test */
    public function search_last_order()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->lastOrders());
        $this->assertInstanceOf(Order::class, $this->vehicle->lastOrder()->getModel());
    }

    /** @test */
    public function should_return_vehicles_has_order_not_finish()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byHasOrderNotFinish(''));
    }

    /** @test */
    public function should_return_by_has_orders_state_ids(){
        $this->assertInstanceOf(Builder::class, $this->vehicle->byHasOrdersStateIds([]));
    }

    /** @test */
    public function should_search_no_active_or_pending_request()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->noActiveOrPendingRequest());
    }

    /** @test */
    public function should_search_by_parameter_defleet()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byParameterDefleet('2000-01-01', 5));
    }

    /** @test */
    public function should_search_by_pending_request_defleet()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byPendingRequestDefleet());
    }

    /** @test */
    public function should_search_that_has_reservation_without_order_without_delivery()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->thathasReservationWithoutOrderWithoutDelivery());
    }

    /** @test */
    public function should_search_with_request_active()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->withRequestActive());
        $this->assertInstanceOf(Request::class, $this->vehicle->withRequestActive()->getModel());
    }


    /** @test */
    public function should_search_with_order_without_contract()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byWithOrderWithoutContract());
    }

    /** @test */
    public function should_search_with_request_defleet_active()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->withRequestDefleetActive());
    }

    /** @test */
    public function should_search_different_defleeted()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->differentDefleeted());
    }

    /** @test */
    public function should_search_defleet_between_date_approved()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->defleetBetweenDateApproved('2021-01-01','2021-12-31'));
    }

    /** @test */
    public function should_search_pending_task_in_progress_or_pending()
    {
        $this->assertInstanceOf(HasMany::class, $this->vehicle->withPendingTaskOrProgress());
    }

    /** @test */
    public function should_return_last_pending_task_or_progress()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->withLastPendingTaskOrProgress());
    }

    /** @test */
    public function should_return_by_task_state_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byTaskSubStatesIds([]));
    }

    /** @test */
    public function should_return_by_task_sub_state_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byTaskSubStatesIds([]));
    }

    /** @test */
    public function should_return_vehicle_by_task_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byTaskIds([]));
    }
}
