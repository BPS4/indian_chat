@vite('resources/js/app.js')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Room</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .chat-box {
            height: 450px;
            overflow-y: auto;
            border-radius: 5px;
            padding: 15px;
            background: #f8f9fa;
        }

        .message-left {
            background: #e9ecef;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 8px;
            max-width: 75%;
        }

        .message-right {
            background: #d1e7ff;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 8px;
            max-width: 75%;
            margin-left: auto;
            text-align: right;
        }

        .message-sender {
            font-weight: bold;
            margin-bottom: 2px;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="card shadow-sm mx-auto" style="max-width: 700px;">
            <div class="card-header">
                <h5 class="mb-0">Chat - Welcome, {{ $sernder_name }}</h5>
            </div>

            <div class="card-body">
                <div class="chat-box" id="chatBox">

                    <!-- Example messages -->

                    <div class="message-right">
                        <div class="message-sender">You:</div>
                        Hi there! How are you?
                    </div>

                    <div class="message-left">
                        <div class="message-sender"></div>
                    
                    </div>

                </div>
            </div>

            <div class="card-footer">
                <form id="messageForm" class="d-flex gap-2">
                    <input type="hidden" id="senderName" value="{{ $sernder_name }}"> <!-- dynamic -->
                    <input type="text" name="message" id="messageInput" class="form-control"
                        placeholder="Type your message..." required>
                    <button class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        document.getElementById("messageForm").addEventListener("submit", function(e) {
            e.preventDefault(); // stop form from reloading page

            let message = document.getElementById("messageInput").value;
            let sender = document.getElementById("senderName").value;


            if (message.trim() === "") return;

            // alert('hi');
            // Send AJAX POST
            fetch("/send-message", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        sender_id: sender,
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Append sender message to chat box
                    const chatBox = document.getElementById("chatBox");

                    chatBox.innerHTML += `
            <div class="message-right">
                <div class="message-sender">You:</div>
                ${message}
            </div>
        `;

                    chatBox.scrollTop = chatBox.scrollHeight; // always scroll to bottom
                });

            document.getElementById("messageInput").value = ""; // clear input
        });

        window.onload = function() {
        console.log('Echo loaded:', Echo !== undefined);
            Echo.channel('user-message')
                .listen('.MessageSent', function(data) {
                //          console.log('EVENT RECEIVED:', data);
                // alert(data.sender_id);

                    const chatBox = document.getElementById("chatBox");
                    const currentUser = "{{ $sernder_name }}"; // your logged-in username

                    // If message comes from OTHER USER → show left side message
                    if (data.sender_id !== currentUser) {

                        chatBox.innerHTML += `
                    <div class="message-left">
                        <div class="message-sender">${data.sender_id}:</div>
                        ${data.message}
                    </div>
                `;
                    }

                    // Scroll to bottom
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        };
    </script>



</body>

</html>
