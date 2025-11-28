<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;

class FileManagerController extends Controller
{
    /**
     * Display the file manager interface within admin layout
     */
    public function index()
    {
        return view('admin.filemanager.index');
    }
}
