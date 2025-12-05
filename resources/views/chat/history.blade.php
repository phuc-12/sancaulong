{{-- resources/views/chat/history.blade.php --}}
@extends('layouts.main') {{-- hoặc layout bạn đang dùng --}}

@section('title', 'Lịch sử trò chuyện với Chatbot')

@section('chat_history_content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6 text-center">
                <h1 class="text-2xl md:text-3xl font-bold flex items-center justify-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Lịch sử trò chuyện với AI đặt sân
                </h1>
                <p class="mt-2 text-purple-100">Tất cả tin nhắn của bạn với chatbot đều được lưu lại</p>
            </div>

            <!-- Chat Container -->
            <div class="p-4 md:p-6 max-h-screen overflow-y-auto" id="chatContainer">
                @forelse ($histories as $chat)
                    <div class="mb-6 flex {{ $chat->message ? 'justify-end' : 'justify-start' }} animate-fade-in">
                        <div class="flex gap-3 max-w-xl {{ $chat->message ? 'flex-row-reverse' : '' }}">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                @if($chat->message)
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg">
                                        {{ substr(auth()->user()->fullname ?? 'U', 0, 1) }}
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Message Bubble -->
                            <div class="{{ $chat->message ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white' : 'bg-gray-100 text-gray-800' }} 
                                        rounded-2xl px-5 py-3 shadow-md max-w-md break-words">
                                @if($chat->message)
                                    <!-- Tin nhắn của người dùng -->
                                    <div class="font-medium">{{ $chat->message }}</div>
                                @else
                                    <!-- Phản hồi của bot (có thể là mảng nhiều tin) -->
                                    @if(is_array($chat->reply))
                                        @foreach($chat->reply as $reply)
                                            <div class="mb-3 last:mb-0">
                                                {!! nl2br(e($reply)) !!}
                                            </div>
                                        @endforeach
                                    @else
                                        {!! nl2br(e($chat->reply)) !!}
                                    @endif
                                @endif

                                <!-- Thời gian + intent nhỏ xíu ở góc -->
                                <div class="text-xs opacity-70 mt-2 text-right">
                                    {{ $chat->created_at->format('d/m/Y H:i') }}
                                    @if($chat->intent && $chat->intent !== 'unknown')
                                        <span class="ml-2 px-2 py-1 rounded-full bg-black bg-opacity-20 text-xs">
                                            {{ $chat->intent }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <p class="text-lg">Chưa có lịch sử trò chuyện nào</p>
                        <p class="mt-2">Hãy bắt đầu chat với bot để đặt sân nào!</p>
                    </div>
                @endforelse
            </div>

            <!-- Nút quay lại -->
            <div class="bg-gray-50 border-t px-6 py-4 text-center">
                <a href="{{ url()->previous() }}" 
                   class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-medium px-6 py-3 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    #chatContainer {
        min-height: 70vh;
        max-height: 80vh;
    }
</style>

@endsection