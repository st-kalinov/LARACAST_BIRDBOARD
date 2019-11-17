<?php

namespace Tests\Unit;

use App\Project;
use App\Task;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_has_a_path()
    {
        $project = factory('App\Project')->create();

        $this->assertEquals('/projects/' . $project->id, $project->path());
    }

    /**
     * @test
     */
    public function it_belongs_to_an_owner()
    {
        $project = factory('App\Project')->create();

        $this->assertInstanceOf('App\User', $project->owner);
    }

    /**
     * @test
     */
    public function it_can_add_a_task()
    {
        $project = factory('App\Project')->create();

        $task = $project->addTask('Test task');

        $this->assertCount(1, $project->tasks);
        $this->assertTrue($project->tasks->contains($task));
    }

    /**
    * @test
    */
    public function it_can_add_multiple_tasks()
    {
        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create();
        $tasks = [
            ['body' => 'Task 1'],
            ['body' => 'Task 2'],
        ];

        $project->addTasks($tasks);

        $this->assertCount(2, $project->tasks);

        $this->assertEquals($tasks[0]['body'], $project->tasks[0]->body);
        $this->assertEquals($tasks[1]['body'], $project->tasks[1]->body);
    }

    /**
     * @test
     */
    public function it_can_invite_a_user()
    {
        $project = factory('App\Project')->create();

        $project->invite($newUser = factory(User::class)->create());

        $this->assertTrue($project->members->contains($newUser));
    }
}
