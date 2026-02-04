<x-guest-layout>
    <h1 class="text-2xl font-semibold mb-4">Welcome to homepage</h1>

    @foreach ($users as $user)
        <p class="font-medium">{{ $user->name }}</p>
        <p class="text-gray-600 mb-2">{{ $user->email }}</p>
    @endforeach
</x-guest-layout>