@extends('layouts.main')

@section('title', 'Lịch sử trò chuyện')

@section('chat_history_content')

    {{-- CSS Riêng cho trang này --}}
    <style>
        /* Ẩn nút chatbot mặc định ở góc để tránh trùng lặp */
        #chatbot-button,
        #chatbot-box {
            display: none !important;
        }

        /* Đẩy nội dung xuống để không bị Header che mất */
        .history-page-wrapper {
            padding-top: 90px;
            margin-bottom: 200px;
            height: 500px;
            padding-bottom: 60px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        /* Style cho khung chat */
        .chat-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #0db27f 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .chat-body {
            height: 600px;
            overflow-y: auto;
            padding: 20px;
            background: #fff;
        }

        /* Bong bóng chat */
        .message-row {
            display: flex;
            margin-bottom: 20px;
            align-items: flex-end;
        }

        .message-row.user {
            flex-direction: row-reverse;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .avatar.bot {
            background: linear-gradient(to right, #11998e, #38ef7d);
            margin-right: 10px;
        }

        .avatar.user {
            background: linear-gradient(to right, #2980b9, #6dd5fa);
            margin-left: 10px;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 15px;
            position: relative;
            font-size: 0.95rem;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .message-row.user .message-content {
            background: #007bff;
            color: white;
            border-bottom-right-radius: 2px;
        }

        .message-row.bot .message-content {
            background: #f1f0f0;
            color: #333;
            border-bottom-left-radius: 2px;
        }

        .message-time {
            font-size: 0.75rem;
            margin-top: 5px;
            opacity: 0.7;
        }

        .message-row.user .message-time {
            text-align: right;
            color: #e0e0e0;
        }

        .intent-badge {
            font-size: 0.7rem;
            background: rgba(0, 0, 0, 0.1);
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
    </style>

    <div class="history-page-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card chat-card">
                        <div class="chat-header">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    style="margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                                <h4 class="m-0 text-white font-weight-bold">Lịch sử trò chuyện</h4>
                            </div>
                            <p class="small m-0 text-white-50">Xem lại các cuộc hội thoại của bạn với trợ lý ảo</p>
                        </div>

                        <div class="chat-body" id="chatContainer">
                            @forelse ($histories as $chat)
                                {{-- USER MESSAGE --}}
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="msg-box" style="max-width: 75%;">
                                        <div class="bg-primary text-white p-3 rounded text-break">
                                            {{ $chat->message }}
                                        </div>
                                        <small
                                            class="text-muted d-block text-end">{{ $chat->created_at->format('H:i') }}</small>
                                    </div>
                                </div>

                                {{-- BOT REPLY --}}
                                @if(!empty($chat->reply))
                                    <div class="message-item bot">
                                        <div class="bot-row">
                                            <div class="bot-avatar">🤖</div>
                                            <div class="message-bubble">
                                                {{-- Kiểm tra xem reply là Mảng hay Chuỗi --}}
                                                @if(is_array($chat->reply))
                                                    @foreach($chat->reply as $line)
                                                        {{-- Dùng {!! !!} để hiển thị HTML (Nút đặt sân) --}}
                                                        <div class="mb-2">{!! $line !!}</div>
                                                    @endforeach
                                                @else
                                                    {{-- Trường hợp dữ liệu cũ là chuỗi --}}
                                                    <div>{!! $chat->reply !!}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="message-time ms-5">
                                            Bot phản hồi • {{ $chat->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <svg width="64" height="64" class="text-muted" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h5 class="text-muted">Chưa có lịch sử trò chuyện</h5>
                                    <p class="text-muted small">Hãy bắt đầu cuộc hội thoại để được hỗ trợ đặt sân.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="card-footer bg-white text-center py-3 border-top">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="feather-arrow-left mr-2"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script cuộn xuống cuối --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var chatContainer = document.getElementById("chatContainer");
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>

@endsection