<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
Route::get('/upload', function () {
    return view('upload');  // Show the upload form
});
Route::post('/upload-excel', function (Request $request) {
    // Validate the uploaded file
    $request->validate([
        'file' => 'required|mimes:xlsx'
    ]);

    // Get the uploaded file
    $file = $request->file('file');

    // Send the file to Node.js server using HTTP
    try {
        $response = Http::attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->post('http://localhost:4000/upload');  // Node.js server URL

        // Check if the upload was successful
        if ($response->successful()) {
            return back()->with('message', 'Users imported successfully!');
        } else {
            return back()->with('error', 'Error while importing users.');
        }
    } catch (\Exception $e) {
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
});
