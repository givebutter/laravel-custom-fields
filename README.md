# Laravel Custom Fields.
## Add custom fields to your Laravel models.
The purpose of this package is to allow you, the user, to add custom fields to Laravel models.
This is done by the use of a few models (by default they are called `Custom Fields` and `Custom Field Responses`) and traits which allow you to relate them to the models already in your application.

## Installation
`composer require givebutter/laravel-custom-fields`

This package publishes a few migrations. To run these in their default configuration, simply run `artisan migrate` after installation.
*You can customize the table names using config options. More on that later.*

## An Example App
For the purposes of the documentation, lets use the example of a Survey taking app. Administrators might use a backend to create `Surveys` full of questions and end users might then fill out those surveys, generating `SurveyResponses`.

## Adding Custom Fields
To add basic custom field support, We'll simply pull in the `HasCusomFields` trait at the top of our `Survey` model.

We then have the ability to add a field to a survey simply by running 
```php
$survey->customFields()->create([
    'title' => `favorite_album`,
    `description` => `What is your favorite album.`
    `type` => `text`
]);
```

## Custom Field Types
Custom fields may be any of 5 types:

 - `text` : Free entry fields which are stored as strings. Try to use these for simple inputs as they have a max-length of 256 characters.
 - `textarea` : Free entry fields which are stored as text columns. Use these for longer bits of text that may not fit within 256 characters.
 - `radio` : These are multi-select fields, which require you to pass and `answers` property.*
 - `select`: These are multi-select fields, which require you to pass and `answers` property.*
 - `checkbox`: Boolean fields.
 - `number` : Free entry fields stored as integers.

 In the future we may provide front-end scaffolding for these fields, but for now, that's up to you.

 * *The `select` and `checkbox` field types require you to fill the `answers` property on the field. This is a simple array of strings, which are valid responses for the field. For example:

 ```php
 $survey->customFields()->create([
    'title' => 'favorite_album',
    'description' => 'What is your favorite album.'
    'type' => 'select',
    'answers' => ['Lil Wayne - Tha Carter II', 'Gang Starr - Moment of Truth'], 
]);
```

## Adding Custom Field Responses
Adding custom field response support is basically the same as adding field support. We'll simply pull in the `HasCusomFieldResponses` trait at the top of our `SurveyResponses` model.

We then have the ability to add a response to a given survey like so:

```php
$favoriteAlbumField->responses()->create([
    'value' => 'Lil Wayne - Tha Carter II'
]);
```
