<!DOCTYPE html>
<html>
<head>
    <title>Chat Application</title>
</head>
<body>
@include('partials._navbar')
    <h1>Chat</h1>
    
    <div id="messages">
        @foreach($messages as $message)
            <p>{{ $message->message }}</p>
        @endforeach
    </div>
    
    <form method="post" action="/chat/send">
        @csrf
        <input type="text" name="message" placeholder="Type your message...">
        <button type="submit">Send