<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

/**
 * If the current field has a value, the other fields (by name) must be empty.
 */
class OthersEmpty implements Rule, ValidatorAwareRule {

    /**
     * The validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Fields to check with.
     */
    private $fields;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($fields) {
        $this->fields = collect($fields);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $validator = $this->validator;

        // All fine if this is empty
        if($value == null || $value == '')
            return true;

        // All defined fields must be empty
        return $this->fields
                ->map(function($field) {
                    return trim($field);
                })
                ->every(function($field) use($validator) {
                    $data = $validator->getData();
                    return !isset($data[$field])
                        || $data[$field] == null
                        || $data[$field] == '';
                });
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return trans('validation.others_empty');
    }

    /**
     * Set the current validator.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator) {
        $this->validator = $validator;
        return $this;
    }
}
