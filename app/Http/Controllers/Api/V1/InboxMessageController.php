<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InboxMessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = \App\Models\InboxMessage::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['data' => $messages]);
    }

    public function markAsRead(Request $request, $id)
    {
        $message = \App\Models\InboxMessage::where('user_id', $request->user()->id)->findOrFail($id);
        $message->update(['is_read' => true]);
        return response()->json(['message' => 'Message marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        \App\Models\InboxMessage::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['message' => 'All messages marked as read']);
    }

    public function destroy(Request $request, $id)
    {
        $message = \App\Models\InboxMessage::where('user_id', $request->user()->id)->findOrFail($id);
        $message->delete();
        return response()->json(['message' => 'Message deleted successfully']);
    }
}
