<?php

namespace Tests\Feature\Patient;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PatientAppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $patient;
    protected $doctor;

    public function setUp(): void
    {
        parent::setUp();

        // Create a patient and doctor for testing
        $this->patient = User::factory()->create(['roleID' => 'patient']);
        $this->doctor = Doctor::factory()->create();

        // Authenticate the patient
        $this->actingAs($this->patient);
    }

    public function test_index_loads_appointments_successfully()
    {
        $appointment = Appointment::factory()->create([
            'UserID' => $this->patient->UserID,
            'DoctorID' => $this->doctor->DoctorID,
            'Status' => 'pending',
        ]);

        $response = $this->get(route('patient.appointments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('appointments');
        $response->assertViewHas('pendingCount', 1);
        $response->assertViewHas('approvedCount', 0);
    }

    public function test_store_creates_appointment_successfully()
    {
        $data = [
            'appointment_date' => now()->addDays(1)->toDateString(),
            'appointment_time' => '10:00',
            'reason' => 'Routine checkup',
            'symptoms' => 'Fever and cough',
            'notes' => 'First-time visit',
            'doctor_id' => $this->doctor->DoctorID,
        ];

        $response = $this->postJson(route('patient.appointments.store'), $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Appointment created successfully']);

        $this->assertDatabaseHas('appointments', [
            'UserID' => $this->patient->UserID,
            'DoctorID' => $data['doctor_id'],
            'Reason' => $data['reason'],
        ]);
    }

    public function test_update_appointment_successfully()
    {
        $appointment = Appointment::factory()->create([
            'UserID' => $this->patient->UserID,
            'DoctorID' => $this->doctor->DoctorID,
            'Status' => 'pending',
        ]);

        $updateData = [
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '11:00',
            'reason' => 'Follow-up check',
            'symptoms' => 'Headache',
            'notes' => 'Needs further tests',
            'doctor_id' => $this->doctor->DoctorID,
        ];

        $response = $this->putJson(route('patient.appointments.update', $appointment->AppointmentID), $updateData);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Cập nhật cuộc hẹn thành công!']);

        $this->assertDatabaseHas('appointments', [
            'AppointmentID' => $appointment->AppointmentID,
            'Reason' => $updateData['reason'],
        ]);
    }

    public function test_destroy_appointment_successfully()
    {
        $appointment = Appointment::factory()->create([
            'UserID' => $this->patient->UserID,
            'DoctorID' => $this->doctor->DoctorID,
            'Status' => 'pending',
        ]);

        $response = $this->deleteJson(route('patient.appointments.destroy', $appointment->AppointmentID));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Hủy cuộc hẹn thành công!']);

        $this->assertDatabaseMissing('appointments', [
            'AppointmentID' => $appointment->AppointmentID,
        ]);
    }

    public function test_show_appointment_details_successfully()
    {
        $appointment = Appointment::factory()->create([
            'UserID' => $this->patient->UserID,
            'DoctorID' => $this->doctor->DoctorID,
        ]);

        $response = $this->getJson(route('patient.appointments.show', $appointment->AppointmentID));

        $response->assertStatus(200);
        $response->assertJson(["AppointmentDate" => $appointment->AppointmentDate]);
    }

    public function test_show_detail_includes_doctor_information()
    {
        $appointment = Appointment::factory()->create([
            'UserID' => $this->patient->UserID,
            'DoctorID' => $this->doctor->DoctorID,
        ]);

        $response = $this->getJson(route('patient.appointments.showDetail', $appointment->AppointmentID));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'AppointmentDate',
            'AppointmentTime',
            'DoctorName',
            'Status',
            'Reason',
            'Symptoms',
            'Notes'
        ]);
    }
}
