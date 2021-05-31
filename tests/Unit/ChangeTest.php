<?php

namespace Tests\Unit;

use App\Http\Controllers\AppointmentController;
use PHPUnit\Framework\TestCase;

class ChangeTest extends TestCase
{
    public function test_it_can_parse_valid_input()
    {
        $data = 'created_at_ASC';

        $result = AppointmentController::parseOrderBy($data);

        $expected = ['orderBy' => 'ASC', 'column' => 'created_at'];
        $this->assertEquals($result, $expected);
    }
}
