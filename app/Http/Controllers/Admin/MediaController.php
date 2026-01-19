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

        // Capture dimensions
        $width = null;
        $height = null;
        try {
             if (str_starts_with($file->getMimeType(), 'image/')) {
                 $dims = getimagesize($file->getRealPath());
                 if ($dims) {
                     $width = $dims[0];
                     $height = $dims[1];
                 }
             }
        } catch (\Exception $e) {}

        Media::create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
            'width' => $width,
            'height' => $height,
        ]);

        return back()->with('toast', ['tone'=>'success','title'=>'Uploaded','message'=>'File uploaded.']);
    }

    public function show(Media $media)
    {
        $media->loadCount(['posts', 'pages']);
        return view('admin.media.show', compact('media'));
    }

    public function update(Request $request, Media $media)
    {
        $data = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:1000'],
        ]);

        $media->update($data);

        return back()->with('toast', ['tone'=>'success','title'=>'Saved','message'=>'Metadata updated.']);
    }

    public function destroy(Media $media)
    {
        // Safety Check: Prevent delete if used
        $inUsePost = \App\Models\Post::where('featured_image_id', $media->id)->exists();
        $inUsePage = \App\Models\Page::where('featured_image_id', $media->id)->exists();

        if ($inUsePost || $inUsePage) {
            return back()->with('toast', [
                'tone' => 'danger',
                'title' => 'Cannot Delete',
                'message' => 'This media is currently used by a Post or Page and cannot be deleted.'
            ]);
        }

        try { Storage::disk($media->disk)->delete($media->path); } catch (\Throwable $e) {}
        $media->delete();

        return back()->with('toast', ['tone'=>'danger','title'=>'Deleted','message'=>'Media deleted.']);
    }
}
