<?php

use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BudgetTest extends TestCase
{

    use DatabaseTransactions;

    private Budget $budget;

    protected function setUp(): void
    {
        parent::setUp();
        $this->budget = Budget::factory()->create();
    }

    /** @test */
    public function it_has_many_budget_lines()
    {
        $this->assertInstanceOf(HasMany::class, $this->budget->budgetLines());
        $this->assertInstanceOf(BudgetLine::class, $this->budget->budgetLines()->getModel());
    }

    /** @test */
    public function it_belongs_to_vehicle()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->budget->vehicle());
        $this->assertInstanceOf(Vehicle::class, $this->budget->vehicle()->getModel());
    }
}
