<?php

namespace Database\Factories;

use Exception;
use Faker\Provider\Lorem;
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
            CustomField::TYPE_CHECKBOX => false,
            CustomField::TYPE_NUMBER => false,
            CustomField::TYPE_RADIO => true,
            CustomField::TYPE_SELECT => true,
            CustomField::TYPE_TEXT => false,
            CustomField::TYPE_TEXTAREA => false,
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
        $this->model->type = CustomField::TYPE_CHECKBOX;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeNumber()
    {
        $this->model->type = CustomField::TYPE_NUMBER;

        return $this;
    }

    /**
     * @param mixed $answerCount
     * @return $this
     * @throws Exception
     */
    public function withTypeRadio($answerCount = 3)
    {
        $this->model->type = CustomField::TYPE_RADIO;

        return $this->withAnswers($answerCount);
    }

    /**
     * @param mixed $optionCount
     * @return $this
     * @throws Exception
     */
    public function withTypeSelect($optionCount = 3)
    {
        $this->model->type = CustomField::TYPE_SELECT;

        return $this->withAnswers($optionCount);
    }

    /**
     * @return $this
     */
    public function withTypeText()
    {
        $this->model->type = CustomField::TYPE_TEXT;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeTextArea()
    {
        $this->model->type = CustomField::TYPE_TEXTAREA;

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
