<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\Tests\Support\Survey;
use Givebutter\Tests\Support\SurveyResponse;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class CustomFieldControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function valid_data_passes_controller_validation(): void
    {
        $survey = Survey::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'email',
                'type' => 'text',
            ])
        );

        $field = $survey->customFields()->first();

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey) {
            $survey->validateCustomFields($request)->validate();

            return response('All good', 200);
        });

        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $field->id => 'daniel@tighten.co',
                ],
            ])->assertOk();
    }

    /** @test */
    public function can_overwrite_response_values(): void
    {
        /** @var Survey $survey */
        $survey = Survey::create();

        /** @var SurveyResponse $surveyResponse */
        $surveyResponse = SurveyResponse::create();

        $field = $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'email',
                'type' => 'text',
            ])
        );

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey, $surveyResponse) {
            $survey->validateCustomFields($request)->validate();

            $surveyResponse->saveCustomFields($request->custom_fields);

            return response('All good', 200);
        });

        // first time
        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $field->id => 'daniel@tighten.co',
                ],
            ])->assertOk();

        $this->assertSame(1, $field->responses()->count());
        $this->assertSame('daniel@tighten.co', $field->responses()->first()->value);

        // second time
        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $field->id => 'clint@givebutter.com',
                ],
            ])->assertOk();

        $this->assertSame(1, $field->responses()->count());
        $this->assertSame('clint@givebutter.com', $field->responses()->first()->value);
    }

    /** @test */
    public function invalid_data_throws_validation_exception(): void
    {
        /** @var Survey $survey */
        $survey = Survey::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'favorite_album',
                'type' => 'select',
                'answers' => ['Tha Carter', 'Tha Carter II', 'Tha Carter III'],
            ])
        );

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey) {
            $validator = $survey->validateCustomFields($request->custom_fields);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'favorite_album')->first()->id;

        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $fieldId => 'Yeezus',
                ],
            ])
            ->assertJsonFragment(['field_1' => ['The selected favorite_album is invalid.']]);
    }

    /** @test */
    public function non_required_fields_can_be_left_null_for_validation(): void
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

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey) {
            $validator = $survey->validateCustomFields($request);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'favorite_album')->first()->id;

        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $fieldId => null,
                ],
            ])->assertSee('All good');
    }

    /**
     * @test
     *
     * @dataProvider checkboxChoices
     */
    public function checkbox_can_pass_validation(mixed $value, callable $assert): void
    {
        $survey = Survey::create();
        $surveyResponse = SurveyResponse::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'Favorite Album',
                'type' => 'checkbox',
            ])
        );

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey, $surveyResponse) {
            $validator = $survey->validateCustomFields($request);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            $surveyResponse->saveCustomFields($request->get('custom_fields'));

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'Favorite Album')->value('id');

        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $fieldId => $value,
                ],
            ])->assertSee('All good');

        $assert($this, $surveyResponse);
    }

    /**
     * @test
     */
    public function multiselect_can_pass_validation(): void
    {
        $survey = Survey::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'Favorite Album',
                'type' => 'multiselect',
                'answers' => ['Tha Carter', 'Tha Carter II', 'Tha Carter III'],
            ])
        );

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey) {
            $validator = $survey->validateCustomFields($request);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'Favorite Album')->value('id');

        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $fieldId => [
                        'Tha Carter',
                        'Tha Carter III',
                    ],
                ],
            ])->assertSee('All good');
    }

    /**
     * @test
     */
    public function multiselect_can_overwrite_values(): void
    {
        $survey = Survey::create();
        $surveyResponse = SurveyResponse::create();
        $field = $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'Favorite Album',
                'type' => 'multiselect',
                'answers' => ['Tha Carter', 'Tha Carter II', 'Tha Carter III'],
            ])
        );

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey, $surveyResponse) {
            $survey->validateCustomFields($request);

            $surveyResponse->saveCustomFields($request->custom_fields);

            return response('All good', 200);
        });

        // first time
        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $field->id => [
                        'Tha Carter II',
                        'Tha Carter III',
                    ],
                ],
            ])->assertOk();

        $this->assertSame(1, $field->responses()->count());
        $this->assertSame([
            'Tha Carter II',
            'Tha Carter III',
        ], $field->responses()->first()->value);

        // second time
        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $field->id => [
                        'Tha Carter I',
                    ],
                ],
            ])->assertOk();

        $this->assertSame(1, $field->responses()->count());
        $this->assertSame([
            'Tha Carter I',
        ], $field->responses()->first()->value);
    }

    public static function checkboxChoices(): iterable
    {
        yield 'true' => [
            true,
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"true"' => [
            'true',
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
            'false',
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
            '1',
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
            '0',
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"on"' => [
            'on',
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"off"' => [
            'off',
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertFalse($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"yes"' => [
            'yes',
            function (TestCase $test, SurveyResponse $surveyResponse) {
                $test->assertTrue($surveyResponse->customFieldResponses()->first()->value);
            },
        ];

        yield '"no"' => [
            'no',
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
    public function fields_can_be_saved_from_request_with_convenience_method(): void
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

        Route::post("/surveys/$survey->id/responses", static function () use ($surveyResponse) {
            $surveyResponse->saveCustomFields(request('custom_fields'));

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'favorite_album')->first()->id;

        $this
            ->post("/surveys/$survey->id/responses", [
                'custom_fields' => [
                    $fieldId => 'Tha Carter',
                ],
            ])->assertOk();

        $this->assertCount(1, $surveyResponse->customFieldResponses);
    }

    /** @test */
    public function can_validate_request_with_no_custom_fields(): void
    {
        $survey = Survey::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'favorite_album',
                'type' => 'select',
                'answers' => ['Tha Carter', 'Tha Carter II', 'Tha Carter III'],
            ])
        );

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey) {
            $survey->validateCustomFields($request)->validate();

            return response('All good', 200);
        });

        $this->post("/surveys/$survey->id/responses")
            ->assertOk();
    }

    /** @test */
    public function fails_validation_on_request_with_no_custom_fields_but_is_required(): void
    {
        $survey = Survey::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'favorite_album',
                'type' => 'select',
                'answers' => ['Tha Carter', 'Tha Carter II', 'Tha Carter III'],
                'required' => true,
            ])
        );

        Route::post("/surveys/$survey->id/responses", static function (Request $request) use ($survey) {
            $survey->validateCustomFields($request)->validate();

            return response('All good', 200);
        });

        $fieldId = $survey->customFields()->value('id');

        try {
            $this->post("/surveys/$survey->id/responses");

            $this->fail('ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('field_'.$fieldId, $e->errors());
        }
    }
}
