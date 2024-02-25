<?php

namespace Database\Factories;

use Exception;
use Faker\Provider\Lorem;
use Givebutter\LaravelCustomFields\Enums\CustomFieldType;
use Givebutter\LaravelCustomFields\Models\CustomField;
use Illuminate\Database\Eloquent\Factories\Factory;

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
     *
     * @return array
     */
    public function definition()
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
    public function withTypeCheckbox()
    {
        $this->model->type = CustomFieldType::CHECKBOX;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeNumber()
    {
        $this->model->type = CustomFieldType::NUMBER;

        return $this;
    }

    /**
     * @param mixed $answerCount
     * @return $this
     * @throws Exception
     */
    public function withTypeRadio($answerCount = 3)
    {
        $this->model->type = CustomFieldType::RADIO;

        return $this->withAnswers($answerCount);
    }

    /**
     * @param mixed $optionCount
     * @return $this
     * @throws Exception
     */
    public function withTypeSelect($optionCount = 3)
    {
        $this->model->type = CustomFieldType::SELECT;

        return $this->withAnswers($optionCount);
    }

    /**
     * @return $this
     */
    public function withTypeText()
    {
        $this->model->type = CustomFieldType::TEXT;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeTextArea()
    {
        $this->model->type = CustomFieldType::TEXTAREA;

        return $this;
    }

    /**
     * @param $defaultValue
     * @return $this
     */
    public function withTypeMultiCheckbox()
    {
        $this->model->type = CustomFieldType::MULTICHECKBOX;

        return $this;
    }

    /**
     * @return $this
     */
    public function withDefaultValue($defaultValue)
    {
        $this->model->default_value = $defaultValue;

        return $this;
    }

    /**
     * @param mixed $answers
     * @return $this
     * @throws Exception
     */
    public function withAnswers($answers = 3)
    {
        if (is_numeric($answers)) {
            $this->model->answers = Lorem::words($answers);

            return $this;
        }

        if (is_array($answers)) {
            $this->model->answers = $answers;

            return $this;
        }

        throw new Exception("withAnswers only accepts a number or an array");
    }
}
