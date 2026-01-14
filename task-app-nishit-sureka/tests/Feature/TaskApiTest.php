<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_create_a_task_successfully()
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'status' => 'pending'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'status' => 'pending',
        ]);
    }

    public function test_fails_when_creating_a_tasks_without_required_fields()
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422);
    }

    public function test_lists_all_tasks()
    {
        Task::factory()->count(5)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
    }

    public function test_update_a_task()
    {
        $task = Task::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'completed',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    public function test_delete_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

}