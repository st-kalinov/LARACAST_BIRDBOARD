<?php

namespace Tests\Feature;

use App\Project;
use App\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;

class GenerateActivityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function creating_a_project_generates_activity()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();

        $this->assertCount(1, $project->activity);

        tap($project->activity->last(), function ($activity) {
            $this->assertEquals('created_project', $activity->description);
            $this->assertNull($activity->changes);
        });

    }

    /**
     * @test
     */
    public function updating_a_project_generates_activity()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();
        $originalTitle = $project->title;

        $project->update(['title' => 'Changed']);

        $this->assertCount(2, $project->activity);

        tap($project->activity->last(), function ($activity) use ($originalTitle) {
            $this->assertEquals('updated_project', $activity->description);

            $expected = [
                'before' => ['title' => $originalTitle],
                'after' => ['title' => 'Changed']
            ];

            $this->assertEquals($expected, $activity->changes);
        });
    }

    /**
     * @test
     */
    public function creating_a_new_task_generates_project_activity()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->create();
        $project->addTask('Some task');

        $this->assertCount(2, $project->activity);

        tap($project->activity->last(), function($activity) {
            $this->assertEquals('created_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            $this->assertEquals('Some task', $activity->subject->body);
        });
    }

    /**
     * @test
     */
    public function completing_a_task_generates_project_activity()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'foobar',
                'completed' => true,
            ]);

        $this->assertCount(3, $project->activity);

        tap($project->activity->last(), function($activity) {
            $this->assertEquals('completed_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
        });
    }

    /**
     * @test
     */
    public function incompleting_a_task_generates_project_activity()
    {
        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'foobar',
                'completed' => true,
            ]);

        $this->assertCount(3, $project->activity);

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'foobar',
                'completed' => false,
            ]);

        $project->refresh();

        $this->assertCount(4, $project->activity);
        $this->assertEquals('incompleted_task', $project->activity->last()->description);
    }

    /**
     * @test
     */
    public function deleting_a_task_generates_activity()
    {
        $this->withoutExceptionHandling();

        /**
         * @var ProjectFactory $projectFactory
         */
        $projectFactory = app(ProjectFactory::class);

        /**
         * @var Project $project
         */
        $project = $projectFactory->withTasks(1)->create();
        $project->tasks[0]->delete();

        $this->assertCount(3, $project->activity);
        $this->assertEquals('deleted_task', $project->activity->last()->description);

    }
}
