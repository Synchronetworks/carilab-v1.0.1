<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function notificationList(Request $request)
    {
        $user = auth()->user();
        $user->last_notification_seen = now();
        $user->save();
        $perPage = $request->input('per_page', 10);

        $type = isset($request->type) ? $request->type : null;
        if ($type == 'mark_as_read') {
            if (count($user->unreadNotifications) > 0) {
                $user->unreadNotifications->markAsRead();
            }
        }
        $page = 1;
        $limit = 100;
        $notifications = $user->Notifications()->orderByDesc('created_at')->paginate($perPage);
        if ($request->has('user_type')) {
            $userType = $request->user_type;
            $notifications = $notifications->filter(function ($notification) use ($userType) {
                $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                return isset($data['data']['user_type']) && $data['data']['user_type'] === $userType;
            });
        }
        $all_unread_count = isset($user->unreadNotifications) ? $user->unreadNotifications->count() : 0;
        $items = NotificationResource::collection($notifications);
        $response = [
            'notification_data' => $items,
            'all_unread_count' => $all_unread_count,
            'message' => __('messages.mark_read'),
            'status' => true,
        ];

        return $response;
    }
}
