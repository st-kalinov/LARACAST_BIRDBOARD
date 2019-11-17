<?php

namespace Tests\Unit;

use App\Project;
use App\User;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_has_a_user()
    {
        $user = $this->singIn();

        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = new ProjectFactory();

        /**
         * @var Project $project
         */
        $project = $projectFactory->ownedBy($user)->create();

        $this->assertEquals($user->id, $project->activity->first()->user->id);
    }
}
