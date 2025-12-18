<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudyMaterial\StoreStudyMaterialRequest;
use App\Http\Requests\StudyMaterial\UpdateStudyMaterialRequest;
use App\Models\StudyMaterial;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyMaterialController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();

        $materials = StudyMaterial::where('uploaded_by', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse('Study materials list', $materials);
    }

    public function store(StoreStudyMaterialRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $file = $request->file('file');

        $path = $file->store('study-materials', 'public');

        $material = StudyMaterial::create([
            'uploaded_by' => $user->id,
            'title' => $data['title'],
            'subject' => $data['subject'] ?? null,
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size_bytes' => $file->getSize(),
        ]);

        return $this->successResponse('Study material uploaded', $material, 201);
    }

    public function show(Request $request, StudyMaterial $studyMaterial)
    {
        $this->authorizeMaterial($request, $studyMaterial);

        return $this->successResponse('Study material details', $studyMaterial);
    }

    // public function update(UpdateStudyMaterialRequest $request, StudyMaterial $studyMaterial)
    // {
    //     $this->authorizeMaterial($request, $studyMaterial);

    //     $studyMaterial->update($request->validated());

    //     return $this->successResponse('Study material updated', $studyMaterial);
    // }
    public function update(UpdateStudyMaterialRequest $request, StudyMaterial $studyMaterial)
{
    $this->authorizeMaterial($request, $studyMaterial);

    // 1) Update العادي (title - subject - description)
    $studyMaterial->update($request->validated());

    // 2) لو فيه ملف جديد جاي مع الريكويست
    if ($request->hasFile('file')) {
        // احذف القديم
        Storage::disk('public')->delete($studyMaterial->file_path);

        // ارفع الجديد
        $file = $request->file('file');
        $path = $file->store('study-materials', 'public');

        // حدّث بيانات الملف
        $studyMaterial->update([
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size_bytes' => $file->getSize(),
        ]);
    }

    // 3) اعمل refresh عشان يرجع البيانات الجديدة
    $studyMaterial->refresh();

    return $this->successResponse('Study material updated', $studyMaterial);
}



    public function destroy(Request $request, StudyMaterial $studyMaterial)
    {
        $this->authorizeMaterial($request, $studyMaterial);

        if ($studyMaterial->file_path) {
            Storage::disk('public')->delete($studyMaterial->file_path);
        }

        $studyMaterial->delete();

        return $this->successResponse('Study material deleted');
    }

    protected function authorizeMaterial(Request $request, StudyMaterial $material): void
    {
        if ($material->uploaded_by !== $request->user()->id) {
            abort(403, 'You are not allowed to access this study material.');
        }
    }
}