<?php

// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:notifications.view')->only(['index']);
        $this->middleware('permission:notifications.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Notification::with('order');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $notifications = $query->latest()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }
}