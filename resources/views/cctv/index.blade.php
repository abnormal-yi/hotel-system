<x-layouts.app title="CCTV Cameras">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div><h1 class="page-title">CCTV Cameras</h1><p class="page-description">Security camera management</p></div>
            <a href="{{ route('cctv.create') }}" class="btn-primary">Add Camera</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($cameras as $camera)
            <div class="card overflow-hidden">
                <div class="aspect-video bg-stone-900 flex items-center justify-center relative overflow-hidden">
                    @php
                        $url = $camera->stream_url ?? '';
                        $isHls = str_contains($url, '.m3u8');
                        $isYoutube = str_contains($url, 'youtube.com/watch') || str_contains($url, 'youtu.be') || str_contains($url, 'youtube.com/embed');
                        $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $url);
                    @endphp
                    @if($url)
                        @if($isYoutube)
                            @php
                                preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches);
                                $embedId = $matches[1] ?? '';
                            @endphp
                            @if($embedId)
                            <iframe src="https://www.youtube.com/embed/{{ $embedId }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            @endif
                        @elseif($isHls)
                            <video class="w-full h-full" controls preload="none">
                                <source src="{{ $url }}" type="application/x-mpegURL">
                            </video>
                        @elseif($isImage)
                            <img src="{{ $url }}" alt="{{ $camera->name }}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none';this.parentElement.querySelector('.fallback').style.display='flex'">
                            <div class="fallback absolute inset-0 flex items-center justify-center text-stone-500" style="display:none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center text-stone-500 gap-2 p-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                                <span class="text-xs text-stone-400 break-all">{{ $url }}</span>
                            </div>
                        @endif
                    @else
                    <div class="flex items-center justify-center text-stone-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                    </div>
                    @endif
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $camera->status === 'online' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                            {{ $camera->status }}
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $camera->name }}</h3>
                            <p class="text-sm text-muted-foreground">{{ $camera->location }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('cctv.edit', $camera->id) }}" class="btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('cctv.destroy', $camera->id) }}" class="inline" onsubmit="return confirm('Remove this camera?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-ghost btn-sm text-destructive">X</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-muted-foreground">
                <p class="text-lg mb-2">No cameras added yet</p>
                <a href="{{ route('cctv.create') }}" class="btn-primary">Add First Camera</a>
            </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
