<?php



namespace App\Http\Controllers;



use App\Models\Notification;

use App\Services\NotificationService;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;



class NotificationController extends Controller

{

    public function index()

    {

        $notifications = Notification::where('user_id', Auth::id())

            ->orderBy('id', 'desc')

            ->paginate(15);



        return view('notifications.index', compact('notifications'));

    }



    public function markAsRead(Notification $notification)

    {

        if ($notification->user_id !== Auth::id()) {

            abort(403);

        }

        \Log::info('Marking notification as read', [
            'notification_id' => $notification->id,
            'user_id' => Auth::id()
        ]);

        $notification->markAsRead();

        return response()->json(['success' => true]);

    }



    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function destroy(Notification $notification)

    {

        if ($notification->user_id !== Auth::id()) {

            abort(403);

        }

        \Log::info('Deleting notification', [
            'notification_id' => $notification->id,
            'user_id' => Auth::id()
        ]);

        $notification->delete();

        return redirect()->route('notifications.index')->with('success', 'Notification deleted successfully!');

    }

    public function markAllAsRead()
    {
        $userId = Auth::id();
        $updatedCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        \Log::info('Mark all as read called', [
            'user_id' => $userId,
            'updated_count' => $updatedCount
        ]);

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount
        ]);
    }

    public function getPusherConfig()
    {
        $config = [
            'key' => \App\Models\SystemSetting::getValue('pusher_app_key'),
            'cluster' => \App\Models\SystemSetting::getValue('pusher_app_cluster', 'mt1'),
        ];

        return response()->json($config);
    }

    /**
     * Get recent notifications for dropdown
     */
    public function getRecentNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'type' => $notification->type
                ];
            })
        ]);
    }

}

