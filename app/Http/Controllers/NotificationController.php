<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * View full notifications page.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(15);
        $unreadCount = $user->unreadNotifications()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'unread_count'  => $unreadCount,
                'notifications' => $user->notifications()->take(10)->get()->map(function ($n) {
                    return [
                        'id'         => $n->id,
                        'read_at'    => $n->read_at ? $n->read_at->diffForHumans() : null,
                        'is_read'    => !is_null($n->read_at),
                        'created_at' => $n->created_at->diffForHumans(),
                        'data'       => $n->data,
                    ];
                }),
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Quick JSON endpoint for the navbar dropdown bell.
     */
    public function quick(Request $request): JsonResponse
    {
        $user = Auth::user();
        $notifications = $user->notifications()->take(8)->get()->map(function ($n) {
            return [
                'id'         => $n->id,
                'read_at'    => $n->read_at ? $n->read_at->diffForHumans() : null,
                'is_read'    => !is_null($n->read_at),
                'created_at' => $n->created_at->diffForHumans(),
                'title'      => $n->data['title'] ?? 'Notification',
                'message'    => $n->data['message'] ?? '',
                'action_url' => $n->data['action_url'] ?? null,
                'type'       => $n->data['type'] ?? 'info',
            ];
        });

        return response()->json([
            'unread_count'  => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($request->filled('redirect')) {
            return redirect($request->input('redirect'));
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification deleted.');
    }
}
