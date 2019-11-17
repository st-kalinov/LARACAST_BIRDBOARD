<?php

namespace Tests\Feature;

use App\Project;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_project_can_have_tasks()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->withTasks(1)
            ->create();

        /*
         * Ili gorniq red ili tozi komentar
         *
         * $project = auth()->user()->projects()->create(
          factory(Project::class)->raw()
        );
        */

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', ['body' => 'Test Task']);

        $this->get($project->path())
            ->assertSee('Test Task');
    }

    /**
     * @test
     */
    public function a_task_can_be_updated()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->ownedBy($this->singIn())
            ->withTasks(1)
            ->create();

        $this->patch($project->tasks->first()->path(), [
            'body' => 'changed',
        ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
        ]);
    }

    /**
     * @test
     */
    public function a_task_can_be_completed()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->ownedBy($this->singIn())
            ->withTasks(1)
            ->create();

        $this->patch($project->tasks->first()->path(), [
            'body' => 'changed',
            'completed' => true,
        ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
            'completed' => true,
        ]);
    }

    /**
     * @test
     */
    public function a_task_can_be_marked_as_incomplete()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->ownedBy($this->singIn())
            ->withTasks(1)
            ->create();

        $this->patch($project->tasks->first()->path(), [
            'body' => 'changed',
            'completed' => true,
        ]);

        $this->patch($project->tasks->first()->path(), [
            'body' => 'changed',
            'completed' => false,
        ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
            'completed' => false,
        ]);
    }

     /**
     * @test
     */
    public function only_the_owner_of_a_project_may_update_a_task()
    {
        $this->singIn();

        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->withTasks(1)
            ->create();

        $this->patch($project->tasks[0]->path(), ['body' => 'changed'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);
    }


    /**
     * @test
     */
    public function only_the_owner_of_a_project_may_add_tasks()
    {
        $this->singIn();

        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);
        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
    }

    /**
     * @test
     */
    public function a_task_requires_a_body()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory
            ->ownedBy($this->singIn())
            ->create();

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->post($project->path() . '/tasks',  $attributes)->assertSessionHasErrors('body');
    }
}
