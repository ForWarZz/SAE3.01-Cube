<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Chatbot</title>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            * {
                box-sizing: border-box;
                font-family: 'Figtree', sans-serif;
                margin: 0;
            }

            html,
            body {
                height: 100%;
                margin: 0;
                padding: 0;
                background-color: #f9fafb;
                overflow: hidden;
            }

            body {
                display: flex;
                flex-direction: column;
            }

            #messageArea {
                flex-grow: 1;
                overflow-y: auto;
                width: 100%;
                scroll-behavior: smooth;

                padding: 0 !important;
            }

            ol.chat {
                padding: 20px !important;
                margin: 0 !important;
            }

            form {
                flex-shrink: 0;
                background: white;
                border-top: 1px solid #e5e7eb;
                width: 100%;
                position: relative;
                z-index: 10;
            }

            #userText {
                width: 100%;
                height: 60px !important;
                border: none !important;
                padding: 0 1.5rem !important;
                outline: none;
                font-size: 1rem;
                background: white;
                color: #1f2937;
            }

            #userText:focus {
                background-color: #ffffff;
            }

            .chatbot .msg ul,
            .chatbot .msg ol {
                display: block !important;
                margin: 0.5rem 0 !important;
                padding-left: 1.5rem !important;
            }
            .chatbot .msg ul {
                list-style-type: disc !important;
            }
            .chatbot .msg ol {
                list-style-type: decimal !important;
            }
            .chatbot .msg li {
                display: list-item !important;
                margin-bottom: 0.25rem;
            }

            .chatbot .msg strong {
                font-weight: 600;
                color: #111827;
            }
            .chatbot .msg p {
                margin-bottom: 0.5rem;
                line-height: 1.5;
            }
            .chatbot .msg p:last-child {
                margin-bottom: 0;
            }

            .visitor {
                justify-content: flex-end;
                display: flex;
                margin-bottom: 1rem;
            }
            .visitor .msg {
                background-color: #2563eb !important;
                color: white !important;
                border-radius: 1rem 1rem 0 1rem !important;
                padding: 0.75rem 1rem !important;
                max-width: 85%;
                font-size: 0.95rem;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            .chatbot {
                justify-content: flex-start;
                display: flex;
                margin-bottom: 1rem;
            }
            .chatbot .msg {
                background-color: white !important;
                color: #374151 !important;
                border: 1px solid #e5e7eb;
                border-radius: 1rem 1rem 1rem 0 !important;
                padding: 1rem !important;
                max-width: 90%;
                font-size: 0.95rem;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }

            @keyframes mercuryTypingAnimation {
                0% {
                    transform: translateY(0px);
                    opacity: 0.6;
                }
                28% {
                    transform: translateY(-5px);
                    opacity: 1;
                }
                44% {
                    transform: translateY(0px);
                    opacity: 0.6;
                }
            }
            .loading-dots {
                display: inline-block;
                padding: 5px 0;
            }
            .loading-dots .dot {
                animation: mercuryTypingAnimation 1.5s infinite ease-in-out;
                background-color: #6b7280;
                border-radius: 50%;
                display: inline-block;
                height: 6px;
                width: 6px;
                margin-right: 4px;
            }
            .loading-dots .dot:nth-child(1) {
                animation-delay: 200ms;
            }
            .loading-dots .dot:nth-child(2) {
                animation-delay: 300ms;
            }
            .loading-dots .dot:nth-child(3) {
                animation-delay: 400ms;
            }
            .time {
                font-size: 0.7rem;
                color: #9ca3af;
                margin-top: 4px;
                display: block;
                text-align: right;
            }

            .visitor .time {
                color: #c9ced4;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <script src="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/chat.js"></script>
    </body>
</html>
