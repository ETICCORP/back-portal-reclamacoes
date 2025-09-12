<?php

namespace Tests\Unit\User;

use App\Models\User;
use Tests\AbstractTest;

class UserTest extends AbstractTest
{
    protected $routeName = "user.index";
    protected $table = "users";
    protected $model = User::class;
}
