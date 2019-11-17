<?php

namespace Tests\Feature;

use App\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     */
    public function guests_cannot_manage_a_project()
    {
        $project = factory('App\Project')->create();

        $this->post('/projects', $project->toArray())->assertRedirect('login');
        $this->get('/projects')->assertRedirect('login');
        $this->get('/projects/create')->assertRedirect('login');
        $this->get($project->path() . '/edit')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
    }

    /**
     * @test
     */
    public function a_user_can_create_a_project()
    {
        $this->singIn();

        $this->get('/projects/create')->assertStatus(200);

        $attributes = factory(Project::class)->raw();

        $response = $this->followingRedirects()->post('/projects', $attributes);

        $response
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    /**
    * @test
    */
    public function tasks_can_be_included_as_part_of_project_creation()
    {
        $this->singIn();

        $attributes = factory(Project::class)->raw();

        $attributes['tasks'] = [
            ['body' => 'Task 1'],
            ['body' => 'Task 2'],
        ];

        $this->post('/projects', $attributes);

        $this->assertCount(2, Project::first()->tasks);
    }

    /**
     * @test
     */
    public function a_user_can_see_all_projects_they_have_been_invited_to_on_their_dashboard()
    {
        $this->withoutExceptionHandling();
        $user = $this->singIn();

        /**
         * @var ProjectFactory $projectFactory;
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();
        $project->invite($user);

        $this->get('/projects')->assertSee($project->title);
    }

    /**
     * @test
     */
    public function guests_cannot_delete_projects()
    {
        /**
         * @var ProjectFactory $projectFactory;
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();

        $this->delete($project->path())
            ->assertRedirect('/login');

        $user = $this->singIn();

        $this->delete($project->path())
            ->assertStatus(403);

        $project->invite($user);

        $this->actingAs($user)
            ->delete($project->path())
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function a_user_can_delete_a_project()
    {
        /**
         * @var ProjectFactory $projectFactory;
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->ownedBy($this->singIn())
            ->create();

        $this->delete($project->path())
            ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects', $project->only('id'));
    }

    /**
     * @test
     */
    public function a_user_can_update_a_project()
    {
        /**
         * @var ProjectFactory $projectFactory;
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->ownedBy($this->singIn())
            ->create();

        $this->patch($project->path(), $attributes = ['title' => 'Changed', 'description' => 'Changed', 'notes' => 'Changed'])
            ->assertRedirect($project->path());

        $this->get($project->path() . '/edit')->assertOk();

        $this->assertDatabaseHas('projects', $attributes);
    }

    /**
     * @test
     */
    public function a_user_can_update_a_projects_general_notes()
    {
        /**
         * @var ProjectFactory $projectFactory;
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->ownedBy($this->singIn())
            ->create();

        $this->patch($project->path(), $attributes = ['notes' => 'Changed'])
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $attributes);
    }

    /**
     * @test
     */
    public function a_user_can_view_their_project()
    {
        /**
         * @var ProjectFactory $projectFactory;
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->ownedBy($this->singIn())
            ->create();

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee(Str::limit($project->description, 100));
    }

    /**
     * @test
     */
    public function an_authenticated_user_cannot_view_the_projects_of_others()
    {
        $this->singIn();

        $project = factory('App\Project')->create();

        $this->get($project->path())->assertStatus(403);
    }

    /**
     * @test
     */
    public function an_authenticated_user_cannot_update_the_projects_of_others()
    {
        $this->singIn();

        $project = factory('App\Project')->create();

        $this->patch($project->path())->assertStatus(403);
    }


    /**
     * @test
     */
    public function a_project_requires_a_title()
    {
        $this->singIn();

        $attributes = factory('App\Project')->raw(['title' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /**
     * @test
     */
    public function a_project_requires_a_description()
    {
        $this->singIn();

        $attributes = factory('App\Project')->raw(['description' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }

}
