<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Models\Laboratory;
use App\Models\LaboratoryType;
use App\Models\Doctor;
use App\Models\User;

class LabController extends Controller
{
    public function lab()
    {
        $labTypes = LaboratoryType::all(); 
        $laboratories = Laboratory::with(['user', 'doctor', 'laboratoryType'])
            ->orderBy('LaboratoryDate', 'desc')
            ->get(); 
        $doctors = Doctor::with('user')->get();
      
        $patients = User::where('roleID', 'patient')->get();

      
        $totalTests = $laboratories->count();
        $pendingTests = $laboratories->where('Status', 'Pending')->count();
        $completedTests = $laboratories->where('Status', 'Completed')->count();
        $totalRevenue = $laboratories->sum('TotalPrice');
       
        return view('doctor.lab', compact(
            'labTypes',
            'laboratories',
            'doctors',
            'patients',
            'totalTests',
            'pendingTests',
            'completedTests',
            'totalRevenue'
        ));
    }


    public function store(Request $request)
        {
            \Log::info('Store Lab Request:', $request->all());
            $request->validate([
                'lab_type' => 'required|exists:laboratory_types,LaboratoryTypeID',
                'user_id' => 'required|exists:users,UserID', 
                'doctor_id' => 'required|exists:doctors,DoctorID',
                'lab_date' => 'required|date',
                'lab_time' => 'required',
                'price' => 'required|numeric|min:0',
            ]);
        
            try {
                Laboratory::create([
                    'LaboratoryTypeID' => $request->lab_type,
                    'UserID' => $request->user_id,
                    'DoctorID' => $request->doctor_id,
                    'LaboratoryDate' => $request->lab_date,
                    'LaboratoryTime' => $request->lab_time,
                    'TotalPrice' => $request->price,
                ]);
        
                return response()->json(['message' => 'Laboratory assignment created successfully']);
            } catch (\Exception $e) {
                \Log::error('Error creating laboratory: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to create laboratory(Controller)'], 500);
            }
        }
    


    public function update(Request $request, $id)
    {
        $request->validate([
            'lab_type' => 'required|exists:laboratory_types,LaboratoryTypeID',
            'patient_id' => 'required|exists:patients,PatientID',
            'doctor_id' => 'required|exists:doctors,DoctorID',
            'lab_date' => 'required|date',
            'lab_time' => 'required',
            'price' => 'required|numeric|min:0',     
        ]);

        $lab = Laboratory::findOrFail($id);
        $lab->update([
            'LaboratoryTypeID' => $request->lab_type,
            'PatientID' => $request->patient_id,
            'DoctorID' => $request->doctor_id,
            'LaboratoryDate' => $request->lab_date,
            'LaboratoryTime' => $request->lab_time,
            'TotalPrice' => $request->price,
            'Status' => $request->status,
        ]);

        return response()->json(['message' => 'Lab test updated successfully']);
    }


    public function destroy($id)
    {
        $lab = Laboratory::findOrFail($id);
        $lab->delete();

        return response()->json(['message' => 'Lab test deleted successfully']);
    }


   


    public function generateReport(Request $request)
    {
       
        return response()->json(['message' => 'Report generated successfully']);
    }

    public function show($id)
    {
        try {
           
            $lab = Laboratory::with(['laboratoryType', 'user', 'doctor'])->findOrFail($id);

          
            return response()->json([
                'labType' => $lab->laboratoryType->LaboratoryTypeName,
                'patientName' => $lab->user->FullName, 
                'doctorName' => $lab->doctor->user->FullName, 
                'labDate' => $lab->LaboratoryDate,
                'labTime' => $lab->LaboratoryTime,
                'price' => $lab->TotalPrice,          
                'result' => $lab->Result ?? 'Pending'
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi và trả về thông báo
            return response()->json([
                'error' => 'Failed to retrieve lab details.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateLab(Request $request, $id)
    {
     

        $request->validate([
            'labType' => 'required|exists:laboratory_types,LaboratoryTypeID',
            'userId' => 'required|exists:users,UserID',
            'doctorId' => 'required|exists:doctors,DoctorID',
            'labDate' => 'required|date',
            'labTime' => 'required',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            $lab = Laboratory::findOrFail($id);
            $lab->update([
                'LaboratoryTypeID' => $request->labType,
                'UserID' => $request->userId,
                'DoctorID' => $request->doctorId,
                'LaboratoryDate' => $request->labDate,
                'LaboratoryTime' => $request->labTime,
                'TotalPrice' => $request->price,
            ]);

            return response()->json(['message' => 'Laboratory assignment updated successfully']);
        } catch (\Exception $e) {
         
            return response()->json(['error' => 'Failed to update laboratory'], 500);
        }
    }

    public function destroyLab($id)
    {
        try {
            $lab = Laboratory::findOrFail($id); 
            $lab->delete(); // Xóa Lab
            return response()->json(['message' => 'Laboratory assignment deleted successfully.']);
        } catch (\Exception $e) {
          
            return response()->json(['error' => 'Failed to delete laboratory.'], 500);
        }
    }
} 
