<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DigicamController extends Controller
{
     private $photoFolder;

    public function __construct()
    {
        // Folder untuk menyimpan foto
        $this->photoFolder = public_path('photos');
        if (!File::exists($this->photoFolder)) {
            File::makeDirectory($this->photoFolder, 0777, true);
        }
    }

    // Tampilkan halaman camera
    public function index()
    {
        // Ambil semua foto di folder
        $photos = File::files($this->photoFolder);
        return view('digicam.index', compact('photos'));
    }

    // Capture foto
    public function capture(Request $request)
    {
        $cmd = '"C:\\Program Files\\DigiCamControl\\DigiCamControlCmd.exe" '
            . '/folder "' . $this->photoFolder . '" '
            . '/filenameTemplate "photo_{counter}" '
            . '/capture /verbose';

        // Jalankan command
        $output = shell_exec($cmd);

        return redirect()->route('camera.index')->with('status', 'Photo captured!');
    }
}
