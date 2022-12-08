<?php

use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateNotesTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /**
     * Testa o endpoint para atualizar uma anotação
     *
     * @return void
     */
    public function test_update_note()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $this->assertAuthenticated();

        $notes = Note::factory(4)->createOne([
            "user_id" => Auth::id(),
        ]);

        $note = Note::inRandomOrder()->first();

        $updateNote = Note::factory()->makeOne();

        $response = $this->putJson('/api/notes/' . strval($note->id), $updateNote->only(['title', 'body']))
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) use ($updateNote) {
                $json->where('data.body', $updateNote->body);
            });
    }

    /**
     * Testa o endpoint para atualizar uma anotação
     * inserindo o parent_id como null
     *
     * @return void
     */
    public function test_update_note_with_null_parentid()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $this->assertAuthenticated();

        Note::factory(4)->createOne([
            "user_id" => Auth::id(),
        ]);

        Note::factory(1)->createOne([
            "user_id" => Auth::id(),
            "parent_id" => Note::inRandomOrder()->first('id')
        ]);

        $note = Note::inRandomOrder()->first();

        $updateNote = Note::factory()->makeOne([
            "parent_id" => 0,
        ]);

        $response = $this->putJson('/api/notes/' . strval($note->id), $updateNote->only(['title', 'body', 'parent_id']));

        $response
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) use ($updateNote) {
                $json->where('data.parent_id', null);
            });
    }
}
