<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CostRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(!is_numeric($value))
            return true;
        
        $this->attribute = $attribute;
        return $value % 5 == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if(!isset($this->attribute)){
            $this->attribute = 'cost';
        }
        return ucfirst($this->attribute) . ' should be multiples of 5.';
    }
}
