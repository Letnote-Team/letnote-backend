<?php

namespace Tests\Feature\API\Notes;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateNotesTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /**
     * Testa o endpoint para criação de anotações
     *
     * @return void
     */
    public function test_create_notes()
    {
        $request = Request::create('/api/notes', 'POST');

        Sanctum::actingAs(
            User::factory()->create()
        );

        $this->assertAuthenticated();

        $note = Note::factory()->makeOne([
            "user_id" => Auth::id(),
        ]);

        $response = $this->postJson(
            '/api/notes',
            $note->only(['title', 'body', 'parent_id'])
        );

        $response->assertCreated();
    }
}
