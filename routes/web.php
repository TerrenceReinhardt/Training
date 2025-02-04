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
    return view('upload');
});
Route::post('/upload-excel', function (Request $request) {
    $request->validate([
        'file' => 'required|mimes:xlsx'
    ]);

    $file = $request->file('file');

    try {
        $response = Http::attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->post('http://localhost:4000/upload');

        if ($response->successful()) {
            return back()->with('message', 'Users imported successfully!');
        } else {
            return back()->with('error', 'Error while importing users.');
        }
    } catch (\Exception $e) {
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
});
