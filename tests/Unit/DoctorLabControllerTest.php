<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Laboratory;
use App\Models\LaboratoryType;
use App\Models\Doctor;
use App\Models\User;

class DoctorLabControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $response = $this->get(route('doctor.lab'));
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

        $response = $this->postJson(route('doctor.lab.store'), $data);
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

        $response = $this->putJson(route('doctor.lab.update', $lab->LaboratoryID), $updateData);
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

        $response = $this->deleteJson(route('doctor.lab.destroy', $lab->LaboratoryID));
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Laboratory assignment deleted successfully']);

        $this->assertDatabaseMissing('laboratories', ['LaboratoryID' => $lab->LaboratoryID]);
    }

    public function test_show_lab_details()
    {
        $lab = Laboratory::factory()->create([
            'LaboratoryTypeID' => $this->labType->LaboratoryTypeID,
            'UserID' => $this->patient->UserID,
            'DoctorID' => $this->doctor->DoctorID,
        ]);

        $response = $this->getJson(route('doctor.lab.show', $lab->LaboratoryID));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'labType',
            'patientName',
            'doctorName',
            'labDate',
            'labTime',
            'price',
            'result'
        ]);
    }

    public function test_generate_report()
    {
        $response = $this->postJson(route('doctor.lab.generateReport'));
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Report generated successfully']);
    }
}
