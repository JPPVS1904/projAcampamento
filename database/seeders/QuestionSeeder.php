<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use App\Models\Option;
use App\Models\Section;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        // Create a default section
        $section = Section::create([
            'title' => 'Informações Gerais',
            'order' => 1
        ]);

        foreach ($categories as $category) {
            // Add a text question
            $q1 = Question::create([
                'text' => 'Quais são suas expectativas para este acampamento?',
                'type' => 'Aberta',
                'order' => 1,
                'section_id' => $section->id
            ]);
            $category->questions()->attach($q1->id);

            // Add a single choice question
            $q2 = Question::create([
                'text' => 'Você possui alguma restrição alimentar?',
                'type' => 'Fechada (Única Escolha)',
                'order' => 2,
                'section_id' => $section->id
            ]);
            Option::create(['question_id' => $q2->id, 'text' => 'Sim']);
            Option::create(['question_id' => $q2->id, 'text' => 'Não']);
            $category->questions()->attach($q2->id);

            // Add a multiple choice question
            $q3 = Question::create([
                'text' => 'Como você conheceu o acampamento?',
                'type' => 'Fechada (Múltipla Escolha)',
                'order' => 3,
                'section_id' => $section->id
            ]);
            Option::create(['question_id' => $q3->id, 'text' => 'Amigos']);
            Option::create(['question_id' => $q3->id, 'text' => 'Igreja']);
            Option::create(['question_id' => $q3->id, 'text' => 'Redes Sociais']);
            $category->questions()->attach($q3->id);
        }
    }
}
