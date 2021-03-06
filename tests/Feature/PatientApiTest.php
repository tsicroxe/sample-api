<?php

namespace Tests\Feature;

use DateTime;
use Tests\TestCase;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;


class PatientApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * @return void
     */
    public function test_creating_a_patient()
    {
        $data = [
            'first_name' => $this->faker()->firstName,
            'last_name' => $this->faker()->lastName,
            'dob' => $this->faker()->dateTime()->format('c'),
            'phone' => $this->faker()->phoneNumber
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(201)
            ->assertJson(['patient' => $data]);
    }


    /**
     * @return void
     */
    public function test_making_an_api_get_request_without_params()
    {
        Patient::factory(1)->create();

        $response = $this->getJson('/api/patients');

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @return void
     */
    public function test_making_an_api_get_request_with_invalid_order_by()
    {
        $response = $this->getJson('/api/patients?order_by=id_DESCFSD');

        $response->assertJsonValidationErrors('order_by');
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

    /**
     * @return void
     */
    public function test_making_an_api_get_request_with_invalid_load_with()
    {
        $response = $this->getJson('/api/patients?load_with=appointments');

        $response->assertJsonValidationErrors('load_with');
    }

        /**
     * @return void
     */
    public function test_making_an_api_get_request_with_valid_load_with()
    {
        Appointment::factory(1)->create();

        $response = $this->getJson('/api/patients?load_with[]=appointments');

        $response->assertSeeText('appointments');
    }
    

            /**
     * @return void
     */
    public function test_making_an_api_get_request_with_valid_query()
    {
        $patient = Patient::factory(1)->create();
        $name = $patient->pluck('first_name')[0];

        $response = $this->getJson("/api/patients?query=$name, $name");

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
