<?php

namespace App\Http\Controllers;

use App\Models\Camera;

class CameraController extends Controller
{
    public function stream(Camera $camera)
    {
        return view('camera.stream', compact('camera'));
    }
}
