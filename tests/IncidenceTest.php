<?php

use App\Models\Incidence;
use App\Models\PendingTask;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class IncidenceTest extends TestCase
{

    use DatabaseTransactions;

    private Incidence $incidence;

    protected function setUp(): void
    {
        parent::setUp();
        $this->incidence = Incidence::factory()->create();
    }

    /** @test */
    public function it_belongs_to_many_pending_tasks()
    {
        $this->assertInstanceOf(BelongsToMany::class, $this->incidence->pending_tasks());
        $this->assertInstanceOf(PendingTask::class, $this->incidence->pending_tasks()->getModel());
    }

    /** @tes */
    public function it_belongs_to_vehicle()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->incidence->vehicle());
        $this->assertInstanceOf(Vehicle::class, $this->incidence->vehicle()->getModel());
    }

    /** @test */
    public function should_return_incidences_by_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->incidence->byIds([]));
    }

    /** @test */
    public function should_return_incidences_by_resolved()
    {
        $this->assertInstanceOf(Builder::class, $this->incidence->byResolved(1));
    }

    /** @test */
    public function should_return_incidences_by_vehicles_ids()
    {
        $this->assertInstanceOf(Builder::class, $this->incidence->byVehicleIds([]));
    }

    /** @test */
    public function should_return_incidences_by_read()
    {
        $this->assertInstanceOf(Builder::class, $this->incidence->byRead(1));
    }

}
