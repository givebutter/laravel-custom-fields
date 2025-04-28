<?php

namespace Database\Factories;

use Exception;
use Faker\Provider\Lorem;
use Givebutter\LaravelCustomFields\Enums\CustomFieldType;
use Givebutter\LaravelCustomFields\Models\CustomField;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

class CustomFieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomField::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        /** @var CustomFieldType $type */
        $type = $this->faker->randomElement(CustomFieldType::cases());

        return [
            'type' => $type->value,
            'required' => false,
            'title' => Lorem::sentence(3),
            'description' => Lorem::sentence(3),
            'answers' => $type->requiresAnswers() ? Lorem::words() : [],
        ];
    }

    public function withTypeCheckbox(): static
    {
        $this->model->type = CustomFieldType::CHECKBOX;

        return $this;
    }

    public function withTypeNumber(): static
    {
        $this->model->type = CustomFieldType::NUMBER;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function withTypeRadio(mixed $answerCount = 3): static
    {
        $this->model->type = CustomFieldType::RADIO;

        return $this->withAnswers($answerCount);
    }

    /**
     * @throws Exception
     */
    public function withTypeSelect(mixed $optionCount = 3): static
    {
        $this->model->type = CustomFieldType::SELECT;

        return $this->withAnswers($optionCount);
    }

    public function withTypeText(): static
    {
        $this->model->type = CustomFieldType::TEXT;

        return $this;
    }

    public function withTypeTextArea(): static
    {
        $this->model->type = CustomFieldType::TEXTAREA;

        return $this;
    }

    public function withTypeMultiselect(): static
    {
        $this->model->type = CustomFieldType::MULTISELECT;

        return $this;
    }


    public function withDefaultValue($defaultValue): static
    {
        $this->model->default_value = $defaultValue;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function withAnswers(mixed $answers = 3): static
    {
        if (is_numeric($answers)) {
            $this->model->answers = Lorem::words($answers);

            return $this;
        }

        if (is_array($answers)) {
            $this->model->answers = $answers;

            return $this;
        }

        throw new RuntimeException('withAnswers only accepts a number or an array');
    }
}
