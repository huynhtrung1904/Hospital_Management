<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Laboratory;
use App\Models\LaboratoryType;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminLabControllerTest extends TestCase
{
    use RefreshDatabase;

    // Define the properties
    protected $doctor;
    protected $patient;
    protected $labType;

    public function setUp(): void
    {
        parent::setUp();

        // Seed some default data
        $this->doctor = Doctor::factory()->create();
        $this->patient = User::factory()->create(['roleID' => 'patient']);
        $this->labType = LaboratoryType::factory()->create();
    }

    public function test_lab_page_loads_successfully()
    {
        $response = $this->get(route('admin.lab'));
        $response->assertStatus(200);
    }

    public function test_store_lab()
    {
        $data = [
            'lab_type' => $this->labType->LaboratoryTypeID,
            'user_id' => $this->patient->UserID,
            'doctor_id' => $this->doctor->DoctorID,
            'lab_date' => now()->toDateString(),
            'lab_time' => now()->toTimeString(),
            'price' => 500,
        ];

        $response = $this->postJson(route('admin.lab.store'), $data);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Laboratory assignment created successfully']);

        $this->assertDatabaseHas('laboratories', [
            'LaboratoryTypeID' => $data['lab_type'],
            'UserID' => $data['user_id'],
        ]);
    }

    public function test_update_lab()
    {
        $lab = Laboratory::factory()->create([
            'LaboratoryTypeID' => $this->labType->LaboratoryTypeID,
            'UserID' => $this->patient->UserID,
            'DoctorID' => $this->doctor->DoctorID,
        ]);

        $updateData = [
            'lab_type' => $this->labType->LaboratoryTypeID,
            'user_id' => $this->patient->UserID,
            'doctor_id' => $this->doctor->DoctorID,
            'lab_date' => now()->toDateString(),
            'lab_time' => now()->toTimeString(),
            'price' => 1000,
        ];

        $response = $this->putJson(route('admin.lab.update', $lab->LaboratoryID), $updateData);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Lab test updated successfully']);

        $this->assertDatabaseHas('laboratories', [
            'LaboratoryID' => $lab->LaboratoryID,
            'TotalPrice' => 1000,
        ]);
    }

    public function test_delete_lab()
    {
        $lab = Laboratory::factory()->create([
            'LaboratoryTypeID' => $this->labType->LaboratoryTypeID,
            'UserID' => $this->patient->UserID,
        ]);

        $response = $this->deleteJson(route('admin.lab.destroy', $lab->LaboratoryID));
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Lab test deleted successfully']);

        $this->assertDatabaseMissing('laboratories', ['LaboratoryID' => $lab->LaboratoryID]);
    }

    public function test_store_lab_type()
    {
        $data = [
            'name' => 'Blood Test',
            'description' => 'Routine blood test',
            'price' => 50,
        ];

        $response = $this->postJson(route('admin.lab_type.store'), $data);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Lab type created successfully']);

        $this->assertDatabaseHas('laboratory_types', [
            'LaboratoryTypeName' => $data['name'],
        ]);
    }

    public function test_update_lab_type()
    {
        $labType = LaboratoryType::factory()->create();

        $updateData = [
            'name' => 'Updated Test',
            'description' => 'Updated description',
            'price' => 100,
        ];

        $response = $this->putJson(route('admin.lab_type.update', $labType->LaboratoryTypeID), $updateData);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Lab type updated successfully']);

        $this->assertDatabaseHas('laboratory_types', [
            'LaboratoryTypeID' => $labType->LaboratoryTypeID,
            'LaboratoryTypeName' => 'Updated Test',
        ]);
    }

    public function test_delete_lab_type()
    {
        $labType = LaboratoryType::factory()->create();

        $response = $this->deleteJson(route('admin.lab_type.destroy', $labType->LaboratoryTypeID));
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Lab type deleted successfully']);

        $this->assertDatabaseMissing('laboratory_types', ['LaboratoryTypeID' => $labType->LaboratoryTypeID]);
    }
}
