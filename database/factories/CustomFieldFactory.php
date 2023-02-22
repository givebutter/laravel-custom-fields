<?php

namespace Database\Factories;

use Exception;
use Faker\Provider\Lorem;
use Givebutter\LaravelCustomFields\Enums\CustomFieldTypes;
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
        $typesRequireAnswers = [
            CustomFieldTypes::TEXT->value => false,
            CustomFieldTypes::RADIO->value => true,
            CustomFieldTypes::SELECT->value => true,
            CustomFieldTypes::NUMBER->value => false,
            CustomFieldTypes::CHECKBOX->value => false,
            CustomFieldTypes::TEXTAREA->value => false,
        ];

        $type = array_keys($typesRequireAnswers)[rand(0, count($typesRequireAnswers) - 1)]; // Pick a random type

        return [
            'type' => $type,
            'required' => false,
            'title' => Lorem::sentence(3),
            'description' => Lorem::sentence(3),
            'answers' => $typesRequireAnswers ? Lorem::words() : [],
        ];
    }

    /**
     * @return $this
     */
    public function withTypeCheckbox()
    {
        $this->model->type = CustomFieldTypes::CHECKBOX;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeNumber()
    {
        $this->model->type = CustomFieldTypes::NUMBER;

        return $this;
    }

    /**
     * @param mixed $answerCount
     * @return $this
     * @throws Exception
     */
    public function withTypeRadio($answerCount = 3)
    {
        $this->model->type = CustomFieldTypes::RADIO;

        return $this->withAnswers($answerCount);
    }

    /**
     * @param mixed $optionCount
     * @return $this
     * @throws Exception
     */
    public function withTypeSelect($optionCount = 3)
    {
        $this->model->type = CustomFieldTypes::SELECT;

        return $this->withAnswers($optionCount);
    }

    /**
     * @return $this
     */
    public function withTypeText()
    {
        $this->model->type = CustomFieldTypes::TEXT;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeTextArea()
    {
        $this->model->type = CustomFieldTypes::TEXTAREA;

        return $this;
    }

    /**
     * @param $defaultValue
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
