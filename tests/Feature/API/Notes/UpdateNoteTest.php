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
}
