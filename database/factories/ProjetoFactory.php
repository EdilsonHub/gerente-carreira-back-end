<?php

namespace Database\Factories;

use App\Models\Projeto;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjetoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Projeto::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome' => $this->faker->name(),
            'descricao' => "Uma descrição singular, apenas para testes",
            'data_limite' => (new \DateTime())->add((new \DateInterval("P7D")))
        ];
    }
}