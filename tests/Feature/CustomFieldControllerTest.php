<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\Tests\Support\Survey;
use Givebutter\Tests\Support\SurveyResponse;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

        Route::post("/surveys/{$survey->id}/responses", function (Request $request) use ($survey) {
            $survey->validateCustomFields($request);

            return response('All good', 200);
        });

        $this
            ->post("/surveys/{$survey->id}/responses", [
                'custom_fields' => [
                    'email' => 'daniel@tighten.co',
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
            ])->assertJsonFragment(["field_1" => ["The selected `favorite_album` field is invalid."]]);
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

    /** @test */
    public function checkbox_can_pass_validation()
    {
        $survey = Survey::create();
        $surveyResponse = SurveyResponse::create();
        $survey->customfields()->save(
            CustomField::factory()->make([
                'title' => 'favorite_album',
                'type' => 'checkbox',
            ])
        );

        Route::post("/surveys/{$survey->id}/responses", function (Request $request) use ($survey, $surveyResponse) {
            $validator = $survey->validateCustomFields($request);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

            $surveyResponse->saveCustomfields($request->get('custom_fields'));

            return response('All good', 200);
        });

        $fieldId = CustomField::where('title', 'favorite_album')->first()->id;

        $this
            ->post("/surveys/{$survey->id}/responses", [
                'custom_fields' => [
                    $fieldId => 'on',
                ],
            ])->assertSee('All good');

        $this->assertTrue($surveyResponse->customFieldResponses()->first()->value);
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
            $surveyResponse->saveCustomfields(request('custom_fields'));

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
}
