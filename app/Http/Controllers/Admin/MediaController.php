<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $items = Media::query()
            ->when($q !== '', function($qr) use ($q) {
                $qr->where('original_name','like',"%{$q}%")
                   ->orWhere('path','like',"%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        return view('admin.media.index', compact('items','q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => ['required','file','mimes:jpg,jpeg,png,webp,gif','max:4096'],
        ]);

        $file = $data['file'];
        $disk = 'public';
        $path = $file->store('uploads', $disk);

        Media::create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('toast', ['tone'=>'success','title'=>'Uploaded','message'=>'File uploaded.']);
    }

    public function destroy(Media $media)
    {
        try { Storage::disk($media->disk)->delete($media->path); } catch (\Throwable $e) {}
        $media->delete();

        return back()->with('toast', ['tone'=>'danger','title'=>'Deleted','message'=>'Media deleted.']);
    }
}
