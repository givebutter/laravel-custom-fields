<?php


namespace Database\Factories;


use Faker\Provider\Lorem;
use Faker\Provider\Text;
use Givebutter\LaravelCustomFields\Models\CustomField;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CustomFieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomField::class;

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

        $type = array_keys($typesRequireAnswers)[rand(0, count($typesRequireAnswers))]; // Pick a random type
        $answers = [];
        if ($typesRequireAnswers) {
            $answers = Lorem::words();
        }

        return [
            'type' => $type,
            'title' => Lorem::sentence(3),
            'description' => Lorem::sentence(3),
            'answers' => $answers,
            'required' => false,
        ];
    }

    public function withTypeCheckbox()
    {
        $this->model->type = CustomField::TYPE_CHECKBOX;

        return $this;
    }

    public function withTypeNumber()
    {
        $this->model->type = CustomField::TYPE_NUMBER;

        return $this;
    }

    public function withTypeRadio($answerCount = 3)
    {
        $this->model->type = CustomField::TYPE_RADIO;

        return $this->withAnswers($answerCount);
    }

    public function withTypeSelect($optionCount = 3)
    {
        $this->model->type = CustomField::TYPE_SELECT;

        return $this->withAnswers($optionCount);
    }

    public function withTypeText()
    {
        $this->model->type = CustomField::TYPE_TEXT;

        return $this;
    }

    public function withTypeTextArea()
    {
        $this->model->type = CustomField::TYPE_TEXTAREA;

        return $this;
    }

    public function withDefaultValue($defaultValue)
    {
        $this->model->default_value = $defaultValue;

        return $this;
    }

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

        throw new \Exception("withAnswers only accepts a number or an array");

    }
}