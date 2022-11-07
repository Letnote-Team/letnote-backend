<?php

namespace Database\Factories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $blocks = [];

        for ($i = 1; $i <= rand(1, 10); $i++) {
            $type = $this->faker->randomElement(['header', 'paragraph']);
            $isHeader = $type == "headerl";
            $block = [
                "id" => $this->faker->randomAscii(6),
                "type" => $type,
                "data" => [
                    "text" => $isHeader ? $this->faker->sentence(4) : $this->faker->text,
                ],
            ];


            if ($isHeader) {
                $block['data'] = ['level' => $this->faker->numberBetween(1, 6)];
            }

            $blocks[] = $block;
        }

        $note = [
            'title' => $this->faker->sentence(2),
            'body' => [
                "time" => 1553964811649,
                "blocks" => $blocks,
                "vesrion" => "2.12.3"
            ],
            'parent_id' => (Note::count() != 0 && $this->faker->boolean(50))
                ? Note::inRandomOrder()->first('id')
                : null,
        ];

        return $note;
    }
}
