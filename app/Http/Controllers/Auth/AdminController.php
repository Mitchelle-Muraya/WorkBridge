<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;  // ✅ Import Worker model
use App\Models\Client;  // ✅ Import Client model
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin.dashboard');
    }

    public function manageWorkers() {
        $workers = Worker::all();  // ✅ Make sure Worker model exists
        return view('admin.workers', ['workers' => $workers]);
    }

    public function manageClients() {
        $clients = Client::all();  // ✅ Make sure Client model exists
        return view('admin.clients', ['clients' => $clients]);
    }
}
