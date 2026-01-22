<?php

return [
    'first_name' => [
        'required' => 'First Name is required.',
        'min' => 'First Name must be at least :min characters.',
        'regex' => 'First Name format is invalid.',
    ],
    'last_name' => [
        'required' => 'Last Name is required.',
        'min' => 'Last Name must be at least :min characters.',
        'regex' => 'Last Name format is invalid.',
    ],
    'email' => [
        'required' => 'Email faild is required.',
        'email' => 'Email must be a valid email address.',
        'unique' => 'this email is already exsits',
    ],
    'phone' => [
        'required' => 'Phone Number is required.',
        'numeric' => 'Phone Number must be a number.',
    ],
    'password' => [
        'required' => 'Password is required.',
        'confirmed' => 'Password confirmation does not match.',
        'password' => 'Password is not strong enough.',
        'min' => 'Password must be at least :min characters.',
    ],
    'password_confirmation' => [
        'required' => 'Password confirmation field is required.',
    ],

];