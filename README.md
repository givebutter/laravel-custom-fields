# Laravel Custom Fields

> ⚠️ Warning: these docs are incomplete and may reference unsupported functions. This is a pre-release version that is not yet ready for production use.

Laravel Custom Fields is a package that allows you to add custom fields to any Laravel model and associate responses to those fields with other models.

[![Latest Stable Version](https://poser.pugx.org/givebutter/laravel-custom-fields/v/stable)](https://packagist.org/packages/givebutter/laravel-custom-fields) [![Total Downloads](https://poser.pugx.org/givebutter/laravel-custom-fields/downloads)](https://packagist.org/packages/givebutter/laravel-custom-fields) [![License](https://poser.pugx.org/givebutter/laravel-custom-fields/license)](https://packagist.org/packages/givebutter/laravel-custom-fields)

## Installation

To get started, add the `givebutter/laravel-custom-fields` package to your `composer.json` file and update your dependencies:

```bash
composer require givebutter/laravel-custom-fields
```

Publish the migration:
```bash
php artisan vendor:publish --provider="Givebutter\LaravelCustomFields\LaravelCustomFieldsServiceProvider" --tag="migrations"
```

Run the migration:
```bash
php artisan migrate
```

*You can customize the table names using configuration options. More on that later.*

## An example - Survey App
For the purposes of the documentation, lets use the example of a Survey building app. Administrators might use a backend to create `Surveys` full of questions and end users might then fill out those surveys, generating `SurveyResponses`.

## Preparing your models

### Adding custom fields

To add basic custom field support, simply add the `HasCusomFields` trait at the top of your model:

```php
use Illuminate\Database\Eloquent\Model;
use Givebutter\LaravelCustomFields\Traits\HasCusomFields;

class Survey extends Model
{
    use HasCustomFields;

    // ...
}
```

### Adding custom field responses

Next, we add support to store custom field responses. We'll simply pull in the `HasCusomFieldResponses` trait at the top of our `SurveyResponse` model.

```php
use Illuminate\Database\Eloquent\Model;
use Givebutter\LaravelCustomFields\Traits\HasCusomFieldResponses;

class SurveyResponse extends Model
{
    use HasCustomFieldResponses;

    // ...
}
```

## Basic usage

### Creating fields

You can add a field to a model like this:

```php
$survey->customFields()->create([
    'title' => `What is your name?`,
    `type` => `text`
]);
```

Each field can contain the following. More on these later:

`title` : The title / question of your custom field.
`description` :  The description of your field. Useful for providing more context to user filling out fields.
`type` :  The type of field you're creating. Available types are outlined in the next section.
`required` :  A boolean representing whether a field is required or not.
`answers` : An array of acceptable values for fields that have user-selection. 

### Creating field responses

To store a response on a field, you can do this:

```php
$field->responses()->create([
    'value' => 'John Doe'
]);
```

### Retrieving fields

To retrieve custom fields, use the `customFields` relation:

```php
$survey->customFields()->get();
```

### Retrieving field responses

To retrieve the responses on a field,  use the `responses()` relation:

```php
$field->responses()->get();
```

## Custom field types
Custom fields may be any of 5 types:

 - `text` : Free entry fields which are stored as strings. Use these for simple inputs as they have a max-length of 255 characters.
 - `textarea` : Free entry fields which are stored as text columns. Use these for longer bits of text that may not fit within the `text` field.
 - `radio` : These are multi-select fields, which require you to pass an `answers` property.*
 - `select` : These are multi-select fields, which require you to pass an `answers` property.*
 - `checkbox` : Boolean fields.

In general, these field types correspond to their respective HTML elements. In the future we may provide front-end scaffolding for these fields, but for now, that's up to you.

*The `radio` and `select` field types require you to fill the `answers` property on the field. This is a simple array of strings, which are valid responses for the field. For example:

 ```php
 $survey->customFields()->create([
    'title' => 'What is your favorite color?',
    'type' => 'select',
    'answers' => ['Red', 'Green', 'Blue', 'Yellow'], 
]);
```

## Validating responses


### Validation helpers

In most cases, you'll want to validate responses to your custom fields before saving them. You can do so by calling the `validateCustomFields()` function on your model:

```php

$responses = [
    '1' => "John Doe",
    '2' => "Blue"
];
$survey->validateCustomFields($responses);
```

You can also pass in a `Request` object:

```php
use Request;

class FooController extends Controller {

    public function index(Request $request) 
    {
        $validation = $survey->validateCustomFields($request);

        // ...
    }

}
```

```html
<form>
    <input type="text" name="custom_fields[]" />
</form>
```
When using a `Request` object, the input key should be an array of values 



### Implicit validation rules

The 5 supported field types described above automatically have the following validation rules applied to them:

 - `text` : `string|max:255`
 - `textarea` : `string`
 - `radio` : `string|max:255|in:answers`
 - `select`: `string|max:255|in:answers`
 - `checkbox`: `in:0,1`

*Important: when using checkboxes, it is important you POST unchecked boxes as well, otherwise your response data may be incomplete.*

### Required fields

Because of how common they are, required fields have native support in this package. To mark a field as required, simply set `required` to true when creating a custom field.

```php
$survey->customFields()->create([
    'title' => 'Do you love Laravel?',
    'type' => 'radio',
    'answers' => ['Yes', 'YES'],
    'required' => true
]);
```

### Custom validation rules

Along with the built in validation rules, you can apply your own rules to the any custom field. For example, if you wanted to validate a field was an integer between 1 and 10, you could do the following:

```php
$survey->customFields()->create([
    'title' => 'Pick a number 1-10',
    'type' => 'text',
    'validation_rules' => 'integer|min:1|max:10'
]);
```

Remember, the `validation_rules` supports any of the [available validation rules](https://laravel.com/docs/6.x/validation#available-validation-rules) in Laravel.


### Validation Rule Sets
-> nah?
In some cases, it's easier and more practical to define validation rules sets. For example, in our Survey app, if we wanted to offer a 

## Saving Responses

To store responses to custom fields, just call `saveCustomFields()` and pass in an array of values

The `saveCustomFields` function can take in a Request or array.

```php
$surveyResponse->saveCustomFields(['
   
']);
```

If you're submitting a form request, you can easily:

```php
Use App\...
$surveyResponse->saveCustomFields($request->input);
```

## Querying responses

You can query for responses on any field by using the `WhereCustomField()` scope. The function takes in the field object and the value you're looking for. To learn more about query scopes visit [this link](https://laravel.com/docs/6.x/eloquent#query-scopes).

For example, if you wanted to find all `SurveyResponses` with a `large` T-shirt size, perform the following query:

```php
Use App\Models\SurveyResponse;
Use App\Models\SurveyResponse;

$field = 

SurveyResponse::WhereCustomField($field, 'large')->get();
```

## Ordering

You can change the order of custom fields on a model by using the `order` function. Pass in either an array or `Collection` of ids. The index position of the field represent the order position of it. 

```php
$survey->orderCustomFields([2, 4, 5]); // Field with id 2 will be ordered first.
```

You can also manually change the value of the `order` column:

```php
$field->order = 3; 
$field->save();
```

By default, custom fields are returned in ascending order when retrieved via the relation:
```php
$survey->customFields()->get(); // Returned in ascending order.
```

## Configuration

To publish the configuration file, run the following command:
```bash
php artisan vendor:publish --provider="Givebutter\LaravelCustomFields\LaravelCustomFieldsServiceProvider" --tag="config"
```

The configuration file should now be published in `config/custom-fields.php`. The available options and their usage are explained inside the published file.

## License
Released under the [MIT](https://choosealicense.com/licenses/mit/) license. See [LICENSE](LICENSE.md) for more information.
