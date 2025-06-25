<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] items-center flex p-6 lg:p-8 min-h-screen flex-col">
    <div class="w-full max-w-5xl bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Usuários Ativos</h1>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($sessions as $session)
                <li class="py-4 px-6 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="shrink-0">
                            <svg class="w-8 h-8 rounded-full" data-slot="icon" aria-hidden="true" fill="none" stroke-width="1.5" stroke="white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                {{ $session->user->name }}
                            </p>
                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                {{ $session->user->email }}
                            </p>
                        </div>
                        <div class="inline-flex flex-col items-end font-semibold text-base text-gray-900 dark:text-white">
                            <p>ativo {{ Carbon\Carbon::parse($session->last_activity)->diffForHumans() }}</p>
                            <p class="text-xs">({{$session->session_count }} {{$session->session_count > 1 ? "sessões" : "sessão"}})</p>
                        </div>
                    </div>
                </li>
            @empty
                <li>
                    <p class="text-white">Nenhum usuário logado</p>
                </li>
            @endforelse
        </ul>
    </div>
</body>
</html>