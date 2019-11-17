<?php

namespace Tests\Feature;

use App\Project;
use App\User;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvatationsTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    */
    public function non_owners_may_not_invite_users()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();

        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post($project->path() . '/invitations')
            ->assertStatus(403);

        $project->invite($user);

        $this->actingAs($user)
            ->post($project->path() . '/invitations')
            ->assertStatus(403);
    }

    /**
    * @test
    */
    public function a_project_owner_can_invite_a_user()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();

        /**
        * @var User $userToInvite
        */
        $userToInvite = factory(User::class)->create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/invitations', [
                'email' => $userToInvite->email,
            ])
            ->assertRedirect($project->path());

        $this->assertTrue($project->members->contains($userToInvite));
    }

    /**
    * @test
    */
    public function the_invited_email_address_must_be_associated_with_a_valid_birdboard_account()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/invitations', [
                'email' => 'notauser@example.com',
                ])
            ->assertSessionHasErrors([
                'email' => 'The user you are inviting must have a Birdboard account.'
            ], null, 'invitations');

    }

    /**
     * @test
     */
    public function invited_users_may_update_project_details()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();

        $project->invite($newUser = factory(User::class)->create());

        $this->singIn($newUser);
        $this->post(action('ProjectTasksController@store', $project), $task = ['body' => 'Foo Task']);

        $this->assertDatabaseHas('tasks', $task);
    }
}
