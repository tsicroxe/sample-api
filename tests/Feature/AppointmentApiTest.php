<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentApiTest extends TestCase
{
    use WithFaker, RefreshDatabase;


        /**
     * @return void
     */
    public function test_creating_an_appointment()
    {
        $patient = Patient::factory(1)->create();
        $patient_id = $patient->pluck('id')[0];
        $data = [
            "patient_id" => $patient_id,
            "occurs_at" => "2005-08-15T00:00:00+00:00",
            "type" => $this->faker->name,
        ];

        $response = $this->postJson('/api/appointments', $data);

        $response->assertStatus(201)
            ->assertJson(['appointment' => $data]);
    }


    /**
     * @return void
     */
    public function test_making_an_api_get_request_without_params()
    {
        Appointment::factory(1)->create();

        $response = $this->getJson('/api/appointments');

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @return void
     */
    public function test_making_an_api_get_request_has_pagination()
    {
        Appointment::factory(20)->create();

        $response = $this->getJson('/api/appointments?per_page=5&page=2');

        $response
            ->assertStatus(200)
            ->assertSee('meta');
    }
}
