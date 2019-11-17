<?php

namespace Tests\Unit;

use App\Project;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_has_projects()
    {
        $user = factory('App\User')->create();

        $this->assertInstanceOf(Collection::class, $user->projects);
    }

    /**
     * @test
     */
    public function a_user_has_accessible_projects()
    {
        $john = $this->singIn();

        /**
         * @var ProjectFactory $projectFactory ;
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $projectFactory->ownedBy($john)->create();

        $this->assertCount(1, $john->accessibleProjects());

        /**
         * @var User $sally
         */
        $sally = factory(User::class)->create();

        /**
        * @var User $nick
        */
        $nick = factory(User::class)->create();

        /**
         * @var Project $sallyProject
         */
        $sallyProject = $projectFactory->ownedBy($sally)->create();
        $sallyProject->invite($nick);

        $this->assertCount(1, $john->accessibleProjects());
        $sallyProject->invite($john);

        $this->assertCount(2, $john->accessibleProjects());

    }
}
