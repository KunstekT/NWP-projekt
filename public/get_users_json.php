@php
$friends_ids_ment = App\Models\Friendship::where('user_id', Auth::id())->pluck('friend_id');
$friends_data_ment =  App\Models\User::whereIn('id', $friends_ids_ment)->get();
$friends_ment_all =[];
$friends_ment=[];
$friends_ment_all = $friends_data_ment->map(function ($friend) {
    return [
        'id' => $friend->id,
        'name' => $friend->name,
        'type' => 'friend',
    ];
});
header('Content-Type: application/json');
echo json_encode($friends_ment_all);
@endphp