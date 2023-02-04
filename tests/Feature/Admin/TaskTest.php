<?php

use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;
use App\Models\{Task, User};

it('create task', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    $faker = Factory::create();

    $payload = [
        'staff_id' => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'title' => $faker->text(20),
        'description' => $faker->text(50),
        'tags'=> $faker->text(5),
        'file' => $faker->url(),
        'completion_date' => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s')
    ];

    $response = $this->withToken($token)
        ->json('POST', TASK_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'staff',
            'title',
            'description',
            'tags',
            'file',
            'completion_date'
        ]);

    $task = Task::find($response->json('id'));
    $this->assertEquals($task->title, $response->json('title'));
    $this->assertEquals($task->description, $response->json('description'));
    $this->assertEquals($task->tags, $response->json('tags'));
    $this->assertEquals($task->file, $response->json('file'));
    $this->assertEquals($task->completion_date, $response->json('completion_date'));
});

it('update task', function (
    $staff_id,
    $title,
    $description,
    $tags,
    $file,
    $completion_date
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Task::latest()->first()->id;
    $response = $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', TASK_URL, $id),
            array_filter(compact(
                'staff_id',
                'title',
                'description',
                'tags',
                'file',
                'completion_date'
            )))
        ->assertStatus(Response::HTTP_ACCEPTED);

    $task = Task::find($response->json('id'));
    $this->assertEquals($task->title, $response->json('title'));
    $this->assertEquals($task->description, $response->json('description'));
    $this->assertEquals($task->tags, $response->json('tags'));
    $this->assertEquals($task->file, $response->json('file'));
    $this->assertEquals($task->completion_date, $response->json('completion_date'));
})->with('tasks');

it('update task with wrong fields', function (
    $staff_id,
    $title,
    $description,
    $tags,
    $file,
    $completion_date
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Task::latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', TASK_URL, $id),
            array_filter(compact(
                'staff_id',
                'title',
                'description',
                'tags',
                'file',
                'completion_date'
            )))
        ->assertUnprocessable();
})->with('wrong_tasks');

it('read task list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Task::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', TASK_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'staff',
            'title',
            'description',
            'tags',
            'file',
            'completion_date'
        ]]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single task', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Task::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', TASK_URL, $id))
        ->assertOK()
        ->assertJsonStructure([
            'id',
            'staff',
            'title',
            'description',
            'tags',
            'file',
            'completion_date'
        ])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop task', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Task::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', TASK_URL, $id))
        ->assertNoContent();
    $this->assertEquals(Task::find($id), null);
});

it('bulk drop tasks', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Task::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', TASK_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(Task::find($id), null);
});
