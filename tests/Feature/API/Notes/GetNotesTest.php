<?php

namespace Tests\Feature\API\Notes;

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

class GetNotesTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /**
     * Testa o endpoint para pegar todas as anotações do usuário
     *
     * @return void
     */
    public function test_get_notes()
    {
        $request = Request::create('/api/notes', 'GET');

        Sanctum::actingAs(
            User::factory()->create()
        );

        $this->assertAuthenticated();

        $notes = Note::factory(4)->create([
            "user_id" => Auth::id(),
        ]);

        $response = $this->getJson('/api/notes')
            ->assertSuccessful()
            ->assertJsonCount(4, 'data')
            ->assertExactJson(
                NoteResource::collection($notes)->response($request)->getData(true)
            );
    }

    /**
     * Testa a proteção da rota
     *
     * @return void
     */
    public function test_get_notes_without_auth()
    {
        $user = User::factory()->createOne();

        Note::factory(4)->create([
            "user_id" => $user->id,
        ]);

        $this->assertGuest();

        $response = $this->getJson('/api/notes')
            ->assertUnauthorized();
    }

    /**
     * Testa o endpoint para pegar a árvore de anotações
     *
     * @return void
     */
    public function test_get_notes_tree()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $this->assertAuthenticated();

        $notes = Note::factory(4)->create([
            "user_id" => Auth::id(),
        ]);

        Note::factory()->create([
            "parent_id" => Note::find(1),
            "user_id" => Auth::id(),
        ]);

        $response = $this->getJson('/api/notes/tree');

        $response->assertSuccessful()
            ->assertJsonCount($notes->count() + 2, 'items')
            ->assertJsonStructure(['items' => [
                '*' => [
                    'id',
                    'children',
                    'hasChildren',
                    'isExpanded',
                    'data' => [
                        'title'
                    ]
                ]
            ], 'rootId']);
    }

    /*
     * Testa o endpoint para pegar anotação pelo id
     *
     * @return void
     */
    public function test_get_note_by_id()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $this->assertAuthenticated();

        Note::factory(4)->create([
            "user_id" => Auth::id(),
        ]);

        $note = Note::inRandomOrder()->first();

        $response = $this->getJson('/api/notes/' . $note->id);

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) use ($note) {
            $json->whereType('data', 'array');
            $json->where('data', $note->makeHidden(['created_at', 'updated_at']));
        });
    }
}
