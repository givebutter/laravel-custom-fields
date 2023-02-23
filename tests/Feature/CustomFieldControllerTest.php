<?php

namespace Givebutter\Tests\Feature;

use Carbon\CarbonImmutable;
use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\ValueObjects\DateRange;
use Givebutter\Tests\Support\Survey;
use Givebutter\Tests\Support\SurveyResponse;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;

class CustomFieldControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function valid_data_passes_controller_validation()
    {
        $survey = Survey::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'email',
                'type' => 'text',
            ])
        );

        $field = $survey->customFields()->first();

        Route::post("/surveys/{$survey->id}/responses", function (Request $request) use ($survey) {
            $survey->validateCustomFields($request);

            return response('All good', 200);
        });

        $this
            ->post("/surveys/{$survey->id}/responses", [
                'custom_fields' => [
                    $field->id => 'daniel@tighten.co',
                ],
            ])->assertOk();
    }


    /** @test */
    public function invalid_data_throws_validation_exception()
    {
        $survey = Survey::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'favorite_album',
                'type' => 'select',
                'answers' => ['Tha Carter', 'Tha Carter II', 'Tha Carter III'],
            ])
        );

        Route::post("/surveys/{$survey->id}/responses", function (Request $request) use ($survey) {
            $validator = $survey->validateCustomFields($request->custom_fields);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'favorite_album')->first()->id;

        $this
            ->post("/surveys/{$survey->id}/responses", [
                'custom_fields' => [
                    $fieldId => 'Yeezus',
                ],
            ])->assertJsonFragment(["field_1" => ["The selected favorite_album is invalid."]]);
    }

    /** @test */
    public function non_required_fields_can_be_left_null_for_validation()
    {
        $survey = Survey::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'favorite_album',
                'type' => 'select',
                'answers' => ['Tha Carter', 'Tha Carter II', 'Tha Carter III'],
                'required' => false,
            ])
        );

        Route::post("/surveys/{$survey->id}/responses", function (Request $request) use ($survey) {
            $validator = $survey->validateCustomFields($request);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'favorite_album')->first()->id;

        $this
            ->post("/surveys/{$survey->id}/responses", [
                'custom_fields' => [
                    $fieldId => null,
                ],
            ])->assertSee('All good');
    }

    /**
     * @test
     * @dataProvider checkboxChoices
     */
    public function checkbox_can_pass_validation(mixed $value, callable $assert)
    {
        $survey = Survey::create();
        $surveyResponse = SurveyResponse::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'Favorite Album',
                'type' => 'checkbox',
            ])
        );

        Route::post("/surveys/{$survey->id}/responses", function (Request $request) use ($survey, $surveyResponse) {
            $validator = $survey->validateCustomFields($request);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            $surveyResponse->saveCustomFields($request->get('custom_fields'));

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'Favorite Album')->value('id');

        $this
            ->post("/surveys/{$survey->id}/responses", [
                'custom_fields' => [
                    $fieldId => $value,
                ],
            ])->assertSee('All good');

        $assert($this, $surveyResponse);
    }

    public function checkboxChoices(): iterable
    {
        yield 'true' => [
            true,
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"true"' => [
            "true",
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield 'false' => [
            false,
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"false"' => [
            "false",
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '1' => [
            1,
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"1"' => [
            "1",
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '0' => [
            0,
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"0"' => [
            "0",
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"on"' => [
            "on",
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"off"' => [
            "off",
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"yes"' => [
            "yes",
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"no"' => [
            "no",
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield 'null' => [
            null,
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];
    }

    /** @test */
    public function fields_can_be_saved_from_request_with_convenience_method()
    {
        $survey = Survey::create();
        $surveyResponse = SurveyResponse::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'favorite_album',
                'type' => 'select',
                'answers' => ['Tha Carter', 'Tha Carter II', 'Tha Carter III'],
            ])
        );

        Route::post("/surveys/{$survey->id}/responses", function () use ($surveyResponse) {
            $surveyResponse->saveCustomFields(request('custom_fields'));

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'favorite_album')->first()->id;

        $this
            ->post("/surveys/{$survey->id}/responses", [
                'custom_fields' => [
                    $fieldId => 'Tha Carter',
                ],
            ])->assertOk();

        $this->assertCount(1, $surveyResponse->customFieldResponses);
    }

    /** @test */
    public function can_save_date_range_fields()
    {
        $survey = Survey::create();
        $surveyResponse = SurveyResponse::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'My Date Range',
                'type' => 'daterange',
            ])
        );

        Route::post("/surveys/{$survey->id}/responses", function (Request $request) use ($survey, $surveyResponse) {
            $validator = $survey->validateCustomFields($request);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            $surveyResponse->saveCustomFields($request->get('custom_fields'));

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'My Date Range')->first()->id;

        $this->post("/surveys/{$survey->id}/responses", [
            'custom_fields' => [
                $fieldId => ['2021-01-01', '2021-01-31'],
            ],
        ])->assertOk();

        $this->assertCount(1, $surveyResponse->customFieldResponses);
        $this->assertTrue(
            DateRange::make(
                CarbonImmutable::parse('2021-01-01'),
                CarbonImmutable::parse('2021-01-31'),
            )->equals($surveyResponse->customFieldResponses->first()->value)
        );
    }

    /**
     * @test
     * @dataProvider daterangeChoices
     * @param array $value
     * @param callable $assert
     */
    public function daterange_can_pass_validation(array $value, callable $assert)
    {
        $survey = Survey::create();
        $surveyResponse = SurveyResponse::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'My Date Range',
                'type' => 'daterange',
            ])
        );

        Route::post("/surveys/{$survey->id}/responses", function (Request $request) use ($survey, $surveyResponse) {
            $validator = $survey->validateCustomFields($request);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            $surveyResponse->saveCustomFields($request->get('custom_fields'));

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'My Date Range')->first()->id;

        $response = $this
            ->post("/surveys/{$survey->id}/responses", [
                'custom_fields' => [
                    $fieldId => $value,
                ],
            ])->assertOk();

        $assert($this, $response, $surveyResponse);
    }

    public function daterangeChoices()
    {
        yield 'standard valid' => [
            ['2021-01-01', '2021-01-31'],
            function (TestCase $test, TestResponse $response, SurveyResponse $surveyResponse) {
                $response->assertOk();
                $test->assertCount(1, $surveyResponse->customFieldResponses);
                $test->assertTrue(
                    DateRange::make(
                        CarbonImmutable::parse('2021-01-01'),
                        CarbonImmutable::parse('2021-01-31'),
                    )->equals($surveyResponse->customFieldResponses->first()->value)
                );
            },
        ];

        yield '[INVALID] start date is after end date' => [
            ['2021-01-31', '2021-01-01'],
            function (TestCase $test, TestResponse $response, SurveyResponse $surveyResponse) {
                $response->assertJsonValidationErrors('field_1.0');
                $test->assertCount(0, $surveyResponse->customFieldResponses);
            },
        ];
    }
}
