<?php

use App\Models\Budget;
use App\Models\Campa;
use App\Models\Category;
use App\Models\Company;
use App\Models\DeliveryVehicle;
use App\Models\GroupTask;
use App\Models\Operation;
use App\Models\Order;
use App\Models\PendingTask;
use App\Models\Questionnaire;
use App\Models\Reception;
use App\Models\Request;
use App\Models\Reservation;
use App\Models\SubState;
use App\Models\TradeState;
use App\Models\TypeModelOrder;
use App\Models\Vehicle;
use App\Models\VehicleExit;
use App\Models\VehicleModel;
use App\Models\VehiclePicture;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    public function search_by_campa_null()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byCampaNull());
    }

    /** @test */
    public function should_search_by_campa()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->byCampaId(1));
    }

    /** @test */
    public function should_search_by_campas()
    {
        $this->assertInstanceOf(Builder::class, $this->vehicle->campasIds([]));
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
    public function search_last_group_task()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->lastGroupTask());
        $this->assertInstanceOf(GroupTask::class, $this->vehicle->lastGroupTask()->getModel());
    }

    /** @test */
    public function should_search_last_unapproved_group_task()
    {
        $this->assertInstanceOf(HasOne::class, $this->vehicle->lastUnapprovedGroupTask());
        $this->assertInstanceOf(GroupTask::class, $this->vehicle->lastUnapprovedGroupTask()->getModel());
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
}
