<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flydolk - Messages</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .message-container {
            height: calc(100vh - 150px); /* Adjust based on header/footer height */
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .conversation-list {
            border-right: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .conversation-item {
            cursor: pointer;
            border-bottom: 1px solid #e9ecef;
        }
        .conversation-item.active, .conversation-item:hover {
            background-color: #ffffff;
        }
        .chat-header {
            border-bottom: 1px solid #dee2e6;
        }
        .chat-body {
            overflow-y: auto;
        }
        .chat-bubble {
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            max-width: 75%;
        }
        .chat-bubble.sent {
            background-color: #0d6efd;
            color: white;
            border-bottom-right-radius: 0.25rem;
        }
        .chat-bubble.received {
            background-color: #e9ecef;
            color: #212529;
            border-bottom-left-radius: 0.25rem;
        }
        .chat-footer {
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>

    <!-- This assumes you have a header file included above -->
    
    <main class="container-fluid p-4">
        <!-- Messages Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Messages</h1>
            <button class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-pencil-square me-2"></i> New Message
            </button>
        </div>

        <div class="message-container d-flex">
            <!-- Left Panel: Conversation List -->
            <div class="col-md-4 col-lg-3 d-flex flex-column conversation-list p-0">
                <div class="p-3 border-bottom">
                    <input type="search" class="form-control" placeholder="Search messages...">
                </div>
                <div class="flex-grow-1" style="overflow-y: auto;">
                    <div class="conversation-item p-3 active">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-0">John Doe</h6>
                            <small class="text-muted">10:30 AM</small>
                        </div>
                        <p class="mb-0 text-muted text-truncate">Awesome, thank you for the update!</p>
                    </div>
                    <div class="conversation-item p-3">
                         <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-0">Jane Smith</h6>
                            <small class="text-muted">Yesterday</small>
                        </div>
                        <p class="mb-0 text-muted text-truncate">I have a question about order #ORD-00451...</p>
                    </div>
                     <div class="conversation-item p-3">
                         <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-0">Support Team</h6>
                            <small class="text-muted">2d ago</small>
                        </div>
                        <p class="mb-0 text-muted text-truncate">Reminder: System maintenance tonight.</p>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Chat Window -->
            <div class="col-md-8 col-lg-9 d-flex flex-column">
                <div class="chat-header p-3 d-flex align-items-center">
                    <h5 class="mb-0 fw-bold">John Doe</h5>
                </div>
                <div class="chat-body p-4 flex-grow-1">
                    <div class="d-flex justify-content-start mb-3">
                        <div class="chat-bubble received">
                            Hi, just checking on the status of my shipment.
                        </div>
                    </div>
                     <div class="d-flex justify-content-end mb-3">
                        <div class="chat-bubble sent">
                            Hello! Your order has been shipped and is scheduled to arrive tomorrow.
                        </div>
                    </div>
                     <div class="d-flex justify-content-start">
                        <div class="chat-bubble received">
                           Awesome, thank you for the update!
                        </div>
                    </div>
                </div>
                <div class="chat-footer p-3 bg-light">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Type a message..." aria-label="Type a message">
                        <button class="btn btn-primary" type="button"><i class="bi bi-send-fill"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- This assumes you have a footer file included below -->

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
