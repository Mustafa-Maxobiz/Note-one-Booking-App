<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Teacher private channel
Broadcast::channel('teacher.{teacherId}', function ($user, $teacherId) {
    return $user->role === 'teacher' && $user->teacher && $user->teacher->id == $teacherId;
});

// Student private channel
Broadcast::channel('student.{studentId}', function ($user, $studentId) {
    return $user->role === 'student' && $user->student && $user->student->id == $studentId;
});

// Admin channel for all notifications
Broadcast::channel('admin', function ($user) {
    return $user->role === 'admin';
});
