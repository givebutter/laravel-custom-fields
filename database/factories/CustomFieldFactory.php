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

    /**
     * @return $this
     */
    public function withTypeCheckbox(): static
    {
        $this->model->type = CustomFieldType::CHECKBOX;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeNumber(): static
    {
        $this->model->type = CustomFieldType::NUMBER;

        return $this;
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    public function withTypeRadio(mixed $answerCount = 3): static
    {
        $this->model->type = CustomFieldType::RADIO;

        return $this->withAnswers($answerCount);
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    public function withTypeSelect(mixed $optionCount = 3): static
    {
        $this->model->type = CustomFieldType::SELECT;

        return $this->withAnswers($optionCount);
    }

    /**
     * @return $this
     */
    public function withTypeText(): static
    {
        $this->model->type = CustomFieldType::TEXT;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeTextArea(): static
    {
        $this->model->type = CustomFieldType::TEXTAREA;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeMultiCheckbox(): static
    {
        $this->model->type = CustomFieldType::MULTICHECKBOX;

        return $this;
    }

    /**
     * @return $this
     */
    public function withDefaultValue($defaultValue): static
    {
        $this->model->default_value = $defaultValue;

        return $this;
    }

    /**
     * @return $this
     *
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
